<?php
namespace AppBundle\Listener;

use AppBundle\Event\SuccessfulCheckoutEvent;
use AppBundle\Manager\StockManager;
use AppBundle\Service\Mailer\MailerNotificator;

class CheckoutListener {
	protected $notificator;

	protected $stockManager;

	public function __construct(MailerNotificator $notificator, StockManager $stockManager) {
		$this->notificator = $notificator;
		$this->stockManager = $stockManager;
	}





	

	public function processCheckoutSuccess(SuccessfulCheckoutEvent $event) {
		//send email confirmation to visitor (including his tickets)
		$this->notificator->sendTicketsByEmail( $event->getOrder() );

		//increment amount of tickets sold today in the stock file
		$this->stockManager->decrementStock( $event->getOrder() );
	}
}
