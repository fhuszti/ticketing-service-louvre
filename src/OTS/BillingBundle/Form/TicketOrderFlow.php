<?php
namespace OTS\BillingBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TicketOrderFlow extends FormFlow {
	protected $container;

	public function __construct(ContainerInterface $container) {
		$this->container = $container;
	}

	protected function loadStepsConfig() {
		$translator = $this->container->get('translator');
		$order = $translator->trans('ots_billing.flow.order');
		$info = $translator->trans('ots_billing.flow.info');
		$payment = $translator->trans('ots_billing.flow.payment');

		return array(
			array(
				'label' => $order,
				'form_type' => 'OTS\BillingBundle\Form\TicketOrderType',
			),
			array(
				'label' => $info,
				'form_type' => 'OTS\BillingBundle\Form\TicketOrderType',
			),
			array(
				'label' => $payment,
				'form_type' => 'OTS\BillingBundle\Form\TicketOrderType',
			),
		);
	}

	public function getFormOptions($step, array $options = array()) {
		$options = parent::getFormOptions($step, $options);

		$formData = $this->getFormData();

		if ($step === 2) {
			$options['date'] = $formData->getDate();
			$options['nbTickets'] = $formData->getNbTickets();
			$options['type'] = $formData->getType();
		}

		if ($step === 3) {
			$options['date'] = $formData->getDate();
			$options['nbTickets'] = $formData->getNbTickets();
			$options['type'] = $formData->getType();
			$options['price'] = $formData->getPrice();
		}

		return $options;
	}

}