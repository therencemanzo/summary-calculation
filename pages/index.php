<?php

require_once('includes/header.php');
require_once('../classes/nonce.php');

$nonce = new Nonce();
$token = $nonce->generateNonce('generate_report');

$session = new Session();
$loggedIn = $session->checkAutentication();
unset($session);

if($loggedIn){
    
    header("Location: dashboard.php");
    die();

}

?>

<div class="container">
    <div class="row">
        <div class="col-md-6 mt-5" >
            <h3>Upload your CSV here</h3>
            <p>Kindly upload your CSV file here. Make use its a csv and and the format is correct. <a href="../sample_format.csv">Download the CSV file here.</a></p>
            <div id="alert-container">
            </div>
            <form action="#" id="submit-csv" enctype="multipart/form-data">
                <div class="progress d-none" id="progress-bar-container">
                    <div class="progress-bar" id="progress-bar"></div>
                </div>
                <input type="hidden" value="generateSummary" name="action" id="action"/>
                <input type="hidden" value="<?php echo $token ?>" name="token" id="token"/>
                <div class="mb-3 mt-3">
                    <label for="csv" class="form-label">CSV File:</label>
                    <input type="file" id="csv" name="csv" class="form-control" accept=".csv"/>
                </div>
                <button type="submit" class="btn btn-primary" id="genere-button">Generate</button>
            </form>
        </div>
        <div class="col-md-6 mt-5 d-none" id="result-container">
            <h3>Result</h3>
            <input type="hidden" value="0" name="file-id" id="file-id"/>
            <p>Summary of your expenses. <a href="" id="download-csv">Download the generated CSV file here.</a></p>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody id="result-table">
                
                    <tr>
                        <td>July</td>
                        <td>Dooley</td>
                    </tr>
                </tbody>
            </table>
            <p><a href="register.php"> Click here </a> to register and manage your uploads.</p>
        </div>
    </div>
</div>

<?php
require_once('includes/footer.php');
?>

<script>

$(function() {
    
    $('#submit-csv').submit(function(e){
        
        e.preventDefault();

        $('#genere-button').prop('disabled', true);
        $('#result-table').empty();
        $('#progress-bar-container').removeClass('d-none');
        $('#progress-bar').css('width', '0%' );

        $.ajax({
            url: '../api.php',
            type: 'POST',
            data: new FormData($('#submit-csv')[0]),
            cache: false,
            contentType: false,
            processData: false,
            async: true,

            // Custom XMLHttpRequest
            xhr: function () {
            var myXhr = $.ajaxSettings.xhr();
            if (myXhr.upload) {
                // For handling the progress of the upload
                myXhr.upload.addEventListener('progress', function (e) {
                if (e.lengthComputable) {
                    $('#progress-bar').css('width', percent = Math.ceil(e.loaded / e.total * 100) + '%' );

                }
                }, false);
            }
            return myXhr;
            },
            success: function (result) {
                let obj = JSON.parse(JSON.stringify(result));
                let success = obj.success;

                if(success){
                    $('#result-container').removeClass('d-none');
                    $('#file-id').val(obj.data.id);
                    let downloadLink = '../export.php?token=' + $('#token').val() + '&file_id=' +obj.data.id ;
                    $('#download-csv').prop('href',downloadLink);
                    let summary = JSON.parse(obj.data.summary);
                    Object.entries(summary).forEach(([key, value]) => {
                        $('#result-table').append(`<tr><td>${key}</td><td>${value}</td></tr>`);
                    });
                }else{
                    $('#alert-container').append('<div class="alert alert-danger alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert"></button><strong>Invalid!</strong> '+ obj.message +'.</div>');
                }
              
                $('#csv').val('');
                $('#progress-bar-container').addClass('d-none');
                $('#genere-button').prop('disabled', false);
            },
            error: function (error) {
                $('#alert-container').append('<div class="alert alert-danger alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert"></button><strong>Invalid!</strong> Something went wrong.</div>');
            },
           
        });
    });
  
});

</script>