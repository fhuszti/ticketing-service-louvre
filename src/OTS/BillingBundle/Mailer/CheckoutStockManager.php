<?php
namespace OTS\BillingBundle\Mailer;

use OTS\BillingBundle\Entity\Stock;
use Doctrine\ORM\EntityManager;

class CheckoutStockManager {
	protected $em;

	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	//increments the amount of tickets sold today in the stock file
	public function decrementStock($order) {
		$date = $order->getDate();
		$nbTickets = $order->getNbTickets();

		$existingDate = $this->em->getRepository('OTSBillingBundle:Stock')->findBy( array('date' => $date) );

		//if it's the first entry for this date
		if ( empty($existingDate) ) {
			$stockEntity = new Stock();
			$new_stock = 1000 - $nbTickets;

			$stockEntity->setDate($date);
			$stockEntity->setStockLeft($new_stock);

			$this->em->persist($stockEntity);
		}
		else {
			//each date is unique, so one result maximum
			$stockEntity = $existingDate[0];

			$current_stock = $stockEntity->getStockLeft();
			$new_stock = $current_stock - $nbTickets;

			$stockEntity->setStockLeft($new_stock);
		}
	}
}