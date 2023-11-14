<?php
//include nonce 
include_once('classes/session.php');
$session = new Session();
$loggedIn = $session->checkAutentication();

if(!$loggedIn){
    
    header("Location: pages/");
    die();

}
