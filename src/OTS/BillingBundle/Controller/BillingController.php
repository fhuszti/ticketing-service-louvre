<?php

namespace OTS\BillingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use OTS\BillingBundle\Form\TicketOrderType;
use OTS\BillingBundle\Entity\TicketOrder;

class BillingController extends Controller
{
    public function indexAction()
    {
    	$order = new TicketOrder();

    	$orderForm = $this->get('form.factory')->createNamed('orderForm', TicketOrderType::class, $order);

        return $this->render('OTSBillingBundle:Billing:index.html.twig', array(
        	'orderForm' => $orderForm->createView(),
        ));
    }
}
