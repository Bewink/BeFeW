<?php

require('init.php');

use vendors\BeFeW\Logger as Logger;


$src = dir('src');
$output = array();

while (false !== ($entry = $src->read())) {
    if (is_dir('src/' . $entry . '/Entity')) {
        $dir = 'src/' . $entry . '/Entity';
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' AND $file != '..') {
                    $classname = substr($file, 0, strrpos($file, '.'));
                    $class = 'src\\' . $entry . '\\Entity\\' . $classname;
                    $obj = new $class();
                    $output = array_merge($output, $obj->tableMatches(isset($_GET['repair'])));
                }
            }
            closedir($dh);
        }
    }
}

echo '<table border="1"><tr><th>Error</th><th>Fix</th><th>Query</th></tr>';

foreach ($output as $line) {
    echo '<tr><td>' . $line['error'] . '</td><td>' . ((isset($line['fix'])) ? $line['fix'] : 'Aucune action') . '</td><td><pre>' . ((isset($line['query'])) ? $line['query'] : 'Aucune requête') . '</pre></td></tr>';
}

echo '</table>';

if(!isset($_GET['repair'])) {
    echo '<a href="?repair">Repair database</a>';
}