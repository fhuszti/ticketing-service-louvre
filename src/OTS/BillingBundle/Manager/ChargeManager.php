<?php
namespace OTS\BillingBundle\Manager;

use OTS\BillingBundle\Entity\Charge;

class ChargeManager {
	/**
	 * CHARGE ENTITY CREATION
	 * ----------------------
	 */

	//return a charge entity from a stripe charge
	public function generateCharge(\Stripe\Charge $stripeCharge) {
		$chargeObj = new Charge();
    	$chargeObj->setAmount($stripeCharge->amount);
    	$chargeObj->setCurrency($stripeCharge->currency);

    	return $chargeObj;
	}

	/**
	 * ----------------------
	 */
}
