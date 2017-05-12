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

    public function chargeCustomer($token, $price, $request, $form, $flow) {
    	\Stripe\Stripe::setApiKey("sk_test_tSvs67jePf7WEqZK5dzgrZHS");

    	$stripeInfo = \Stripe\Token::retrieve($token);
 		$email = $stripeInfo->email;

		// Create a Customer:
		$customer = \Stripe\Customer::create(array(
		  	"email" => $email,
		  	"source" => $token,
		));

		try {
			// Charge the Customer:
			$charge = \Stripe\Charge::create(array(
			  	"amount" => $price * 100,
			  	"currency" => "eur",
			  	"customer" => $customer->id
			));
		}
		catch(\Stripe\Error\Card $e) {
		  	// Since it's a decline, \Stripe\Error\Card will be caught
		  	$body = $e->getJsonBody();
		  	$err  = $body['error'];

		  	$request->getSession()->getFlashBag()->add('error', $err['message']);

			$form = $flow->createForm();

			return $this->render('OTSBillingBundle:Billing:index.html.twig', array(
			       'orderForm' => $form->createView(),
			       'flow' => $flow,
			));
		}
		catch (\Stripe\Error\Api $e) {
		  	// Stripe's servers are down!
		  	$request->getSession()->getFlashBag()->add('error', 'Stripe servers seem to be down, please try again later. You have not been charged.');

			$form = $flow->createForm();

			return $this->render('OTSBillingBundle:Billing:index.html.twig', array(
			       'orderForm' => $form->createView(),
			       'flow' => $flow,
			));
		}
		catch (\Stripe\Error\InvalidRequest $e) {
		  	// Invalid parameters were supplied to Stripe's API
		  	$request->getSession()->getFlashBag()->add('error', 'There was an error on our part, we are working hard to fix it. Please try again later. You have not been charged.');

			$form = $flow->createForm();

			return $this->render('OTSBillingBundle:Billing:index.html.twig', array(
			       'orderForm' => $form->createView(),
			       'flow' => $flow,
			));
		}
		catch (\Stripe\Error\Authentication $e) {
		  	// Authentication with Stripe's API failed
		  	// (maybe you changed API keys recently)
		  	$request->getSession()->getFlashBag()->add('error', 'We couldn\'t connect with Stripe\'s servers, please try again later. You have not been charged.');

			$form = $flow->createForm();

			return $this->render('OTSBillingBundle:Billing:index.html.twig', array(
			       'orderForm' => $form->createView(),
			       'flow' => $flow,
			));
		}
		catch (\Stripe\Error\ApiConnection $e) {
		  	// Network communication with Stripe failed
		  	$request->getSession()->getFlashBag()->add('error', 'There was a network error, please try again later. You have not been charged.');

			$form = $flow->createForm();

			return $this->render('OTSBillingBundle:Billing:index.html.twig', array(
			       'orderForm' => $form->createView(),
			       'flow' => $flow,
			));
		}
		catch (\Stripe\Error\Base $e) {
		  	// Display a very generic error to the user, and maybe send
		  	// yourself an email
		  	$request->getSession()->getFlashBag()->add('error', 'There was an error processing your payment. Please try again later. You have not been charged.');

			$form = $flow->createForm();

			return $this->render('OTSBillingBundle:Billing:index.html.twig', array(
			       'orderForm' => $form->createView(),
			       'flow' => $flow,
			));
		}
		catch (Exception $e) {
		  	// Something else happened, completely unrelated to Stripe
		  	$request->getSession()->getFlashBag()->add('error', 'There was an error processing your payment. Please try again later. You have not been charged.');

			$form = $flow->createForm();

			return $this->render('OTSBillingBundle:Billing:index.html.twig', array(
			       'orderForm' => $form->createView(),
			       'flow' => $flow,
			));
		}

    	return array(
    		'customer' => $customer,
    		'charge' => $charge
    	);
    }

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
				$checkout = $this->chargeCustomer( $form->get('checkoutToken')->getData(), $order->getPrice(), $request, $form, $flow );

				//generate and manage necessary entities before persisting
				$this->manageEntities($order, $checkout, $request, $form, $flow);

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
