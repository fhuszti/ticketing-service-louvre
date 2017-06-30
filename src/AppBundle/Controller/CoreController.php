<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\TicketOrder;
use AppBundle\Event\PlatformEvents;
use AppBundle\Event\SuccessfulCheckoutEvent;

class CoreController extends Controller
{
    /**
     * @Route("/", name="core_home")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->render('core/index.html.twig');
    }

    /**
     * @Route("/legal", name="core_legal")
     * @Route("legal", name="core_legal")
     * @Route("/legal/", name="core_legal")
     * @Route("legal/", name="core_legal")
     * @Method("GET")
     */
    public function legalAction()
    {
        return $this->render('core/legal.html.twig');
    }

    /**
     * @Route("/terms", name="core_terms")
     * @Route("terms", name="core_terms")
     * @Route("/terms/", name="core_terms")
     * @Route("terms/", name="core_terms")
     * @Method("GET")
     */
    public function termsAction()
    {
        return $this->render('core/terms.html.twig');
    }







    /**
	 * @Route("/booking", name="core_booking")
     * @Route("booking", name="core_booking")
     * @Route("/booking/", name="core_booking")
     * @Route("booking/", name="core_booking")
     * @Method({"GET", "POST"})
	 */
    public function bookingAction(Request $request)
    {
    	$order = new TicketOrder();
    	
    	$flow = $this->get('app.form.flow.ticketOrder'); // must match the flow's service id
		$flow->bind($order);

		// form of the current step
		$form = $flow->createForm();
		if ($flow->isValid($form)) {
			$flow->saveCurrentStepData($form);

			if ($flow->nextStep()) {
				// form for the next step
				$form = $flow->createForm();
			} else {
				$checkoutToken = $form->get('checkoutToken')->getData();

				//we check if everything is alright with the order
				$orderSubmissionHandler = $this->get( 'app.booking_form.order_submission_handler' );
				$error = $orderSubmissionHandler->processSubmittedOrder($order, $checkoutToken);
				//if there's any error, we add the message to the flashbag and we abort the controller action
				if ( $error[0] ) {
					//$error[1] is an array if multiple errors where returned at the same time
					if ( is_array($error[1]) ) {
						foreach ($error[1] as $val) {
							$request->getSession()->getFlashBag()->add('error', $val);
						}
					}
					//else it's just a single string
					else {
						$request->getSession()->getFlashBag()->add('error', $error[1]);
					}
				            
		            $form = $flow->createForm();
		            return $this->render('core/booking.html.twig', array(
		                    'orderForm' => $form->createView(),
		                    'flow' => $flow,
		                )
		            );
				}

				// flow finished
				$em = $this->getDoctrine()->getManager();
				$em->persist($order);

				//we dispatch the event associated with a successful checkout
				$checkoutEvent = new SuccessfulCheckoutEvent($order);
				$this->get('event_dispatcher')->dispatch(PlatformEvents::SUCCESSFUL_CHECKOUT, $checkoutEvent);

				//we don't forget to flush
				$em->flush();

				$flow->reset(); // remove step data from the session

				return $this->redirect($this->generateUrl('core_confirmation')); // redirect when done
			}
		}

		return $this->render('core/booking.html.twig', array(
        	'orderForm' => $form->createView(),
        	'flow' => $flow
        ));
    }

	/**
	 * @Route("/booking/confirmation", name="core_confirmation")
     * @Route("/booking/confirmation/", name="core_confirmation")
     * @Method("GET")
	 */
    public function confirmationAction()
    {
        return $this->render('core/confirmation.html.twig');
    }
}
