<?php
namespace AppBundle\Manager;

use AppBundle\Entity\Charge;

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
