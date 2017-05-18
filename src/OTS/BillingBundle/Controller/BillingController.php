<?php

namespace OTS\BillingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use OTS\BillingBundle\Entity\TicketOrder;
use OTS\BillingBundle\Event\PlatformEvents;
use OTS\BillingBundle\Event\SuccessfulCheckoutEvent;

class BillingController extends Controller
{
    public function indexAction(Request $request)
    {
    	$order = new TicketOrder();
    	
    	$flow = $this->get('ots_billing.form.flow.ticketOrder'); // must match the flow's service id
		$flow->bind($order);

		// form of the current step
		$form = $flow->createForm();
		if ($flow->isValid($form)) {
			$flow->saveCurrentStepData($form);

			if ($flow->nextStep()) {
				// form for the next step
				$form = $flow->createForm();
			} else {
				$checkoutToken = $form->get('checkoutToken')->getData();

				//we get the services we're gonna use
				$stockManager =     $this->get('ots_billing.entity.stock_manager');
				$orderManager =     $this->get('ots_billing.entity.order_manager');
				$customerManager =  $this->get('ots_billing.entity.customer_manager');
				$chargeManager =    $this->get('ots_billing.entity.charge_manager');
				$entityManager =    $this->get('ots_billing.entity.entity_manager');
				$stripeManager =    $this->get('ots_billing.stripe.stripe_manager');
				$translator =       $this->get('translator');

				//we abort everything if there's not enough left in stock for the chosen date
				if ( !$stockManager->checkIfStockOkForDate($order) ) {
					$error = $translator->trans('ots_billing.controller.action.error');

		  			$request->getSession()->getFlashBag()->add('error', $error);

					$form = $flow->createForm();

					return $this->render('OTSBillingBundle:Billing:index.html.twig', array(
						'orderForm' => $form->createView(),
						'flow' => $flow,
					));
				}

				//setup order entity
				$orderManager->manageOrder($order, $flow);

				//we generate the stripe customer
				$stripeCustomer = $stripeManager->generateCustomer($checkoutToken);
				//we charge the stripe customer
				$stripeCharge = $stripeManager->chargeCustomer( $stripeCustomer->id, $order->getPrice(), $flow );

				//create a customer entity from the stripe equivalent
				$customer = $customerManager->generateCustomer($stripeCustomer);
				//create a charge entity from the stripe equivalent
				$charge = $chargeManager->generateCharge($stripeCharge);
				
				//associate entities before persisting
				$entityManager->prepareEntitiesForPersist($order, $customer, $charge, $flow);

				// flow finished
				$em = $this->getDoctrine()->getManager();
				$em->persist($order);

				//we dispatch the event associated with a successful checkout
				$event = new SuccessfulCheckoutEvent($order);
				$this->get('event_dispatcher')->dispatch(PlatformEvents::SUCCESSFUL_CHECKOUT, $event);

				//we don't forget to flush
				$em->flush();

				$flow->reset(); // remove step data from the session

				return $this->redirect($this->generateUrl('ots_billing_thanks')); // redirect when done
			}
		}

		return $this->render('OTSBillingBundle:Billing:index.html.twig', array(
        	'orderForm' => $form->createView(),
        	'flow' => $flow
        ));
    }









    public function confirmationAction()
    {
        return $this->render('OTSBillingBundle:Billing:confirmation.html.twig');
    }
}
