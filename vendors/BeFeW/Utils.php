<?php

namespace vendors\BeFeW;

class Utils {
    private static $SQLMAP = [
        'tinyint' => '(4)',
        'smallint' => '(6)',
        'mediumint' => '(9)',
        'int' => '(11)',
        'bigint' => '(20)',
        'decimal' => '(10, 0)',
        'float' => '',
        'double' => '',
        'bit' => '(64)',
        'date' => '',
        'datetime' => '',
        'timestamp' => '',
        'time' => '',
        'year' => '(4)',
        'char' => '(30)',
        'varchar' => '(255)',
        'tinytext' => '',
        'text' => '',
        'mediumtext' => '',
        'longtext' => '',
        'binary' => '(255)',
        'longblob' => '',
        'tinyblob' => '',
        'mediumblob' => '',
        'blob' => '',
        'geometry' => '',
        'point' => '',
        'linestring' => '',
        'polygon' => '',
        'multipoint' => '',
        'multilinestring' => '',
        'multipolygon' => '',
        'geometrycollection' => ''
    ];

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

    public static function getSQLDefaultLengthForType($type) {
        return (isset(self::$SQLMAP[$type])) ? self::$SQLMAP[$type] : null;
    }
}