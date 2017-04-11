$(function() {
    function disableDates(date) {
        var noTuesday = date.getDay() != 2,
            disabledDates = ['01-05', '01-11', '25-12'],
            stringDate = $.datepicker.formatDate('dd-mm', date);

        return [noTuesday && disabledDates.indexOf(stringDate) == -1, ''];
    }

    function setupDatepicker() {
        $("#orderForm_date").datepicker({
            altField: '#orderForm_php_date',
            altFormat: "yy-mm-dd",
            minDate: 0,
            beforeShowDay: disableDates
        });

        $("#orderForm_date").attr({
            name: ''
        });
    }

    setupDatepicker();
});