<?php

namespace models;

class Auth extends Main
{
    public static $table = "users";

    public static $columnDB = ["email"];


    public $email;



    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    public function ExisteUser()
    {
        $r = self::findBy("email", $this->email);
        if ($r) {
            return true;
        } else {
            return false;
        }
    }

}
