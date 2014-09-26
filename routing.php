<?php

use vendors\BeFeW\Request as Request;
use vendors\BeFeW\Logger as Logger;

$url = Request::getGetVar('page', 'index');

$routes = array(
    'index' => 'Acme/Home/HomeController.php'
);

if(Request::getVar($routes[$url]) != null) {
    if(DEBUG) {
        Logger::info('Route found by URL');
    }

    require('src/'.$routes[$url]);
} else {
    if(Request::getVar($routes['index']) != null) {
        if(DEBUG) {
            Logger::info('Route index used');
        }

        require('src/'.$routes['index']);
    } else {
        if(DEBUG) {
            Logger::warning('No route found, calling 404...');
        }

        require('app/404.php');
    }
}