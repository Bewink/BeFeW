<?php

namespace vendors\BeFeW;

class Response {
    public static function throwStatus($code) {
        switch($code) {
            case 404:
                include($_SERVER['DOCUMENT_ROOT'].'/app/404.php');
                break;

            default:
                include($_SERVER['DOCUMENT_ROOT'].'/app/404.php');
        }
    }
}