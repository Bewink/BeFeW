<?php

namespace vendor\Befew;

class Path {
    private $path;

    public function __construct($path) {
        $this->path = $path;
    }

    public function __toString() {
        return $this->getPath();
    }

    public function getPath() {
        return $this->path;
    }

    public function getPathWithWebSeparators() {
        return str_replace('\\', '/', $this->getPath());
    }
}