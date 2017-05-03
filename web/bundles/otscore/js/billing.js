$(function() {
    /**
     * STEP 1
     * ----------------------
     * ----------------------
     */
    

    /**
     * DATEPICKER DATES DISABLING IN STEP 1
     * ------------------------------------
     */

    //disable some dates in datepicker
    function disableDates(date) {
        var noTuesday = date.getDay() != 2,
            noSunday = date.getDay() != 0,
            disabledDates = ['01-05', '01-11', '25-12'],
            stringDate = $.datepicker.formatDate('dd-mm', date);

        return [noTuesday && noSunday && disabledDates.indexOf(stringDate) == -1, ''];
    }

    /**
     * ------------------------------------
     */
    


    /**
     * FULL-DAY RADIO OPTION MANAGEMENT IN STEP 1
     * ------------------------------------------
     */

    //disable the full day radio option
    function disableFullDayRadio() {
        //disable the radio option
        $('#ots_billingbundle_ticketorder_type_1').attr('disabled', true);

        //if it's already selected, switch to the other option
        if ($('#ots_billingbundle_ticketorder_type_1').is(':checked')) {
            $('#ots_billingbundle_ticketorder_type_1').prop('checked', false);
            $('#ots_billingbundle_ticketorder_type_0').prop('checked', true);
        }
    }

    //enable the full day radio option
    function enableFullDayRadio() {
        $('#ots_billingbundle_ticketorder_type_1').attr('disabled', false);
    }

    //get the current Paris time
    function getCurrentParisTime() {
        var loc = '48.860618, 2.338170', // Paris expressed as lat,lng tuple
            targetDate = new Date(), // Current date/time of user computer
            timestamp = targetDate.getTime()/1000 + targetDate.getTimezoneOffset() * 60, // Current UTC date/time expressed as seconds since midnight, January 1, 1970 UTC
            apikey = 'AIzaSyDKHyrzlT9M8otV06G4pQOOK_0NgF1UKGQ';
         
        var apicall = 'https://maps.googleapis.com/maps/api/timezone/json?location='+loc+'&timestamp='+timestamp+'&key='+apikey;

        $.getJSON(apicall, function(output) {
            if (output.status == 'OK'){ // if API reports everything was returned successfully
                var offsets = output.dstOffset * 1000 + output.rawOffset * 1000, // get DST and time zone offsets in milliseconds
                    localdate = new Date(timestamp * 1000 + offsets); // Date object containing current time of Tokyo (timestamp + dstOffset + rawOffset)
                
                if (localdate.getHours() >= 14) {
                    disableFullDayRadio();
                }
                else {
                    enableFullDayRadio();
                }
            }
        });
    }

    //get the current date in the ISO format
    function getTodayDate() {
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth() + 1; //January is 0!
        var yy = today.getFullYear();

        if (dd < 10) {
            dd = '0'+dd;
        } 

        if (mm < 10) {
            mm = '0'+mm;
        } 

        return yy+'-'+mm+'-'+dd;
    }

    //check if selected date is today and if it's after 2pm
    //if it is, disable "Full-day" ticket type option
    function checkDate(dateText) {
        if (dateText === getTodayDate()) {
            getCurrentParisTime();
        }
        else {
            enableFullDayRadio();
        }
    }

    /**
     * ------------------------------------------
     */
    


    /**
     * DATEPICKER SETUP FOR STEP 1
     * ---------------------------
     */

    //initial setup datepicker
    function setupDatepickerStep1() {
        var inputDate = $('#ots_billingbundle_ticketorder_date').val();

        $("#order_datepicker").datepicker({
            altField: '#ots_billingbundle_ticketorder_date',
            altFormat: "yy-mm-dd",
            minDate: 0,
            beforeShowDay: disableDates,
            onSelect: checkDate,
            defaultDate: inputDate ? inputDate : null,
            dateFormat: 'yy-mm-dd'
        });
        
        //to still have the chosen date selected when user comes back to step 1 from later in the flow
        if (inputDate) {
            $("#order_datepicker").datepicker('setDate', new Date(inputDate));
        }
    }

    /**
     * ---------------------------
     */
    


    /**
     * CALCULATOR
     * ----------
     */

    function calculator() {
        var numbers = $('.calc_number'),
            del = $('.calc_del'),
            input = $('#ots_billingbundle_ticketorder_nbTickets');

        numbers.on('click', function() {
            var newValue = input.val() + $(this).text();
            input.val(newValue);
        });
        del.on('click', function() {
            var newValue = input.val().slice(0, -1);
            input.val(newValue);
        });
    }

    /**
     * ----------
     */
    
    /**
     * ----------------------
     * ----------------------
     */

    

    



    /**
     * STEP 2
     * ----------------------
     * ----------------------
     */


    /**
     * STEP 2 TICKET FORMS MANAGEMENT
     * ------------------------------
     */

    //create one ticket form
    function addTicketForm(collectionHolder) {
        //get data-prototype
        var prototype = collectionHolder.data('prototype');
        //get current index
        var index = collectionHolder.data('index');

        //replace necessary parts in the prototype
        var newForm = prototype.replace(/__name__label__/g, 'Ticket n°'+(index+1))
                               .replace(/__name__/g, index);

        //increment the index for next time
        collectionHolder.data('index', index + 1);

        collectionHolder.append($('<div class="row"></div>')
                            .append(newForm)
                            .append($('<div class="col-xs-12 col-sm-6 col-sm-pull-6"><h3>Price : <span id="price_'+index+'">-</span></h3></div>'))
                        )
                        .append($('<hr />'));
    }

    //add necessary classes to a few elements of the generated forms
    function addBootstrapClasses(container) {
        var containerId = container.attr('id'),
            formControlDivs = $('div[id^="ots_billingbundle_ticketorder_tickets_"] div'),
            reducedPriceDivs = $('div[id^="ots_billingbundle_ticketorder_tickets_"] div:last-child');
            
        formControlDivs.addClass('col-xs-12 col-sm-6');
        reducedPriceDivs.addClass('col-sm-push-6');
    }

    //dynamically creates ticket forms on number of tickets change
    function generateTicketForms() {
        var i,
            collectionHolder = $('#ots_billingbundle_ticketorder_tickets'),
            nbTickets = $('#ots_billingbundle_ticketorder_nbTickets').val();
        
        collectionHolder.data('index', 0);

        for(i = 0; i < nbTickets; i++) {
            addTicketForm(collectionHolder);
        }

        addBootstrapClasses(collectionHolder);
    }

    /**
     * ------------------------------
     */
    
    /**
     * DATEPICKER SETUP FOR STEP 2
     * ---------------------------
     */

    function setupDatepickerStep2() {
        var dateInputs = $("input[name$='[birthDate]']");
        
        for (var i = 0; i < dateInputs.length; i++) {
            if (dateInputs[i]) {
                $(dateInputs[i]).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    yearRange: "-120:+0",
                    dateFormat: "dd/mm/yy",
                    altField: '#ots_billingbundle_ticketorder_tickets_'+i+'_php_birthDate',
                    altFormat: "yy-mm-dd"
                });

                $(dateInputs[i]).attr('name', '');
            }
        }
    }

    /**
     * ---------------------------
     */
    
    /**
     * ----------------------
     * ----------------------
     */




    //Everything needed for step 1
    if ($('#ots_billingbundle_ticketorder_flow_ticketOrder_step').val() === '1') {
        setupDatepickerStep1();
        checkDate($('#ots_billingbundle_ticketorder_date').val());

        calculator();
    }
    //generate ticket forms at step 2 only
    else if ($('#ots_billingbundle_ticketorder_flow_ticketOrder_step').val() === '2') {
        generateTicketForms();
        setupDatepickerStep2();
    }
});