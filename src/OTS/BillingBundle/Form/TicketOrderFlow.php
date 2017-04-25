<?php
namespace OTS\BillingBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;

class TicketOrderFlow extends FormFlow {
	protected $allowDynamicStepNavigation = true;

	protected function loadStepsConfig() {
		return array(
			array(
				'label' => '1. Order',
				'form_type' => 'OTS\BillingBundle\Form\TicketOrderType',
			),
			array(
				'label' => '2. Informations',
				'form_type' => 'OTS\BillingBundle\Form\TicketOrderType',
			),
			array(
				'label' => '3. Payment',
			),
		);
	}

	public function getFormOptions($step, array $options = array()) {
		$options = parent::getFormOptions($step, $options);

		$formData = $this->getFormData();

		if ($step === 2) {
			$options['nbTickets'] = $formData->getNbTickets();
		}

		return $options;
	}

}