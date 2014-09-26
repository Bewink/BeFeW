<?php
define('DEBUG', true);

if(DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_STRICT);
    ini_set('display_errors', 0);
}
