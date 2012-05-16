<?php

use Illuminate\Foundation\Application;

class ApplicationTest extends Illuminate\Foundation\TestCase {

	public function testRouteRedirect()
	{
		$app = new Application;
		$app->get('foo', function() {})->bind('bar');
		$response = $app->redirectToRoute('bar');
		$this->assertEquals('/foo', $response->getTargetUrl());
		$this->assertEquals(302, $response->getStatusCode());
		$response = $app->redirectToBar();
		$this->assertEquals('/foo', $response->getTargetUrl());
		$this->assertEquals(302, $response->getStatusCode());

		$app->get('baz/{name}', function() {})->bind('boom');
		$app->flush();
		$response = $app->redirectToRoute('boom', array('name' => 'taylor'));
		$this->assertEquals('/baz/taylor', $response->getTargetUrl());
		$response = $app->redirectToBoom(array('name' => 'taylor'));
		$this->assertEquals('/baz/taylor', $response->getTargetUrl());
	}


	public function testCreateMountable()
	{
		$application = new Application;
		$mount = $application->newMountable();
		$this->assertTrue($mount instanceof Illuminate\Foundation\ControllerCollection);
		$this->assertTrue($application === $mount->getApplication());
	}


	public function testEnvironmenetDetection()
	{
		$app = new Application;
		$app['request_context']->setHost('foo');
		$app->detectEnvironment(array(
			'local'   => array('localhost')
		));
		$this->assertEquals('default', $app['env']);

		$app = new Application;
		$app['request_context']->setHost('localhost');
		$app->detectEnvironment(array(
			'local'   => array('localhost')
		));
		$this->assertEquals('local', $app['env']);
	}

}