<?php
global $DBH;

define('DEBUG', true);
define('CACHE_TWIG', 'cache' . DIRECTORY_SEPARATOR . 'twig');
define('STYLES_FOLDER', 'css');
define('TEMPLATES_FOLDER', 'twig');
define('SCRIPTS_FOLDER', 'js');

if(DEBUG) {
    error_reporting(-1);
    ini_set('display_errors', 1);
    $DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    $DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
}
