$(document).ready(function() {

    $("table.bu-data-table").on("click", ".view-record", function(event) {

        //var DTRowIndex = $('#transactions').DataTable().row($(this).parents('tr')).index();
        //$('form#update-transaction input[type=text][name=row-index]').val(DTRowIndex)

        //$(this).attr('id', 'view-record-link-clicked');     // Add an ID to the link that was clicked so it can be targetted in the on 'show.bs.modal' event

        //$('#update-transaction-modal').data('modal-record-id',$(this).data('record-id'));
        //$('#update-transaction-modal').data('modal-mysql-table',$(this).data('mysql-table'));

    });

// UPDATE TRANSACTION MODAL
    $("#update-transaction-modal").on("show.bs.modal", async function (e) {

        /***
             * 'e.relatedTarget' is a reference to the link that, when clicked, displayed the modal
             * 'e.currentTarget' is a reference to the the modal
        */ 

        var DTRowIndex = $('#transactions').DataTable().row($(e.relatedTarget).parents('tr')).index()
        $('form#update-transaction input[type=text][name=row-index]').val(DTRowIndex)

        console.log(e.currentTarget)
        console.log($(e.currentTarget))
        console.log($(e.currentTarget).find('form#update-transaction input[type=text][name=row-index]').val())
        console.log($(this).find('form#update-transaction input[type=text][name=row-index]').val())

        //console.log('ROW: ' +$('#transactions').DataTable().row($(e.relatedTarget).parents('tr')).index())
               
        var recordID = $(e.relatedTarget).data("record-id")    // $(e.relatedTarget).data("record-id")
        console.log(e.relatedTarget)
        console.log(JSON.stringify($(e.relatedTarget)))
        console.log(recordID)
        //console.log($(this).data("modal-record-id"))
        //console.log($('#view-record-link-clicked').data("record-id"))
        var mysqlTable = $(e.relatedTarget).data("mysql-table")    // $('table#transactions .view-record').data('mysql-table')
        console.log(mysqlTable)
        //console.log($(this).data("modal-mysql-table"))
        //console.log($('#view-record-link-clicked').data("mysql-table"))

        console.log($(e.currentTarget))


        //$('#view-record-link-clicked').removeAttr('id')

        $.ajax({
            method: "POST",
            dataType: "json",       // A JSON-encoded string appears to be treated as a JSON object, unlike an XMLHttpRequest object which treats it as text.
            //contentType: false,		// Will throw an error when using a FormData object if omitted or set to anything other than 'false'  
            //processData: false,		// Will throw an error when using a FormData object if omitted or set to 'true' (default) 
            url: "ajax/bu_ajax_get_transaction_record.php",
            data: {
                'mysql-table': mysqlTable,
                'record-id': recordID,
            }
        })  // $.ajax
        .done(function( dataReturnedByServer, textStatus, jqXHR) {  // Used instead of the AJAX local callback event 'success: function()'. See https://stackoverflow.com/a/15821199/2518495
            //console.log(dataReturnedByServer['data'])
            if ( dataReturnedByServer['success'] == 1 ) {
                console.log('DATA ' + dataReturnedByServer['data'])
                $.each( JSON.parse(dataReturnedByServer['data']), function( index, value ){

                    //console.log($(e.currentTarget).attr('id', 'record-id' ).val())

                    if (index === 'id') {
                        //$("#record-id").val(value);
                        //$('form#update-transaction input[type=text][name=record-id]').val(value)
                        $(e.currentTarget).find('form#update-transaction input[type=text][name=record-id]').val(value)
                        //$(e.currentTarget).attr('id', 'record-id' ).val(value)
                        console.log(index, value)
                    }
                    if (index === 'account_id_alpha') {
                        //$("#account-id-ignore").val(value);
                        //$('form#update-transaction #account-id-ignore').val(value)
                        $(e.currentTarget).find('form#update-transaction #account-id-ignore').val(value)

                        //$("#account-id-alpha").val(value).change();
                        //$('form#update-transaction #account-id-alpha').val(value).change()
                        $(e.currentTarget).find('form#update-transaction #account-id-alpha').val(value).change()
                       
                        //$("#account-id-alpha option[value='" + value +"']").attr('selected',true);
                        console.log(index, value)
                    }
                    if (index === 'amount') {
                        //$("#amount").val(value);
                        $(e.currentTarget).find('form#update-transaction #amount').val(value)
                        console.log(index, value)
                    }
                    if (index === 'type') {
                        //$("#type").val(value).change();
                        $(e.currentTarget).find('form#update-transaction #type').val(value).change()
                        console.log(index, value)
                    }
                    if (index === 'entity_id') {
                        //$("#entity_id").val(value);
                        //$("#entity-id").val(value).change();
                        $(e.currentTarget).find('form#update-transaction #entity-id').val(value).change()
                        //$("#entity-id option[value='" + value +"']").attr('selected','selected');
                        console.log(index, value)
                    }
                    if (index === 'date') {
                        //$( "#datepicker" ).datepicker({
                        $(e.currentTarget).find('form#update-transaction #datepicker').datepicker({
                            dateFormat: "yy-mm-dd",
                            firstDay: 1
                        });
            
                        $(e.currentTarget).find('form#update-transaction #datepicker').datepicker("setDate", value);
                        console.log(index, value)
                    }
                    if (index === 'notes') {
                        //$("#notes").val(value);
                        $(e.currentTarget).find('form#update-transaction #notes')
                        console.log(index, value)
                    }
                });
            // Update successful
            /*
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
            */
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




    })

})   