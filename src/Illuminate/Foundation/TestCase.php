<?php namespace Illuminate\Foundation;

class TestCase extends \Silex\WebTestCase {

	/**
	 * Setup the testing environment.
	 *
	 * @return void
	 */
	public function setUp()
	{
		LightSwitch::flip();

		parent::setUp();
	}

	/**
	 * Create a new Illuminate application instance.
	 *
	 * @return Illuminate\Foundation\Application
	 */
	public function createApplication()
	{
		$application = new Application;

		$application['debug'] = true;

		return $application;
	}

}