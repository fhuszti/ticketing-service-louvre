<?php
namespace OTS\BillingBundle\Listener;

use OTS\BillingBundle\Event\SubmittedOrderEvent;
use OTS\BillingBundle\Manager\StockManager;
use OTS\BillingBundle\Manager\OrderManager;
use OTS\BillingBundle\Manager\CustomerManager;
use OTS\BillingBundle\Manager\ChargeManager;
use OTS\BillingBundle\Manager\EntityManager;
use OTS\BillingBundle\Service\Stripe\StripeService;
use Symfony\Component\Translation\TranslatorInterface;
use OTS\BillingBundle\Service\BillingForm\ErrorReturn;

class SubmittedOrderListener {
	protected $stockManager;

	protected $orderManager;

	protected $customerManager;

	protected $chargeManager;

	protected $entityManager;

	protected $stripeService;

	protected $translator;

	protected $errorReturnManager;

	public function __construct(StockManager $stockManager, OrderManager $orderManager, CustomerManager $customerManager, ChargeManager $chargeManager, EntityManager $entityManager, StripeService $stripeService, TranslatorInterface $translator, ErrorReturn $errorReturnManager) {
		$this->stockManager =       $stockManager;
		$this->orderManager =       $orderManager;
		$this->customerManager =    $customerManager;
		$this->chargeManager =      $chargeManager;
		$this->entityManager =      $entityManager;
		$this->stripeService =      $stripeService;
		$this->translator =         $translator;
		$this->errorReturnManager = $errorReturnManager;
	}







	//process all submitted data and charge the customer if everything is okay
	public function processSubmittedOrder(SubmittedOrderEvent $event) {
		$order = $event->getOrder();
		$flow = $event->getFlow();
		$checkoutToken = $event->getCheckoutToken();

		//we abort everything if there's not enough left in stock for the chosen date
		if ( !$this->stockManager->checkIfStockOkForDate($order) ) {
			$error = $this->translator->trans('ots_billing.controller.action.error');

		  	$this->errorReturnManager->returnToFormWithError($flow, $error);
		}

		//setup order entity
		$this->orderManager->manageOrder($order, $flow);

		//we generate the stripe customer
		$stripeCustomer = $this->stripeService->generateCustomer($checkoutToken);
		//we charge the stripe customer
		$stripeCharge = $this->stripeService->chargeCustomer( $stripeCustomer->id, $order->getPrice(), $flow );

		//create a customer entity from the stripe equivalent
		$customer = $this->customerManager->generateCustomer($stripeCustomer);
		//create a charge entity from the stripe equivalent
		$charge = $this->chargeManager->generateCharge($stripeCharge);
				
		//associate entities before persisting
		$this->entityManager->prepareEntitiesForPersist($order, $customer, $charge, $flow);
	}
}
