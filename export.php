<?php
require_once('classes/nonce.php');
require_once('classes/file.php');
require_once('classes/session.php');

$session = new Session();
$loggedIn = $session->checkAutentication();

if(!$loggedIn){

    if(!isset($_GET['token'])){
        response_json([], 0, 'Invalid access' );
    }else{
        $nonce = new Nonce();
        $token = $_GET['token'];
        $result = $nonce->verifyNonce($token);

        if(!$result){
            response_json([], 0, 'Invalid token access.');
        }

        unset($nonce);
    }
}

$file_id = $_GET['file_id'];
$file = new File();
$file->exportSummary($file_id);
unset($file);
