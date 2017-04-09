<?php

namespace OTS\BillingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use OTS\BillingBundle\Form\TicketOrderType;
use OTS\BillingBundle\Entity\TicketOrder;
use OTS\BillingBundle\Form\TicketType;
use OTS\BillingBundle\Entity\Ticket;

class BillingController extends Controller
{
    public function indexAction()
    {
    	$order = new TicketOrder();
    	$ticket = new Ticket();

    	$orderForm = $this->get('form.factory')->createNamed('orderForm', TicketOrderType::class, $order);
    	$ticketForm = $this->get('form.factory')->createNamed('ticketForm', TicketType::class, $ticket);

        return $this->render('OTSBillingBundle:Billing:index.html.twig', array(
        	'orderForm' => $orderForm->createView(),
        	'ticketForm' => $ticketForm->createView(),
        ));
    }
}
