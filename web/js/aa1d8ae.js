$(function() {
	/**
	 * COMMON FUNCTIONS
	 * ----------------
	 */

	function initLocale(){
        if(global.locale){
            locale = global.locale;
        }
        else{
            //Set a default locale if the user's one is not managed
            locale = "en";
        }

        return locale;
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
	 * TRANSLATIONS
	 * ------------
	 */
	
	function addTranslations() {
		Translator.add(
		    'ots_billing.step2.ticket.label',
		    'Ticket n°%index%',
		    'messages',
		    'en'
		);
		Translator.add(
		    'ots_billing.step2.ticket.label',
		    'Billet n°%index%',
		    'messages',
		    'fr'
		);

		Translator.add(
		    'ots_billing.step2.price.label',
		    'Price:',
		    'messages',
		    'en'
		);
		Translator.add(
		    'ots_billing.step2.price.label',
		    'Prix :',
		    'messages',
		    'fr'
		);
	}

	/**
	 * ------------
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
        var index = collectionHolder.data('index'),
        	indexUp = index + 1;

        //replace necessary parts in the prototype
        var newForm = prototype.replace(/__name__label__/g, Translator.trans('ots_billing.step2.ticket.label', { "index": indexUp }))
                               .replace(/__name__/g, index);

        //increment the index for next time
        collectionHolder.data('index', indexUp);

        collectionHolder.append($('<div class="row"></div>')
                            .append(newForm)
                            .append($('<div class="col-xs-12 col-sm-6 col-sm-pull-6"><h3>'+Translator.trans('ots_billing.step2.price.label')+' <span id="price_'+index+'">-</span></h3></div>'))
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
        rowDiv.children('div').children('label').text( Translator.trans('ots_billing.step2.ticket.label', { "index": indexUp }) );
        rowDiv.append( $('<div class="col-xs-12 col-sm-6 col-sm-pull-6"><h3>'+Translator.trans('ots_billing.step2.price.label')+' <span id="price_'+index+'">-</span></h3></div>') );

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
                //define fr locale if needed
                if( initLocale() == 'fr' || initLocale() == 'fr_FR' ) {
                    //setup datepicker in french
                    $.datepicker.regional['fr'] = {
                        closeText: 'Fermer',
                        prevText: 'Précédent',
                        nextText: 'Suivant',
                        currentText: 'Aujourd\'hui',
                        monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
                        monthNamesShort: ['Janv.','Févr.','Mars','Avril','Mai','Juin','Juil.','Août','Sept.','Oct.','Nov.','Déc.'],
                        dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
                        dayNamesShort: ['Dim.','Lun.','Mar.','Mer.','Jeu.','Ven.','Sam.'],
                        dayNamesMin: ['D','L','M','M','J','V','S'],
                        weekHeader: 'Sem.',
                        firstDay: 1,
                        isRTL: false,
                        showMonthAfterYear: false,
                        yearSuffix: ''
                    };

                    $.datepicker.setDefaults( $.datepicker.regional['fr'] );
                }

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





    addTranslations();

    generateTicketForms();
    setupDatepickerStep2();

    managePriceSpecialRate();
});
/**
 * @author William DURAND <william.durand1@gmail.com>
 * @license MIT Licensed
 */
!function(e,a){"function"==typeof define&&define.amd?define("Translator",a):"object"==typeof module&&module.exports?module.exports=a():e.Translator=a()}(this,function(){"use strict";function e(e,a){var s,n=p.placeHolderPrefix,t=p.placeHolderSuffix;for(s in a){var c=new RegExp(n+s+t,"g");c.test(e)&&(e=e.replace(c,a[s]))}return e}function a(e,a,n,t,c){var r=n||t||c,i=a,u=r.split("_")[0];if(!(r in l))if(u in l)r=u;else{if(!(c in l))return e;r=c}if("undefined"==typeof i||null===i)for(var o=0;o<f.length;o++)if(s(r,f[o],e)||s(u,f[o],e)||s(c,f[o],e)){i=f[o];break}if(s(r,i,e))return l[r][i][e];for(var d,h,p,m;r.length>2&&(d=r.length,h=r.split(/[\s_]+/),p=h[h.length-1],m=p.length,1!==h.length);)if(r=r.substring(0,d-(m+1)),s(r,i,e))return l[r][i][e];return s(c,i,e)?l[c][i][e]:e}function s(e,a,s){return e in l&&(a in l[e]&&s in l[e][a])}function n(e,a,s){var n,r,i=[],l=[],u=e.split(p.pluralSeparator),f=[];for(n=0;n<u.length;n++){var m=u[n];d.test(m)?(f=m.match(d),i[f[0]]=f[f.length-1]):o.test(m)?(f=m.match(o),l.push(f[1])):l.push(m)}for(r in i)if(h.test(r))if(f=r.match(h),f[1]){var g,v=f[2].split(",");for(g in v)if(a==v[g])return i[r]}else{var b=t(f[4]),k=t(f[5]);if(("["===f[3]?a>=b:a>b)&&("]"===f[6]?a<=k:a<k))return i[r]}return l[c(a,s)]||l[0]||void 0}function t(e){return"-Inf"===e?Number.NEGATIVE_INFINITY:"+Inf"===e||"Inf"===e?Number.POSITIVE_INFINITY:parseInt(e,10)}function c(e,a){var s=a;switch("pt_BR"===s&&(s="xbr"),s.length>3&&(s=s.split("_")[0]),s){case"bo":case"dz":case"id":case"ja":case"jv":case"ka":case"km":case"kn":case"ko":case"ms":case"th":case"tr":case"vi":case"zh":return 0;case"af":case"az":case"bn":case"bg":case"ca":case"da":case"de":case"el":case"en":case"eo":case"es":case"et":case"eu":case"fa":case"fi":case"fo":case"fur":case"fy":case"gl":case"gu":case"ha":case"he":case"hu":case"is":case"it":case"ku":case"lb":case"ml":case"mn":case"mr":case"nah":case"nb":case"ne":case"nl":case"nn":case"no":case"om":case"or":case"pa":case"pap":case"ps":case"pt":case"so":case"sq":case"sv":case"sw":case"ta":case"te":case"tk":case"ur":case"zu":return 1==e?0:1;case"am":case"bh":case"fil":case"fr":case"gun":case"hi":case"ln":case"mg":case"nso":case"xbr":case"ti":case"wa":return 0===e||1==e?0:1;case"be":case"bs":case"hr":case"ru":case"sr":case"uk":return e%10==1&&e%100!=11?0:e%10>=2&&e%10<=4&&(e%100<10||e%100>=20)?1:2;case"cs":case"sk":return 1==e?0:e>=2&&e<=4?1:2;case"ga":return 1==e?0:2==e?1:2;case"lt":return e%10==1&&e%100!=11?0:e%10>=2&&(e%100<10||e%100>=20)?1:2;case"sl":return e%100==1?0:e%100==2?1:e%100==3||e%100==4?2:3;case"mk":return e%10==1?0:1;case"mt":return 1==e?0:0===e||e%100>1&&e%100<11?1:e%100>10&&e%100<20?2:3;case"lv":return 0===e?0:e%10==1&&e%100!=11?1:2;case"pl":return 1==e?0:e%10>=2&&e%10<=4&&(e%100<12||e%100>14)?1:2;case"cy":return 1==e?0:2==e?1:8==e||11==e?2:3;case"ro":return 1==e?0:0===e||e%100>0&&e%100<20?1:2;case"ar":return 0===e?0:1==e?1:2==e?2:e>=3&&e<=10?3:e>=11&&e<=99?4:5;default:return 0}}function r(e,a){for(var s=0;s<e.length;s++)if(a===e[s])return!0;return!1}function i(){return"undefined"!=typeof document?document.documentElement.lang.replace("-","_"):u}var l={},u="en",f=[],o=new RegExp(/^\w+\: +(.+)$/),d=new RegExp(/^\s*((\{\s*(\-?\d+[\s*,\s*\-?\d+]*)\s*\})|([\[\]])\s*(-Inf|\-?\d+)\s*,\s*(\+?Inf|\-?\d+)\s*([\[\]]))\s?(.+?)$/),h=new RegExp(/^\s*(\{\s*(\-?\d+[\s*,\s*\-?\d+]*)\s*\})|([\[\]])\s*(-Inf|\-?\d+)\s*,\s*(\+?Inf|\-?\d+)\s*([\[\]])/),p={locale:i(),fallback:u,placeHolderPrefix:"%",placeHolderSuffix:"%",defaultDomain:"messages",pluralSeparator:"|",add:function(e,a,s,n){var t=n||this.locale||this.fallback,c=s||this.defaultDomain;return l[t]||(l[t]={}),l[t][c]||(l[t][c]={}),l[t][c][e]=a,!1===r(f,c)&&f.push(c),this},trans:function(s,n,t,c){var r=a(s,t,c,this.locale,this.fallback);return e(r,n||{})},transChoice:function(s,t,c,r,i){var l=a(s,r,i,this.locale,this.fallback),u=parseInt(t,10);return c=c||{},void 0===c.count&&(c.count=t),"undefined"==typeof l||isNaN(u)||(l=n(l,u,i||this.locale||this.fallback)),e(l,c)},fromJSON:function(e){if("string"==typeof e&&(e=JSON.parse(e)),e.locale&&(this.locale=e.locale),e.fallback&&(this.fallback=e.fallback),e.defaultDomain&&(this.defaultDomain=e.defaultDomain),e.translations)for(var a in e.translations)for(var s in e.translations[a])for(var n in e.translations[a][s])this.add(n,e.translations[a][s][n],s,a);return this},reset:function(){l={},f=[],this.locale=i()}};return p});
