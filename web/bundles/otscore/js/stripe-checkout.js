$(function() {
	function setupStripeCheckout() {
		var btn = $('#btn-stripe-checkout');
	
		var handler = StripeCheckout.configure({
		  	key:    'pk_test_hoXdqVAmVW3pIQOWqanoshlw',
		  	image:  'https://stripe.com/img/documentation/checkout/marketplace.png',
		  	locale: 'auto',
		  	allowRememberMe: false,
		  	token:  function(token) {
		    	//we fill the checkout token hidden field of the form with the token id so it's passed to back-end too
		    	$('#ots_billingbundle_ticketorder_checkoutToken').val(token.id);

		    	//then we send the form
		    	$('form[name="ots_billingbundle_ticketorder"]').submit();
		  	}
		});
	
		btn.on('click', function(e) {
			e.preventDefault();
	
			// Open Checkout with further options:
			handler.open({
				name: 'Musée du Louvre',
			    description: btn.data('nbtickets')+" ticket(s)",
			    currency: 'eur',
			    amount: btn.data('amount')
			});
		});
	
		// Close Checkout on page navigation:
		$(window).on('popstate', function() {
			handler.close();
		});
	}

	setupStripeCheckout();
});