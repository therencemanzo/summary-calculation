<?php

require_once("db.php");
require_once("session.php");
require_once("file.php");

class User extends db{

    public $salt;
    public $email;

    public function __construct(){
        parent::__construct('users');
    }

    public function register($user){

        $this->salt = hash('sha256', date('Y-m-d').$user['email'].time());
        $password = $this->salt.$user['password'].$this->salt; 
        $hashedPassword = hash('sha256', $password);

        $data = array( 
            'email' => $user['email'],
            'password'=> $hashedPassword,
            'salt' => $this->salt
        );

        $user = $this->insert($data);

        if($user){
            $session = new Session();
            $session->generateSession();
            $generated = $session->getSession('generatedFile');

            foreach($generated as $key => $value){

                $file = new File();
                $data = array(
                    'id' => $value,
                    'user_id' => $user,
                );
                $file->update($data);
    
            }
            $session->destroySession('generatedFile');
            $session->storeSession(array('user' => $user));
            unset($session);
        }else{
            echo 'not created';
        }

    }

    public function authenticate($user){
        $userData = $this->select( [], array('email' => $user['email']) );

        $password = $userData['salt'].$user['password'].$userData['salt'];

        $hashedPassword = hash('sha256', $password);

        //echo $hashedPassword . ' == '. $userData['password'];
        if($hashedPassword == $userData['password']){
            $session = new Session();
            $session->generateSession();
            $session->storeSession(array('user' => $userData['id']));
            unset($session);

            return true;

        }else{
            return false;
        }
        

    }
    public function find($user){
        $userData =  $this->select( [], array('id' => $user) );

        return $userData;
    }

    public function checkAccountExist($email){

       
        $user =  $this->select( [], array('email' => $email) );
        
        if($user){
            return true;
        }

        return false;
    }
    public function getEmail(){

    }
}