<?php
//include nonce 
require_once('includes/header.php');

?>

<div class="row">
    <div class="col-md-6 mt-5" >
        <h3>Account Information</h3>
        <p>Kindly fill up the form below.</p>
        <div id="alert-container">
        </div>
        <form action="" id="register-form">
            <div class="mb-3 mt-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" placeholder="Enter email" name="email">
            </div>
            <div class="mb-3">
                <label for="pswd" class="form-label">Password:</label>
                <input type="password" class="form-control" id="pswd" placeholder="Enter password" name="pswd"  >
            </div>
            <div class="mb-3">
                <label for="confirm-pswd" class="form-label">Confirm password:</label>
                <input type="password" class="form-control" id="confirm-pswd" placeholder="Confirm password" name="confirm-pswd"  >
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>
</div>

<?php

require_once('includes/footer.php');

?>

<script>

$(function() {
    
    $('#register-form').submit(function(e){
        
        e.preventDefault();
        let password = $('#pswd').val();
        let confirmPassword = $('#confirm-pswd').val();

        if(password != confirmPassword){

            $('#alert-container').append('<div class="alert alert-danger alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert"></button><strong>Invalid!</strong> Password doesnt match.</div>');
            return false;
        }

        let data = {
            'email' : $('#email').val(),
            'password' : $('#pswd').val(),
            'action' : 'register'
        };

        $.post('../api.php', data, function(res){

            if(res.success == true){
                window.location.href = 'dashboard.php';
            }else{
                $('#alert-container').append('<div class="alert alert-danger alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert"></button><strong>Invalid!</strong> '+res.message+'</div>');
            }
        }, 'json');

    });
  
});

</script>



