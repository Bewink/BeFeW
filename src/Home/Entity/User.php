<?php

namespace src\Home\Entity;

use vendors\BeFeW\Entity as Entity;

class User extends Entity {
    protected $pseudo;

    public function __construct($id = null) {
        if($id != null) {
            $this->find($id);
        }
    }
}