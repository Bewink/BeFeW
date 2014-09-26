<?php

if(!class_exists('PDO')) {
    exit('FATAL ERROR: PDO isn\'t enabled on this server');
}

$DBH = null;

try {
    $DBH = new PDO(DB_DRIVER.':dbname='.DB_NAME.';host='.DB_HOST, DB_USER, DB_PASSWORD);
} catch (PDOException $e) {
    echo 'WARNING: Database connection error: ' . $e->getMessage();
}

function autoloader($className) {
    $className = str_replace("_", "\\", $className);
    $className = ltrim($className, '\\');
    $fileName = '';
    if ($lastNsPos = strripos($className, '\\'))
    {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    require $fileName;
}

spl_autoload_register('autoloader');