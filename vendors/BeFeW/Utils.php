<?php

namespace vendors\BeFeW;

class Utils {
    public static function getVar(&$var, $default = null, $secure = false) {
        if(!isset($var)) {
            return $default;
        } else if(empty($var)) {
            return $default;
        } else {
            if($secure) {
                return htmlspecialchars($var);
            } else {
                return $var;
            }
        }
    }
}