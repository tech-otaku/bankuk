/**
 * The following DataTables callback functions appear to be triggered BEFORE the DOM is fully loaded;
 * 1. createdRow    (Not fired when paging)
 * 2. footerCallback
 * 3. drawCallback
 * 4. initComplete  (Not fired when paging)
 * Consequently, any user-defined functions used by these callbacks should be declared outside of any `$(document).ready(function() {})`
 */ 

/*
$(document).ready(function() {
    //console.log('Document Ready')
    $('#transactions').DataTable().on('draw.dt', function() {
       //console.log('Table redrawn!');
    });
 });
*/


/*
const useData = async () => {
    let jsonData = await getData();
    console.log(jsonData)
}
*/
    


/*
async function myGetJSON() {
    const response = await fetch("JSON/transactions.json");
    const data = await response.json();
    return data
}

async function collectData() {
   return myGetJSON();
}
   

function getData() {
    var jsonTemp = null;
    $.ajax({
        'async': false,
        'url': "JSON/transactions.json",
        'success': function (data) {
            jsonTemp = data;
        }
    });
    return jsonTemp;
}


function getJSON() {
    var jsonTemp = null;
    $.ajax({
        'async': false,
        'url': "JSON/transactions.json"
    })
    .done(function( data, textStatus, jqXHR) {  // Used instead of the AJAX local callback event 'success: function()'. See https://stackoverflow.com/a/15821199/2518495
        jsonTemp = data
    })  // done
    .fail(function ( jqXHR, textStatus, errorThrown) {  // Used instead of the AJAX local callback event 'error: function()'. See https://stackoverflow.com/a/15821199/2518495
        console.log(jqXHR)
    }); // fail
    return jsonTemp
}

    */

function DatePickerPlacement (e,i,p) {
    /***
     * `e` represents the input field `div#datepicker` 
     * `i` is a JQuery object representing the current datepicker instance `div#ui-datepicker-div`
     * `p` represents the placement of the datepicker instance relative to the input field `div#datepicker`: 0 = to the right of, 1 = above and 2 = below
     * 
     * Normally, the parent of the `div#ui-datepicker-div` is the `body` tag, so it's positioned relative to the 
     * viewport (browser window) and can sometimes be displayed outside of the modal window defined by `div.modal-content`.
     * To prevent this the parent of the `div#ui-datepicker-div` is first changed to `div.modal-content`. In addition, the 
     * `position` of `div.modal-content` is set to `relative` in bu_transaction.php.
    */
    $('.modal-content').append($('div#ui-datepicker-div'));    // See https://stackoverflow.com/a/42733236
    /***
      * Now, the position of `div#ui-datepicker-div` can be fixed relative to its new parent `div.modal-content` by setting 
      * its `position` to `absolute` and defining its `top` and `left` properties.
    */
    setTimeout(function () {
        var parent = []
        parent['t'] = $(e).closest('.modal-content').offset().top     // The top position of `div.modal-content` relative to the viewport (browser window)
        parent['l'] = $(e).closest('.modal-content').offset().left    // The left position of `div.modal-content` relative to the viewport (browser window)
        var textBox = []
        textBox['t'] = $(e).offset().top                              // The top position of `div#datepicker` relative to the viewport (browser window)
        textBox['l'] = $(e).offset().left                             // The left position of `div#datepicker` relative to the viewport (browser window)
        textBox['h'] = parseFloat($(e).css('height'))                 // The height of `div#datepicker`
        textBox['w'] = parseFloat($(e).css('width'))                  // The width of `div#datepicker`
        var datePicker = [] 
        datePicker ['h'] = parseFloat(i.dpDiv.css('height'))         // The height of `div#ui-datepicker-div`

        switch (p) {
        // Right of
            case 0:
                t = textBox.t - parent.t + (-1)
                l = textBox.l - parent.l + textBox.w + 4
                break;
        // Above
            case 1:
                t = textBox.t - parent.t - datePicker.h + (-4)
                l = textBox.l - parent.l + (-1)
                break;
        // Below
            case 2:
                t = textBox.t + textBox.h - parent.t + 4
                l = textBox.l - parent.l + (-1)
                break;
        }

        i.dpDiv.css({
            position: 'absolute',
            top:t,
            left:l
        });
    }, 0);
}


function SuccessAlert (duration, dataReturnedByServer, reload=false) {
    // See https://sweetalert2.github.io/#examples
    //let timerInterval;
    Swal.fire({
        title: "<strong>Success</strong>",
        html: dataReturnedByServer['message'],
        icon: "success",
        timer: duration,
        timerProgressBar: true,
        footer: reload ? 'The page will be reloaded when this alert is dismissed.' : '',    // Optional message in footer if reload is `true`
        didOpen: () => {
            //Swal.showLoading();
            /*
            const timer = Swal.getPopup().querySelector("b");
            timerInterval = setInterval(() => {
            timer.textContent = `${Swal.getTimerLeft()}`;
            }, 100);
            */
        },
        willClose: () => {
            /*
            clearInterval(timerInterval);
            */
        }
        }).then((result) => {
        /* Read more about handling dismissals below */
        if (reload) {
            location.reload();
        }
        /*
        if (result.dismiss === Swal.DismissReason.timer) {
            console.log("I was closed by the timer");
        }
        */
    });

}

