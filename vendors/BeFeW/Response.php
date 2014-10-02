<?php

namespace vendors\BeFeW;

class Response {
    public static function throwStatus($code) {
        switch($code) {
            case 404:
                include(BEFEW_BASE_URL.'app/404.php');
                break;

            default:
                include(BEFEW_BASE_URL.'app/404.php');
        }
    }
}