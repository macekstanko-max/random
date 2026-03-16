<?php
$db = new PDO('sqlite:' . __DIR__ . '/profile.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec("CREATE TABLE IF NOT EXISTS interests (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);");

echo "Inicializace dokončena. Tabulka interests je připravená.\n";