function UpdateDataTable(x,y,z,data) {  // The 'data' param is optional and is returned from bu_ajax-update.php
    switch(x) {
// ACCOUNT
        case 'update-account':
            var DTTable = $('#accounts').DataTable()
            var DTRowIndex = y
            var DOMRowIndex = z

            var inputForm = $('form#' + x)
            var inputFormFields = new Map()

        // Bank Name
            inputFormFields.set('bankName',inputForm.find('#bank-id option:selected').text())
        // Account Name
            inputFormFields.set('accountName',inputForm.find('#account-name').val())
        // Sort Code
            inputFormFields.set('sortCode',inputForm.find('#sort-code').val())
        // Account Number
            inputFormFields.set('accountNumber',inputForm.find('#account-number').val())
        // Status
            inputFormFields.set('status',inputForm.find('#status option:selected').text())
        //Notes
            inputFormFields.set('notes',inputForm.find('#notes').val())


        // Bank Name
            DTTable.cell(DTRowIndex, 3).data(inputFormFields.get('bankName'))
        // Account Name
            DTTable.cell(DTRowIndex, 4).data(inputFormFields.get('accountName'))
        // Sort Code
            DTTable.cell(DTRowIndex, 5).data(inputFormFields.get('sortCode'))
        // Account Number
            DTTable.cell(DTRowIndex, 6).data(inputFormFields.get('accountNumber'))
        // Status
            DTTable.cell(DTRowIndex, 7).data(inputFormFields.get('status'))

        if ($('form#update-account #notes').val().length === 0) {
        // Remove the `has-note` class and associated `data-*` attributes if the record's note has been deleted
            $( DTTable.cell(DTRowIndex,0).node() ).removeClass('details-control has-note right down')
            $( DTTable.cell(DTRowIndex,0).node() ).removeAttr('data-counter')
            $( DTTable.cell(DTRowIndex,0).node() ).removeAttr('data-note')
            $( DTTable.cell(DTRowIndex,0).node() ).removeAttr('data-account-name')
        } else {
        // Add the `has-note` class and associated `data-*` attributes if the record's existing note has been updated or a new note has been added
            $( DTTable.cell(DTRowIndex,0).node() ).addClass('details-control has-note right')
            $( DTTable.cell(DTRowIndex,0).node() ).attr('data-counter', $( DTTable.cell(DTRowIndex,0).node() ).text())
            $( DTTable.cell(DTRowIndex,0).node() ).attr('data-note', nl2br(inputFormFields.get('notes')))
            $( DTTable.cell(DTRowIndex,0).node() ).attr('data-account-name', inputFormFields.get('bankName') + ' ' + inputFormFields.get('accountName'))
        }


        // Re-draw the DataTable now the its record has been updated
            DTTable.draw(false)


            break;

// ACCOUNTING PERIOD
        case 'update-accounting-period':
            var DTTable = $('#periods').DataTable()
            var DTRowIndex = y
            var DOMRowIndex = z

            var inputForm = $('form#' + x)
            var inputFormFields = new Map()

        // Start
            inputFormFields.set('periodStart',inputForm.find('#period-start').val())
            //inputFormFields.set('date',inputForm.find('#datepicker-input').val())    // Tempus Dominus DatePicker
        // End
            inputFormFields.set('periodEnd',inputForm.find('#period-end').val())
            //inputFormFields.set('date',inputForm.find('#datepicker-input').val())    // Tempus Dominus DatePicker
        // Period
            inputFormFields.set('period',inputForm.find('#period').val())


        // Start
            DTTable.cell(DTRowIndex, 1).data(inputFormFields.get('periodStart'))
        // End
            DTTable.cell(DTRowIndex, 2).data(inputFormFields.get('periodEnd'))
        // Period
            DTTable.cell(DTRowIndex, 3).data(inputFormFields.get('period'))
            

        // Re-draw the DataTable now the its record has been updated
            DTTable.draw(false)

            break;

// BANK
        case 'update-bank':
            var DTTable = $('#banks').DataTable()
            var DTRowIndex = y
            var DOMRowIndex = z

            var inputForm = $('form#' + x)
            var inputFormFields = new Map()

        // Bank's Legal Name
            inputFormFields.set('legalName',inputForm.find('#legal-name').val())
        // Banks's Trading Name
            inputFormFields.set('tradingName',inputForm.find('#trading-name').val())


        // Bank's Legal Name
            DTTable.cell(DTRowIndex, 2).data(inputFormFields.get('legalName'))
        // Banks's Trading Name
            DTTable.cell(DTRowIndex, 3).data(inputFormFields.get('tradingName'))


        // Re-draw the DataTable now the its record has been updated
            DTTable.draw(false)

            break;
// ENTITY
        case 'update-entity':
            var DTTable = $('#entities').DataTable()
            var DTRowIndex = y
            var DOMRowIndex = z

            var inputForm = $('form#' + x)
            var inputFormFields = new Map()

        // Entity Description
            inputFormFields.set('entityDescription',inputForm.find('#entity-description').val())


        // Entity Description
            DTTable.cell(DTRowIndex, 2).data(inputFormFields.get('entityDescription'))


        // Re-draw the DataTable now the its record has been updated
            DTTable.draw(false)

            break;

// PRE-FILL
        case 'update-prefill':
            var DTTable = $('#prefills').DataTable()
            var DTRowIndex = y
            var DOMRowIndex = z

            var inputForm = $('form#' + x)
            var inputFormFields = new Map()

        // Account Name
            inputFormFields.set('accountName',inputForm.find('#account-id-alpha option:selected').text())
        // Type
            inputFormFields.set('typeID',inputForm.find('#type-id option:selected').text())
        // Sub-Type
            if (inputForm.find('#sub-type-id option:selected').val() === '') {
                inputFormFields.set('subTypeID','')
            } else {
                inputFormFields.set('subTypeID',inputForm.find('#sub-type-id option:selected').text())
            }
        // Method
            inputFormFields.set('methodID',inputForm.find('#method-id option:selected').text())
        // Entity
            inputFormFields.set('entity',inputForm.find('#entity-id option:selected').text())

            console.log(inputFormFields)


        // Account Name
            DTTable.cell(DTRowIndex, 2).data(inputFormFields.get('accountName'))
        // Type
            DTTable.cell(DTRowIndex, 3).data(inputFormFields.get('typeID'))
        // Sub-Type
            DTTable.cell(DTRowIndex, 4).data(inputFormFields.get('subTypeID'))
        // Sub-Type
            DTTable.cell(DTRowIndex, 5).data(inputFormFields.get('methodID'))
        // Entity
            DTTable.cell(DTRowIndex, 1).data(inputFormFields.get('entity'))
            

        // Re-draw the DataTable now the its record has been updated
            DTTable.draw(false)

            break;

// REGULAR DEBIT
        case 'update-regular-debit':

            console.log(data)

            var DTTable = $('#regular-debits').DataTable()
            var DTRowIndex = y
            var DOMRowIndex = z

            var inputForm = $('form#' + x)
            var inputFormFields = new Map()

        // Account ID Alpha
            inputFormFields.set('accountIDAlpha',inputForm.find('#account-id-alpha option:selected').val())
        // Account Name
            inputFormFields.set('accountName',inputForm.find('#account-id-alpha option:selected').text())
        // Amount
            inputFormFields.set('amount',inputForm.find('#amount').val())
        // Type
            inputFormFields.set('typeID',inputForm.find('#type-id option:selected').text())
        // Sub-Type
            if (inputForm.find('#sub-type-id option:selected').val() === '') {
                inputFormFields.set('subTypeID','')
            } else {
                inputFormFields.set('subTypeID',inputForm.find('#sub-type-id option:selected').text())
            }
        // Entity
            inputFormFields.set('entity',inputForm.find('#entity-id option:selected').text())
        // Method
            inputFormFields.set('methodID',inputForm.find('#method-id option:selected').text())
        // Day
            inputFormFields.set('day',inputForm.find('#day').val())
        // Period
            inputFormFields.set('period',data['period'])
        // Last
            inputFormFields.set('last',inputForm.find('#last').val())
        // Next
            inputFormFields.set('next',inputForm.find('#next').val())
        //Notes
            inputFormFields.set('notes',inputForm.find('#notes').val())


        // Account ID
            DTTable.cell(DTRowIndex, 2).data(inputFormFields.get('accountIDAlpha'))
        // Account Name
            DTTable.cell(DTRowIndex, 3).data(inputFormFields.get('accountName'))
        // Amount
            DTTable.cell(DTRowIndex, 4).data(inputFormFields.get('amount'))
        // Type
            DTTable.cell(DTRowIndex, 5).data(inputFormFields.get('typeID'))
        // Sub-Type
            DTTable.cell(DTRowIndex, 6).data(inputFormFields.get('subTypeID'))
        // Entity
            DTTable.cell(DTRowIndex, 7).data(inputFormFields.get('methodID'))
        // Entity
            DTTable.cell(DTRowIndex, 8).data(inputFormFields.get('entity'))
        // Day
            DTTable.cell(DTRowIndex, 9).data(inputFormFields.get('day'))
        // Period
            DTTable.cell(DTRowIndex, 10).data(inputFormFields.get('period'))
        // Last
            DTTable.cell(DTRowIndex, 11).data(inputFormFields.get('last'))
        // Next
            DTTable.cell(DTRowIndex, 12).data(inputFormFields.get('next'))
        // Notes
            // Notes are not displayed

        // Add or remove 'debit' class in case the record's amount has changed     
            if (parseFloat(inputFormFields.get('amount')) < 0) {
                $( DTTable.cell(DTRowIndex,4).node() ).addClass('debit')
            } else {
                $( DTTable.cell(DTRowIndex,4).node() ).removeClass('debit')
            }

        // Remove the `past`, `today` or `future` class and re-add it in case the record's date has changed
            //$( DTTable.cell(DTRowIndex,7).node() ).removeClass('past today future').addClass(Chronology(inputFormFields.get('last')))
            
            if ($('form#update-regular-debit #notes').val().length === 0) {
            // Remove the `has-note` class and associated `data-*` attributes if the record's note has been deleted
                $( DTTable.cell(DTRowIndex,0).node() ).removeClass('details-control has-note right down')
                $( DTTable.cell(DTRowIndex,0).node() ).removeAttr('data-counter')
                $( DTTable.cell(DTRowIndex,0).node() ).removeAttr('data-note')
                $( DTTable.cell(DTRowIndex,0).node() ).removeAttr('data-entity-description')
                $( DTTable.cell(DTRowIndex,0).node() ).removeAttr('data-amount')
                $( DTTable.cell(DTRowIndex,0).node() ).removeAttr('data-date')
            } else {
            // Add the `has-note` class and associated `data-*` attributes if the record's existing note has been updated or a new note has been added
                $( DTTable.cell(DTRowIndex,0).node() ).addClass('details-control has-note right')
                $( DTTable.cell(DTRowIndex,0).node() ).attr('data-counter', $( DTTable.cell(DTRowIndex,0).node() ).text())
                $( DTTable.cell(DTRowIndex,0).node() ).attr('data-note', nl2br(inputFormFields.get('notes')))
                $( DTTable.cell(DTRowIndex,0).node() ).attr('data-entity-description', inputFormFields.get('entity'))
                $( DTTable.cell(DTRowIndex,0).node() ).attr('data-amount', inputFormFields.get('amount'))
                $( DTTable.cell(DTRowIndex,0).node() ).attr('data-date', moment(inputFormFields.get('last')).format('ddd DD/MM/YYYY'))  // requires moment.js
            }

        // Re-draw the DataTable now the its record has been updated
            DTTable.draw(false)

            break;

// REGULAR DEBIT TYPE
        case 'update-regular-debit-type':
            var DTTable = $('#regular-debit-types').DataTable()
            var DTRowIndex = y
            var DOMRowIndex = z

            var inputForm = $('form#' + x)
            var inputFormFields = new Map()

        // Description
            inputFormFields.set('description',inputForm.find('#description').val())


        // Description
            DTTable.cell(DTRowIndex, 2).data(inputFormFields.get('description'))


        // Re-draw the DataTable now the its record has been updated
            DTTable.draw(false)

            break;



// TRANSACTION
        case 'update-transaction':
            var DTTable = $('#transactions').DataTable()
            var DTRowIndex = y
            var DOMRowIndex = z

            var inputForm = $('form#' + x)
            var inputFormFields = new Map()

        // Account ID Alpha
            inputFormFields.set('accountIDAlpha',inputForm.find('#account-id-alpha-read-only').val())
        // Account Name
            inputFormFields.set('accountName',inputForm.find('#account-id-alpha option:selected').text())
        // Amount
            inputFormFields.set('amount',inputForm.find('#amount').val())
        // Entity
            inputFormFields.set('entity',inputForm.find('#entity-id option:selected').text())
        // Type
            inputFormFields.set('typeID',inputForm.find('#type-id option:selected').text())
        // Sub-Type
            if (inputForm.find('#sub-type-id option:selected').val() === '') {
                inputFormFields.set('subTypeID','')
            } else {
                inputFormFields.set('subTypeID',inputForm.find('#sub-type-id option:selected').text())
            }
        // Method
            inputFormFields.set('methodID',inputForm.find('#method-id option:selected').text())
        // Date
            inputFormFields.set('date',inputForm.find('#transaction-date').val())
            //inputFormFields.set('date',inputForm.find('#datepicker-input').val())    // Tempus Dominus DatePicker
        // Notes
            inputFormFields.set('notes',inputForm.find('#notes').val())


        // Account ID
            DTTable.cell(DTRowIndex, 1).data(inputFormFields.get('accountIDAlpha'))
        // Account Name
            DTTable.cell(DTRowIndex, 2).data(inputFormFields.get('accountName'))
        // Amount
            DTTable.cell(DTRowIndex, 3).data(inputFormFields.get('amount'))
        // Entity
            DTTable.cell(DTRowIndex, 4).data(inputFormFields.get('entity'))
        // Type
            DTTable.cell(DTRowIndex, 5).data(inputFormFields.get('typeID'))
        // Sub-Type
            DTTable.cell(DTRowIndex, 6).data(inputFormFields.get('subTypeID'))
        // Method
            DTTable.cell(DTRowIndex, 7).data(inputFormFields.get('methodID'))
        // Date
            DTTable.cell(DTRowIndex, 8).data(inputFormFields.get('date'))
        // Notes
            
        // Remove the `account-code-*` class and re-add it in case the record's Account ID Alpha has changed  
            $( DTTable.row(DTRowIndex).node() ).removeClass(function (index, css) {     //See https://codepen.io/jakob-e/pen/GJWZvx
                return (css.match (/\baccount-code-\S+/g) || []).join(' '); 
            }).addClass('account-code-' + inputFormFields.get('accountIDAlpha').toLowerCase());

        // Add or remove 'debit' class in case the record's amount has changed     
            if (parseFloat(inputFormFields.get('amount')) < 0) {
                $( DTTable.cell(DTRowIndex,3).node() ).addClass('debit')
            } else {
                $( DTTable.cell(DTRowIndex,3).node() ).removeClass('debit')
            }

        // Remove the `past`, `today` or `future` class and re-add it in case the record's date has changed
            $( DTTable.cell(DTRowIndex,7).node() ).removeClass('past today future').addClass(Chronology(inputFormFields.get('date')))
            
            if ($('form#update-transaction #notes').val().length === 0) {               
            // Remove the `has-note` class and associated `data-*` attributes if the record's note has been deleted
                $( DTTable.cell(DTRowIndex,0).node() ).removeClass('details-control has-note right down')
                $( DTTable.cell(DTRowIndex,0).node() ).removeAttr('data-counter')
                $( DTTable.cell(DTRowIndex,0).node() ).removeAttr('data-note')
                $( DTTable.cell(DTRowIndex,0).node() ).removeAttr('data-entity-description')
                $( DTTable.cell(DTRowIndex,0).node() ).removeAttr('data-amount')
                $( DTTable.cell(DTRowIndex,0).node() ).removeAttr('data-date')
            } else {
            // Add the `has-note` class and associated `data-*` attributes if the record's existing note has been updated or a new note has been added
                $( DTTable.cell(DTRowIndex,0).node() ).addClass('details-control has-note right')
                $( DTTable.cell(DTRowIndex,0).node() ).attr('data-counter', $( DTTable.cell(DTRowIndex,0).node() ).text())
                $( DTTable.cell(DTRowIndex,0).node() ).attr('data-note', nl2br(inputFormFields.get('notes')))
                $( DTTable.cell(DTRowIndex,0).node() ).attr('data-entity-description', inputFormFields.get('entity'))
                $( DTTable.cell(DTRowIndex,0).node() ).attr('data-amount', inputFormFields.get('amount'))
                $( DTTable.cell(DTRowIndex,0).node() ).attr('data-date', moment(inputFormFields.get('date')).format('ddd DD/MM/YYYY'))  // requires moment.js
            }

        // Re-draw the DataTable now the its record has been updated
            DTTable.draw(false)

            break;

// TRANSACTION METHOD
        case 'update-transaction-method':
            var DTTable = $('#transaction-methods').DataTable()
            var DTRowIndex = y
            var DOMRowIndex = z

            var inputForm = $('form#' + x)
            var inputFormFields = new Map()

        // Description
            inputFormFields.set('methodDescription',inputForm.find('#method-description').val())


        // Description
            DTTable.cell(DTRowIndex, 2).data(inputFormFields.get('methodDescription'))


        // Re-draw the DataTable now the its record has been updated
            DTTable.draw(false)

            break;


// TRANSACTION SUB-TYPE
        case 'update-transaction-sub-type':
            var DTTable = $('#transaction-sub-types').DataTable()
            var DTRowIndex = y
            var DOMRowIndex = z

            var inputForm = $('form#' + x)
            var inputFormFields = new Map()

        // Description
            inputFormFields.set('subTypeDescription',inputForm.find('#sub-type-description').val())


        // Description
            DTTable.cell(DTRowIndex, 2).data(inputFormFields.get('subTypeDescription'))


        // Re-draw the DataTable now the its record has been updated
            DTTable.draw(false)

            break;


// TRANSACTION TYPE
        case 'update-transaction-type':
            var DTTable = $('#transaction-types').DataTable()
            var DTRowIndex = y
            var DOMRowIndex = z

            var inputForm = $('form#' + x)
            var inputFormFields = new Map()

        // Description
            inputFormFields.set('typeDescription',inputForm.find('#type-description').val())


        // Description
            DTTable.cell(DTRowIndex, 2).data(inputFormFields.get('typeDescription'))


        // Re-draw the DataTable now the its record has been updated
            DTTable.draw(false)

            break;
    }
}

