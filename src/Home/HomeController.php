<?php

use vendors\BeFeW\Response as Response;
use vendors\BeFeW\Template as Template;
use src\Home\Entity\User as User;
use vendors\BeFeW\Logger as Logger;

/* Use $page for the switch, and $tplpath for the template engine */
switch($page) {
    case '':
    case 'home':
        $tpl = new Template($tplpath);
        $tpl->setTitle('| Home');
        $tpl->addStyle('default.css');

        $user = new User();
        $user->setPseudo('Elian');
        $user->save();

        $tpl->render('index.php');
        break;

    default:
        Response::throwStatus(404);
}