<?php
namespace OTS\BillingBundle\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BillingControllerTest extends WebTestCase {

	public function testIndex() {
		$client = static::createClient();

	    $crawler = $client->request('GET', '/en/booking');
	    $response = $client->getResponse();

	    $this->assertEquals(200, $response->getStatusCode());

	    // the name of our button is "Next"
	    /*$form = $crawler->selectButton('Next')->form();

	    $crawler = $client->submit($form);
	    $this->assertEquals(200, $client->getResponse()->getStatusCode());
	    $this->assertRegexp(
	        '/This value should not be blank/',
	        $client->getResponse()->getContent()
	    );*/
	}

	public function testConfirmation() {
		$client = static::createClient();

	    $client->request('GET', '/en/booking/confirmation');
	    $response = $client->getResponse();

	    $this->assertEquals(200, $response->getStatusCode());
	}
}