/**
 * See https://gist.github.com/yidas/41cc9272d3dff50f3c9560fb05e7255e
 * This function is same as PHP's nl2br() with default parameters.
 *
 * @param {string} str Input text
 * @param {boolean} replaceMode Use replace instead of insert
 * @param {boolean} isXhtml Use XHTML 
 * @return {string} Filtered text
 */
function nl2br (str, replaceMode, isXhtml) {

    var breakTag = (isXhtml) ? '<br />' : '<br>';
    var replaceStr = (replaceMode) ? '$1'+ breakTag : '$1'+ breakTag +'$2';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, replaceStr);
}

// Remove the formatting to get integer data for summation
function intVal(i) {
    //console.log(i);
    return typeof i === 'string'
        //? i.replace(/[\$,]/g, '') * 1
        ? i.replace(/[^0-9.-]+/g, '') * 1
        : typeof i === 'number'
            ? i
            : 0;
}




function getUrlVars()
{
    //console.log('getUrlVar')

    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}



function customClass (elementName, className) {

    if (elementName === 'account-code') {  
        $( "td." + elementName ).each(function( index ) {
            //console.log(elementName);
            $(this).parent().addClass(className + $( this ).text().toLowerCase());
        });
    }

    
    if (elementName === 'currency') {
        $( "." + elementName).each(function( index ) {
            //console.log(parseFloat($( this ).text()))
            if (parseFloat($( this ).text().replace(/[^0-9.,-]+/,'')) < 0) {
                //console.log(parseFloat($( this ).text()))
                $(this).addClass(className);
            } else {
                $(this).removeClass(className);
            }
        });
    }
    


    if (elementName === 'transaction-date') {
        //console.log(elementName);
        let todaysDate = new Date();
        todaysDate.setHours(0,0,0,0);
        todaysDate = todaysDate.getTime();

        //console.log(todaysDate);
    
        $( "td." + elementName ).each(function( index ) {

            let transactionDate = new Date($( this ).text().substr(10), parseFloat($( this ).text().substr(7,2)) - 1, $( this ).text().substr(4,2));
            //console.log(transactionDate);
            transactionDate = transactionDate.getTime();
            //console.log(transactionDate);

            if (transactionDate > todaysDate) { 
                $(this).addClass('future'); 
            } else if (transactionDate === todaysDate) { 
                $(this).addClass('today'); 
            } else if (transactionDate < todaysDate) { 
                $(this).addClass('past');
            }
    
        });
    }
}

