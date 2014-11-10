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
                if(strpos($message, "\n") !== false) {
                    die('<pre class="befew-logger-' . $level . '">' . $message . '</pre>');
                } else {
                    die('<p class="befew-logger-' . $level . '">' . $message . '</p>');
                }
            } else {
                if(strpos($message, "\n") !== false) {
                    echo '<pre class="befew-logger-' . $level . '">' . $message . '</pre>';
                } else {
                    echo '<p class="befew-logger-' . $level . '">' . $message . '</p>';
                }
            }
        } else {
            ob_start();
            var_dump($message);
            $debug = ob_get_clean();

            if ($die) {
                die('<div class="befew-logger-' . $level . '">' . $debug . '</div>');
            } else {
                echo '<div class="befew-logger-' . $level . '">' . $debug . '</div>';
            }
        }
    }
}