<?php
namespace OTS\BillingBundle\Service\BillingForm;

use OTS\BillingBundle\Form\TicketOrderFlow;
use Symfony\Component\HttpFoundation\RequestStack;

class ErrorReturn {
	protected $request;

    protected $twig;

	public function __construct(RequestStack $requestStack, \Twig_Environment $twig) {
		$this->request = $requestStack->getCurrentRequest();
		$this->twig = $twig;
	}







	public function returnToFormWithError(TicketOrderFlow $flow, $error) {
		$this->request->getSession()->getFlashBag()->add('error', $error);

		$form = $flow->createForm();

		return $this->twig->render('OTSBillingBundle:Billing:index.html.twig', array(
				'orderForm' => $form->createView(),
				'flow' => $flow,
			)
		);
	}
}
