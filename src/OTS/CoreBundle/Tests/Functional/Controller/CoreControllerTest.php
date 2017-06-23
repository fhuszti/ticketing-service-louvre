<?php
namespace OTS\CoreBundle\Tests\Functional\Controller;

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

	    $client->request('GET', '/legal/en/');
	    $response = $client->getResponse();

	    $this->assertEquals(200, $response->getStatusCode());
	    $this->assertContains('Legal information and Terms of Use', $response->getContent());
	}

	public function testTerms() {
	    $client = static::createClient();

	    $client->request('GET', '/terms/en/');
	    $response = $client->getResponse();

	    $this->assertEquals(200, $response->getStatusCode());
	    $this->assertContains('Terms &amp; Conditions', $response->getContent());
	}

	public function test404() {
		$client = static::createClient();

	    $client->request('GET', '/sfdsf');
	    $response = $client->getResponse();

	    $this->assertEquals(404, $response->getStatusCode());
	    $this->assertContains('404 Not Found', $response->getContent());
	}
}