<?php namespace Illuminate\Foundation\Testing;

use Illuminate\Auth\UserInterface;

class TestCase extends \PHPUnit_Framework_TestCase {

	/**
	 * The Illuminate application instance.
	 *
	 * @var Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * The HttpKernel client instance.
	 *
	 * @var Illuminate\Foundation\Testing\CLient
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
	 * Set the currently logged in user for the application.
	 *
	 * @param  Illuminate\Auth\UserInterface  $user
	 * @param  string  $driver
	 * @return void
	 */
	public function be(UserInterface $user, $driver = null)
	{
		$this->app['auth']->driver($driver)->setUser($user);
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

}