<?php
namespace AppBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;
use Symfony\Component\Translation\TranslatorInterface;

class TicketOrderFlow extends FormFlow {
	protected $translator;

	public function __construct(TranslatorInterface $translator) {
		$this->translator = $translator;
	}

	protected function loadStepsConfig() {
		$order = $this->translator->trans('core.service.flow.order');
		$info = $this->translator->trans('core.service.flow.info');
		$payment = $this->translator->trans('core.service.flow.payment');

		return array(
			array(
				'label' => $order,
				'form_type' => 'AppBundle\Form\Type\TicketOrderType',
			),
			array(
				'label' => $info,
				'form_type' => 'AppBundle\Form\Type\TicketOrderType',
			),
			array(
				'label' => $payment,
				'form_type' => 'AppBundle\Form\Type\TicketOrderType',
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
