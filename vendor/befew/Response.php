<?php

namespace vendor\Befew;

class Response {
    public static function throwStatus($code) {
        switch($code) {
            case 404:
                header("HTTP/1.0 404 Not Found");
                include(BEFEW_BASE_URL.'app/404.php');
                break;

            default:
                include(BEFEW_BASE_URL.'app/404.php');
        }
    }
}