function CreateFilterDropdowns(api) {
    console.log(api)
    api.every(function () {
        var column = this; 
        var select = $('<select id="filter-col-' + column.index() +'" class="filter-dropdown"><option value="">Show all</option></select>')
        .appendTo($(column.footer())).on('change', function () {
            //console.log('Value: ' + $(this).val())
            //console.log('Index: ' + $(this)[0].selectedIndex)
            //console.log('Text:' + $("option:selected", this).text())
            if ($("option:selected", this).text() != 'Show all') {
                if ($("option:selected", this).text() != '') {
                // Specific, non-blank value e.g. ATM, Balance Adjustment etc.
                    column.search($(this).val(),
                        {
                            regex: false,
                            exact: true,
                            smart: true
                        }).draw();
                } else {
                // Blanks
                    column.search(new RegExp(/^$/)).draw(); // See https://datatables.net/forums/discussion/80913/search-using-a-regexp-object#Comment_239799
                    /*
                    column.search('^$', 
                        {
                            regex: true,
                            exact: false,   // The default is `false`, but if previously set to `true`, search() will treat '^$' as a literal string and not a regular expression
                            smart: false
                        }
                    ).draw();
                    */
                }
            } else {
            // 'Show all'
                column.search('').draw()    
            }
        
        }); // on('change', ...)
                    
        $( select ).on('click', function(e) {
            e.stopPropagation();
        }); // on('click', ...)

        column.data().unique().sort().each(function (d, j, c) {
            select.append('<option value="' + d + '">' + d + '</option>')
        }); // each

        /**
         * When a column filter is applied - say Entity = BT - and stateSave is true, if the page is reloaded the filter is still in effect, but the filter dropdown now displays 'Show all' and not 'BT'.
         * To overcome this, the correct dropdown filter value is restored from the saved 'state' object. See https://stackoverflow.com/a/49878256
         * See https://datatables.net/reference/api/state() for the structure of the 'state' object 
         */
        var state = this.state.loaded();
        if (state) {
            var val = state.columns[this.index()];
            select.val(val.search.search);
        }

    }); // every
}

function CreateFilterDropdownsIntegerSort(api) {
    console.log(api)
    api.every(function () {
        var column = this;                
        var select = $('<select id="filter-col-' + column.index() +'" class="filter-dropdown"><option value="">Show all</option></select>')
        .appendTo($(column.footer())).on('change', function () {
            column.search($(this).val(), {exact: true}).draw();
        });

        $( select ).click( function(e) {
            e.stopPropagation();
        });

        /**
         * For some columns containing numeric data, the options in the select dropdown are sorted as strings 1, 10, 11, 12, 2, 20 etc.
         * The arrow function '(a, b) => a - b' passed to sort() enures they are sorted as numbers - 1, 2, 10, 11, 12, 20 etc
         */
        column.data().unique().sort(function (a, b) {
            return a - b          // See https://stackoverflow.com/a/68980030/2518495 re sorting on integers
        }).each(function (d, j) {
            select.append(
                '<option value="' + d + '">' + d + '</option>'
            );
        });

        /**
         * When a column filter is applied - say Entity = BT - and stateSave is true, if the page is reloaded the filter is still in effect, but the filter dropdown now displays 'Show all' and not 'BT'.
         * To overcome this, the correct dropdown filter value is restored from the saved 'state' object. See https://stackoverflow.com/a/49878256
         * See https://datatables.net/reference/api/state() for the structure of the 'state' object 
         */
        var state = this.state.loaded();
        if (state) {
            var val = state.columns[this.index()];
            select.val(val.search.search);
        }
    }); //every
}


$('#clear-filters').on('click', (e) => {

    tableID = $(document).find('table.bu-data-table').attr('id')
    
    $('.filter-dropdown').each(function( index ) {
        //console.log( index + ": " + $( this ).text() );
        $(this).prop("selectedIndex", 0).val();
        $(this).removeClass('filter-applied')
      });
      //$( this ).text('Show all')

    //$('#clear-filters').prop("disabled", true);
    $('#clear-filters').addClass('d-none');
      
    $('#' + tableID).DataTable().search('').columns().search('').draw()
    //CreateFilterDropdowns ($('#transactions').DataTable().columns(['account_id_alpha:name','account_details:name','entity:name','type:name','sub_type:name','method:name']))
    //CreateFilterDropdownsIntegerSort ($('#transactions').DataTable().columns(['amount:name','tax_year:name','period:name']))
})

//$(function(){
    $(document).on('change','.filter-dropdown', function() {     // A non-direct, delegated event handler. See https://stackoverflow.com/a/16512067/2518495 and https://api.jquery.com/on/
        console.log($(this).parent('th').index())
        idx = $(this).parent('th').index()
        if ($(this)[0].selectedIndex != 0) {
            $(this).addClass('filter-applied')
            ///$(this).closest('table').find('tr.titles th:nth-child(' + (idx + 1) + ')').addClass('filter-applied')
        } else {
            $(this).removeClass('filter-applied')
            //$(this).closest('table').find('tr.titles th:nth-child(' + (idx + 1) + ')').removeClass('filter-applied')
        }
        $('#clear-filters').addClass('d-none');
        //$('#clear-filters').prop("disabled", true);
        $('.filter-dropdown').each(function( index ) {
            if ($(this)[0].selectedIndex != 0) {
                //$('#clear-filters').prop("disabled", false);
                $('#clear-filters').removeClass('d-none');
                return false    // Exit each(). See https://stackoverflow.com/a/1799290/2518495
            }
        });
    });
//});


