<?php
namespace OTS\BillingBundle\Service\Entity;

use OTS\BillingBundle\Entity\Customer;
use OTS\BillingBundle\Entity\Charge;
use OTS\BillingBundle\Entity\TicketOrder;
use Symfony\Component\Validator\Validator;
use Symfony\Component\HttpFoundation\RequestStack;

class EntityManager {
	protected $validator;

	protected $request;

	public function __construct(Validator $validator, RequestStack $requestStack) {
		$this->validator = $validator;

		$this->request = $requestStack->getCurrentRequest();
	}







	/**
	 * CUSTOMER ENTITY MANAGEMENT
	 * --------------------------
	 */

	//return a customer entity from a stripe customer
	public function generateCustomer(\Stripe\Customer $stripeCustomer) {
		$customerObj = new Customer();
    	$customerObj->setStripeId($stripeCustomer->id);
    	$customerObj->setEmail($stripeCustomer->email);

    	return $customerObj;
	}

	/**
	 * --------------------------
	 */
	





	
	/**
	 * CHARGE ENTITY MANAGEMENT
	 * ------------------------
	 */

	//return a charge entity from a stripe charge
	public function generateCharge(\Stripe\Charge $stripeCharge) {
		$chargeObj = new Charge();
    	$chargeObj->setAmount($stripeCharge->amount);
    	$chargeObj->setCurrency($stripeCharge->currency);

    	return $chargeObj;
	}

	/**
	 * ------------------------
	 */
	






	/**
	 * ORDER ENTITY MANAGEMENT
	 * -----------------------
	 */

	//check whether the Order entity passed and is valid
	public function validateOrder(TicketOrder $order, $flow) {
		$errors = $this->validator->validate($order);
	    
	    if (count($errors) > 0) {
	        $errorsString = (string) $errors;

	        $this->request->getSession()->getFlashBag()->add('error', $errorsString);

			$form = $flow->createForm();

			return $this->render('OTSBillingBundle:Billing:index.html.twig', array(
			       'orderForm' => $form->createView(),
			       'flow' => $flow,
			));
	    }
	}

	/**
	 * -----------------------
	 */







	/**
	 * GENERAL ENTITIES MANAGEMENT
	 * ---------------------------
	 */

	//add entities to each other in the right order so it's a matter of a simple one-entity-only persist afterward
	public function associateEntities(TicketOrder $order, Customer $customer, Charge $charge, $flow) {
    	//we put the entities all together
    	$customer->addCharge($charge);
    	$order->setCustomer($customer);
    	$order->setCharge($charge);

    	//and we check if everything is ok on $order
    	//as the underlying entity has a relation with every other entities
    	$this->validateOrder($order, $flow);
    }

    /**
	 * ---------------------------
	 */
}