<?php
namespace OTS\BillingBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use OTS\BillingBundle\Entity\TicketOrder;
use OTS\BillingBundle\Form\TicketOrderFlow;

class SubmittedOrderEvent extends Event {
	protected $order;

	protected $flow;

	protected $checkoutToken;

	public function __construct(TicketOrder $order, TicketOrderFlow $flow, $checkoutToken) {
		$this->order =         $order;
		$this->flow =          $flow;
		$this->checkoutToken = $checkoutToken;
	}

	//the listener only has to fetch informations about the order, not modify it, so no setter
	public function getOrder() {
		return $this->order;
	}

	public function getFlow() {
		return $this->flow;
	}

	public function getCheckoutToken() {
		return $this->checkoutToken;
	}
}
