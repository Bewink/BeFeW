<?php

namespace vendors\BeFeW;

class Request extends Utils {
    public static function getPostVar($id, $default = null, $secure = false) {
        return parent::getVar($_POST[$id], $default, $secure);
    }

    public static function getGetVar($id, $default = null, $secure = false) {
        return parent::getVar($_GET[$id], $default, $secure);
    }
}