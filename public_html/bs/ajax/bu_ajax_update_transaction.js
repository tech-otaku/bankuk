$(function() {

// Update
    $('form#update-transaction').on('submit', function(event) {                    // Any form whose class is 'update-form' will be processed by this event handler when it's submitted
        event.preventDefault(); // Prevent form from submitting normally

        //var table = $('#transactions').DataTable().row('.selected').data()
        //console.log(table[0])

      //console.log('BALLS' + $('#update-transaction [name=entity-id]').text())
      //console.log($('form#update-transaction #account-id-alpha option:selected').text())
      //console.log($('form#update-transaction #type option:selected').text())
      //console.log($('form#update-transaction #sub-type option:selected').text())
      //console.log('Entity' + $('form#update-transaction #entity-id option:selected').text())

      //var table = $('#transactions').DataTable()
      //var selectedRowIndex = table.row('.selected').index()
      //console.log('selectedRowIndex' + selectedRowIndex)
      //table.cell(selectedRowIndex,6).data($('form#update-transaction #entity-id option:selected').text())
      //table.draw(false)


      /*
        var table = $('#transactions').DataTable()
        var selectedRow = table.row('.selected').index() + 1   // 0-based

        //console.log(table.row('.selected').index())
        //console.log('HTML: ' + $('#transactions tr:eq(' + selectedRow + ') td:eq(1)').html())
        //$('#transactions tr:eq(' + selectedRow  + ') td:eq(1)').html('Z')
        //console.log('HTML: ' + $('#transactions tr:eq(4) td:eq(1)').html())
        //console.log(table.row('.selected').data()[1])
        //table.row('.selected').data()[1] = 'A'
        //console.log(table.row('.selected').data()[1])
        //table.draw(true)
        */

        formData = new FormData($(this)[0]);    // Bind the FormData object and the form element
        formData.append('form-id', $(this).attr('id'))

        for (var pair of formData.entries()) {
            if (pair[0] === 'row-index') {
                var rowIndex = pair[1]
                //console.log(pair[0]+ ', ' + pair[1]);
            }
        }

        $.ajax({
            method: "POST",
            dataType: "json",       // The type of data expected back from the server
            contentType: false,		// Will throw an error when using a FormData object if omitted or set to anything other than 'false'  
            processData: false,		// Will throw an error when using a FormData object if omitted or set to 'true' (default) 
            url: "ajax/bu_ajax_update.php",
            data: formData
        })  // $.ajax
        .done(function( dataReturnedByServer, textStatus, jqXHR) {  // Used instead of the AJAX local callback event 'success: function()'. See https://stackoverflow.com/a/15821199/2518495
            if ( dataReturnedByServer['success'] == 1 ) { 
            // Update successful

                var table = $('#transactions').DataTable()
                table.cell(rowIndex, 6).data($('form#update-transaction #entity-id option:selected').text())
                table.draw(false)

                //$('#transactions tr:eq(' + rowIndex + 1 + ') td:eq(6)').text($('form#update-transaction #entity-id option:selected').text())


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

    });

});