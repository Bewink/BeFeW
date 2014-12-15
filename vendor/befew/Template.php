<?php

namespace vendor\Befew;

class Template {
    private $twig;
    private $path;

    public function __construct($path) {
        $this->path = $path;
        $loader = new \Twig_Loader_Filesystem(realpath($_SERVER["DOCUMENT_ROOT"]) . $this->path);
        $this->twig = new \Twig_Environment($loader, array(
            'cache' => BEFEW_BASE_URL . 'cache/twig',
            'debug' => DEBUG
        ));
    }

    public function render($file, $vars = array()) {
        $path = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $this->path), '/');
        $path = substr($path, 0, strrpos($path, '/')) . '/';
        echo $this->twig->render($file, array_merge($vars, array('CSS_PATH' => $path . 'Styles/', 'JS_PATH' => $path . 'Scripts/')));
    }
}