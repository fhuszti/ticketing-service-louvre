<?php
namespace OTS\BillingBundle\Mailer;

use OTS\BillingBundle\Event\SuccessfulCheckoutEvent;

class CheckoutListener {
	protected $notificator;

	public function __construct(CheckoutNotificator $notificator) {
		$this->notificator = $notificator;
	}

	public function processCheckoutSuccess(SuccessfulCheckoutEvent $event) {
		$this->notificator->sendTicketsByEmail( $event->getOrder() );
	}
}