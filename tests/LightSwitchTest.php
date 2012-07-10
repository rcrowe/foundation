<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Lightbulb;

class LightbulbTest extends PHPUnit_Framework_TestCase {

	public function testFlipReturnsApplication()
	{
		$application = Lightbulb::on();
		$this->assertTrue($application instanceof Application);
		$this->assertTrue($GLOBALS['__illuminate.app'] === $application);
		unset($GLOBALS['__illuminate.app']);
	}

}