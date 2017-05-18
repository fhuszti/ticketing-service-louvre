<?php
namespace OTS\BillingBundle\Service\Mailer;

use OTS\BillingBundle\Entity\TicketOrder;
use Symfony\Component\Translation\TranslatorInterface;

class MailerNotificator {
	protected $mailer;

	protected $twig;

	protected $translator;

	public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, TranslatorInterface $translator) {
		$this->mailer = $mailer;
		$this->twig = $twig;
		$this->translator = $translator;
	}







	/**
	 * MAIL SENDER
	 * -----------
	 */

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
		$subject = $this->translator->trans('ots_billing.mail.subject');

		$cus_email = $order->getCustomer()->getEmail();

		$mail = \Swift_Message::newInstance();

		$imgUrl = $mail->embed(\Swift_Image::fromPath('http://assets.fhuszti.com/louvre/logo.png'));
		$body = $this->renderTemplate($order, $imgUrl);
		
		$mail->setSubject($subject)
			 ->setFrom('contact@fhuszti.com')
			 ->setTo($cus_email)
			 ->setBody(
				 $body,
				 'text/html'
			 );

		$this->mailer->send($mail);
	}
	
	/**
	 * -----------
	 */
}