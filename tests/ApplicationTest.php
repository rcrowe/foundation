<?php

use Mockery as m;
use Illuminate\Foundation\Application;

class ApplicationTest extends Illuminate\Foundation\TestCase {

	public function tearDown()
	{
		m::close();
	}


	public function testRouteRedirect()
	{
		$app = new Application;
		$app->register(new Silex\Provider\UrlGeneratorServiceProvider);
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


	public function testAuthMiddlewareIsRegistered()
	{
		$app = new Application;
		$app['auth'] = m::mock('Illuminate\Auth\Guard');
		$app['auth']->shouldReceive('isGuest')->once()->andReturn(true);
		$app['url_generator'] = m::mock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
		$app['url_generator']->shouldReceive('generate')->andReturn('foo');
		$middleware = $app->getMiddleware('auth');
		$this->assertTrue($middleware instanceof Closure);
		$response = $middleware();
		$this->assertTrue($response instanceof Illuminate\Foundation\RedirectResponse);
		$this->assertEquals('foo', $response->getTargetUrl());

		$app = new Application;
		$app['auth'] = m::mock('Illuminate\Auth\Guard');
		$app['auth']->shouldReceive('isGuest')->once()->andReturn(false);
		$middleware = $app->getMiddleware('auth');
		$this->assertNull($middleware());
	}


	/**
	 * @expectedException Illuminate\Session\TokenMismatchException
	 */
	public function testCsrfMiddlewareThrowsExcpetion()
	{
		$app = new Application;
		$app['session'] = m::mock('Illuminate\Session\Store');
		$app['session']->shouldReceive('getToken')->once()->andReturn('foo');
		$app['request'] = Symfony\Component\HttpFoundation\Request::create('/', 'GET', array('csrf_token' => 'bar'));
		$middleware = $app->getMiddleware('csrf');
		$middleware();
	}


	public function testCsrfMiddlewareDoesntThrowWhenMatch()
	{
		$app = new Application;
		$app['session'] = m::mock('Illuminate\Session\Store');
		$app['session']->shouldReceive('getToken')->once()->andReturn('foo');
		$app['request'] = Symfony\Component\HttpFoundation\Request::create('/', 'GET', array('csrf_token' => 'foo'));
		$middleware = $app->getMiddleware('csrf');
		$middleware();
		$this->assertTrue(true);
	}

}