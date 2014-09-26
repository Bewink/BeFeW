<?php

use BeFeW\Request\Request as Request;

$url = Request::getGetVar('page', 'index');

$routes = array(
    'index' => 'Acme/Home/HomeController.php'
);

if(Request::getVar($routes[$url]) != null) {
    require($routes[$url]);
} else {
    if(Request::getVar($routes['index']) != null) {
        require($routes['index']);
    } else {
        require('app/404.php');
    }
}