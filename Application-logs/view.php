<?php
// view.php
require __DIR__ . '/db.php';
function h($s){return htmlspecialchars((string)$s, ENT_QUOTES,'UTF-8');}

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare("SELECT * FROM logs WHERE id = :id");
$stmt->execute([':id'=>$id]);
$log = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$log) { http_response_code(404); echo "Log introuvable."; exit; }

?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Log #<?= (int)$log['id'] ?></title>
<style>
body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;margin:20px;}
pre{background:#f6f8fa;border:1px solid #ddd;padding:10px;overflow:auto;}
.badge{padding:2px 6px;border-radius:4px;background:#eee;}
.code{font-family:ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;}
</style>
</head>
<body>
  <p><a href="index.php">&larr; Retour</a></p>
  <h1>Log #<?= (int)$log['id'] ?></h1>
  <ul>
    <li><strong>Timestamp:</strong> <span class="code"><?=h($log['ts'])?></span></li>
    <li><strong>Niveau:</strong> <span class="badge"><?=h($log['level'])?></span></li>
    <li><strong>Source:</strong> <?=h($log['source'])?></li>
    <li><strong>Host:</strong> <span class="code"><?=h($log['host'])?></span></li>
    <?php if ($log['user_id']): ?><li><strong>User ID:</strong> <?=h($log['user_id'])?></li><?php endif; ?>
    <?php if ($log['request_id']): ?><li><strong>Request ID:</strong> <span class="code"><?=h($log['request_id'])?></span></li><?php endif; ?>
  </ul>
  <h3>Message</h3>
  <pre><?=h($log['message'])?></pre>

  <h3>Payload JSON (brut)</h3>
  <pre><?= $log['payload_json'] !== null && $log['payload_json'] !== '' ? h($log['payload_json']) : '(aucun)' ?></pre>
</body>
</html>
