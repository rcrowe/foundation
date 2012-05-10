<?php

use Illuminate\Foundation\Application;

class ApplicationTest extends Illuminate\Foundation\TestCase {

	public function testRouteRedirect()
	{
		$app = new Application;
		$app->get('foo', function() {})->bind('bar');
		$response = $app->redirect_to_route('bar');
		$this->assertEquals('/foo', $response->getTargetUrl());
		$this->assertEquals(302, $response->getStatusCode());
		$response = $app->redirect_to_bar();
		$this->assertEquals('/foo', $response->getTargetUrl());
		$this->assertEquals(302, $response->getStatusCode());
	}


	public function testCreateMountable()
	{
		$application = new Application;
		$mount = $application->newMountable();
		$this->assertTrue($mount instanceof Illuminate\Foundation\ControllerCollection);
		$this->assertTrue($application === $mount->getApplication());
	}

}