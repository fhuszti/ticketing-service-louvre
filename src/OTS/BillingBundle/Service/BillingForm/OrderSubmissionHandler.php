<?php
namespace OTS\BillingBundle\Service\BillingForm;

use OTS\BillingBundle\Manager\StockManager;
use OTS\BillingBundle\Manager\OrderManager;
use OTS\BillingBundle\Manager\CustomerManager;
use OTS\BillingBundle\Manager\ChargeManager;
use OTS\BillingBundle\Manager\EntityManager;
use OTS\BillingBundle\Service\Stripe\StripeService;
use Symfony\Component\Translation\TranslatorInterface;
use OTS\BillingBundle\Entity\TicketOrder;

class OrderSubmissionHandler {
	protected $stockManager;

	protected $orderManager;

	protected $customerManager;

	protected $chargeManager;

	protected $entityManager;

	protected $stripeService;

	protected $translator;

	public function __construct(StockManager $stockManager, OrderManager $orderManager, CustomerManager $customerManager, ChargeManager $chargeManager, EntityManager $entityManager, StripeService $stripeService, TranslatorInterface $translator) {
		$this->stockManager =       $stockManager;
		$this->orderManager =       $orderManager;
		$this->customerManager =    $customerManager;
		$this->chargeManager =      $chargeManager;
		$this->entityManager =      $entityManager;
		$this->stripeService =      $stripeService;
		$this->translator =         $translator;
	}







	//process all submitted data and charge the customer if everything is okay
	public function processSubmittedOrder(TicketOrder $order, $checkoutToken) {
		$error = array(false, '');

		//we abort everything if there's not enough left in stock for the chosen date
		if ( !$this->stockManager->checkIfStockOkForDate($order) ) {
			$errString = $this->translator->trans('ots_billing.controller.action.error');

		  	$error = array(true, $errString);
		  	return $error;
		}
		
		//setup order entity
		$response = $this->orderManager->manageOrder($order);
		if ( !is_bool($response) ) {
			$error = array(true, $response);
		  	return $error;
		}

		//we generate the stripe customer
		$stripeCustomer = $this->stripeService->generateCustomer($checkoutToken);
		//we charge the stripe customer
		$stripeCharge = $this->stripeService->chargeCustomer( $stripeCustomer->id, $order->getPrice() );
		//if $stripeCharge is a string, then it means there was an error and the string is the error to display
		if ( is_string($stripeCharge) ) {
			$error = array(true, $stripeCharge);
		  	return $error;
		}

		//create a customer entity from the stripe equivalent
		$customer = $this->customerManager->generateCustomer($stripeCustomer);
		//create a charge entity from the stripe equivalent
		$charge = $this->chargeManager->generateCharge($stripeCharge);
				
		//associate entities before persisting
		$response = $this->entityManager->prepareEntitiesForPersist($order, $customer, $charge);
		if ( !is_bool($response) ) {
			$error = array(true, $response);
		  	return $error;
		}

		//if everything is fine, we return the original $error with false at index 0
		return $error;
	}
}
