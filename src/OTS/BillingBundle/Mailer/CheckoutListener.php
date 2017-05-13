<?php
namespace OTS\BillingBundle\Mailer;

use OTS\BillingBundle\Event\SuccessfulCheckoutEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CheckoutListener {
	protected $notificator;

	protected $stockManager;

	protected $container;

	public function __construct(CheckoutNotificator $notificator, CheckoutStockManager $stockManager, ContainerInterface $container) {
		$this->notificator = $notificator;
		$this->stockManager = $stockManager;
		$this->container = $container;
	}

	public function processCheckoutSuccess(SuccessfulCheckoutEvent $event) {
		//send email confirmation to visitor (including his tickets)
		$this->notificator->sendTicketsByEmail( $event->getOrder() );

		//increment amount of tickets sold today in the stock file
		$this->stockManager->decrementStock( $event->getOrder() );
	}
}