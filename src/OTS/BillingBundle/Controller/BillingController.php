<?php

namespace OTS\BillingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use OTS\BillingBundle\Form\TicketOrderType;
use OTS\BillingBundle\Entity\TicketOrder;
use OTS\BillingBundle\Form\TicketType;
use OTS\BillingBundle\Entity\Ticket;
use OTS\BillingBundle\Entity\Customer;
use OTS\BillingBundle\Entity\Charge;
use OTS\BillingBundle\Entity\Stock;
use OTS\BillingBundle\Event\PlatformEvents;
use OTS\BillingBundle\Event\SuccessfulCheckoutEvent;

class BillingController extends Controller
{
    public function manageEntities($order, $checkout, $request, $form, $flow) {
    	$customer = new Customer();
    	$customer->setStripeId($checkout['customer']->id);
    	$customer->setEmail($checkout['customer']->email);

    	$charge = new Charge();
    	$charge->setAmount($checkout['charge']->amount);
    	$charge->setCurrency($checkout['charge']->currency);

    	//Now we put the entities all together
    	$customer->addCharge($charge);
    	$order->setCustomer($customer);
    	$order->setCharge($charge);

    	//and we check if everything is ok on $order
    	//as the underlying entity has a relation with every other entities
    	$validator = $this->get('validator');
	    $errors = $validator->validate($order);
	    if (count($errors) > 0) {
	        $errorsString = (string) $errors;

	        $request->getSession()->getFlashBag()->add('error', $errorsString);

			$form = $flow->createForm();

			return $this->render('OTSBillingBundle:Billing:index.html.twig', array(
			       'orderForm' => $form->createView(),
			       'flow' => $flow,
			));
	    }
    }

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
				//we get the services we're gonna use
				$stockManager = $this->get('ots_billing.stock.stock_manager');
				$translator = $this->get('translator');
				$orderManager = $this->get('ots_billing.order.order_manager');
				$chargeManager = $this->get('ots_billing.stripe_charge.charge_manager');

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

				//to prevent a bug where order type would be null instead of false when Half-Day option chosen
				$orderManager->manageOrderType($order);
				//set the total order price depending on visitors birthdate
				$orderManager->manageOrderPrice($order, $form, $flow);
				//generate a random reference code for the order
				$orderManager->manageOrderReference($order);

				//we generate the customer
				$customer = $chargeManager->generateCustomer( $form->get('checkoutToken')->getData() );
				//we charge the customer
				$checkout = $chargeManager->chargeCustomer( $customer->id, $order->getPrice(), $form, $flow );

				//generate and manage necessary entities before persisting
				$this->manageEntities($order, $customer, $charge, $request, $form, $flow);

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
