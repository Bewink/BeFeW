<?php

namespace src\Home;

use vendor\Befew\Template as Template;
use vendor\Befew\Controller as Controller;

class HomeController extends Controller {
    public function indexAction() {
        $tpl = new Template($this->tplpath);
        $tpl->render('index.twig');
    }
}