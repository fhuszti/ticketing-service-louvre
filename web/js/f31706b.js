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

    /**
	 * ----------------
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

        //define datepicker on the page
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




    
    setupDatepickerStep1();
    checkDate($('#ots_billingbundle_ticketorder_date').val());

    calculator();
});
/**
 * @author William DURAND <william.durand1@gmail.com>
 * @license MIT Licensed
 */
!function(e,a){"function"==typeof define&&define.amd?define("Translator",a):"object"==typeof module&&module.exports?module.exports=a():e.Translator=a()}(this,function(){"use strict";function e(e,a){var s,n=p.placeHolderPrefix,t=p.placeHolderSuffix;for(s in a){var c=new RegExp(n+s+t,"g");c.test(e)&&(e=e.replace(c,a[s]))}return e}function a(e,a,n,t,c){var r=n||t||c,i=a,u=r.split("_")[0];if(!(r in l))if(u in l)r=u;else{if(!(c in l))return e;r=c}if("undefined"==typeof i||null===i)for(var o=0;o<f.length;o++)if(s(r,f[o],e)||s(u,f[o],e)||s(c,f[o],e)){i=f[o];break}if(s(r,i,e))return l[r][i][e];for(var d,h,p,m;r.length>2&&(d=r.length,h=r.split(/[\s_]+/),p=h[h.length-1],m=p.length,1!==h.length);)if(r=r.substring(0,d-(m+1)),s(r,i,e))return l[r][i][e];return s(c,i,e)?l[c][i][e]:e}function s(e,a,s){return e in l&&(a in l[e]&&s in l[e][a])}function n(e,a,s){var n,r,i=[],l=[],u=e.split(p.pluralSeparator),f=[];for(n=0;n<u.length;n++){var m=u[n];d.test(m)?(f=m.match(d),i[f[0]]=f[f.length-1]):o.test(m)?(f=m.match(o),l.push(f[1])):l.push(m)}for(r in i)if(h.test(r))if(f=r.match(h),f[1]){var g,v=f[2].split(",");for(g in v)if(a==v[g])return i[r]}else{var b=t(f[4]),k=t(f[5]);if(("["===f[3]?a>=b:a>b)&&("]"===f[6]?a<=k:a<k))return i[r]}return l[c(a,s)]||l[0]||void 0}function t(e){return"-Inf"===e?Number.NEGATIVE_INFINITY:"+Inf"===e||"Inf"===e?Number.POSITIVE_INFINITY:parseInt(e,10)}function c(e,a){var s=a;switch("pt_BR"===s&&(s="xbr"),s.length>3&&(s=s.split("_")[0]),s){case"bo":case"dz":case"id":case"ja":case"jv":case"ka":case"km":case"kn":case"ko":case"ms":case"th":case"tr":case"vi":case"zh":return 0;case"af":case"az":case"bn":case"bg":case"ca":case"da":case"de":case"el":case"en":case"eo":case"es":case"et":case"eu":case"fa":case"fi":case"fo":case"fur":case"fy":case"gl":case"gu":case"ha":case"he":case"hu":case"is":case"it":case"ku":case"lb":case"ml":case"mn":case"mr":case"nah":case"nb":case"ne":case"nl":case"nn":case"no":case"om":case"or":case"pa":case"pap":case"ps":case"pt":case"so":case"sq":case"sv":case"sw":case"ta":case"te":case"tk":case"ur":case"zu":return 1==e?0:1;case"am":case"bh":case"fil":case"fr":case"gun":case"hi":case"ln":case"mg":case"nso":case"xbr":case"ti":case"wa":return 0===e||1==e?0:1;case"be":case"bs":case"hr":case"ru":case"sr":case"uk":return e%10==1&&e%100!=11?0:e%10>=2&&e%10<=4&&(e%100<10||e%100>=20)?1:2;case"cs":case"sk":return 1==e?0:e>=2&&e<=4?1:2;case"ga":return 1==e?0:2==e?1:2;case"lt":return e%10==1&&e%100!=11?0:e%10>=2&&(e%100<10||e%100>=20)?1:2;case"sl":return e%100==1?0:e%100==2?1:e%100==3||e%100==4?2:3;case"mk":return e%10==1?0:1;case"mt":return 1==e?0:0===e||e%100>1&&e%100<11?1:e%100>10&&e%100<20?2:3;case"lv":return 0===e?0:e%10==1&&e%100!=11?1:2;case"pl":return 1==e?0:e%10>=2&&e%10<=4&&(e%100<12||e%100>14)?1:2;case"cy":return 1==e?0:2==e?1:8==e||11==e?2:3;case"ro":return 1==e?0:0===e||e%100>0&&e%100<20?1:2;case"ar":return 0===e?0:1==e?1:2==e?2:e>=3&&e<=10?3:e>=11&&e<=99?4:5;default:return 0}}function r(e,a){for(var s=0;s<e.length;s++)if(a===e[s])return!0;return!1}function i(){return"undefined"!=typeof document?document.documentElement.lang.replace("-","_"):u}var l={},u="en",f=[],o=new RegExp(/^\w+\: +(.+)$/),d=new RegExp(/^\s*((\{\s*(\-?\d+[\s*,\s*\-?\d+]*)\s*\})|([\[\]])\s*(-Inf|\-?\d+)\s*,\s*(\+?Inf|\-?\d+)\s*([\[\]]))\s?(.+?)$/),h=new RegExp(/^\s*(\{\s*(\-?\d+[\s*,\s*\-?\d+]*)\s*\})|([\[\]])\s*(-Inf|\-?\d+)\s*,\s*(\+?Inf|\-?\d+)\s*([\[\]])/),p={locale:i(),fallback:u,placeHolderPrefix:"%",placeHolderSuffix:"%",defaultDomain:"messages",pluralSeparator:"|",add:function(e,a,s,n){var t=n||this.locale||this.fallback,c=s||this.defaultDomain;return l[t]||(l[t]={}),l[t][c]||(l[t][c]={}),l[t][c][e]=a,!1===r(f,c)&&f.push(c),this},trans:function(s,n,t,c){var r=a(s,t,c,this.locale,this.fallback);return e(r,n||{})},transChoice:function(s,t,c,r,i){var l=a(s,r,i,this.locale,this.fallback),u=parseInt(t,10);return c=c||{},void 0===c.count&&(c.count=t),"undefined"==typeof l||isNaN(u)||(l=n(l,u,i||this.locale||this.fallback)),e(l,c)},fromJSON:function(e){if("string"==typeof e&&(e=JSON.parse(e)),e.locale&&(this.locale=e.locale),e.fallback&&(this.fallback=e.fallback),e.defaultDomain&&(this.defaultDomain=e.defaultDomain),e.translations)for(var a in e.translations)for(var s in e.translations[a])for(var n in e.translations[a][s])this.add(n,e.translations[a][s][n],s,a);return this},reset:function(){l={},f=[],this.locale=i()}};return p});
