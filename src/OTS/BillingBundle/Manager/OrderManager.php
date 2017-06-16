<?php
namespace OTS\BillingBundle\Manager;

use OTS\BillingBundle\Entity\TicketOrder;
use OTS\BillingBundle\Entity\Ticket;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use OTS\BillingBundle\Form\TicketOrderFlow;
use Symfony\Component\HttpFoundation\RequestStack;
use OTS\BillingBundle\Service\BillingForm\ErrorReturn;

class OrderManager {
	protected $translator;

    protected $validator;

    protected $request;

    protected $twig;

    protected $errorReturn;

	public function __construct(TranslatorInterface $translator, RecursiveValidator $validator, RequestStack $requestStack, \Twig_Environment $twig, ErrorReturn $errorReturn) {
		$this->translator = $translator;

        $this->validator = $validator;

        $this->request = $requestStack->getCurrentRequest();
        $this->twig = $twig;

        $this->errorReturn = $errorReturn;
	}





    

	/**
	 * ORDER TYPE
     * ----------
	 */

	//set order type to fix a bug where it'll be returned as null from the form, instead of false
    public function manageOrderType(TicketOrder $order) {
    	$orderType = $order->getType();
		
		if ( is_null($orderType) )
			$order->setType(false);
    }

    /**
     * ----------
     */







    /**
     * ORDER PRICE
     * -----------
     */

    //return an array of dates useful to calculate a ticket's price
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

    //return the price of the given ticket depending on the birthdate
    public function checkTicketPrice(Ticket $ticket) {
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

    //check the price for each ticket and return their total
    public function manageTotalPrice(ArrayCollection $tickets, TicketOrder $order) {
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

    //set the total order price depending on visitors birthdate
    public function manageOrderPrice(TicketOrder $order, TicketOrderFlow $flow) {
    	$error = $this->translator->trans('ots_billing.controller.order_price.error');

    	$totalPrice = $this->manageTotalPrice( $order->getTickets(), $order );
		
		//if it's free, problem
		if ($totalPrice === 0) {
			$this->request->getSession()->getFlashBag()->add('error', $error);
            $form = $flow->createForm();
            return $this->twig->render('OTSBillingBundle:Billing:index.html.twig', array(
                    'orderForm' => $form->createView(),
                    'flow' => $flow,
                )
            );
		}
				
		//we set the correct order price
		$order->setPrice($totalPrice);
    }

    /**
     * -----------
     */







    /**
     * ORDER REFERENCE
     * ---------------
     */

    //generate a random reference code for the order
	public function manageOrderReference(TicketOrder $order) {
		$factory = new \RandomLib\Factory;
		$generator = $factory->getLowStrengthGenerator();

		//we generate a random string and set it as the order reference
		$reference = $generator->generateString(15, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

		$order->setReference($reference);
	}

	/**
     * ---------------
     */
    






    /**
     * ORDER VALIDATION
     * -----------------------
     */

    //check whether the Order entity passed is valid
    public function validateOrderPreCharge(TicketOrder $order, TicketOrderFlow $flow) {
        $errors = $this->validator->validate($order, null, array('pre-charge'));
        
        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            //we clean up the expression that comes up
            //originally looks something like :
            //Object(OTS\BillingBundle\Entity\TicketOrder).tickets[0].birthDate: Une date de naissance doit être renseignée. (code c1051bb4-d103-4f74-8988-acbcafc7fdc3)
            $splitError = preg_split("/[([\]:]/", $errorsString, null, PREG_SPLIT_NO_EMPTY);
            $ticketNumber = 1 + (int) $splitError[2];

            return "Ticket n°".$ticketNumber." : ".$splitError[4];
        }

        return '';
    }

    //check whether the Order entity passed is valid
    public function validateOrder(TicketOrder $order, TicketOrderFlow $flow) {
        $errors = $this->validator->validate($order);
        
        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return $errorsString;
        }

        return '';
    }

    /**
     * -----------------------
     */
    






    /**
     * GENERAL
     * -------
     */

    //call order setup methods
    public function manageOrder(TicketOrder $order, TicketOrderFlow $flow) {
        //first we check if the data we got is nice and clean
        $error = $this->validateOrderPreCharge($order, $flow);
        if ( $error !== '' )
            return $error;

        //to prevent a bug where order type would be null instead of false when Half-Day option chosen
        $this->manageOrderType($order);
                
        //set the total order price depending on visitors birthdate
        $this->manageOrderPrice($order, $flow);
                
        //generate a random reference code for the order
        $this->manageOrderReference($order);

        return '';
    }

    /**
     * -------
     */
}
