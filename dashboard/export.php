<?php
require __DIR__ . '/db.php';

$q = trim($_GET['q'] ?? '');
$pdo = get_pdo();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=logs_export.csv');

$out = fopen('php://output', 'w');
fputcsv($out, ['#','Date','Machine','Application','Niveau','Message']);

$sql = "SELECT id, created_at, host, source, level, message
        FROM logs
        WHERE (:q = '' 
               OR host   LIKE :like
               OR source LIKE :like
               OR level  LIKE :like
               OR message LIKE :like)
        ORDER BY created_at DESC, id DESC
        LIMIT 5000";
$stmt = $pdo->prepare($sql);
$like = '%'.$q.'%';
$stmt->execute([':q'=>$q, ':like'=>$like]);

$i = 1;
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $date = (new DateTime($r['created_at']))->format('Y-m-d H:i');
    fputcsv($out, [$i++, $date, $r['host'], $r['source'], $r['level'], $r['message']]);
}
fclose($out);
