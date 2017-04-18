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
				// flow finished
				/*$em = $this->getDoctrine()->getManager();
				$em->persist($formData);
				$em->flush();*/

				$flow->reset(); // remove step data from the session

				return $this->redirect($this->generateUrl('home')); // redirect when done
			}
		}

    	// Step we're currently at
    	/*$step = 0;

    	$nbTickets = 0;
    	$ticketForms = array();

    	$orderForm = $this->get('form.factory')->createNamed('orderForm', TicketOrderType::class, $order);
    	

    	if('POST' === $request->getMethod()) {
			//first step
			if ($request->request->has($orderForm->getName())) {
		        $orderForm->submit($request->request->get($orderForm->getName()), false);
		        if ($orderForm->isValid()) {
		        	$step = 1;

		        	//we get the number of tickets wanted
		        	$postData = $request->request->get('orderForm');
		        	$nbTickets = $postData['nbTickets'];
		        	
		        	//we dynamically create as many ticket forms as needed
		        	$ticketFormBuilders = $this->generateTicketFormBuilders($nbTickets);
		        	$ticketForms = $this->generateForms($ticketFormBuilders);
		        }
		    }

		    //second step
		    foreach ($ticketFormBuilders as $ticketFormBuilder) {
		    	if ($request->request->has($ticketFormBuilder->getName())) {
			        $step = 1;

				   	//then we check for the step 2 form
				    $ticketFormBuilder->submit($request->request->get($ticketFormBuilder->getName()), false);
			        if ($ticketFormBuilder->isValid()) {
			        	$step = 2;
			        }
			    }
		    }
		}*/

        return $this->render('OTSBillingBundle:Billing:index.html.twig', array(
        	'orderForm' => $form->createView(),
        	'flow' => $flow,
        	/*'step' => $step,
        	'nbTickets' => $nbTickets,
        	'ticketForms' => $ticketForms,*/
        ));
    }
}
