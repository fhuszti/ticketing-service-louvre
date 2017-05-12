<?php
namespace OTS\BillingBundle\Mailer;

use OTS\BillingBundle\Entity\TicketOrder;

class CheckoutNotificator {
	protected $mailer;

	protected $twig;

	public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig) {
		$this->mailer = $mailer;
		$this->twig = $twig;
	}

	//render an html template
	public function renderTemplate($order, $imgUrl) {
		return $this->twig->render(
			'emails/confirmation.html.twig',
			array(
				'order' => $order,
				'imgUrl' => $imgUrl
			)
		);
	}

	//send an email to the client containing his tickets
	public function sendTicketsByEmail(TicketOrder $order) {
		$cus_email = $order->getCustomer()->getEmail();

		$mail = \Swift_Message::newInstance();

		$imgUrl = $mail->embed(\Swift_Image::fromPath('http://assets.fhuszti.com/louvre/logo.png'));
		$body = $this->renderTemplate($order, $imgUrl);
		
		$mail->setSubject('Thank you for booking online - Musée du Louvre')
			 ->setFrom('contact@fhuszti.com')
			 ->setTo($cus_email)
			 ->setBody(
				 $body,
				 'text/html'
			 );

		$this->mailer->send($mail);
	}
}