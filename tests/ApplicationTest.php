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

		$app->get('baz/{name}', function() {})->bind('boom');
		$app->flush();
		$response = $app->redirect_to_route('boom', array('name' => 'taylor'));
		$this->assertEquals('/baz/taylor', $response->getTargetUrl());
		$response = $app->redirect_to_boom(array('name' => 'taylor'));
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
		$app->registerEnvironment(array(
			'default' => array('config' => __DIR__.'/config/default.yml'),
			'local'   => array('hosts' => array('localhost'), 'config' => __DIR__.'/config/local.yml'),
		));
		$this->assertEquals('file', $app['session.foo']);

		$app = new Application;
		$app['request_context']->setHost('localhost');
		$app->registerEnvironment(array(
			'default' => array('config' => __DIR__.'/config/default.yml'),
			'local'   => array('hosts' => array('localhost'), 'config' => __DIR__.'/config/local.yml'),
		));
		$this->assertEquals('apc', $app['session.foo']);
		$this->assertEquals('taylor', $app['dev.name']);
	}

}