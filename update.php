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

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>

    <title>BeFeW | Update Database</title>

    <link href='http://fonts.googleapis.com/css?family=Cutive+Mono' rel='stylesheet' type='text/css'/>
    <style type="text/css">
        html {
            font-family: "Cutive Mono", "Helvetica", sans-serif;
            font-weight: 300;
            font-size: 2rem;
            text-align: center;
            color: #272727;
            background-color: #272727;
        }

        body {
            padding: 2.5% 5%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border: 1px solid white;
            table-layout: fixed;
        }

        th {
            height: 25%;
            font-size: 0.8rem;
            background-color: rgba(127, 139, 148, 1);
            color: #DEDEDE;
        }

        th:nth-of-type(2) {
            border-right: 1px solid white;
            border-left: 1px solid white;
        }

        tr:nth-of-type(2n) td {
            background-color: rgba(49, 80, 104, 0.2);
        }

        tr:hover {
            background-color: rgba(0, 0, 0, 0.6);
            color: #DEDEDE;
        }

        tr:last-of-type td {
            border-bottom: none;
        }

        td {
            font-size: 0.5rem;
            padding: 10px 5px 10px 5px;
            background-color: rgba(30, 48, 62, 0.3);
            border-bottom: 1px solid white;
        }

        td:nth-of-type(2) {
            border-right: 1px solid white;
            border-left: 1px solid white;
        }

        td pre {
            word-break: break-all;
            word-wrap: break-word;
        }

        p#repair {
            margin-bottom: 0;
        }

        p#repair a {
            display: inline-block;
            color: #DEDEDE;
            text-decoration: none;
            padding: 10px 30px;
            background: #3D5961;
        }

        p#repair a:hover {
            background: #5D6972;
        }

        p#valid {
            color: #D4DBE0;
            font-weight: bold;
        }

        p#valid::before {
            content: '✓';
            color: #D4DBE0;
            font-size: 30px;
        }
    </style>
</head>
<body>
<?php
if (count($output) > 0) {
    ?>
    <table border="1">
        <tr>
            <th>
                Error
            </th>
            <th>
                Fix
            </th>
            <th>
                Query
            </th>
        </tr>
        <?php
        foreach ($output as $line) {
            ?>
            <tr>
                <td>
                    <?php echo $line['error']; ?>
                </td>
                <td>
                    <?php echo (isset($line['fix'])) ? $line['fix'] : 'Aucune action'; ?>
                </td>
                <td>
                    <pre><?php echo (isset($line['query'])) ? $line['query'] : 'Aucune requête'; ?></pre>
                </td>
            </tr>
        <?php
        }
        ?>
    </table>

    <?php
    if (!isset($_GET['repair'])) {
        ?>
        <p id="repair">
            <a href="?repair">Repair database</a>
        </p>
    <?php
    }
} else {
    ?>
    <p id="valid">
        Database matches!
    </p>
<?php
}
?>
</body>
</html>