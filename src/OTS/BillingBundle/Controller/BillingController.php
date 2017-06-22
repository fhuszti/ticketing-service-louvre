<?php

namespace OTS\BillingBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use OTS\BillingBundle\Entity\TicketOrder;
use OTS\BillingBundle\Event\PlatformEvents;
use OTS\BillingBundle\Event\SuccessfulCheckoutEvent;

class BillingController extends Controller
{
    /**
	 * @Route("/booking/{_locale}", requirements={"_locale" = "|fr|en"}, defaults={"_locale"="fr"}, name="ots_billing_home")
	 * @Route("/booking/{_locale}/", requirements={"_locale" = "|fr|en"}, defaults={"_locale"="fr"}, name="ots_billing_home")
     * @Method({"GET", "POST"})
	 */
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
				$checkoutToken = $form->get('checkoutToken')->getData();

				//we check if everything is alright with the order
				$orderSubmissionHandler = $this->get( 'ots_billing.billing_form.order_submission_handler' );
				$error = $orderSubmissionHandler->processSubmittedOrder($order, $flow, $checkoutToken);
				//if there's any error, we add the message to the flashbag and we abort the controller action
				if ( $error !== '' ) {
					//$error is an array if multiple errors where returned at the same time
					if ( is_array($error) ) {
						foreach ($error as $val) {
							$request->getSession()->getFlashBag()->add('error', $val);
						}
					}
					//else it's just a single string
					else
						$request->getSession()->getFlashBag()->add('error', $error);
				            
		            $form = $flow->createForm();
		            return $this->render('OTSBillingBundle:Billing:index.html.twig', array(
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

				return $this->redirect($this->generateUrl('ots_billing_thanks')); // redirect when done
			}
		}

		return $this->render('OTSBillingBundle:Billing:index.html.twig', array(
        	'orderForm' => $form->createView(),
        	'flow' => $flow
        ));
    }









    /**
	 * @Route("/booking/confirmation/{_locale}", requirements={"_locale" = "|fr|en"}, defaults={"_locale"="fr"}, name="ots_billing_thanks")
	 * @Route("/booking/confirmation/{_locale}/", requirements={"_locale" = "|fr|en"}, defaults={"_locale"="fr"}, name="ots_billing_thanks")
     * @Method("GET")
	 */
    public function confirmationAction()
    {
        return $this->render('OTSBillingBundle:Billing:confirmation.html.twig');
    }
}
