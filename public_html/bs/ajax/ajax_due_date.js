$(function() {

// Update
    $("a.add-current-due-date").click(function() {      // Any anchor whose class is 'add-current-due-date' will be processed by this event handler when it's clicked

        var row =  $(this).closest('tr')                // A reference to the table row containing the anchor that was clicked   
        //console.log(row.children('td.current-due-date').html()) 
        //console.log(row.children('td.next-due-date').html()) 
        
        //id = row.children('td.id').html();              // Get the MySQL record id of the targetted row. This is displayed in a table cell (td) with a class name of 'id'.
        id = $(this).data('id');                        // The value assigned to the anchor element's 'data-id' attribute
        //console.log(id);

        var table = $(this).closest('table').dataTable().api();     // Returns an instance of the DataTables API object, but is a jQuery type object with an attached api() method
        //console.log(table);

        $.ajax({
            method: "POST",
            dataType: "json",       // A JSON-encoded string appears to be treated as a JSON object, unlike an XMLHttpRequest object which treats it as text.
            //contentType: false,		// Will throw an error when using a FormData object if omitted or set to anything other than 'false'  
            //processData: false,		// Will throw an error when using a FormData object if omitted or set to 'true' (default) 
            url: "ajax/ajax_due_date.php",
            data: {'record-id': id}
        })  // $.ajax
        .done(function( dataReturnedByServer, textStatus, jqXHR) {  // Used instead of the AJAX local callback event 'success: function()'. See https://stackoverflow.com/a/15821199/2518495
            if ( dataReturnedByServer['success'] == 1 ) { 
            // Update successful
                //console.log(dataReturnedByServer['current'])
                //console.log(dataReturnedByServer['next'])
                //console.log(dataReturnedByServer['period'])
            // Uses jQuery UI DatePicker Widget (jquery-ui.js loaded in bu_manage regular_debits.php). See https://api.jqueryui.com/datepicker/#utility-formatDate
                row.children('td.current-due-date').html($.datepicker.formatDate("D dd/mm/yy", new Date(dataReturnedByServer['current'])));     
                row.children('td.next-due-date').html($.datepicker.formatDate("D dd/mm/yy", new Date(dataReturnedByServer['next'])));
                table.draw(true)
                Swal.fire({
                    title: "<strong>Success</strong>",
                    icon: "success",
                    html: dataReturnedByServer['message'],
                    footer:'The page will be reloaded when this alert is dismissed.'
                }).then(function(){ 
                    location.reload();
                });
            } else {
            // Update failed
                Swal.fire({
                    title: "Failure",
                    icon: "error",
                    html: dataReturnedByServer['message']
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