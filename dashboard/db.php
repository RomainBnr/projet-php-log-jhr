<?php
// db.php : connexion PDO MySQL (à adapter à ton environnement)
$DB_HOST = '127.0.0.1';
$DB_NAME = 'logsdb';
$DB_USER = 'root';
$DB_PASS = ''; // ← mets ton mot de passe MySQL

function get_pdo(): PDO {
    global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS;
    $dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";
    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    return new PDO($dsn, $DB_USER, $DB_PASS, $opt);
}
