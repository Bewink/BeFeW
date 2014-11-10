<?php
global $DBH;

define('DEBUG', true);

if(DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    $DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} else {
    error_reporting(E_STRICT);
    ini_set('display_errors', 0);
    $DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
}
