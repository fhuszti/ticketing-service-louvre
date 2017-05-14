$(function() {
	/**
	 * COMMON FUNCTIONS
	 * ----------------
	 */

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

    function convertDatePhpToFrench(date) {
        var parts = date.split('-');

        return parts[2]+'/'+parts[1]+'/'+parts[0];
    }

    function convertDateFrenchToPhp(date) {
        var parts = date.split('/');

        return parts[2]+'-'+parts[1]+'-'+parts[0];
    }

	/**
	 * ----------------
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
            formControlDivs = $('div[id^="ots_billingbundle_ticketorder_tickets_"] div:not([class*="alert alert-danger"])'),
            reducedPriceDivs = $('div[id^="ots_billingbundle_ticketorder_tickets_"] div:nth-child(5)');
            
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

    function managePriceOnDateChange(dateText, currentIndex, specialRate) {
        var price = getPriceFromDate(dateText),
            priceSpan = $('#price_'+currentIndex),
            priceElmt = $('#ots_billingbundle_ticketorder_tickets_'+currentIndex+'_price'),
            ticketTypeField = $('#ots_billingbundle_ticketorder_type'),
            discountedInput = $('#ots_billingbundle_ticketorder_tickets_'+currentIndex+'_discounted');

        if (discountedInput.prop('checked'))
            price = 10;
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

    function checkOnDateChange(dateText, datepickerInst, dateFieldElement = '' ) {
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
        var dateInputs = $("input[name$='[birthDate]']:not([id$='_php_birthDate'])"),
            discountInputs = $("input[name$='[discounted]']");
        
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
                    checkOnDateChange( $(this).val(), '', $(this) );
                });

                formatDate( $(dateInputs[i]), i );

                $(dateInputs[i]).attr('name', '');
            }
        }
    }





    generateTicketForms();
    setupDatepickerStep2();

    managePriceSpecialRate();
});