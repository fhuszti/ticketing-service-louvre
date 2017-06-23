<?php
namespace OTS\BillingBundle\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BillingControllerTest extends WebTestCase {

	public function testIndex() {
		$client = static::createClient();

	    $crawler = $client->request('GET', '/booking/en/');
	    //$crawler = $client->followRedirect();

	    $response = $client->getResponse();

	    $this->assertEquals(200, $response->getStatusCode());
	    $this->assertContains('Book online', $response->getContent());

	    // the name of our button is "Next"
	    $form = $crawler->selectButton('Next')->form();

	    $form['ots_billingbundle_ticketorder[date]'] = '2019-05-25';
	    $form['ots_billingbundle_ticketorder[type]']->select('1');
	    $form['ots_billingbundle_ticketorder[nbTickets]'] = '2';
	    $crawler = $client->submit($form);
	    $this->assertEquals(200, $client->getResponse()->getStatusCode());
	}

	public function testConfirmation() {
		$client = static::createClient();

	    $client->request('GET', '/booking/confirmation/en/');
	    $response = $client->getResponse();

	    $this->assertEquals(200, $response->getStatusCode());
	    $this->assertContains('Thank you', $response->getContent());
	}
}