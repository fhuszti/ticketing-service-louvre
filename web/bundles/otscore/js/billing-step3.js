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
     * FILL RECAP VALUES FOR STEP 3
     * ----------------------------
     */

    function fillRecap() {
        var date = convertDatePhpToFrench( $('#ots_billingbundle_ticketorder_date').val() );
        $('#recap_date').text(date);
        
        var type = $('#ots_billingbundle_ticketorder_type').val() === '1' ? "Full-day" : "Half-day";
        $('#recap_type').text(type);
        
        $('#recap_nbTickets').text( $('#ots_billingbundle_ticketorder_nbTickets').val() );
        
        $('#recap_price').text( $('#ots_billingbundle_ticketorder_price').val()+'€' );
     }





     fillRecap();
});