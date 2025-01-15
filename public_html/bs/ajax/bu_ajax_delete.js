$(function() {

    // Update
    $("table.bu-data-table").on("click", ".delete-record", function(event) {       // See https://stackoverflow.com/a/58588595/2518495
    //$("a.delete-record").click(function(event) {              // Any anchor whose class is 'delete-record' will be processed by this event handler when it's clicked
        event.preventDefault()
        
        mysqlTable = $(this).data('mysql-table');               // The value assigned to the anchor element's 'data-mysql-table' attribute
        recordID = $(this).data('record-id');                   // The value assigned to the anchor element's 'data-record-id' attribute
        recordType = $(this).data('record-type');               // The value assigned to the anchor element's 'data-record-type' attribute
        recordIdentifier = $(this).data('record-identifier');   // The value assigned to the anchor element's 'data-record-identifier' attribute

        //var table = $(this).closest('table').DataTable();         // Returns an instance of the DataTables API object
        var table = $(this).closest('table').dataTable().api();     // Returns an instance of the DataTables API object, but is a jQuery type object with an attached api() method
        var row = table.row($(this).parents('tr'))                  // A reference to the tables's targetted row

        console.log(mysqlTable);
        console.log(recordID);

        Swal.fire({
            title: "Delete record?",
            html: "Are you sure you want to delete the <span class=\"text-grey\">" + recordType + "</span> record for <span class=\"text-grey\">" + recordIdentifier + "</span>?",
            showCancelButton: true,
            confirmButtonText: "Delete",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    dataType: "json",       // A JSON-encoded string appears to be treated as a JSON object, unlike an XMLHttpRequest object which treats it as text.
                    //contentType: false,		// Will throw an error when using a FormData object if omitted or set to anything other than 'false'  
                    //processData: false,		// Will throw an error when using a FormData object if omitted or set to 'true' (default) 
                    url: "ajax/bu_ajax_delete.php",
                    data: {
                        'mysql-table': mysqlTable,
                        'record-id': recordID,
                        'record-type': recordType,
                        'record-identifier': recordIdentifier,
                    }
                })  // $.ajax
                .done(function( dataReturnedByServer, textStatus, jqXHR) {  // Used instead of the AJAX local callback event 'success: function()'. See https://stackoverflow.com/a/15821199/2518495
                    if ( dataReturnedByServer['success'] == 1 ) { 
                    // Update successful
                        console.log(dataReturnedByServer['message']); // Display the response
                        row.remove().draw()     // Remove the targetted row and redraw the table                                 

                        Swal.fire({
                            title: "<strong>Success</strong>",
                            icon: "success",
                            html: dataReturnedByServer['message']
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
            //Swal.fire("Deleted!", "", "success");
            } else if (result.isDismissed) {
                Swal.fire("Delete Request Cancelled", "The record has not been deleted", "info");
            }   // isConfirmed
        });
    });     // Event Handler  
});     // Document Ready