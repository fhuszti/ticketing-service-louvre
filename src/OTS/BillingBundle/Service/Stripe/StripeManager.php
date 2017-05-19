<?php
namespace OTS\BillingBundle\Service\Stripe;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpKernel\Exception;
use OTS\BillingBundle\Form\TicketOrderFlow;
use OTS\BillingBundle\Service\BillingForm\ErrorReturn;

class StripeManager {
	protected $translator;

	protected $errorReturnManager;

	public function __construct(TranslatorInterface $translator, ErrorReturn $errorReturnManager) {
		$this->translator = $translator;

		$this->errorReturnManager = $errorReturnManager;

		\Stripe\Stripe::setApiKey("sk_test_tSvs67jePf7WEqZK5dzgrZHS");
	}







	/**
	 * CUSTOMER MANAGEMENT
	 * -------------------
	 */

	//return a Stripe Token object
	public function retrieveStripeInfo($token) {
		return \Stripe\Token::retrieve($token);
	}

	//extract the email from a Stripe Token Object and generate a new customer
	public function generateCustomer($token) {
		$stripeInfo = $this->retrieveStripeInfo($token);

		// return a Customer
		return \Stripe\Customer::create(
			array(
			  "email" => $stripeInfo->email,
			  "source" => $token,
			)
		);
	}

	/**
	 * -------------------
	 */







	/**
	 * CHARGE MANAGEMENT
	 * -----------------
	 */

	public function chargeCustomer($cus_id, $price, TicketOrderFlow $flow) {
    	try {
	    	// Charge the Customer
			$charge = \Stripe\Charge::create(array(
			  	"amount" => $price * 100,
			  	"currency" => "eur",
			  	"customer" => $cus_id
			));

    		return $charge;
		}
		catch(\Stripe\Error\Card $e) {
		  	// Since it's a decline, \Stripe\Error\Card will be caught
		  	$body = $e->getJsonBody();
		  	$err  = $body['error'];

		  	$this->errorReturnManager->returnToFormWithError($flow, $err['message']);
		}
		catch (\Stripe\Error\Api $e) {
		  	$error = $this->translator->trans('ots_billing.controller.charge.api');

		  	// Stripe's servers are down!
		  	$this->errorReturnManager->returnToFormWithError($flow, $error);
		}
		catch (\Stripe\Error\InvalidRequest $e) {
		  	$error = $this->translator->trans('ots_billing.controller.charge.invalid_request');

		  	// Invalid parameters were supplied to Stripe's API
		  	$this->errorReturnManager->returnToFormWithError($flow, $error);
		}
		catch (\Stripe\Error\Authentication $e) {
		  	$error = $this->translator->trans('ots_billing.controller.charge.authentication');

		  	// Authentication with Stripe's API failed
		  	// (maybe you changed API keys recently)
		  	$this->errorReturnManager->returnToFormWithError($flow, $error);
		}
		catch (\Stripe\Error\ApiConnection $e) {
		  	$error = $this->translator->trans('ots_billing.controller.charge.api_connection');

		  	// Network communication with Stripe failed
		  	$this->errorReturnManager->returnToFormWithError($flow, $error);
		}
		catch (\Stripe\Error\Base $e) {
		  	$error = $this->translator->trans('ots_billing.controller.charge.base');

		  	// Display a very generic error to the user
		  	$this->errorReturnManager->returnToFormWithError($flow, $error);
		}
		catch (Exception $e) {
		  	$error = $this->translator->trans('ots_billing.controller.charge.base');

		  	// Something else happened, completely unrelated to Stripe
		  	$this->errorReturnManager->returnToFormWithError($flow, $error);
		}
    }

    /**
	 * -----------------
	 */
}
