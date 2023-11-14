<?php
//include nonce 
require_once('includes/header.php');
require_once('../classes/session.php');

$session = new Session();
$loggedIn = $session->checkAutentication();

if(!$loggedIn){
    
    header("Location: ../index.php");
    die();

}
?>
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

            </tbody>
        </table>
    </div>
</div>

<div class="row" style="margin-bottom:20px;">
    <div class="col mt-5" >
        <h3>Uploaded CSV</h3>
        <p>Here are the list of your uploaded CSV.</p>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Date Uploaded</th>
                    <th>Filename</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="uploads-table">
            
            </tbody>
        </table>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-primary" id="button-previous" disabled>Prev</button>
            <button type="button" class="btn btn-outline-primary" id="button-next" disabled>Next</button>
        </div>
    </div>
</div>

<?php

require_once('includes/footer.php');

?>

<script>

$(function() {
    
    let page = 1;
    loadUploads(page);
    $('#button-next').click(function(e){
        page += 1;
        loadUploads(page);
    });

    $('#button-previous').click(function(e){

        page -= 1;
        loadUploads(page);
        
    });

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
                    page = 1;
                    loadUploads(page);
                    $('#result-container').removeClass('d-none');
                    $('#file-id').val(obj.data.id);
                    let downloadLink = '../export.php?file_id=' +obj.data.id ;
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

function loadUploads(page){

    $('#uploads-table').html(`<tr><td colspan="3" align="center"> <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>`);

    let data = {
        'action' : 'getUploads',
        'page' : page
    };

    $.get('../api.php', data, function(res){
        let uploads = res.data;
        let length = uploads.length;

        $('#uploads-table').empty();
        if(length != 0){
            
            Object.entries(uploads).forEach(([key, value]) => {

                let details = '';
                let detailsObj = JSON.parse(value.summary);
                Object.entries(detailsObj).forEach(([k, v]) => {
                    details += `<p class="h6">${k} <span class="text-secondary"> ${v} </span> </p>`;
                });

                $('#uploads-table').append(`<tr id="${key}"><td>${value.created_at}</td><td>${value.original_filename}</td><td> <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample-${value.id}" aria-expanded="false" aria-controls="collapseExample-${value.id}">View details</button>&nbsp;<a class="btn btn-outline-primary" type="button" href="../export.php?file_id=${value.id}">Download csv</a></td></tr>`);
                $('#uploads-table').append(`<tr class="collapse" id="collapseExample-${value.id}" ><td colspan="3">${details}</td></tr>`);
            });

            if(page != 1){
                $('#button-previous').prop('disabled', false);
            }else{
                $('#button-previous').prop('disabled', true);
            }

            $('#button-next').prop('disabled', false);
        }else{
            $('#button-next').prop('disabled', true);
            $('#button-previous').prop('disabled', false);
            $('#uploads-table').append(`<tr><td colspan="3" align="center"> No uploaded files.</td></tr>`);
        }
    }, 'json');

}

function viewDetails(id, key){

    
    let text = $('#link-details-'+id).text();

    if(text == 'View details'){
        $('#link-details-'+id).text('Close');
        // let data = {
        //     'action' : 'getFileDetails',
        //     'file_id' : id
        // };
        
        // $.get('../api.php', data, function(res){

        //     let table = `
        //         <table class="table table-borderless col-md-5">
        //             <thead>
        //             <tr>
        //                 <th>Desription</th>
        //                 <th>Total</th>
        //             </tr>
        //             </thead>
        //             <tbody>
        //             <tr>
        //                 <td>John</td>
        //                 <td>Doe</td>
                    
        //             </tr>
                
        //             </tbody>
        //         </table>
        
        //     `;
        //     $('#uploads-table > tr').eq(key).after(`<tr id="details-${id}"><td colspan="3"> ${table}</td></tr>`);
        // },'json');
    }

    return false; 
}

</script>



