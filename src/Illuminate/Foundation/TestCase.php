<?php namespace Illuminate\Foundation;

use Symfony\Component\HttpKernel\Client;

class TestCase extends PHPUnit_Framework_TestCase {

	/**
	 * The Illuminate application instance.
	 *
	 * @var Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * The HttpKernel client instance.
	 *
	 * @var Symfony\Component\HttpKernel\Client
	 */
	protected $client;

	/**
	 * Setup the test environment.
	 *
	 * @return void
	 */
	public function setUp()
	{
		$this->app = $this->createApplication();

		$this->client = $this->createClient();
	}

	/**
	 * Create a new HttpKernel client instance.
	 *
	 * @param  array  $server
	 * @return Symfony\Component\HttpKernel\Client
	 */
	protected function createClient(array $server = array())
	{
		return new Client($this->app, $server);
	}

	/**
	 * Execute a request against the test client application.
	 *
	 * @return Symfony\Component\DomCrawler\Crawler
	 */
	public function request()
	{
		// @todo
	}

}