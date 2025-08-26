<?php
// index.php
require __DIR__ . '/db.php';

// Récupération des filtres GET
$q       = trim($_GET['q'] ?? '');
$level   = trim($_GET['level'] ?? '');
$source  = trim($_GET['source'] ?? '');
$host    = trim($_GET['host'] ?? '');
$from    = trim($_GET['from'] ?? ''); // ISO 8601 ou "YYYY-MM-DD"
$to      = trim($_GET['to'] ?? '');
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 50;

$where = [];
$params = [];

// Filtres dynamiques
if ($q !== '') {
    $where[] = "(message LIKE :q OR payload_json LIKE :q OR user_id LIKE :q OR request_id LIKE :q)";
    $params[':q'] = '%' . $q . '%';
}
if ($level !== '') { $where[] = "level = :level";   $params[':level'] = $level; }
if ($source !== ''){ $where[] = "source = :source"; $params[':source'] = $source; }
if ($host !== '')  { $where[] = "host = :host";     $params[':host'] = $host; }
if ($from !== '')  { $where[] = "ts >= :from";      $params[':from'] = normalizeDate($from); }
if ($to !== '')    { $where[] = "ts <= :to";        $params[':to']   = normalizeDate($to, true); }

$sqlWhere = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// Compte total
$countSql = "SELECT COUNT(*) FROM logs $sqlWhere";
$stmt = db()->prepare($countSql);
$stmt->execute($params);
$total = (int)$stmt->fetchColumn();

$offset = ($page - 1) * $perPage;

// Données page
$listSql = "SELECT id, ts, level, source, host, message FROM logs
            $sqlWhere
            ORDER BY ts DESC, id DESC
            LIMIT :limit OFFSET :offset";
$stmt = db()->prepare($listSql);
foreach ($params as $k => $v) $stmt->bindValue($k, $v);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Valeurs distinctes pour listes déroulantes
$levels  = fetchDistinct('level');
$sources = fetchDistinct('source');
$hosts   = fetchDistinct('host');

function fetchDistinct(string $col): array {
    $stmt = db()->query("SELECT DISTINCT $col AS v FROM logs WHERE $col IS NOT NULL AND $col != '' ORDER BY v ASC");
    return array_map(fn($r) => $r['v'], $stmt->fetchAll(PDO::FETCH_ASSOC));
}

function normalizeDate(string $s, bool $end=false): string {
    // Si "YYYY-MM-DD", on complète en ISO basique
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $s)) {
        return $end ? ($s . 'T23:59:59Z') : ($s . 'T00:00:00Z');
    }
    // sinon on suppose que c'est déjà ISO
    return $s;
}

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Pagination
$totalPages = max(1, (int)ceil($total / $perPage));
function linkWith(array $overrides): string {
    $q = array_merge($_GET, $overrides);
    return '?' . http_build_query($q);
}
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Mini Log Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;margin:20px;}
input,select{padding:6px;margin:2px;}
table{border-collapse:collapse;width:100%;margin-top:10px;}
th,td{border:1px solid #ccc;padding:6px;text-align:left;font-size:14px;}
.badge{padding:2px 6px;border-radius:4px;background:#eee;}
.level-ERROR{background:#ffd6d6;}
.level-WARN{background:#fff3cd;}
.level-INFO{background:#d7f3ff;}
.code{font-family:ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;}
.pagination a{margin:0 2px; text-decoration:none;}
.header{display:flex;gap:10px;align-items:center;flex-wrap:wrap;}
</style>
</head>
<body>
  <h1>Mini Log Dashboard</h1>

  <form method="get" class="header">
    <input type="text" name="q" placeholder="Rechercher (message, payload, userId…)" value="<?=h($_GET['q']??'')?>">
    <select name="level">
      <option value="">Niveau (tous)</option>
      <?php foreach ($levels as $lv): ?>
        <option value="<?=h($lv)?>" <?= $lv === $level ? 'selected' : '' ?>><?=h($lv)?></option>
      <?php endforeach; ?>
    </select>
    <select name="source">
      <option value="">Source (toutes)</option>
      <?php foreach ($sources as $s): ?>
        <option value="<?=h($s)?>" <?= $s === $source ? 'selected' : '' ?>><?=h($s)?></option>
      <?php endforeach; ?>
    </select>
    <select name="host">
      <option value="">Host (tous)</option>
      <?php foreach ($hosts as $hhost): ?>
        <option value="<?=h($hhost)?>" <?= $hhost === $host ? 'selected' : '' ?>><?=h($hhost)?></option>
      <?php endforeach; ?>
    </select>
    <input type="text" name="from" placeholder="Depuis (YYYY-MM-DD ou ISO)" value="<?=h($from)?>">
    <input type="text" name="to" placeholder="Jusqu'à (YYYY-MM-DD ou ISO)" value="<?=h($to)?>">
    <button type="submit">Filtrer</button>
    <a href="<?=linkWith(['page'=>1])?>">Réinitialiser</a>
    <a href="export_csv.php?<?=http_build_query($_GET)?>">Export CSV</a>
  </form>

  <p>
    <span class="badge">Résultats: <?= $total ?></span>
    <?php if ($totalPages>1): ?>
      <span class="pagination">
        Page:
        <?php for ($p=1;$p<=$totalPages;$p++): ?>
          <?php if ($p===$page): ?>
            <strong>[<?=$p?>]</strong>
          <?php else: ?>
            <a href="<?=h(linkWith(['page'=>$p]))?>"><?=$p?></a>
          <?php endif; ?>
        <?php endfor; ?>
      </span>
    <?php endif; ?>
  </p>

  <table>
    <thead>
      <tr>
        <th>Heure</th>
        <th>Niveau</th>
        <th>Source</th>
        <th>Host</th>
        <th>Message</th>
      </tr>
    </thead>
    <tbody>
    <?php if (!$rows): ?>
      <tr><td colspan="5"><em>Aucun log pour ces filtres.</em></td></tr>
    <?php endif; ?>
    <?php foreach ($rows as $r): ?>
      <tr class="level-<?=h($r['level'])?>">
        <td><a class="code" href="view.php?id=<?= (int)$r['id'] ?>"><?=h($r['ts'])?></a></td>
        <td><span class="badge"><?=h($r['level'])?></span></td>
        <td><?=h($r['source'])?></td>
        <td class="code"><?=h($r['host'])?></td>
        <td><?=h($r['message'])?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>
