<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\LightSwitch;

class LightSwitchTest extends PHPUnit_Framework_TestCase {

	public function testFlipReturnsApplication()
	{
		$application = LightSwitch::flip();
		$this->assertTrue($application instanceof Application);
		$this->assertTrue($GLOBALS['__illuminate.app'] === $application);
		unset($GLOBALS['__illuminate.app']);
	}

}