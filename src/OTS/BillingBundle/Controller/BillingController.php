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
    public function indexAction(Request $request)
    {
    	$order = new TicketOrder();
    	$ticket = new Ticket();
    	// Step we're currently at
    	$step = 0;

    	$orderForm = $this->get('form.factory')->createNamed('orderForm', TicketOrderType::class, $order);
    	$ticketForm = $this->get('form.factory')->createNamed('ticketForm', TicketType::class, $ticket);

    	if('POST' === $request->getMethod()) {
			if ($request->request->has($orderForm->getName())) {
		        $orderForm->submit($request->request->get($orderForm->getName()), false);
		        if ($orderForm->isValid()) {
		        	$step = 1;

		        	$orderForm = $this->get('form.factory')->createNamed('orderForm', TicketOrderType::class, $order);
		        }
		    }

		    if ($request->request->has($ticketForm->getName())) {
		        $ticketForm->submit($request->request->get($ticketForm->getName()), false);
		        if ($ticketForm->isValid()) {
		        	$step = 2;
		        } 
		    }
		}

        return $this->render('OTSBillingBundle:Billing:index.html.twig', array(
        	'orderForm' => $orderForm->createView(),
        	'ticketForm' => $ticketForm->createView(),
        	'step' => $step,
        ));
    }
}
