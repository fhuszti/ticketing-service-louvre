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
    	$validator = $this->get('validator');
    	
    	$flow = $this->get('app.form.flow.ticketOrder'); // must match the flow's service id
		$flow->bind($order);

		// form of the current step
		$form = $flow->createForm();
		if ($flow->isValid($form)) {
			//validating data for step 1
			if ( $flow->getCurrentStepNumber() === 1 ) {
				$this->get('app.manager.order_manager')->manageOrderType($order);

				$errors = $validator->validate($order, null, array('step1'));
		        
		        //if there's any problem with step 1, we abort and display an error
		        if (count($errors) > 0) {
		            //we add all error messages to flashbag
		            $i = 0;
		            while ( $errors->has($i) == 1 ) {
		                $request->getSession()->getFlashBag()->add( 'error', $errors->get($i)->getMessage() );
		                
		                $i++;
		            }

		            $form = $flow->createForm();
		            return $this->render('core/booking.html.twig', array(
		                    'orderForm' => $form->createView(),
		                    'flow' => $flow,
		                )
		            );
		        }
			}

			//validating data for step 2
			if ( $flow->getCurrentStepNumber() === 2 ) {
				$this->get('app.manager.order_manager')->manageOrderType($order);

				$errors = $validator->validate($order, null, array('pre-charge'));
		        
		        //if there's any problem with step 2, we abort and display an error
		        if (count($errors) > 0) {
		            //we add all error messages to flashbag
		            $i = 0;
		            while ( $errors->has($i) == 1 ) {
		                $request->getSession()->getFlashBag()->add( 'error', $errors->get($i)->getMessage() );
		                
		                $i++;
		            }

		            $form = $flow->createForm();
		            return $this->render('core/booking.html.twig', array(
		                    'orderForm' => $form->createView(),
		                    'flow' => $flow,
		                )
		            );
		        }
			}

	        //whatever the current step, we also want to know if there's enough tickets left in stock
	        if ( !$this->get('app.manager.stock_manager')->checkIfStockOkForDate($order) ) {
	        	$errString = $this->get('translator')->trans('core.service.stock.error');
	        	$request->getSession()->getFlashBag()->add('error', $errString);

	        	$form = $flow->createForm();
	            return $this->render('core/booking.html.twig', array(
	                    'orderForm' => $form->createView(),
	                    'flow' => $flow,
	                )
	            );
	        }

			//it's all good, we save data
			$flow->saveCurrentStepData($form);

			//is there another step after this one ?
			if ($flow->nextStep()) {
				// form for the next step
				$form = $flow->createForm();
			} 
			else {
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
