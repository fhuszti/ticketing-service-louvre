<?php
namespace OTS\BillingBundle\Tests\Unit\Manager;

use OTS\BillingBundle\Manager\OrderManager;
use OTS\BillingBundle\Entity\Ticket;
use PHPUnit\Framework\TestCase;

class OrderManagerTest extends TestCase {
    
    public function testCheckTicketPrice() {
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
    	$translator =  $this->getMockBuilder('Symfony\Component\Translation\TranslatorInterface')
                           ->disableOriginalConstructor()
                           ->getMock();
        $validator =   $this->getMockBuilder('Symfony\Component\Validator\Validator\RecursiveValidator')
                          ->disableOriginalConstructor()
                          ->getMock();
        $errorReturn = $this->getMockBuilder('OTS\BillingBundle\Service\BillingForm\ErrorReturn')
                            ->disableOriginalConstructor()
                            ->getMock();

        $orderManager = new OrderManager( $translator, $validator, $errorReturn );

        $ticket = new Ticket();
		$ticket->setBirthDate( $date );
        
        return $this->assertEquals( $expected, $orderManager->checkTicketPrice($ticket) );
    }
}