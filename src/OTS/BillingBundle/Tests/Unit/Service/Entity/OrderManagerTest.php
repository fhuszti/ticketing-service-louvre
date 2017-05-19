<?php
namespace OTS\BillingBundle\Tests\Unit\Service\Entity;

use OTS\BillingBundle\Service\Entity\OrderManager;
use OTS\BillingBundle\Entity\Ticket;
use PHPUnit\Framework\TestCase;

class OrderManagerTest extends TestCase {
    
    /*public function testCheckTicketPrice() {
        $dates = array(
        	'toddlerRate' => new \DateTime('-2 years'),
    		'childRate' =>   new \DateTime('-7 years'),
    		'normalRate' =>  new \DateTime('-20 years'),
    		'seniorRate' =>  new \DateTime('-70 years')
        );

        $this->ticketAssertion( $dates['toddlerRate'], 0 );
        $this->ticketAssertion( $dates['childRate'], 8 );
        $this->ticketAssertion( $dates['normalRate'], 16 );
        $this->ticketAssertion( $dates['seniorRate'], 12 );
    }







    public function ticketAssertion($date, $expected) {
    	$orderManager = $this->getMockBuilder("OTS\BillingBundle\Service\Entity\OrderManager")
        					 ->disableOriginalConstructor()
        					 ->getMock();

        $ticket = new Ticket();
		$ticket->setBirthDate( $date );
        
        return $this->assertEquals( $expected, $orderManager->checkTicketPrice($ticket) );
    }*/
}