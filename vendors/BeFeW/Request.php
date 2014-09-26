<?php

namespace BeFeW\Request;

use BeFeW\Utils\Utils as Utils;

class Request extends Utils {
    public static function getPostVar($id, $default = null, $secure = 'mysql') {
        return parent::getVar($_POST[$id], $default, $secure);
    }

    public static function getGetVar($id, $default = null, $secure = 'mysql') {
        return parent::getVar($_GET[$id], $default, $secure);
    }
}