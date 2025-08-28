<?php
require __DIR__ . '/db.php';

$q = trim($_GET['q'] ?? '');  // texte libre
$pdo = get_pdo();

// Requête : on recherche le texte dans host/source/level/message
$sql = "SELECT id, created_at, host, source, level, message
        FROM logs
        WHERE (:q = '' 
               OR host   LIKE :like
               OR source LIKE :like
               OR level  LIKE :like
               OR message LIKE :like)
        ORDER BY created_at DESC, id DESC
        LIMIT 200";
$stmt = $pdo->prepare($sql);
$like = '%'.$q.'%';
$stmt->execute([
    ':q'    => $q,
    ':like' => $like
]);
$rows = $stmt->fetchAll();
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Dashboard de visualisation des logs</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    /* Style minimaliste, pas de framework */
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:#f7f7f7;margin:0;padding:0}
    .container{max-width:1100px;margin:32px auto;padding:0 16px}
    h1{font-weight:600;margin:0 0 16px}
    .toolbar{display:flex;gap:12px;align-items:center;margin:16px 0}
    input[type="text"]{flex:1;padding:10px 12px;border:1px solid #ccc;border-radius:6px;font-size:14px}
    .btn{display:inline-block;padding:10px 14px;border:1px solid #2d7a44;background:#2e7d32;color:#fff;
         text-decoration:none;border-radius:6px;font-size:14px}
    .btn:hover{filter:brightness(0.95)}
    .table{width:100%;border-collapse:separate;border-spacing:0;background:#fff;border:1px solid #e5e5e5;border-radius:8px;overflow:hidden}
    .table th,.table td{padding:10px 12px;font-size:14px;border-bottom:1px solid #efefef}
    .table th{background:#111;color:#fff;text-align:left}
    .table tr:last-child td{border-bottom:none}
    .badge{padding:3px 8px;border-radius:999px;font-weight:600;font-size:12px;display:inline-block}
    .badge.INFO{background:#e8f5e9;color:#1b5e20}
    .badge.WARNING{background:#fff8e1;color:#8a6d00}
    .badge.ERROR{background:#ffebee;color:#b71c1c}
    .muted{color:#666}
    .col-num{width:48px}
  </style>
</head>
<body>
<div class="container">
  <h1>Dashboard de visualisation des logs</h1>

  <form class="toolbar" method="get" action="index.php">
      <input type="text" name="q" placeholder="Rechercher un log..." value="<?= htmlspecialchars($q, ENT_QUOTES) ?>">
      <a class="btn" href="export.php?q=<?= urlencode($q) ?>">Exporter en CSV</a>
  </form>

  <table class="table">
    <thead>
      <tr>
        <th class="col-num">#</th>
        <th>Date</th>
        <th>Machine</th>
        <th>Application</th>
        <th>Niveau</th>
        <th>Message</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$rows): ?>
        <tr><td colspan="6" class="muted" style="text-align:center">Aucun résultat</td></tr>
      <?php else: ?>
        <?php
        $i = 1;
        foreach ($rows as $r):
            $date = (new DateTime($r['created_at']))->format('Y-m-d H:i');
        ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($date) ?></td>
            <td><?= htmlspecialchars($r['host']) ?></td>
            <td><?= htmlspecialchars($r['source']) ?></td>
            <td><span class="badge <?= htmlspecialchars($r['level']) ?>"><?= htmlspecialchars($r['level']) ?></span></td>
            <td><?= htmlspecialchars($r['message']) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
  <p class="muted" style="margin-top:8px">Affichage des 200 derniers éléments (trier par date décroissante).</p>
</div>
</body>
</html>
