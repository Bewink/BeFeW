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

function autoloader($classname) {
    $classname = str_replace("_", "\\", $classname);
    $classname = ltrim($classname, '\\');
    $fileName = '';
    if ($lastNsPos = strripos($classname, '\\'))
    {
        $namespace = substr($classname, 0, $lastNsPos);
        $classname = substr($classname, $lastNsPos + 1);
        $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $classname) . '.php';

    require $fileName;
}

spl_autoload_register('autoloader');