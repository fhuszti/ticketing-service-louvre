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
    public function generateForms ($array) {
    	$ticketFormViews = array();

    	foreach ($array as $formBuilder) {
    		$ticketFormViews[] = $formBuilder->createView();
    	}

    	return $ticketFormViews;
    }

    public function generateTicketFormBuilders($nbTickets) {
    	$ticketForms = array();

    	for ($i = 0; $i < $nbTickets; $i++) {
    		$ticket = new Ticket();
    		$ticketForms[] = $this->get('form.factory')
    							  ->createNamed('ticketForm-'.$i, TicketType::class, $ticket);
    	}

    	return $ticketForms;
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
				$orderType = $order->getType();
				if ( is_null($orderType) )
					$order->setType(false);

				// flow finished
				$em = $this->getDoctrine()->getManager();
				$em->persist($order);
				$em->flush();
/*echo $form->get('checkoutToken')->getData();
echo '<pre>';
var_dump($order);
echo '</pre>';*/
				$flow->reset(); // remove step data from the session

				return $this->redirect($this->generateUrl('ots_billing_home')); // redirect when done
			}
		}

		return $this->render('OTSBillingBundle:Billing:index.html.twig', array(
        	'orderForm' => $form->createView(),
        	'flow' => $flow,
        ));
    }
}
