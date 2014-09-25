<?php

namespace Request;

class Request {
    public static function getPostVar($id, $default = null, $secure = 'mysql') {
        if(!isset($_POST[$id])) {
            return $default;
        } else if(empty($_POST[$id])) {
            return $default;
        } else {
            if($secure == 'mysql') {
                return mysql_real_escape_string($_POST[$id]);
            } else if($secure == 'html') {
                return htmlentities($_POST[$id]);
            } else {
                return $_POST[$id];
            }
        }
    }

    public static function getGetVar($id, $default = null, $secure = 'mysql') {
        if(!isset($_GET[$id])) {
            return $default;
        } else if(empty($_GET[$id])) {
            return $default;
        } else {
            if($secure == 'mysql') {
                return mysql_real_escape_string($_GET[$id]);
            } else if($secure == 'html') {
                return htmlentities($_GET[$id]);
            } else {
                return $_GET[$id];
            }
        }
    }
}