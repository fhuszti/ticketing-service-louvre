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

    //disable some dates in datepicker
    function disableDates(date) {
        var noTuesday = date.getDay() != 2,
            noSunday = date.getDay() != 0,
            disabledDates = ['01-05', '01-11', '25-12'],
            stringDate = $.datepicker.formatDate('dd-mm', date);

        return [noTuesday && noSunday && disabledDates.indexOf(stringDate) == -1, ''];
    }

    //initial setup datepicker
    function setupDatepicker() {
        $("#ots_billingbundle_ticketorder_date").datepicker({
            altField: '#ots_billingbundle_ticketorder_php_date',
            altFormat: "yy-mm-dd",
            minDate: 0,
            beforeShowDay: disableDates,
            dateFormat: 'dd/mm/yy'
        });

        $("#ots_billingbundle_ticketorder_date").attr({
            name: ''
        });
    }

    setupDatepicker();

    //generate ticket forms at step 2 only
    if ($('#step_checker').text() === '2') {
        generateTicketForms();
    }
});