<?php

use vendors\BeFeW\Response as Response;
use vendors\BeFeW\Template as Template;

/* Use $page for the switch, and $tplpath for the template engine */
switch($page) {
    case 'home':
        $tpl = new Template($tplpath);
        $tpl->setTitle('| Home');
        $tpl->addStyle('default.css');
        $tpl->render('index.php');
        break;

    default:
        Response::throwStatus(404);
}