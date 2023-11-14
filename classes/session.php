<?php

class Session{  

    public function __construct(){

        if (session_id() === '') { 
            session_start();
        }
    }

    public function setUserId(){
        $this->userId = session_id();
    }
    public function generateSession(){
        session_regenerate_id();
    }

    public function storeSession($data){
        $_SESSION[key($data)] = $data[key($data)];
    }

    public function getSession($param){
        return isset($_SESSION[$param]) ? $_SESSION[$param] : null;
    }

    public function destroySession($param){
        unset($_SESSION[$param]);
    }

    public function checkAutentication(){

        if(isset($_SESSION['user']))
            return true;

        return false;
    }

    public function logout(){
        unset($_SESSION['user']);
        header("Location: index.php");
        die();
    }

}