<?php

require_once('./../classes/user.php');
require_once('./../classes/session.php');

$session = new Session();
$loggedIn = $session->checkAutentication();
$userId = $session->getSession('user');

$user = new User();
$userData = $user->find($userId);

?>

<!DOCTYPE html>
<html lang="en">
    <head>
    <title>Claromentis</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    </head>
    <body>

    <div class="container-fluid p-5 bg-light text-secondary text-center">
        <h1>Summary of expenses</h1>
        <?php if ($loggedIn) { ?>
            <p>Welcome <?php echo $userData['email']; ?>, <a href="../api.php?action=logout" >Logout </a></p> 
        <?php } else { ?>
            <p><a href="login.php"> Login </a> to manage your uploads or register <a href="register.php">here</a>. </p> 
        <?php } ?>
        
    </div>

    <div class="container">