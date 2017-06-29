<?php
namespace OTS\BillingBundle\Manager;

use OTS\BillingBundle\Entity\TicketOrder;
use OTS\BillingBundle\Entity\Ticket;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\RecursiveValidator;

class OrderManager {
	protected $translator;

    protected $validator;

	public function __construct(TranslatorInterface $translator, RecursiveValidator $validator) {
		$this->translator = $translator;

        $this->validator = $validator;
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
        if ( $orderType === '1' )
            $order->setType(true);
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
    public function manageOrderPrice(TicketOrder $order) {
    	$error = $this->translator->trans('ots_billing.controller.order_price.error');

    	$totalPrice = $this->manageTotalPrice( $order->getTickets(), $order );
		
		//if it's free, problem
		if ($totalPrice === 0) {
			return $error;
		}
				
		//we set the correct order price
		$order->setPrice($totalPrice);

        return;
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
    public function validateOrderPreCharge(TicketOrder $order) {
        $errors = $this->validator->validate($order, null, array('pre-charge'));
        
        if (count($errors) > 0) {
            //then we add them all in an array for display
            $messages = array();
            $i = 0;
            while ( $errors->has($i) == 1 ) {
                $messages[] = $errors->get($i)->getMessage();
                
                $i++;
            }

            return $messages;
        }

        return;
    }

    //check whether the Order entity passed is valid
    public function validateOrder(TicketOrder $order) {
        $errors = $this->validator->validate($order);
        
        if (count($errors) > 0) {
            //then we add them all in an array for display
            $messages = array();
            $i = 0;
            while ( $errors->has($i) == 1 ) {
                $messages[] = $errors->get($i)->getMessage();
                
                $i++;
            }

            return $messages;
        }

        return;
    }

    /**
     * -----------------------
     */
    






    /**
     * GENERAL
     * -------
     */

    //call order setup methods
    public function manageOrder(TicketOrder $order) {
        //first we have to sanitize order type
        //as we get either "null" or "1" and we want "false" or "true"
        $this->manageOrderType($order);

        //then we check if the data we got is nice and clean
        $error = $this->validateOrderPreCharge($order);
        if ( !is_bool($error) )
            return $error;
                
        //set the total order price depending on visitors birthdate
        $error = $this->manageOrderPrice($order);
        if ( !is_bool($error) )
            return $error;
                
        //generate a random reference code for the order
        $this->manageOrderReference($order);

        return;
    }

    /**
     * -------
     */
}
