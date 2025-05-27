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
    $("div[id^=view-][id$=-modal]").on("show.bs.modal", function (e) {

        /***
             * 'e.relatedTarget' is a reference to the link that, when clicked, displayed the modal
             * 'e.currentTarget' is a reference to the the modal
        */ 

        //console.log($(e.relatedTarget).closest('table').attr('id'))
        //let dataTableID = $(e.relatedTarget).closest('table').attr('id')     // The id of the DataTable, i.e. '#transactions'

        //console.log($(e.currentTarget).find('form.update-form').attr('id'))
        //let updateFormID = $(e.currentTarget).find('form.update-form').attr('id')   // The id of the form with a class of 'update-form', i.e. '#update-transaction'

        //var DTRowIndex = $('#' + dataTableID).DataTable().row($(e.relatedTarget).parents('tr')).index()   // Based on the total number of rows in a table. With a table containing 5,000 records the row index is between 0 and 4,999.
        //$('form#' + updateFormID + ' input[type=text][name=dt-row-index]').val(DTRowIndex)

        //var DOMRowIndex = $(e.relatedTarget).closest("tr").index()                                      // Based on the table rows currently being displayed. With 25 records currently displayed the row index is between 0 and 24.
        //$('form#' + updateFormID + ' input[type=text][name=dom-row-index]').val(DOMRowIndex)
               
        var recordID = $(e.relatedTarget).data("record-id")         // data-record-id
        var mysqlTable = $(e.relatedTarget).data("mysql-table")     // data-mysql-table
        //var usedBy = $(e.relatedTarget).data("used-by")             // data-used-by (not always used)
        //var recordType = $(e.relatedTarget).data("record-type")     // data-record-type (not always used)

        //console.log(usedBy)
        
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

            // Fetch successful
                data = JSON.parse(dataReturnedByServer['data'])
                console.log(dataReturnedByServer['data'])

                // Heading
                $('div#view-monthly-spend-modal').find('h4#staticBackdropLabel').html(`Monthly Spend for Period <span class="text-primary">${data['period']}</span>, Ending <span class="text-primary">${moment(data['end']).format('dddd DD/MM/YYYY')}</span>`)

                const ignore = ['id', 'period', 'end']
                Object.entries(data).forEach(([key, value]) => {
                    if (!ignore.includes(key)) {
                        var filterCol = 5

                        switch(key) {
                            case 'salary':
                                var filterCol = 6
                                var filterValue = 'Salary'
                                break;
                            case 'pension':
                                var filterCol = 6
                                var filterValue = 'Pension'
                                break;
                            case 'cash':
                                var filterValue = 'Cash'
                                break;
                            case 'utilities':
                                var filterValue = 'Utility'
                                break;
                            case 'commute':
                                var filterValue = 'Commute'
                                break;
                            case 'cards':
                                var filterValue = 'Card'
                                break;
                            case 'supermarket':
                                var filterValue = 'Supermarket'
                                break;
                            case 'other':
                                var filterValue = 'Other'
                                break;
                            case 'rent':
                                var filterValue = 'Rent'
                                break;
                            case 'charities':
                                var filterValue = 'Charity'
                                break;
                            default:
                              // code block
                        }

                        var amount = new Intl.NumberFormat('en-GB', { style: 'currency', currency: 'GBP', currencyDisplay: 'symbol' }).format(value)

                        if (value != 0) {
                            $(`div#view-monthly-spend div#${key}`).html(`<a class="no-link-color" href="bu_manage_transactions.php?filter-col-${filterCol}=${filterValue}&filter-col-10=${data['period']}">${amount}</a>`)
                        } else {
                            $(`div#view-monthly-spend div#${key}`).text(amount)                        
                        }

                        if (value < 0) {
                            $(`div#view-monthly-spend div#${key}`).addClass('debit')
                        }
                    }
                });

                const include = ['total_spend', 'remaining']
                Object.entries(data).forEach(([key, value]) => {
                    if (include.includes(key)) {
                        $(`div#view-monthly-spend div#${key}`).text(new Intl.NumberFormat('en-GB', { style: 'currency', currency: 'GBP', currencyDisplay: 'symbol' }).format(value))

                        if (value < 0) {
                            $(`div#view-monthly-spend div#${key}`).addClass('debit')
                        }
                    }
                });
            } else {
            // Fetch failed
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

})   