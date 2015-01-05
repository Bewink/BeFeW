<?php

namespace vendor\Befew;

class Controller {
    protected $request;
    protected $tplpath;

    public function __construct($url, $action) {
        $action = $action . 'Action';
        $reflector = new \ReflectionClass(get_class($this));

        $fn = DIRECTORY_SEPARATOR . $reflector->getNamespaceName();
        $this->tplpath = str_replace('/', DIRECTORY_SEPARATOR, $fn) . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR;

        $this->request = new Request($url);

        if(method_exists($this, $action)) {
            $this->$action();
        } else {
            $this->errorAction();
        }
    }

    public function errorAction($code = 404) {
        Response::throwStatus($code);
    }
}