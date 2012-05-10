<?php

use Illuminate\Foundation\Application;

class ApplicationTest extends Illuminate\Foundation\TestCase {

	public function testCreateMountable()
	{
		$application = new Application;
		$mount = $application->newMountable();
		$this->assertTrue($mount instanceof Illuminate\Foundation\ControllerCollection);
		$this->assertTrue($application === $mount->getApplication());
	}

}