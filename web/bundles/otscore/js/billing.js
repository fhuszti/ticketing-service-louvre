$(function() {
    //create one ticket form
    function addTicketForm(collectionHolder) {
        //get data-prototype
        var prototype = collectionHolder.data('prototype');
        //get current index
        var index = collectionHolder.data('index');

        //replace __name__ variables in the prototype by current index
        var newForm = prototype.replace(/__name__label__/g, 'Ticket n°'+(index+1))
                               .replace(/__name__/g, index);

        //increment the index for next time
        collectionHolder.data('index', index + 1);

        collectionHolder.append($('<div></div>').append(newForm));
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
    }

    function formatDateIsoToShort(date) {
        var dateParts = date.split('-');

        return dateParts[1]+'/'+dateParts[2]+'/'+dateParts[0];
    }

    //disable some dates in datepicker
    function disableDates(date) {
        var noTuesday = date.getDay() != 2,
            noSunday = date.getDay() != 0,
            disabledDates = ['01-05', '01-11', '25-12'],
            stringDate = $.datepicker.formatDate('dd-mm', date);

        return [noTuesday && noSunday && disabledDates.indexOf(stringDate) == -1, ''];
    }

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

    //initial setup datepicker
    function setupDatepicker() {
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

    setupDatepicker();
    checkDate($('#ots_billingbundle_ticketorder_date').val());

    //generate ticket forms at step 2 only
    if ($('#ots_billingbundle_ticketorder_flow_ticketOrder_step').val() === '2') {
        generateTicketForms();
    }
});