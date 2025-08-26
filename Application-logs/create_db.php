<?php
// create_db.php
require __DIR__ . '/db.php';

$sql = <<<SQL
CREATE TABLE IF NOT EXISTS logs (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  ts TEXT NOT NULL,              -- horodatage ISO8601 (UTC de préférence)
  level TEXT,                    -- INFO/WARN/ERROR...
  source TEXT,                   -- nom de l'appli ou du service
  host TEXT,                     -- machine d'origine
  user_id TEXT,                  -- optionnel
  request_id TEXT,               -- optionnel (corrélation)
  message TEXT,                  -- message humain
  payload_json TEXT              -- JSON brut (facultatif)
);
CREATE INDEX IF NOT EXISTS idx_logs_ts ON logs(ts);
CREATE INDEX IF NOT EXISTS idx_logs_level ON logs(level);
CREATE INDEX IF NOT EXISTS idx_logs_source ON logs(source);
CREATE INDEX IF NOT EXISTS idx_logs_host ON logs(host);
SQL;

db()->exec($sql);
echo "OK: base et index créés.\n";
