<?php
// export_csv.php
require __DIR__ . '/db.php';

// Réutiliser (copier/coller) la logique de filtres d'index.php (simplifiée)
$q       = trim($_GET['q'] ?? '');
$level   = trim($_GET['level'] ?? '');
$source  = trim($_GET['source'] ?? '');
$host    = trim($_GET['host'] ?? '');
$from    = trim($_GET['from'] ?? '');
$to      = trim($_GET['to'] ?? '');

$where=[]; $params=[];
if ($q!==''){ $where[]="(message LIKE :q OR payload_json LIKE :q OR user_id LIKE :q OR request_id LIKE :q)"; $params[':q']='%'.$q.'%'; }
if ($level!==''){ $where[]="level = :level"; $params[':level']=$level; }
if ($source!==''){ $where[]="source = :source"; $params[':source']=$source; }
if ($host!==''){ $where[]="host = :host"; $params[':host']=$host; }
if ($from!==''){ $where[]="ts >= :from"; $params[':from']=normalize($from); }
if ($to!==''){ $where[]="ts <= :to"; $params[':to']=normalize($to,true); }
$sqlWhere = $where ? ('WHERE '.implode(' AND ',$where)) : '';

$stmt = db()->prepare("SELECT * FROM logs $sqlWhere ORDER BY ts DESC, id DESC");
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="logs_export.csv"');

$out = fopen('php://output', 'w');
// En-têtes
fputcsv($out, array_keys($rows ? $rows[0] : [
    'id','ts','level','source','host','user_id','request_id','message','payload_json'
]));
foreach ($rows as $r) {
    fputcsv($out, $r);
}
fclose($out);

function normalize(string $s, bool $end=false): string {
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/',$s)) return $end ? $s.'T23:59:59Z' : $s.'T00:00:00Z';
    return $s;
}
