<?php

namespace mf\auth;

use mf\auth\exception\AuthentificationException;

class Authentification extends AbstractAuthentification
{
    public function __construct(){
        if (isset($_SESSION['user_login'])){
            $this->user_login = $_SESSION['user_login'];
            $this->access_level = $_SESSION['access_level'];
            $this->logged_in = true;
        } else {
            $this->user_login = null;
            $this->access_level = self::ACCESS_LEVEL_NONE;
            $this->logged_in = false;
        }
    }

    protected function updateSession($username, $level)
    {
        $this->user_login = $username;
        $this->access_level = $level;

        $_SESSION['user_login'] = $username;
        $_SESSION['access_level'] = $level;

        $this->logged_in = true;
    }

    public function logout()
    {
        unset($_SESSION['user_login']);
        unset($_SESSION['access_level']);
        $this->user_login = null;
        $this->access_level = self::ACCESS_LEVEL_NONE;
        $this->logged_in = false;
    }

    public function checkAccessRight($requested): bool
    {
        if ($requested > $this->access_level){
            return false;
        } else {
            return true;
        }
    }

    public function login($username, $db_pass, $given_pass, $level)
    {
        if(!self::verifyPassword($given_pass, $db_pass)){
            throw new AuthentificationException('Mauvais identifiant ou mot de passe');
        } else {
            self::updateSession($username, $level);
        }
    }

    protected function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    protected function verifyPassword($password, $hash)
    {
        if(password_verify($password, $hash)){
            return true;
        } else {
            return false;
        }
    }
}