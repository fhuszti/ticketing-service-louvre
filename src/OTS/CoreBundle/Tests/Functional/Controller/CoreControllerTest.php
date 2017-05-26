<?php
namespace OTS\CoreBundle\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CoreControllerTest extends WebTestCase {

	public function testIndex() {
	    $client = static::createClient();

	    $client->request('GET', '/en/');
	    $response = $client->getResponse();

	    $this->assertEquals(200, $response->getStatusCode());
	}

	public function testLegal() {
	    $client = static::createClient();

	    $client->request('GET', '/en/legal');
	    $response = $client->getResponse();

	    $this->assertEquals(200, $response->getStatusCode());
	}

	public function testTerms() {
	    $client = static::createClient();

	    $client->request('GET', '/en/terms');
	    $response = $client->getResponse();

	    $this->assertEquals(200, $response->getStatusCode());
	}

	public function test404() {
		$client = static::createClient();

	    $client->request('GET', '/en/sfdsf');
	    $response = $client->getResponse();

	    $this->assertEquals(404, $response->getStatusCode());
	}
}