<?php
// db.php
// Ouvre (ou crée) une base SQLite dans le fichier data.sqlite à la racine.
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO('sqlite:' . __DIR__ . '/data.sqlite');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Activer les clés étrangères (par habitude)
        $pdo->exec('PRAGMA foreign_keys = ON;');
    }
    return $pdo;
}
