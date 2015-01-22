<?php

namespace src\Home;

use vendor\Befew\Controller as Controller;

class HomeController extends Controller {
    public function indexAction() {
        $this->template->addCSS('default.css');
        $this->template->render('index.html.twig');
    }
}