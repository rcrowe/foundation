<?php namespace Illuminate\Foundation;

class TestCase extends \Silex\WebTestCase {

	/**
	 * Setup the test environment.
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
		//
	}

}