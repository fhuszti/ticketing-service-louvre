<?php
namespace OTS\BillingBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;

class TicketOrderFlow extends FormFlow {
	protected $allowDynamicStepNavigation = true;

	protected function loadStepsConfig() {
		return array(
			array(
				'label' => 'Order',
				'form_type' => 'OTS\BillingBundle\Form\TicketOrderType',
			),
			array(
				'label' => 'Informations',
				'form_type' => 'OTS\BillingBundle\Form\TicketOrderType',
			),
			array(
				'label' => 'Payment',
			),
		);
	}

}