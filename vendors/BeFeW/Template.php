<?php

namespace vendors\BeFeW;

class Template {
    private $head = array();
    private $foot = array();
    private $basepath;
    private $title;
    private $styles = array();
    private $headJavascripts = array();
    private $headTags = array();
    private $footJavascripts = array();
    private $tplPath = 'Templates/';
    private $stylePath = 'Styles/';
    private $scriptPath = 'Scripts/';

    public function __construct($basepath, $head = null, $foot = null) {
        $this->head[] = $_SERVER['DOCUMENT_ROOT'].'/app/head.php';
        $this->foot[] = $_SERVER['DOCUMENT_ROOT'].'/app/foot.php';
        $this->basepath = BEFEW_BASE_URL.substr($basepath, strpos($basepath, 'src/'));

        if($head != null) {
            if (is_array($head)) {
                foreach ($head as $h) {
                    $path = $this->basepath . $this->tplPath . $h;
                    if (file_exists($path)) {
                        $this->head[] = $path;
                    }
                }
            } else {
                $path = $this->basepath . $head;
                if (file_exists($path)) {
                    $this->head[] = $path;
                }
            }
        }
        if($foot != null) {
            if (is_array($foot)) {
                foreach ($foot as $f) {
                    $path = $this->basepath . $this->tplPath . $f;
                    if (file_exists($path)) {
                        $this->foot[] = $path;
                    }
                }
            } else {
                $path = $this->basepath . $this->tplPath . $foot;
                if (file_exists($path)) {
                    $this->foot[] = $path;
                }
            }
        }
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getTitle() {
        return $this->title;
    }

    public function addStyle($url) {
        $this->styles[] = $this->basepath . $this->stylePath . $url;
        return $this;
    }

    public function removeStyle($url) {
        $this->styles = array_diff($this->styles, array($this->basepath . $this->stylePath . $url));
        return $this;
    }

    public function getStyles() {
        return $this->styles;
    }

    public function addHeadJavascript($url) {
        $this->headJavascripts[] = $this->basepath . $this->scriptPath . $url;
        return $this;
    }

    public function removeHeadJavascript($url) {
        $this->headJavascripts = array_diff($this->headJavascripts, array($this->basepath . $this->scriptPath . $url));
        return $this;
    }

    public function getHeadJavascript() {
        return $this->headJavascripts;
    }

    public function addFootJavascript($url) {
        $this->footJavascripts[] = $this->basepath . $this->scriptPath . $url;
        return $this;
    }

    public function removeFootJavascript($url) {
        $this->footJavascripts = array_diff($this->footJavascripts, array($this->basepath . $this->scriptPath . $url));
        return $this;
    }

    public function getFootJavascript() {
        return $this->footJavascripts;
    }

    public function addHeadTag($tag) {
        $this->headTags[] = $tag;
        return $this;
    }

    public function removeHeadTag($tag) {
        $this->headTags = array_diff($this->headTags, array($tag));
        return $this;
    }

    public function getHeadTag() {
        return $this->headTags;
    }

    public function render($url, $vars = null) {
        if ($vars) {
            extract($vars);
        }

        $title = $this->title;
        $styles = $this->styles;
        $headJavascripts = $this->headJavascripts;
        $footJavascripts = $this->footJavascripts;
        $headTags = $this->headTags;

        foreach($this->head as $h) {
            include($h);
        }

        include($this->basepath . $this->tplPath . $url);

        foreach($this->foot as $f) {
            include($f);
        }
    }
}