$(function() {
	var btn = $('#btn-stripe-checkout');

	var handler = StripeCheckout.configure({
	  	key:    'pk_test_hoXdqVAmVW3pIQOWqanoshlw',
	  	image:  'https://stripe.com/img/documentation/checkout/marketplace.png',
	  	locale: 'auto',
	  	allowRememberMe: false,
	  	token:  function(token) {
	    	// You can access the token ID with `token.id`.
	    	// Get the token ID to your server-side code for use.
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
});

/*class="stripe-button"
                    data-key="pk_test_hoXdqVAmVW3pIQOWqanoshlw"
                    data-amount="{{ orderForm.price.vars.value * 100 }}"
                    data-name="Musée du Louvre"
                    data-description="{{ orderForm.nbTickets.vars.value }} ticket(s)"
                    data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
                    data-locale="auto"
                    data-currency="eur"
                    data-allow-remember-me="false"*/