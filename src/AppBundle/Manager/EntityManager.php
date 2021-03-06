<?php
namespace AppBundle\Manager;

use AppBundle\Entity\Customer;
use AppBundle\Entity\Charge;
use AppBundle\Entity\TicketOrder;

class EntityManager {
	protected $orderManager;

	public function __construct(OrderManager $orderManager) {
		$this->orderManager = $orderManager;
	}
	






	/**
	 * GENERAL ENTITIES MANAGEMENT
	 * ---------------------------
	 */

	//add entities to each other in the right order
	public function associateEntities(TicketOrder $order, Customer $customer, Charge $charge) {
    	//we put the entities all together
    	$customer->addCharge($charge);
    	$order->setCustomer($customer);
    	$order->setCharge($charge);
    }

    //manage entities association and validation
    public function prepareEntitiesForPersist(TicketOrder $order, Customer $customer, Charge $charge) {
    	//we associate entites together
    	$this->associateEntities($order, $customer, $charge);

    	//and we check if everything is ok on $order
    	//as the underlying entity has a relation with every other entities
        // returns either an empty string if no error, or the error message to display
    	return $this->orderManager->validateOrder($order);
    }

    /**
	 * ---------------------------
	 */
}
