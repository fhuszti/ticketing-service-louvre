<?php
namespace OTS\BillingBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use OTS\BillingBundle\Entity\TicketOrder;

class SuccessfulCheckoutEvent extends Event {
	protected $order;

	public function __construct(TicketOrder $order) {
		$this->order = $order;
	}

	//the listener only has to fetch informations about the order, not modify it, so no setter
	public function getOrder() {
		return $this->order;
	}
}