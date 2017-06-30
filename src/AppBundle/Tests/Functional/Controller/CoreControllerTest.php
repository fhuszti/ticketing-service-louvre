<?php
namespace AppBundle\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CoreControllerTest extends WebTestCase {

	public function testIndex() {
	    $client = static::createClient();

	    $client->request('GET', '/en/');
	    $response = $client->getResponse();

	    $this->assertEquals(200, $response->getStatusCode());
	    $this->assertContains('Online Ticketing Service', $response->getContent());
	}

	public function testLegal() {
	    $client = static::createClient();

	    $client->request('GET', '/en/legal/');
	    $response = $client->getResponse();

	    $this->assertEquals(200, $response->getStatusCode());
	    $this->assertContains('Legal information and Terms of Use', $response->getContent());
	}

	public function testTerms() {
	    $client = static::createClient();

	    $client->request('GET', '/en/terms/');
	    $response = $client->getResponse();

	    $this->assertEquals(200, $response->getStatusCode());
	    $this->assertContains('Terms &amp; Conditions', $response->getContent());
	}







	public function testBooking() {
		$client = static::createClient();

	    $crawler = $client->request('GET', '/en/booking/');
	    //$crawler = $client->followRedirect();

	    $response = $client->getResponse();

	    $this->assertEquals(200, $response->getStatusCode());
	    $this->assertContains('Book online', $response->getContent());

	    // the name of our button is "Next"
	    $form = $crawler->selectButton('Next')->form();

	    $form['appbundle_ticketorder[date]'] = '2019-05-25';
	    $form['appbundle_ticketorder[type]']->select('1');
	    $form['appbundle_ticketorder[nbTickets]'] = '2';
	    $crawler = $client->submit($form);
	    $this->assertEquals(200, $client->getResponse()->getStatusCode());
	}

	public function testConfirmation() {
		$client = static::createClient();

	    $client->request('GET', '/en/booking/confirmation/');
	    $response = $client->getResponse();

	    $this->assertEquals(200, $response->getStatusCode());
	    $this->assertContains('Thank you', $response->getContent());
	}







	public function test404() {
		$client = static::createClient();

	    $client->request('GET', '/sfdsf');
	    $response = $client->getResponse();

	    $this->assertEquals(404, $response->getStatusCode());
	    $this->assertContains('404 Not Found', $response->getContent());
	}
}