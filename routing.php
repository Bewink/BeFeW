<?php

use vendors\BeFeW\Request as Request;
use vendors\BeFeW\Response as Response;

$url = Request::getGetVar('page', 'index', true);

/* Add your routes here */
$routes = array(
    'index' => 'Home/HomeController.php',
    'home' => 'Home/HomeController.php',
);

$routeFound = false;
foreach($routes as $key => $path) {
    if(strpos($url, $key) === 0) {
        $page = substr($url, strlen($key) + 1);
        $tplpath = BEFEW_BASE_URL.'src/'.dirname($path).'/View/';
        require('src/'.$routes[$key]);
        $routeFound = true;
        break;
    }
}

if($routeFound == false) {
    Response::throwStatus(404);
}