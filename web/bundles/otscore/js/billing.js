$(function() {
    function disableDates(date) {
        var noTuesday = date.getDay() != 2,
            noSunday = date.getDay() != 0,
            disabledDates = ['01-05', '01-11', '25-12'],
            stringDate = $.datepicker.formatDate('dd-mm', date);

        return [noTuesday && noSunday && disabledDates.indexOf(stringDate) == -1, ''];
    }

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
});