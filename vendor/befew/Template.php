<?php

namespace vendor\Befew;

class Template {
    private $twig;
    private $path;
    private $styles;
    private $scripts;
    private $footScripts;

    public function __construct($path) {
        $this->styles = array();
        $this->scripts = array();
        $this->path = new Path($path);
        $this->formattedPath = str_replace(DIRECTORY_SEPARATOR, '/', $this->path);
        $loader = new \Twig_Loader_Filesystem(realpath($_SERVER["DOCUMENT_ROOT"]));
        $this->twig = new \Twig_Environment($loader, array(
            'cache' => CACHE_TWIG,
            'debug' => DEBUG
        ));
    }

    public function addCSS($path) {
        array_push($this->styles, $this->path->getPathWithWebSeparators() . 'Styles/' . $path);
    }

    public function addJS($path, $head = true) {
        if($head) {
            array_push($this->scripts, $this->path->getPathWithWebSeparators() . 'Script/' . $path);
        } else {
            array_push($this->footScripts, $this->path->getPathWithWebSeparators() . 'Script/' . $path);
        }
    }

    public function render($file, $vars = array()) {
        echo $this->twig->render(
            $this->path . 'Templates' . DIRECTORY_SEPARATOR . $file,
            array_merge(
                $vars,
                array(
                    'styles' => $this->styles,
                    'scripts' => $this->scripts,
                    'footScripts' => $this->footScripts
                )
            )
        );
    }
}