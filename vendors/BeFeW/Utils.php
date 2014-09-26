<?php

namespace BeFeW\Utils;

class Utils {
    public static function getVar(&$var, $default = null, $secure = 'mysql') {
        if(!isset($var)) {
            return $default;
        } else if(empty($var)) {
            return $default;
        } else {
            if($secure == 'mysql') {
                return mysql_real_escape_string($var);
            } else if($secure == 'html') {
                return htmlentities($var);
            } else {
                return $var;
            }
        }
    }
}