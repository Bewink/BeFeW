<?php

namespace vendors\BeFeW;

class Logger {
    public static function info($message, $die = false) {
        self::display('info', $message, $die);
    }

    public static function warning($message, $die = false) {
        self::display('warning', $message, $die);
    }

    public static function error($message, $die = false) {
        self::display('error', $message, $die);
    }

    public static function display($level, $message, $die = false)
    {
        if (is_string($message)) {
            if ($die) {
                die('<p class="befew-logger-' . $level . '">' . $message . '</p>');
            } else {
                echo '<p class="befew-logger-' . $level . '">' . $message . '</p>';
            }
        } else {
            if ($die) {
                die('<p class="befew-logger-' . $level . '"><pre>' . var_dump($message) . '</pre></p>');
            } else {
                echo '<p class="befew-logger-' . $level . '"><pre>' . var_dump($message) . '</pre></p>';
            }
        }
    }
}