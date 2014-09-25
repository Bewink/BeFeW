<?php

namespace Logger;

class Logger {
    public static function info($message, $die = false) {
        if($die) {
            die('<p class="befew-logger-info">'.$message.'</p>');
        } else {
            echo '<p class="befew-logger-info">'.$message.'</p>';
        }
    }

    public static function warning($message, $die = false) {
        if($die) {
            die('<p class="befew-logger-warning">'.$message.'</p>');
        } else {
            echo '<p class="befew-logger-warning">'.$message.'</p>';
        }
    }

    public static function error($message, $die = false) {
        if($die) {
            die('<p class="befew-logger-error">'.$message.'</p>');
        } else {
            echo '<p class="befew-logger-error">'.$message.'</p>';
        }
    }
}