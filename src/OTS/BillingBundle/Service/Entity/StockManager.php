<?php
namespace OTS\BillingBundle\Service\Entity;

use OTS\BillingBundle\Entity\Stock;
use OTS\BillingBundle\Entity\TicketOrder;
use Doctrine\ORM\EntityManager;

class StockManager {
	protected $em;

	private $repo;

	public function __construct(EntityManager $em) {
		$this->em = $em;

		$this->repo = $em->getRepository('OTSBillingBundle:Stock');
	}







	/**
	 * STOCK CHECKER
	 * -------------
	 */

	//return false if there's nothing left in stock for the chosen date, true if everything is okay
	public function checkIfStockOkForDate(TicketOrder $order) {
		$existingDate = $this->repo->findBy( array('date' => $order->getDate()) );

		//if the date entry exists
		if ( array_key_exists(0, $existingDate) ) {
			//if there's not enough stock left to satisfy the order
			if ( $existingDate[0]->getStockLeft() < $order->getNbTickets() )
				return false;
		}

		return true;
    }

    /**
	 * -------------
	 */







	/**
	 * STOCK DECREMENTATOR
	 * -------------------
	 */

	//increments the amount of tickets sold today in the stock file
	public function decrementStock(TicketOrder $order) {
		$date = $order->getDate();
		$nbTickets = $order->getNbTickets();

		$existingDate = $this->repo->findBy( array('date' => $date) );

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

	/**
	 * -------------------
	 */
}