/***
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



// Remove the formatting to get integer data for summation
var intVal = function (i) {
    //console.log(i);
    return typeof i === 'string'
        //? i.replace(/[\$,]/g, '') * 1
        ? i.replace(/[^0-9.-]+/g, '') * 1
        : typeof i === 'number'
        ? i
        : 0;
};




function getUrlVars()
{
    console.log('getUrlVar')

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

function chronology (tDate) {
    let todaysDate = new Date();
    todaysDate.setHours(0,0,0,0);
    todaysDate = todaysDate.getTime();

    //console.log(todaysDate);
    //console.log(tDate)

    //let transactionDate = new Date(tDate.substr(10), parseFloat(tDate.substr(7,2)) - 1, tDate.substr(4,2));
    let transactionDate = new Date(tDate);
    //console.log(transactionDate);
    transactionDate = transactionDate.getTime();
    //console.log(transactionDate);

    if (transactionDate > todaysDate) { 
        return 'future'; 
    } else if (transactionDate === todaysDate) { 
        return 'today'; 
    } else if (transactionDate < todaysDate) { 
        return 'past';
    }

}

function excludedDates(date, excludedDays){           //  See https://stackoverflow.com/a/3354421/2518495
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
            '2024-01-01', '2024-03-29', '2024-04-01', '2024-05-06', '2024-5-27', '2024-8-26', '2024-12-25', '2024-12-26', 
        // 2025
            '2025-01-01', '2025-4-18', '2025-4-21', '2025-05-05', '2025-5-26', '2025-8-25', '2025-12-25', '2025-12-26',
        // 2026
            '2026-01-01', '2026-04-03', '2026-04-06', '2026-05-04', '2026-5-25', '2026-8-31', '2026-12-25', '2026-12-28',
        // 2027
            '2027-01-01', '2027-3-26', '2027-3-29', '2027-05-03', '2027-5-31', '2027-8-30', '2027-12-27', '2027-12-28',
        // 2028
            '2028-01-03', '2028-4-14', '2028-4-17', '2028-05-01', '2028-5-29', '2028-8-28', '2028-12-25', '2028-12-26',
        // 2029
            '2029-01-01', '2029-3-30', '2029-04-02', '2029-05-07', '2029-5-28', '2029-8-27', '2029-12-25', '2029-12-26',
        // 2030
            '2030-01-01', '2030-4-19', '2030-4-22', '2030-05-06', '2030-5-27', '2030-8-26', '2030-12-25', '2030-12-26',
        ];

    //var closedDays = [[Friday], [Saturday]];
    for (var i = 0; i < excludedDays.length; i++) {
        if (day == excludedDays[i][0]) {
            return [false];
        }
    }

    for (i = 0; i < publicHolidays.length; i++) {
        if (
            //date.getTime() === new Date(publicHolidays[i][0] + '-' + publicHolidays[i][1] + '-' + publicHolidays[i][2]).getTime()
            date.getTime() === new Date(publicHolidays[i]).getTime()
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
    console.log('$(document).ready() Fired')

    $("table.bu-data-table").on("click", ".has-note", function(event) {
        direction = ' to '
        if (intVal($(this).data("amount")) >= 0) {
            direction = ' from '
        }
        Swal.fire(
            {
                //title: "Note",
                html: '<div class="text-left">[<span class="text-grey">' + $(this).data("counter") + '</span>]<br /><br /><span class="text-grey">' + $(this).data("amount") + '</span>' + direction + '<span class="text-grey">' + $(this).data("entity-name") + '</span> on <span class="text-grey">' + $(this).data("date") + '</span><br /><br /><i>' + $(this).data("note") + '</></div>',
                icon: "info",
                position: "top-end"
            }
        );
    })

    $(".prefill-supermarket").on("click", function(event) {
        $("#account-id-alpha option[value='G']").attr("selected", true);
        //$("#account-id-alpha").val('G');    // Santander Edge Up Current Account - XXX9151 [G]
        $("#type").val('5');                // Supermarket
        $("#entity-id").val('P1280');        // Sainsbury's

    })


    $('#prefill').on('change', function() {
        var selected = $(this).find('option:selected');     // See https://stackoverflow.com/a/4564711
        console.log(selected.val())
        //var extra = selected.data('foo'); 
        console.log(selected.data('entity-id') );
        //$("#account-id-alpha option[value='G']").attr("selected", true);
        $("#account-id-alpha").val(selected.data('account-id-alpha'));
        $("#type").val(selected.data('type'));
        $("#entity-id").val(selected.data('entity-id'));

        if (selected.val() === "clear") {
            //$("option:eq(0)").prop("selected", true);
            $("option")[0].selected = true

            //$("option").val('Pre-fill...');
        }
        
        /*
        switch(this.value) {
            case 'co-op':
                // code block
                $("#type").val(selected.data('type'));
                $("#entity-id").val(selected.data('entity'));    // Co-op
                break;
            case 'dunelm':
                // code block
                $("#type").val('6');
                $("#entity-id").val('P6038');    // Dunelm
                break;
            case 'national-lottery':
                $("#type").val('6');
                // code block
                $("#entity-id").val('P0700');    // National Lottery
                break;
            case 'sainsburys':
                $("#type").val('5');
                $("#entity-id").val('P1280');    // Sainsbury's
                break;
            case 'tesco-express':
                $("#type").val('5');
                $("#entity-id").val('P0186');    // Tesco Express
                break;
            default:
                // code block
          }
                */
      });
});
