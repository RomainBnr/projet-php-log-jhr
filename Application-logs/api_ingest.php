<?php
// api_ingest.php
require __DIR__ . '/db.php';
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Use POST']);
    exit;
}

// Accepte soit application/json, soit form-data (pour tester vite)
$raw = file_get_contents('php://input');
$ct  = $_SERVER['CONTENT_TYPE'] ?? '';
$data = [];

if (stripos($ct, 'application/json') !== false && $raw) {
    $data = json_decode($raw, true);
    if (!is_array($data)) $data = [];
} else {
    // fallback simple
    $data = $_POST;
}

// Valeurs par défaut
$nowIso = (new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d\TH:i:s\Z');

$ts         = $data['ts']         ?? $nowIso;
$level      = $data['level']      ?? 'INFO';
$source     = $data['source']     ?? 'unknown-app';
$host       = $data['host']       ?? ($_SERVER['REMOTE_ADDR'] ?? 'unknown-host');
$user_id    = $data['user_id']    ?? null;
$request_id = $data['request_id'] ?? null;
$message    = $data['message']    ?? '(no message)';
$payload    = $data['payload']    ?? null;

// si payload n'est pas string, on l'encode
if ($payload !== null && !is_string($payload)) {
    $payload = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

$sql = "INSERT INTO logs(ts, level, source, host, user_id, request_id, message, payload_json)
        VALUES(:ts, :level, :source, :host, :user_id, :request_id, :message, :payload)";
$stmt = db()->prepare($sql);
$stmt->execute([
    ':ts' => $ts,
    ':level' => $level,
    ':source' => $source,
    ':host' => $host,
    ':user_id' => $user_id,
    ':request_id' => $request_id,
    ':message' => $message,
    ':payload' => $payload
]);

echo json_encode(['ok' => true, 'id' => db()->lastInsertId()]);