function Chronology (tDate) {
    let todaysDate = new Date();
    todaysDate.setHours(0,0,0,0);
    todaysDate = todaysDate.getTime();

    //console.log('Today ' + todaysDate);
    //console.log('Transaction ' + tDate)

    //let transactionDate = new Date(tDate.substr(10), parseFloat(tDate.substr(7,2)) - 1, tDate.substr(4,2));
    let transactionDate = new Date(tDate);
    transactionDate.setHours(0,0,0,0);
    //console.log('transactionDate' + transactionDate);
    transactionDate = transactionDate.getTime();
    //console.log('transactionDate' + transactionDate);

    if (transactionDate > todaysDate) { 
        return 'future'; 
    } else if (transactionDate === todaysDate) { 
        return 'today'; 
    } else if (transactionDate < todaysDate) { 
        return 'past';
    }

}
function ExcludedDates(date, excludedDays){           //  See https://stackoverflow.com/a/3354421/2518495
    //console.log('date' + date.getTime() + ' string ' + date)
    var day = date.getDay(), Sunday = 0, Monday = 1, Tuesday = 2, Wednesday = 3, Thursday = 4, Friday = 5, Saturday = 6;
    
    /*
    publicHolidays = [
    // 2024
        [2024, 1, 1], [2024, 3, 29], [2024, 4, 1], [2024, 5, 6], [2024, 5, 27], [2024, 8, 26], [2024, 12, 25], [2024, 12, 26], 
    // 2025
        [2025, 1, 1], [2025, 4, 18], [2025, 4, 21], [2025, 5, 5], [2025, 5, 26], [2025, 8, 25], [2025, 12, 25], [2025, 12, 26],
    // 2026
        [2026, 1, 1], [2026, 4, 3], [2026, 4, 6], [2026, 5, 4], [2026, 5, 25], [2026, 8, 31], [2026, 12, 25], [2026, 12, 28],
    // 2027
        [2027, 1, 1], [2027, 3, 26], [2027, 3, 29], [2027, 5, 3], [2027, 5, 31], [2027, 8, 30], [2027, 12, 27], [2027, 12, 28],
    // 2028
        [2028, 1, 3], [2028, 4, 14], [2028, 4, 17], [2028, 5, 1], [2028, 5, 29], [2028, 8, 28], [2028, 12, 25], [2028, 12, 26],
    // 2029
        [2029, 1, 1], [2029, 3, 30], [2029, 4, 2], [2029, 5, 7], [2029, 5, 28], [2029, 8, 27], [2029, 12, 25], [2029, 12, 26],
    // 2030
        [2030, 1, 1], [2030, 4, 19], [2030, 4, 22], [2030, 5, 6], [2030, 5, 27], [2030, 8, 26], [2030, 12, 25], [2030, 12, 26],
    ];
    */
    

    
    publicHolidays = [
        // 2024
            '2024-01-01', '2024-03-29', '2024-04-01', '2024-05-06', '2024-05-27', '2024-08-26', '2024-12-25', '2024-12-26', 
        // 2025
            '2025-01-01', '2025-04-18', '2025-04-21', '2025-05-05', '2025-05-26', '2025-08-25', '2025-12-25', '2025-12-26',
        // 2026
            '2026-01-01', '2026-04-03', '2026-04-06', '2026-05-04', '2026-05-25', '2026-08-31', '2026-12-25', '2026-12-28',
        // 2027
            '2027-01-01', '2027-03-26', '2027-03-29', '2027-05-03', '2027-05-31', '2027-08-30', '2027-12-27', '2027-12-28',
        // 2028
            '2028-01-03', '2028-04-14', '2028-04-17', '2028-05-01', '2028-05-29', '2028-08-28', '2028-12-25', '2028-12-26',
        // 2029
            '2029-01-01', '2029-03-30', '2029-04-02', '2029-05-07', '2029-05-28', '2029-08-27', '2029-12-25', '2029-12-26',
        // 2030
            '2030-01-01', '2030-04-19', '2030-04-22', '2030-05-06', '2030-05-27', '2030-08-26', '2030-12-25', '2030-12-26',
        ];
    

    //var closedDays = [[Friday], [Saturday]];
    for (var i = 0; i < excludedDays.length; i++) {
        if (day == excludedDays[i][0]) {
            return [false];
        }
    }

    /***
         AN ISSUE WITH DATE COMPARISON AND BRITISH SUMMER TIME (BST)
         - The JavaScript Date() constructor can accept a date-only string (e.g. '2025-02-28') to create a new Date object
         - `new Date('2025-2-28')` and `new Date('2025-02-28')` - the former without a leading zero for the month portion - both return the same Date object -> 'Fri Feb 28 2025 00:00:00 GMT+0000 (Greenwich Mean Time)'
         - 28th February is before the start of BST which in 2025 starts on 31st March
         - For dates before the start of BST, the leading zero for the month portion of the date string appears irrelevant, but for dates on or after the start of BST it's significant
         - `new Date('2025-3-31')`  returns 'Mon Mar 31 2025 00:00:00 GMT+0100 (British Summer Time)' - Time is 12 a.m.
         - `new Date('2025-03-31')` returns 'Mon Mar 31 2025 01:00:00 GMT+0100 (British Summer Time)' - Time is  1 a.m.
         - The Date object for 31st March 2025 from the JQuery UI DatePicker contained in the `date` variable is 'Mon Mar 31 2025 00:00:00 GMT+0100 (British Summer Time)' - Time is 12 a.m.
         - When comparing the `date` variable with the date of a public holiday the public holidat Date object should be created with either:
            `new Date('2025-3-31')`             = 'Mon Mar 31 2025 00:00:00 GMT+0100 (British Summer Time)' - Time is 12 a.m.
            `new Date('2025-03-31T00:00:00')`   = 'Mon Mar 31 2025 00:00:00 GMT+0100 (British Summer Time)' - Time is 12 a.m. - Do not use `new Date('2025-03-31T00:00:00Z')` with a 'Z' it returns 1 a.m.!
            `date`                              = 'Mon Mar 31 2025 00:00:00 GMT+0100 (British Summer Time)' - Time is 12 a.m

    */ 

    for (i = 0; i < publicHolidays.length; i++) {
        if (
            //date.getTime() === new Date(publicHolidays[i][0] + '-' + publicHolidays[i][1] + '-' + publicHolidays[i][2]).getTime()
            date.getTime() === new Date(publicHolidays[i] + 'T00:00:00').getTime()
            /*
            date.getMonth() == publicHolidays[i][0] - 1 &&
            date.getDate() == publicHolidays[i][1] &&
            date.getFullYear() == publicHolidays[i][2]
            */
        ) {
            return [false];
        }
    }

    return [true];
}

