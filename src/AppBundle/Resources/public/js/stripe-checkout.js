$(function() {
	function addTranslations() {
		Translator.add(
		    'core.stripe.name',
		    'Louvre Museum',
		    'messages',
		    'en'
		);
		Translator.add(
		    'core.stripe.name',
		    'Musée du Louvre',
		    'messages',
		    'fr'
		);

		Translator.add(
		    'core.stripe.description',
		    '{1}%count% ticket|[2,+Inf]%count% tickets',
		    'messages',
		    'en'
		);
		Translator.add(
		    'core.stripe.description',
		    '{1}%count% billet|[2,+Inf]%count% billets',
		    'messages',
		    'fr'
		);

		Translator.add(
		    'core.stripe.button',
		    'Pay {{amount}}',
		    'messages',
		    'en'
		);
		Translator.add(
		    'core.stripe.button',
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
		  	panelLabel: Translator.trans('core.stripe.button'),
		  	token:  function(token) {
		    	//we fill the checkout token hidden field of the form with the token id so it's passed to back-end too
		    	$('#appbundle_ticketorder_checkoutToken').val(token.id);

		    	//then we send the form
		    	$('form[name="appbundle_ticketorder"]').submit();
		  	}
		});
	
		btn.on('click', function(e) {
			e.preventDefault();
			
			var nbTickets = btn.data('nbtickets');

			// Open Checkout with further options:
			handler.open({
				name:        Translator.trans('core.stripe.name'),
			    description: Translator.transChoice('core.stripe.description', nbTickets, {"count" : nbTickets}),
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