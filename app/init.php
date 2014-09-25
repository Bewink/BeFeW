<?php

if(!class_exists('PDO')) {
    exit('FATAL ERROR: PDO isn\'t enabled on this server');
}

try {
    $dbh = new PDO(DB_DRIVER.':dbname='.DB_NAME.';host='.DB_HOST, DB_USER, DB_PASSWORD);
} catch (PDOException $e) {
    echo 'WARNING: Database connection error: ' . $e->getMessage();
}

define('DB', $dbh);