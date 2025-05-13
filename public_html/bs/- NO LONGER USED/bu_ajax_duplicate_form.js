$(document).ready(function() {


    $("table.bu-data-table").on("click", ".view-record", function(event) {

    });

    // See https://stackoverflow.com/a/47604306
    const getData = async () => {
        let objectReturnedByServer = await fetch('JSON/update_forms.json')
        let objectReturnedByServerAsJSON = await objectReturnedByServer.json();
        return objectReturnedByServerAsJSON;
    }


// Populate the update form fields
    //$("div#update-transaction-modal").on("show.bs.modal", function (e) {
    $("div[id^=duplicate-][id$=-modal]").on("show.bs.modal", function (e) {

        /***
             * 'e.relatedTarget' is a reference to the link that, when clicked, displayed the modal
             * 'e.currentTarget' is a reference to the the modal
        */ 

        //console.log($(e.relatedTarget).closest('table').attr('id'))
        let dataTableID = $(e.relatedTarget).closest('table').attr('id')     // The id of the DataTable, i.e. '#transactions'

        //console.log($(e.currentTarget).find('form.update-form').attr('id'))
        let updateFormID = $(e.currentTarget).find('form.duplicate-form').attr('id')   // The id of the form with a class of 'update-form', i.e. '#update-transaction'

        var DTRowIndex = $('#' + dataTableID).DataTable().row($(e.relatedTarget).parents('tr')).index()   // Based on the total number of rows in a table. With a table containing 5,000 records the row index is between 0 and 4,999.
        $('form#' + updateFormID + ' input[type=text][name=dt-row-index]').val(DTRowIndex)

        var DOMRowIndex = $(e.relatedTarget).closest("tr").index()                                      // Based on the table rows currently being displayed. With 25 records currently displayed the row index is between 0 and 24.
        $('form#' + updateFormID + ' input[type=text][name=dom-row-index]').val(DOMRowIndex)
               
        var recordID = $(e.relatedTarget).data("record-id")         // data-record-id
        var mysqlTable = $(e.relatedTarget).data("mysql-table")     // data-mysql-table
        var usedBy = $(e.relatedTarget).data("used-by")             // data-used-by (not always used)
        var recordType = $(e.relatedTarget).data("record-type")     // data-record-type (not always used)

        console.log(usedBy)
        
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
                console.log(dataReturnedByServer['data'])

                /**
                 * This is an Imediatley Invoked Function Expression (IIFE). See https://developer.mozilla.org/en-US/docs/Glossary/IIFE
                 * It's the same as first declaring the function, then invoking it...
                 *     // Declare function
                 *     const PopulateFormFields = async ($w, x, y, z) => {
                 *         let jsonData = await getData();
                 *         ....
                 *     }
                 * 
                 *     // Invoke function
                 *     userData($(e.currentTarget), dataTableID, updateFormID, data)
                 */
                const PopulateFormFields = (async ($w, x, y, z) => {
                    /**
                     * $w = $(e.currentTarget)
                     * x  = dataTableID
                     * y  = updateFormID
                     * z  = data
                     */
                    let jsonData = await getData();     // getData() is declared in bs_custom.js
                    let tableObject = jsonData.tables.find(function (obj) { return obj.table === x })      // The JSON object whose `table` key value is equal to the id of the DataTable e.g. "table": "transactions",
                    $w.find('h5#staticBackdropLabel').html('Duplicate ' + tableObject.header + ' [Record ID <span class="text-grey">' + recordID + '</span>]')
                    for (const field of tableObject.fields) {   // Loop through each object in the `fields` array

                        let todaysDate = new Date();
                        todaysDate.setHours(0,0,0,0);
                        todaysDate = todaysDate.getTime();

                        if (field.type === 'datepicker') {
                            
                            $w.find('form#' + y + ' #' + field.id).datepicker({             
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
                 
                             $w.find('form#' + y + ' #' + field.id).datepicker("setDate", moment(new Date()).format('YYYY-MM-DD')
                            );
                        }


                        if (field.type === 'number') {
                            $w.find('form#' + y + ' #' + field.id).val(z[field.column])
                            $w.find('form#' + y + ' #' + field.id).val(z[field.column]).trigger('change')    // Trigger the `change` event to add or remove the 'debit' class
                        }

                        if (field.type === 'select') {
                            $w.find('form#' + y + ' #' + field.id).val(z[field.column]).change()
                        }

                        if (field.type === 'text') {
                            $w.find('form#' + y + ' #' + field.id).val(z[field.column])
                        }

                        if (field.type === 'info') {    // Used By
                            console.log('HI')
                            $w.find('form#' + y + ' span#' + field.id).text('Used by ' + usedBy + ' ' + recordType + ' records')
                            //$(e.currentTarget).find('form#update-account span#used-by').text('Used by ' + usedBy + ' ' + recordType + ' records')
                        }

                    } // for

                }) ($(e.currentTarget), dataTableID, updateFormID, data);

                fetch('JSON/update_forms.json')
                .then((response) => { 
                    // Convert to JSON
                    return response.json();
                }).then((json) => {
                    // Update the form being processed using the JSON data returned by fetch()
                    let t = json.tables.find(key => key.table === dataTableID)    // The JSON object whose `table` key value is equal to the id of the DataTable e.g. "table": "transactions",
                    //console.log(t.table);
                    //console.log(t.maps);

                    //$(e.currentTarget).find('h5#staticBackdropLabel').html('View | Update ' + t.header + ' [Record ID <span class="text-grey">' + recordID + '</span>]')

                    /*
                    for (var i of t.maps) {
                        //console.log(i.type);
                        //console.log(i.id);
                        //console.log(i.column);

                        if (i.type === 'datepicker') {
                            
                            $(e.currentTarget).find('form#' + updateFormID + ' #' + i.id).datepicker({             
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
                 
                             $(e.currentTarget).find('form#' + updateFormID + ' #' + i.id).datepicker("setDate", data[i.column]);
                        }


                        if (i.type === 'number') {
                            $(e.currentTarget).find('form#' + updateFormID + ' #' + i.id).val(data[i.column])
                            $(e.currentTarget).find('form#' + updateFormID + ' #' + i.id).val(data[i.column]).trigger('change')    // Trigger the `change` event to add or remove the 'debit' class
                        }

                        if (i.type === 'select') {
                            $(e.currentTarget).find('form#' + updateFormID + ' #' + i.id).val(data[i.column]).change()
                        }

                        if (i.type === 'text') {
                            $(e.currentTarget).find('form#' + updateFormID + ' #' + i.id).val(data[i.column])
                        }

                    }
                        */

                });

                /**
                 * The 'Handling JSON' section at https://davidwalsh.name/fetch
                 * https://www.digitalocean.com/community/tutorials/how-to-use-the-javascript-fetch-api-to-get-data
                 * https://stackoverflow.com/a/47604112
                 */
                /*
                fetch('JSON/update_forms.json')
                .then((response) => { 
                    // Convert to JSON
                    return response.json();
                }).then((json) => {
                    // Update the form being processed using the JSON data returned by fetch()
                    let t = json.tables.find(key => key.table === dataTableID)    // The JSON object whose `table` key value is equal to the id of the DataTable e.g. "table": "transactions",
                    //console.log(t.table);
                    //console.log(t.maps);

                    $(e.currentTarget).find('h5#staticBackdropLabel').html('View | Update ' + t.header + ' [Record ID <span class="text-grey">' + recordID + '</span>]')

                    for (var i of t.maps) {
                        //console.log(i.type);
                        //console.log(i.id);
                        //console.log(i.column);

                        if (i.type === 'datepicker') {
                            
                            $(e.currentTarget).find('form#' + updateFormID + ' #' + i.id).datepicker({             
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
                 
                             $(e.currentTarget).find('form#' + updateFormID + ' #' + i.id).datepicker("setDate", data[i.column]);
                        }


                        if (i.type === 'number') {
                            $(e.currentTarget).find('form#' + updateFormID + ' #' + i.id).val(data[i.column])
                            $(e.currentTarget).find('form#' + updateFormID + ' #' + i.id).val(data[i.column]).trigger('change')    // Trigger the `change` event to add or remove the 'debit' class
                        }

                        if (i.type === 'select') {
                            $(e.currentTarget).find('form#' + updateFormID + ' #' + i.id).val(data[i.column]).change()
                        }

                        if (i.type === 'text') {
                            $(e.currentTarget).find('form#' + updateFormID + ' #' + i.id).val(data[i.column])
                        }

                    }

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

    });  // `show.bs.modal` listener

    $('form.duplicate-form').on('submit', function(event) {                    // Any form whose class is 'update-form' will be processed by this event handler when it's submitted
        event.preventDefault(); // Prevent form from submitting normally

        formData = new FormData($(this)[0]);    // Bind the FormData object and the form element
        formData.append('form-id', $(this).attr('id'))

        for (var pair of formData.entries()) {
            console.log(pair[0]+ ', ' + pair[1]);
        }

        dataTableID = $(this).data('datatable-id')
        DTRowIndex = formData.get('dt-row-index')
        DOMRowIndex = formData.get('dom-row-index')
        formID = formData.get('form-id')

        $.ajax({
            method: "POST",
            dataType: "json",       // The type of data expected back from the server
            contentType: false,		// Will throw an error when using a FormData object if omitted or set to anything other than 'false'  
            processData: false,		// Will throw an error when using a FormData object if omitted or set to 'true' (default) 
            url: "ajax/bu_ajax_duplicate_form.php",
            data: formData
        })  // $.ajax
        .done(function( dataReturnedByServer, textStatus, jqXHR) {  // Used instead of the AJAX local callback event 'success: function()'. See https://stackoverflow.com/a/15821199/2518495
            if ( dataReturnedByServer['success'] == 1 ) {
        // Update successful

                 /**
                 * This is an Imediatley Invoked Function Expression (IIFE). See https://developer.mozilla.org/en-US/docs/Glossary/IIFE
                 * It's the same as first declaring the function, then invoking it...
                 *     // Declare function
                 *     const UpdateDataTableDisplay = async (w, x, y, z) => {
                 *         let jsonData = await getData();
                 *         ....
                 *     }
                 * 
                 *     // Invoke function
                 *     userData(formID, dataTableID, DTRowIndex, DOMRowIndex)
                 */
                 const UpdateDataTableDisplay = (async (w, x, y, z) => {
                    /**
                     * x = formID
                     * x = dataTableID
                     * y = DTRowIndex
                     * z = DOMRowIndex
                     */
                    const jsonData = await getData();    
                    //console.log(jsonData)
                    const tableObject = jsonData.tables.find(function (obj) { return obj.table === x })      // The JSON object whose `table` key value is equal to the id of the DataTable e.g. "table": "transactions",
                    //console.log(x)
                    //console.log(tableObject.table)

                    const $DTTable = $('#' + x).DataTable()
                    const $inputForm = $('form#' + w)

                    r = $DTTable.row.add(['9','X','ACCOUNT DETAILS','0.00','ENTITY','TYPE','SUB-TYPE','METHOD','3001-01-01','3000/01','9999','<a class="btn btn-success btn-sm view-record" href="#" data-bs-toggle="modal" data-bs-target="#update-transaction-modal" data-mysql-table="bu_transactions" data-record-id="8610"><i class="fa fa-edit"></i></a> <a data-mysql-table="bu_transactions" data-record-id="8610" data-record-type="transaction" data-record-identifier="Interest on Barclays Cash ISA - Fixed Term [2025/26]" class="btn btn-danger btn-sm delete-record" href="#"><i class="fa fa-trash"></i></a> <a class="btn btn-primary btn-sm duplicate-record" href="#" data-bs-toggle="modal" data-bs-target="#duplicate-transaction-modal" data-mysql-table="bu_transactions" data-record-id="8610"><i class="fa fa-copy"></i></a>'])

                    y = r.index()
                    
                    for (const field of tableObject.fields) {   // Loop through each object in the `fields` array
                        if (field.datatable) {

                            if (field.type === 'datepicker') {
                                
                                /**
                                    $('form#update-transaction')
                                        .find('#transaction-date')
                                            .val()                                 
                                */
                                displayText = $inputForm.find('#' + field.id).val()
                                
                                /**
                                    $('#transactions')
                                        .DataTable()
                                            .cell(0,'date:name')
                                                .data( 
                                                    $('form#update-transaction')
                                                        .find('#transaction-date')
                                                            .val() 
                                                )
                                */
                                $DTTable.cell(y, field.dt_name + ':name').data(displayText) // e.g. $('#transactions').DataTable().cell(0,'date:name').data( $('form#update-transaction').find('#transaction-date').val() )

                                //if (field.type === 'datepicker') {
                                // Remove the `past`, `today` or `future` class and re-add the correct one in case the record's date has changed
                                    $( $DTTable.cell(y, field.dt_name + ':name').node() ).removeClass('past today future').addClass(Chronology(displayText))
                                //}

                                

                            }

                            
                            if (field.type === 'number') {
                                $DTTable.cell(y, field.dt_name + ':name').data($inputForm.find('#' + field.id).val())

                            // Add or remove 'debit' class in case the record's amount has changed
                                if (parseFloat($DTTable.cell(y, field.dt_name + ':name').data()) < 0) {
                                    $( $DTTable.cell(y, field.dt_name + ':name').node() ).addClass('debit')
                                } else {
                                    $( $DTTable.cell(y, field.dt_name + ':name').node() ).removeClass('debit')
                                }

                            }

                            if (field.type === 'select') {
                                displayText = ''
                                if ($inputForm.find('#' + field.id + ' option:selected').val() != '') {
                                    displayText = $inputForm.find('#' + field.id + ' option:selected').text()
                                }
                                $DTTable.cell(y, field.dt_name + ':name').data(displayText)
                            }

                            
                            if (field.type === 'text') {
                                
                                displayText = $inputForm.find('#' + field.id).val()
                                //console.log("TEXT: " + displayText)

                                $DTTable.cell(y, field.dt_name + ':name').data(displayText)



                                if (tableObject.table === 'transactions') { // Only do this for the transactions DataTable
                                    if (field.type === 'text' && field.id === 'account-id-alpha-read-only') {
                                    // Remove the `account-code-*` class and re-add it in case the record's account has changed  
                                        $( $DTTable.row(y).node() ).removeClass(function (index, css) {     //See https://codepen.io/jakob-e/pen/GJWZvx
                                            return (css.match (/\baccount-code-\S+/g) || []).join(' '); 
                                        }).addClass('account-code-' + displayText.toLowerCase());
                                    }
                                }
                                

                            }
                                


                        }

                    }   // for

                    if ( tableObject.fields.find(function (obj) { return obj.id === 'notes'}) != undefined) {
                        
                        field = tableObject.fields.find(function (obj) { return obj.id === 'notes'})

                        //console.log($inputForm.find('#' + field.id).val())

                    
                        if ($inputForm.find('#' + field.id).val().length === 0) {               
                        // Remove the `has-note` class and associated `data-*` attributes if the record's note has been deleted
                            $( $DTTable.cell(y, 'counter:name').node() ).removeClass('details-control has-note right down')
                            $( $DTTable.cell(y, 'counter:name').node() ).removeAttr('data-note')
                            if ( $( $DTTable.row(y).node() ).hasClass('dt-hasChild') ) {
                                $DTTable.row(y).child.hide()
                            }
                        } else {
                        // Add the `has-note` class and associated `data-*` attributes if the record's existing note has been updated or a new note has been added
                            $( $DTTable.cell(y, 'counter:name').node() ).addClass('details-control has-note right')
                            $( $DTTable.cell(y, 'counter:name').node() ).attr('data-note', nl2br($inputForm.find('#' + field.id).val()))
                        }

                        if ( $( $DTTable.row(y).node() ).hasClass('dt-hasChild') ) {

                            //$DTTable.row(DTRowIndex).child().addClass('BALLS')
                            //console.log($DTTable.row(y).child().find($('div.note')).html())
                            $DTTable.row(y).child().find($('div.note')).html(nl2br($inputForm.find('#' + field.id).val()))
                        }
                                
                    }

                    //$DTTable.draw(false)

                    /*
                    $(r.node())
                        .addClass('duplicated-record');
                    */
                        
                }) (formID, dataTableID, DTRowIndex, DOMRowIndex);                


                SuccessAlert (2500, dataReturnedByServer, true)

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