$(function() {
    $('#login').on('submit', function(event) {

        event.preventDefault();

        formData = new FormData($(this)[0]);

        for (var pair of formData.entries()) {
            console.log(pair[0]+ ', ' + pair[1]); 
        }
        
        $.ajax({
            method: "POST",
            dataType: "json",       // The type of data expected back from the server
            contentType: false,		// Will throw an error when using a FormData object if omitted or set to anything other than 'false'  
            processData: false,		// Will throw an error when using a FormData object if omitted or set to 'true' (default) 
            url: "ajax/bu_ajax_login.php",
            data: formData
        })  // $.ajax
        .done(function( dataReturnedByServer, textStatus, jqXHR) {  // Used instead of the AJAX local callback event 'success: function()'. See https://stackoverflow.com/a/15821199/2518495
            if ( dataReturnedByServer['success'] == 1 ) { 
            // Login successful
                $(".login-box").fadeOut(1000, function() {
                    window.location.href = "bu_dashboard.php"
                });
            } else {
            // Login failed
                Swal.fire({
                    title: "There was a problem!",
                    icon: "error",
                    html: "Please check your credentials and try again."
                });          
            }
        })  // done
        .fail(function ( jqXHR, textStatus, errorThrown) {  // Used instead of the AJAX local callback event 'error: function()'. See https://stackoverflow.com/a/15821199/2518495
            if ( jqXHR.readyState === 4 ) {
                message = "Request failed. Returned status of <b>" + jqXHR.status + " - " + errorThrown + "</b>";
            } else if ( jqXHR.readyState === 0 ) {
                message = "ERROR: Network request failed. Check your browser's JavaScript Console for more information.";
            } else {
                message = "Something wierd just happened!"
            }
            Swal.fire({
                title: "The server encountered an error!",
                icon: "error",
                html: message
            });    
        }); // fail
            
    });
    
});