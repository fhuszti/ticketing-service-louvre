$(function() {
	function addTranslations() {
		Translator.add(
		    'ots_billing.stripe.name',
		    'Louvre Museum',
		    'messages',
		    'en'
		);
		Translator.add(
		    'ots_billing.stripe.name',
		    'Musée du Louvre',
		    'messages',
		    'fr'
		);

		Translator.add(
		    'ots_billing.stripe.description',
		    '{1}%count% ticket|[2,+Inf]%count% tickets',
		    'messages',
		    'en'
		);
		Translator.add(
		    'ots_billing.stripe.description',
		    '{1}%count% billet|[2,+Inf]%count% billets',
		    'messages',
		    'fr'
		);

		Translator.add(
		    'ots_billing.stripe.button',
		    'Pay {{amount}}',
		    'messages',
		    'en'
		);
		Translator.add(
		    'ots_billing.stripe.button',
		    'Payer {{amount}}',
		    'messages',
		    'fr'
		);
	}

	function setupStripeCheckout() {
		var btn = $('#btn-stripe-checkout');
	
		addTranslations();

		var handler = StripeCheckout.configure({
		  	key:    'pk_test_hoXdqVAmVW3pIQOWqanoshlw',
		  	image:  'https://stripe.com/img/documentation/checkout/marketplace.png',
		  	locale: 'auto',
		  	allowRememberMe: false,
		  	panelLabel: Translator.trans('ots_billing.stripe.button'),
		  	token:  function(token) {
		    	//we fill the checkout token hidden field of the form with the token id so it's passed to back-end too
		    	$('#ots_billingbundle_ticketorder_checkoutToken').val(token.id);

		    	//then we send the form
		    	$('form[name="ots_billingbundle_ticketorder"]').submit();
		  	}
		});
	
		btn.on('click', function(e) {
			e.preventDefault();
			
			var nbTickets = btn.data('nbtickets');

			// Open Checkout with further options:
			handler.open({
				name:        Translator.trans('ots_billing.stripe.name'),
			    description: Translator.transChoice('ots_billing.stripe.description', nbTickets, {"count" : nbTickets}),
			    currency:    'eur',
			    amount:      btn.data('amount')
			});
		});
	
		// Close Checkout on page navigation:
		$(window).on('popstate', function() {
			handler.close();
		});
	}

	setupStripeCheckout();
});