//import { TempusDominus } from '@eonasdan/tempus-dominus';

$(document).ready(function() {


    $("table.bu-data-table").on("click", ".view-record", function(event) {

    });

// UPDATE TRANSACTION MODAL
    $("div#update-transaction-modal").on("show.bs.modal", function (e) {

        /***
             * 'e.relatedTarget' is a reference to the link that, when clicked, displayed the modal
             * 'e.currentTarget' is a reference to the the modal
        */ 

        var DTRowIndex = $('#transactions').DataTable().row($(e.relatedTarget).parents('tr')).index()   // Based on the total number of rows in a table. With a table containing 5,000 records the row index is between 0 and 4,999.
        $('form#update-transaction input[type=text][name=dt-row-index]').val(DTRowIndex)

        var DOMRowIndex = $(e.relatedTarget).closest("tr").index()                                      // Based on the table rows currently being displayed. With 25 records currently displayed the row index is between 0 and 24.
        $('form#update-transaction input[type=text][name=dom-row-index]').val(DOMRowIndex)
               
        var recordID = $(e.relatedTarget).data("record-id")    // $(e.relatedTarget).data("record-id")
        var mysqlTable = $(e.relatedTarget).data("mysql-table")    // $('table#transactions .view-record').data('mysql-table')
        
        $.ajax({
            method: "POST",
            dataType: "json",       // A JSON-encoded string appears to be treated as a JSON object, unlike an XMLHttpRequest object which treats it as text.
            //contentType: false,		// Will throw an error when using a FormData object if omitted or set to anything other than 'false'  
            //processData: false,		// Will throw an error when using a FormData object if omitted or set to 'true' (default) 
            url: "ajax/bu_ajax_get_record.php",
            data: {
                'mysql-table': mysqlTable,
                'record-id': recordID,
            }
        })  // $.ajax
        .done(function( dataReturnedByServer, textStatus, jqXHR) {  // Used instead of the AJAX local callback event 'success: function()'. See https://stackoverflow.com/a/15821199/2518495
            if ( dataReturnedByServer['success'] == 1 ) {

            // Update successful
                data = JSON.parse(dataReturnedByServer['data'])
                console.log(data)

            // Modal Header
                $(e.currentTarget).find('#staticBackdropLabel').html('View | Update Transaction [Record ID <span class="text-grey">' + recordID + '</span>]')

            // Record ID [Hidden]
                $(e.currentTarget).find('form#update-transaction input[type=text][name=record-id]').val(data['id'])
            // Account ID [Read-only]
                $(e.currentTarget).find('form#update-transaction #account-id-ignore').val(data['account_id_alpha'])
            // Account Name [Dropdown]
                $(e.currentTarget).find('form#update-transaction #account-id-alpha').val(data['account_id_alpha']).change()
            // Amount
                $(e.currentTarget).find('form#update-transaction #amount').val(data['amount'])
                $(e.currentTarget).find('form#update-transaction #amount').trigger('change')    // Trigger the `change` event to add or remove the 'debit' class
            // Type [Dropdown]
                $(e.currentTarget).find('form#update-transaction #type').val(data['type']).change()
            // Sub-type [Dropdown]
                $(e.currentTarget).find('form#update-transaction #sub-type').val(data['sub_type']).change()
            // Entity [Dropdown]
                $(e.currentTarget).find('form#update-transaction #entity-id').val(data['entity_id']).change()
            // Date
                $(e.currentTarget).find('form#update-transaction #datepicker').datepicker({
                   /*
                    beforeShowDay: function (date){     // See https://stackoverflow.com/a/13514816/2518495
                        return ExcludedDates(
                            date, [
                                [5],    // Friday
                                [6]     // Saturday
                            ]
                        );
                    },
                    */

                    beforeShow: function (
                        element,    // Represents the input field `div#datepicker` 
                        instance    // A JQuery object representing the current datepicker instance `div#ui-datepicker-div`
                    ) {
                        var placement = 0   // Represents the placement of the datepicker instance relative to the input field `div#datepicker`: 0 = to the right of, 1 = above and 2 = below
                        return DatePickerPlacement (
                            element,        
                            instance,       
                            placement        
                        )
                    },
                    dateFormat: "yy-mm-dd",
                    firstDay: 1
                });
    
                $(e.currentTarget).find('form#update-transaction #datepicker').datepicker("setDate", data['date']);
            // Notes
                $(e.currentTarget).find('form#update-transaction #notes').val(data['notes'])

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


    });  // `show.bs.modal` listener

    $('form#update-transaction').on('submit', function(event) {                    // Any form whose class is 'update-form' will be processed by this event handler when it's submitted
        event.preventDefault(); // Prevent form from submitting normally

        formData = new FormData($(this)[0]);    // Bind the FormData object and the form element
        formData.append('form-id', $(this).attr('id'))

        for (var pair of formData.entries()) {
            console.log(pair[0]+ ', ' + pair[1]);
        }

        DTRowIndex = formData.get('dt-row-index')
        DOMRowIndex = formData.get('dom-row-index')
        formID = formData.get('form-id')

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

                UpdateDataTable(formID, DTRowIndex, DOMRowIndex)

                Swal.fire({
                    title: "<strong>Success</strong>",
                    icon: "success",
                    html: dataReturnedByServer['message']
                });

            // Hide modal
                //$('#' + formID + '-modal').modal().hide()
                $('#' + formID + '-modal').modal('toggle')


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

    }); // `submit` listener

})   