<?php

require('init.php');

use vendors\BeFeW\Logger as Logger;


$src = dir('src');
$output = array();

while (false !== ($entry = $src->read())) {
    if(is_dir('src/' . $entry . '/Entity')) {
        $dir = 'src/' . $entry . '/Entity';
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if($file != '.' AND $file != '..') {
                    $classname = substr($file, 0, strrpos($file, '.'));
                    $class = 'src\\' . $entry . '\\Entity\\' . $classname;
                    $obj = new $class();
                    $output = array_merge($output, $obj->tableMatches());
                }
            }
            closedir($dh);
        }
    }
}

echo '<ul>';

foreach($output as $line) {
    echo '<li>' . $line['error'] . '</li>';
}

echo '</ul>';