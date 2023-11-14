<?php

//include nonce 
require_once('classes/nonce.php');
require_once('classes/file.php');
require_once('classes/user.php');
require_once('classes/session.php');
//create new instance of the class


if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {

    
    if($_POST['action'] === 'generateSummary'){
       
        $session = new Session();
        $loggedIn = $session->checkAutentication();

        if(!$loggedIn){

            if(!isset($_POST['token'])){
                response_json([], 0, 'Invalid token access' );
            }else{
                $nonce = new Nonce();
                $token = $_POST['token'];
                $result = $nonce->verifyNonce($token);
        
                if(!$result){
                    response_json([], 0, 'Invalid token access');
                }
        
                unset($nonce);
            }
        }

        if($_FILES['csv']['type'] !== 'text/csv'){
            response_json([], 0, 'File not supported');
        }

        $file = new File();
        $summary = $file->generateSummary($_FILES['csv']);
        unset($file);

        if($summary){
            response_json($summary,1);
        }else{
            response_json([], 0, 'Invalid csv format');
        }

    }

    if($_POST['action'] === 'register'){
       
        $user = new User();
        $email = $_POST['email'];
        $password = $_POST['password'];

        $userExist = $user->checkAccountExist($email);

        if($userExist){
            response_json([], 0, 'User email exist.');
        }

        $data = array(
            'email' => $email,
            'password' => $password
        );

        $user->register($data);
        unset($user);

        response_json([], 1);

    }

    if($_POST['action'] === 'login'){
       
        $user = new User();
        $email = $_POST['email'];
        $password = $_POST['password'];

        $userExist = $user->checkAccountExist($email);

        if(!$userExist){
            response_json([], 0, 'User do not exist.');
        }

        $data = array(
            'email' => $email,
            'password' => $password
        );

        $auth = $user->authenticate($data);
        unset($user);

        if(!$auth){
            response_json([], 0, 'Invalid password.');
        }

        response_json([], 1);

    }

   
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' ) {

    $session = new Session();
    $loggedIn = $session->checkAutentication();
    unset($session);

    if(!$loggedIn){
        response_json([], 0, 'Invalid access.' );
    }

    if($_GET['action'] === 'getUploads'){

        $file = new File();
        $page = $_GET['page'];
        $files = $file->getFiles($page);
        unset($file);

        response_json($files,1);
    }

    if($_GET['action'] === 'getFileDetails'){

        $file = new File();
        $file_id = $_GET['file_id'];
        $files = $file->getSummary($file_id);
        unset($file);
        $summary = json_decode($files['summary']);

        response_json($summary,1);
    }

    if($_GET['action'] === 'logout'){

        $session = new Session();
        $loggedIn = $session->logout();
        unset($session);

    }
   
}



function response_json($data, $success = 1, $message = ''){

    header("Content-Type: application/json");

    echo json_encode([
        "success"=> $success ? true: false,
        "message"=> $message,
        "data"=> $data
    ]);

    die();
}