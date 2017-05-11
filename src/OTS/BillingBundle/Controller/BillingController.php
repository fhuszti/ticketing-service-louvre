<?php

namespace OTS\BillingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use OTS\BillingBundle\Form\TicketOrderType;
use OTS\BillingBundle\Entity\TicketOrder;
use OTS\BillingBundle\Form\TicketType;
use OTS\BillingBundle\Entity\Ticket;

class BillingController extends Controller
{
    public function getUsefulDates() {
    	$currentDate = date('Y-m-d');
    	$normalRateDateTimestamp = strtotime( '-12 years' );
    	$normalRateDate = date('Y-m-d', $normalRateDateTimestamp);
    	$childRateDateTimestamp = strtotime( '-4 years' );
    	$childRateDate = date('Y-m-d', $childRateDateTimestamp);
    	$seniorRateDateTimestamp = strtotime( '-60 years' );
    	$seniorRateDate = date('Y-m-d', $seniorRateDateTimestamp);

    	return array(
    		'current' => $currentDate,
    		'normalRate' => $normalRateDate,
    		'childRate' => $childRateDate,
    		'seniorRate' => $seniorRateDate
    	);
    }

    public function checkTicketPrice($ticket) {
    	$usefulDates = $this->getUsefulDates();
    	$birthdate = $ticket->getBirthDate()->format('Y-m-d');

    	//if below 4 years old
        if ($birthdate > $usefulDates['childRate']) {
            return 0;
        }
        //if between 4 and 12 years old
        else if ($birthdate > $usefulDates['normalRate']) {
            return 8;
        }
        //if between 12 and 60 years old
        else if ($birthdate > $usefulDates['seniorRate']) {
            return 16;
        }
        //more than 60 years old
        else {
            return 12;
        }
    }

    public function checkTotalPrice($tickets, $order) {
    	//we check the price and birthdate of each ticket
		$totalPrice = 0;

		foreach( $tickets as $ticket ) {
			//price 
			$ticketPrice = $this->checkTicketPrice($ticket);
			if ($ticket->getDiscounted())
				$ticketPrice = 10;
			//if it's a Half-Day ticket
			if (!$order->getType())
				$ticketPrice *= 0.5;

			$ticket->setPrice($ticketPrice);

			$totalPrice += $ticketPrice;
		}

		return $totalPrice;
    }

    public function chargeCustomer($token, $price) {
    	\Stripe\Stripe::setApiKey("sk_test_tSvs67jePf7WEqZK5dzgrZHS");

    	$stripeInfo = \Stripe\Token::retrieve($token);
 		$email = $stripeInfo->email;

		// Create a Customer:
		$customer = \Stripe\Customer::create(array(
		  	"email" => $email,
		  	"source" => $token,
		));

		// Charge the Customer:
		$charge = \Stripe\Charge::create(array(
		  	"amount" => $price * 100,
		  	"currency" => "eur",
		  	"customer" => $customer->id
		));

    	return array(
    		'customer' => $customer,
    		'charge' => $charge
    	);
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
				//to prevent a bug where order type would be null instead of false when Half-Day option chosen
				$orderType = $order->getType();
				if ( is_null($orderType) )
					$order->setType(false);

				$totalPrice = $this->checkTotalPrice( $order->getTickets(), $order );
				
				//if it's free, problem
				if ($totalPrice === 0) {
					$request->getSession()->getFlashBag()->add('error', 'You can\'t pay 0€.');

					$form = $flow->createForm();

					return $this->render('OTSBillingBundle:Billing:index.html.twig', array(
					       'orderForm' => $form->createView(),
					       'flow' => $flow,
					));
				}
				//if not, we're good to go
				$order->setPrice($totalPrice);

				//we charge the customer
				$checkout = $this->chargeCustomer( $form->get('checkoutToken')->getData(), $order->getPrice() );

				// flow finished
				$em = $this->getDoctrine()->getManager();
				$em->persist($order);
				$em->flush();

				$flow->reset(); // remove step data from the session

				return $this->redirect($this->generateUrl('ots_billing_thanks')); // redirect when done
			}
		}

		return $this->render('OTSBillingBundle:Billing:index.html.twig', array(
        	'orderForm' => $form->createView(),
        	'flow' => $flow,
        ));
    }









    public function confirmationAction()
    {
        return $this->render('OTSBillingBundle:Billing:confirmation.html.twig');
    }
}
