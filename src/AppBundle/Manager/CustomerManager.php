<?php
namespace AppBundle\Manager;

use AppBundle\Entity\Customer;

class CustomerManager {
	/**
	 * CUSTOMER ENTITY CREATION
	 * ------------------------
	 */

	//return a customer entity from a stripe customer
	public function generateCustomer(\Stripe\Customer $stripeCustomer) {
		$customerObj = new Customer();
    	$customerObj->setStripeId($stripeCustomer->id);
    	$customerObj->setEmail($stripeCustomer->email);

    	return $customerObj;
	}

	/**
	 * ------------------------
	 */
}