$(document).ready(function() {

    $(".add-form select#sub-type-id.form-control, .update-form select#sub-type-id.form-control, .duplicate-form select#sub-type-id.form-control").on('change', function() {   
        //console.log('Fired')
        if (this.value) {
            $(this).css('color', '#495057')
        } else {
            $(this).css('color', '#cacaca')
        }
    });

    $(".add-form input#amount.form-control, .update-form input#amount.form-control, .duplicate-form input#amount.form-control").on("change paste", function() {
        if (parseFloat($(this).val()) < 0) {
            $(this).addClass('debit')
        } else {
            $(this).removeClass('debit')
        }
    });

    /**
     * - This event is fired when the user selects a new option in the Account Data dropdown.
     * - It updates the read-only input element that contains the account's Alpha ID (`account_id_alpha`) to 
     * that of the currently selected (changed) option.
     */ 
    $(".add-form select#account-id-alpha.form-control, .update-form select#account-id-alpha.form-control").on('change', function() {
        var alphaID = this.value                                            // The value of the currently selected (changed) option.
        $(this).closest('form').find('#account-id-alpha-read-only').val(alphaID)     // The read-only input element
    });

    /*
    $("table.bu-data-table").on("click", ".has-note", function(event) {
        console.log($(this).closest('table').attr('id'))
        switch($(this).closest('table').attr('id')) {
            case 'accounts':
                Swal.fire(
                    {
                        //title: "Note",
                        html: '<div class="text-left">[<span class="text-grey">' + $(this).attr("data-counter") + '</span>]&nbsp;<span class="text-grey">' + $(this).attr("data-account-name") + '</span><br /><br /><i>' + $(this).attr("data-note") + '</></div>',
                        icon: "info",
                        position: "top-end"
                    }
                );
                break;
            default:    // table#regular-debits, table#transactions
                direction = ' to '
                if (intVal($(this).data("amount")) >= 0) {
                    direction = ' from '
                }
                Swal.fire(
                    {
                        //title: "Note",
                        html: '<div class="text-left">[<span class="text-grey">' + $(this).attr("data-counter") + '</span>]&nbsp;<span class="text-grey">' + $(this).attr("data-amount") + '</span>' + direction + '<span class="text-grey">' + $(this).attr("data-entity-description") + '</span> on <span class="text-grey">' + $(this).attr("data-date") + '</span><br /><br /><i>' + $(this).attr("data-note") + '</></div>',
                        icon: "info",
                        position: "top-end"
                    }
                );
                break; 
            
        }

        
    })
        */

    // Add event listener for opening and closing details
    $('table').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = $('table#' + $(this).closest('table').attr('id')).DataTable().row(tr);   // $('table#<tableid>').DataTable().row(tr)

        console.log($(this).attr("class"))
        console.log($(this).closest('table').attr('id'))

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
            $(this).removeClass('down')
            $(this).addClass('right')
        } else {
            // Open this row
            row.child('<div class="note">' + $(this).attr("data-note")  + '</div>').show();
            tr.addClass('shown');
            $(this).removeClass('right')
            $(this).addClass('down')
        }
    });
    

    $('#prefill').on('change', function() {
        var selected = $(this).find('option:selected');     // See https://stackoverflow.com/a/4564711
        //console.log(selected.data('account-id-alpha'))
        //console.log(selected.data('type-id'))
        console.log(selected.data())
        //var extra = selected.data('foo'); 
        //console.log(selected.data('entity-id') );
        //$("#account-id-alpha option[value='G']").attr("selected", true);
        $("#account-id-alpha").val(selected.data('account-id-alpha'));
        $("#type-id").val(selected.data('type-id'));
        $("#sub-type-id").val(selected.data('sub-type-id'));
        $("#entity-id").val(selected.data('entity-id'));
        $("#method-id").val(selected.data('method-id'));
        $("#notes").val(selected.data('notes'));
        $('form#add-transaction.add-form select#sub-type-id').change();
        $('form#add-transaction.add-form #amount').focus();
        $('form#add-transaction.add-form #create-prefill').prop("checked", false);
        $('form#add-transaction.add-form #create-prefill').prop("disabled", true);

        if (selected.val() === "clear") {
            //$("option:eq(0)").prop("selected", true);
            $("option")[0].selected = true
            $('form#add-transaction.add-form #amount').blur();
            $('form#add-transaction.add-form #create-prefill').prop("disabled", false);

            //$("option").val('Pre-fill...');
        }
        
    });


    $('form#add-tax-year input#start-year.form-control').on('change', function() {
        startYear = parseInt($('form#add-tax-year input#start-year.form-control').val())
        $('form#add-tax-year input#end-year.form-control').val(startYear + 1)
        $('form#add-tax-year input#tax-year.form-control').val(startYear + '/' + (startYear + 1).toString().slice(-2))
        $('form#add-tax-year input#tax-year-start.form-control').val(startYear + '-04-06')
        $('form#add-tax-year input#tax-year-end.form-control').val(startYear + 1 + '-04-05')
    });

   




    

});
