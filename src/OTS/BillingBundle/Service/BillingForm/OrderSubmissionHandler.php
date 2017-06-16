<?php
namespace OTS\BillingBundle\Service\BillingForm;

use OTS\BillingBundle\Manager\StockManager;
use OTS\BillingBundle\Manager\OrderManager;
use OTS\BillingBundle\Manager\CustomerManager;
use OTS\BillingBundle\Manager\ChargeManager;
use OTS\BillingBundle\Manager\EntityManager;
use OTS\BillingBundle\Service\Stripe\StripeService;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use OTS\BillingBundle\Entity\TicketOrder;
use OTS\BillingBundle\Form\TicketOrderFlow;

class OrderSubmissionHandler {
	protected $stockManager;

	protected $orderManager;

	protected $customerManager;

	protected $chargeManager;

	protected $entityManager;

	protected $stripeService;

	protected $translator;

	protected $request;

    protected $twig;

	public function __construct(StockManager $stockManager, OrderManager $orderManager, CustomerManager $customerManager, ChargeManager $chargeManager, EntityManager $entityManager, StripeService $stripeService, TranslatorInterface $translator, RequestStack $requestStack, \Twig_Environment $twig) {
		$this->stockManager =       $stockManager;
		$this->orderManager =       $orderManager;
		$this->customerManager =    $customerManager;
		$this->chargeManager =      $chargeManager;
		$this->entityManager =      $entityManager;
		$this->stripeService =      $stripeService;
		$this->translator =         $translator;

		$this->request = $requestStack->getCurrentRequest();
		$this->twig = $twig;
	}







	//process all submitted data and charge the customer if everything is okay
	public function processSubmittedOrder(TicketOrder $order, TicketOrderFlow $flow, $checkoutToken) {
		//we abort everything if there's not enough left in stock for the chosen date
		if ( !$this->stockManager->checkIfStockOkForDate($order) ) {
			$error = $this->translator->trans('ots_billing.controller.action.error');

		  	return $error;
		}
		
		//setup order entity
		$error = $this->orderManager->manageOrder($order, $flow);
		if ( $error !== '' )
			return $error;

		//we generate the stripe customer
		$stripeCustomer = $this->stripeService->generateCustomer($checkoutToken);
		//we charge the stripe customer
		$stripeCharge = $this->stripeService->chargeCustomer( $stripeCustomer->id, $order->getPrice(), $flow );
		//if $stripeCharge is a string, then it means there was an error and the string is the error to display
		if ( is_string($stripeCharge) )
			return $stripeCharge;

		//create a customer entity from the stripe equivalent
		$customer = $this->customerManager->generateCustomer($stripeCustomer);
		//create a charge entity from the stripe equivalent
		$charge = $this->chargeManager->generateCharge($stripeCharge);
				
		//associate entities before persisting
		$error = $this->entityManager->prepareEntitiesForPersist($order, $customer, $charge, $flow);
		if ( $error !== '' )
			return $error;

		//if everything is fine, we return an empty string
		return '';
	}
}
