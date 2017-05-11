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

    //check if a given date is valid
    function isDateValid(date) {
        var noTuesday = date.getDay() != 2,
            noSunday = date.getDay() != 0,
            disabledDates = ['01-05', '01-11', '25-12'],
            stringDate = $.datepicker.formatDate('dd-mm', date);

        return noTuesday && noSunday && disabledDates.indexOf(stringDate) == -1;
    }

    //disable some dates in datepicker
    function disableDates(date) {
        return [isDateValid(date), ''];
    }

    //get the default date if today isn't enabled
    function getDefaultDate() {
        var date = new Date();
        
        while ( !isDateValid(date) ) {
            date.setDate( date.getDate() + 1 );
        }
        
        return date;
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
            defaultDate: getDefaultDate(),
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

    //apply a design to the form generated via php if step 2 has already been validated
    function designTicketForm(collectionHolder) {
        var containerId = collectionHolder.attr('id'),
            index = collectionHolder.data('index'),
            indexUp = index + 1,
            rowDiv = $( '#'+containerId+'>div:nth-of-type('+indexUp+')' );

        rowDiv.addClass('row');
        rowDiv.children().wrapAll('<div />');
        rowDiv.children('div').children('label').text( 'Ticket n°'+(indexUp) );
        rowDiv.append( $('<div class="col-xs-12 col-sm-6 col-sm-pull-6"><h3>Price : <span id="price_'+index+'">-</span></h3></div>') );

        rowDiv.after( $('<hr />') );

        //increment the index for next time
        collectionHolder.data('index', indexUp);
    }

    //add necessary classes to a few elements of the generated forms
    function addBootstrapClasses(container) {
        var containerId = container.attr('id'),
            formControlDivs = $('div[id^="ots_billingbundle_ticketorder_tickets_"] div'),
            reducedPriceDivs = $('div[id^="ots_billingbundle_ticketorder_tickets_"] div:nth-last-child(2)');
            
        formControlDivs.addClass('col-xs-12 col-sm-6');
        reducedPriceDivs.addClass('col-sm-push-6');
    }

    //dynamically creates ticket forms on first time at step 2
    function generateTicketForms() {
        var i, j,
            collectionHolder = $('#ots_billingbundle_ticketorder_tickets'),
            nbTickets = $('#ots_billingbundle_ticketorder_nbTickets').val();
        
        collectionHolder.data('index', 0);

        if ( $('#ots_billingbundle_ticketorder_tickets_0').length <= 0 ) {
            for(i = 0; i < nbTickets; i++) {
                addTicketForm(collectionHolder);
            }
        }
        else {
            for(j = 0; j < nbTickets; j++) {
                designTicketForm(collectionHolder);
            }
        }
        
        addBootstrapClasses(collectionHolder);
    }

    /**
     * ------------------------------
     */
    
    /**
     * STEP 2 DATE AND PRICE MANAGEMENT
     * --------------------------------
     */

    function convertDateFrenchToPhp(date) {
        var parts = date.split('/');

        return parts[2]+'-'+parts[1]+'-'+parts[0];
    }

    function formatDate(dateElmt, index) {
        var dateString = dateElmt.val();

        if (dateString.split('-').length > 1) {
            var convertedDate = convertDatePhpToFrench(dateString),
                phpDateElmt = $('#ots_billingbundle_ticketorder_tickets_'+index+'_php_birthDate');

            phpDateElmt.val(dateString);
            checkOnDateChange(dateString, '', dateElmt);
            dateElmt.val(convertedDate);
        }
    }

    //returns an array of dates used to calculate prices dynamically (12yo date threshold, senior date threshold...)
    function getUsefulDates(birthdayDate) {
        var currentDateString = getTodayDate(),
            currentDate = new Date(currentDateString),
            birthdateString = convertDateFrenchToPhp(birthdayDate),
            birthdate = new Date(birthdateString),
            normalRateDate = new Date(currentDate.getFullYear() - 12, currentDate.getMonth(), currentDate.getDate() < 10 ? '0'+currentDate.getDate() : currentDate.getDate()),
            childRateDate = new Date(currentDate.getFullYear() - 4, currentDate.getMonth(), currentDate.getDate() < 10 ? '0'+currentDate.getDate() : currentDate.getDate()),
            seniorRateDate = new Date(currentDate.getFullYear() - 60, currentDate.getMonth(), currentDate.getDate() < 10 ? '0'+currentDate.getDate() : currentDate.getDate());
        
        //Don't know why but birthdate would be created with hours = 2 for some reason
        birthdate.setHours(0);

        var dates = [];
        dates['current'] = currentDate;
        dates['birthday'] = birthdate;
        dates['normalRate'] = normalRateDate;
        dates['childRate'] = childRateDate;
        dates['seniorRate'] = seniorRateDate;

        return dates;
    }

    function getPriceFromDate(birthdayDate) {
        var dates = getUsefulDates(birthdayDate);
        
        //if below 4 years old
        if (dates.birthday > dates.childRate) {
            return 0;
        }
        //if between 4 and 12 years old
        else if (dates.birthday > dates.normalRate) {
            return 8;
        }
        //if between 12 and 60 years old
        else if (dates.birthday > dates.seniorRate) {
            return 16;
        }
        //more than 60 years old
        else {
            return 12;
        }
     }

    function managePriceOnDateChange(dateText, currentIndex) {
        var price = getPriceFromDate(dateText),
            priceSpan = $('#price_'+currentIndex),
            priceElmt = $('#ots_billingbundle_ticketorder_tickets_'+currentIndex+'_price'),
            ticketTypeField = $('#ots_billingbundle_ticketorder_type');

        //divide price by 2 if ticket type chosen is half-day
        if (ticketTypeField.val() === '')
            price = price * 0.5;
        
        priceSpan.text(price+'€');
        priceElmt.val(price);

        updateTotalPrice();
     }

     function manageSpecialRateCheckboxOnDateChange(birthdayDate, currentIndex) {
        var dates = getUsefulDates(birthdayDate),
            checkbox = $('#ots_billingbundle_ticketorder_tickets_'+currentIndex+'_discounted');

        //if below 12 years old
        if (dates.birthday > dates.normalRate) {
            //disable the 'Special Rate' option
            checkbox.prop('checked', false);
            checkbox.prop('disabled', true);
        }
        else {
            //enable the 'Special Rate' option
            checkbox.prop('disabled', false);
        }
     }

    function checkOnDateChange(dateText, datepickerInst, dateFieldElement = '') {
        var splitFieldId = dateFieldElement === '' ? $(this).attr('id').split('_') : dateFieldElement.attr('id').split('_'),
            currentIndex = splitFieldId[splitFieldId.length - 2];

        managePriceOnDateChange(dateText, currentIndex);

        manageSpecialRateCheckboxOnDateChange(dateText, currentIndex);
    }

    function managePriceOnSpecialRateChange(specialRate, checkboxElement) {
        var splitFieldId = checkboxElement.attr('id').split('_'),
            currentIteration = splitFieldId[splitFieldId.length - 2],
            dateText = $('#ots_billingbundle_ticketorder_tickets_'+currentIteration+'_birthDate').val(),
            price = specialRate ? 10 : (dateText === '' ? 0 : getPriceFromDate(dateText)),
            priceSpan = $('#price_'+currentIteration),
            priceElmt = $('#ots_billingbundle_ticketorder_tickets_'+currentIteration+'_price'),
            ticketTypeField = $('#ots_billingbundle_ticketorder_type');

        //divide price by 2 if ticket type chosen is half-day
        if (ticketTypeField.val() === '')
            price = price * 0.5;
        
        priceSpan.text(price+'€');
        priceElmt.val(price);

        updateTotalPrice();
    }

    function managePriceSpecialRate() {
        var i,
            specialRateCheckboxes = $("input[name$='[discounted]']");

        //display reduced price when user is coming back to step 2 after checking a box previously
        for (i = 0; i < specialRateCheckboxes.length; i++) {
            if (specialRateCheckboxes[i] && specialRateCheckboxes[i].checked)
                managePriceOnSpecialRateChange( true, $(specialRateCheckboxes[i]) );
        }

        //click event instead of change because might be manually unchecked by javascript too
        specialRateCheckboxes.on('click', function() {
            var checkboxElement = $(this);

            if (this.checked) {
                managePriceOnSpecialRateChange(true, checkboxElement);
            }
            else {
                managePriceOnSpecialRateChange(false, checkboxElement);
            }
        });
    }

    function updateTotalPrice() {
        var priceSpans = $('span[id^="price_"]'),
            totalPriceSpan = $('#total_price'),
            totalPrice = 0,
            cleanPrice;

        for (var i = 0; i < priceSpans.length; i++) {
            if (priceSpans[i]) {
                //the price is either 0 is ticket price is default, or it's text() with no € sign at the end
                cleanPrice = $(priceSpans[i]).text() === '-' ? 0 : parseInt($(priceSpans[i]).text().slice(0, -1));

                totalPrice += cleanPrice;
            }
        }

        totalPriceSpan.text(totalPrice+'€');
        $('#ots_billingbundle_ticketorder_price').val(totalPrice);
    }

    /**
     * --------------------------------
     */
    
    /**
     * DATEPICKER SETUP FOR STEP 2
     * ---------------------------
     */

    function setupDatepickerStep2() {
        var dateInputs = $("input[name$='[birthDate]']:not([id$='_php_birthDate'])");
        
        for (var i = 0; i < dateInputs.length; i++) {
            if (dateInputs[i]) {
                $(dateInputs[i]).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    yearRange: "-120:+0",
                    dateFormat: "dd/mm/yy",
                    altField: '#ots_billingbundle_ticketorder_tickets_'+i+'_php_birthDate',
                    altFormat: "yy-mm-dd",
                    onSelect: checkOnDateChange,
                    maxDate: 0
                });

                //so date changes are checked even when done manually
                $(dateInputs[i]).on('change', function() {
                    checkOnDateChange($(this).val(), '', $(this));
                });

                formatDate( $(dateInputs[i]), i );

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
    






    /**
     * STEP 3
     * ----------------------
     * ----------------------
     */

     /**
     * FILL RECAP VALUES FOR STEP 3
     * ----------------------------
     */

    function convertDatePhpToFrench(date) {
        var parts = date.split('-');

        return parts[2]+'/'+parts[1]+'/'+parts[0];
     }

    function fillRecap() {
        var date = convertDatePhpToFrench( $('#ots_billingbundle_ticketorder_date').val() );
        $('#recap_date').text(date);
        
        var type = $('#ots_billingbundle_ticketorder_type').val() === '1' ? "Full-day" : "Half-day";
        $('#recap_type').text(type);
        
        $('#recap_nbTickets').text( $('#ots_billingbundle_ticketorder_nbTickets').val() );
        
        $('#recap_price').text( $('#ots_billingbundle_ticketorder_price').val()+'€' );
     }

    /**
     * ----------------------------
     */
    
    /**
     * ----------------------
     * ----------------------
     */
    




     var stepInput = $('#ots_billingbundle_ticketorder_flow_ticketOrder_step');

    //Everything needed for step 1
    if (stepInput.val() === '1') {
        setupDatepickerStep1();
        checkDate($('#ots_billingbundle_ticketorder_date').val());

        calculator();
    }
    //generate ticket forms at step 2 only
    else if (stepInput.val() === '2') {
        generateTicketForms();
        setupDatepickerStep2();

        managePriceSpecialRate();
    }
    //step 3 only
    else if (stepInput.val() === '3') {
        fillRecap();
    }
});