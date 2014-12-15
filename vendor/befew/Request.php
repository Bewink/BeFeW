<?php

namespace vendor\Befew;

class Request extends Utils {
    private $url;
    private $get;
    private $post;

    public function __construct($url) {
        $this->url = $url;
        $this->get = self::getGet();
        $this->post = self::getPost();
    }

    public function __toString() {
        return $this->url;
    }

    public function getUrl() {
        return $this->url;
    }

    public static function getPost($id = null, $default = null, $secure = false) {
        return ($id == null) ? parent::getVar($_POST, $default, $secure) : parent::getVar($_POST[$id], $default, $secure);
    }

    public static function getGet($id = null, $default = null, $secure = false) {
        return ($id == null) ? parent::getVar($_GET, $default, $secure) : parent::getVar($_GET[$id], $default, $secure);
    }

    public function get($id, $default = null, $secure = false, $type = "all") {
        switch(strtolower($type)) {
            case 'get':
                return $this->getGet($id, $default, $secure);
                break;

            case 'post':
                return $this->getPost($id, $default, $secure);
                break;

            default:
                return ($this->getGet($id) == null) ? $this->getPost($id, $default, $secure) : $this->getGet($id, $default, $secure);
                break;
        }
    }
}