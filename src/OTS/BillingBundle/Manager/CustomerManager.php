<?php
namespace OTS\BillingBundle\Manager;

use OTS\BillingBundle\Entity\Customer;

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
