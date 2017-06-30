$(function() {
	/**
	 * COMMON FUNCTIONS
	 * ----------------
	 */

	 function convertDatePhpToFrench(date) {
        var parts = date.split('-');

        return parts[2]+'/'+parts[1]+'/'+parts[0];
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
		    'core.step3.full',
		    'Full-day',
		    'messages',
		    'en'
		);
		Translator.add(
		    'core.step3.full',
		    'Journée complète',
		    'messages',
		    'fr'
		);

		Translator.add(
		    'core.step3.half',
		    'Half-day',
		    'messages',
		    'en'
		);
		Translator.add(
		    'core.step3.half',
		    'Demi-journée',
		    'messages',
		    'fr'
		);
	}

	/**
	 * ------------
	 */






	/**
     * FILL RECAP VALUES FOR STEP 3
     * ----------------------------
     */

    function fillRecap() {
        var date = convertDatePhpToFrench( $('#appbundle_ticketorder_date').val() );
        $('#recap_date').text(date);
        
        var type = $('#appbundle_ticketorder_type').val() === '1' ? Translator.trans('core.step3.full') : Translator.trans('core.step3.half');
        $('#recap_type').text(type);
        
        $('#recap_nbTickets').text( $('#appbundle_ticketorder_nbTickets').val() );
        
        $('#recap_price').text( $('#appbundle_ticketorder_price').val()+'€' );
     }







     addTranslations();

    fillRecap();
});