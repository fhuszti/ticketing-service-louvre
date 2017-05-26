<?php
namespace OTS\BillingBundle\Tests\Unit\Manager;

use OTS\BillingBundle\Manager\OrderManager;
use OTS\BillingBundle\Entity\Ticket;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class OrderManagerTest extends TestCase {
    public function generateOrderManager() {
        $translator =  $this->getMockBuilder('Symfony\Component\Translation\TranslatorInterface')
                           ->disableOriginalConstructor()
                           ->getMock();
        $validator =   $this->getMockBuilder('Symfony\Component\Validator\Validator\RecursiveValidator')
                          ->disableOriginalConstructor()
                          ->getMock();
        $errorReturn = $this->getMockBuilder('OTS\BillingBundle\Service\BillingForm\ErrorReturn')
                            ->disableOriginalConstructor()
                            ->getMock();

        return new OrderManager( $translator, $validator, $errorReturn );
    }

    public function generateTicket($date, $discounted = false) {
        $ticket = new Ticket();
        $ticket->setDiscounted( $discounted );
        $ticket->setBirthDate( $date );

        return $ticket;
    }





    

    public function ticketAssertion($date, $expected) {
        $orderManager = $this->generateOrderManager();

        $ticket = $this->generateTicket($date);
        
        return $this->assertEquals( $expected, $orderManager->checkTicketPrice($ticket) );
    }

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







    public function generateTicketData() {
        $data = array(
            array(
                'date' => new \DateTime('-67 years'),
                'discounted' => false
            ),
            array(
                'date' => new \DateTime('-19 years'),
                'discounted' => true
            ),
            array(
                'date' => new \DateTime('-2 years'),
                'discounted' => false
            )
        );

        return $data;
    }

    public function generateTicketCollection( $tickets = array() ) {
        $ticketsCollection = new ArrayCollection();

        if ( !empty($tickets) ) {
            foreach( $tickets as $ticket ) {
                if ( is_array($ticket) ) {
                    $ticketsCollection[] = $this->generateTicket( $ticket['date'], $ticket['discounted'] );
                }
            }
        }

        return $ticketsCollection;
    }

    public function testManageTotalPrice() {
        $orderManager = $this->generateOrderManager();

        

        //empty test
        $tickets = $this->generateTicketCollection();

        $order = $this->getMockBuilder('OTS\BillingBundle\Entity\TicketOrder')
                      ->disableOriginalConstructor()
                      ->getMock();

        $this->assertEquals( 0, $orderManager->manageTotalPrice($tickets, $order) );



        //filled test
        $tickets = $this->generateTicketCollection( $this->generateTicketData() );

        $order = $this->getMockBuilder('OTS\BillingBundle\Entity\TicketOrder')
                      ->disableOriginalConstructor()
                      ->getMock();

        $order->expects( $this->any() )
              ->method('getType')
              ->will( $this->returnValue(true) );

        $this->assertEquals( 22, $orderManager->manageTotalPrice($tickets, $order) );
    }
}