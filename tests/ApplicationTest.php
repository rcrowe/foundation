<?php

use Mockery as m;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Lightbulb;

class ApplicationTest extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass()
	{
		Lightbulb::on();
	}


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


	public function testRedirectSetsSession()
	{
		$app = new Application;
		$app['session'] = m::mock('Illuminate\Session\Store');
		$redirect = $app->redirect('foo');
		$this->assertInstanceOf('Illuminate\Session\Store', $redirect->getSession());
	}


	public function testRedirectWithSetsSessiosValue()
	{
		$app = new Application;
		$app['session'] = m::mock('Illuminate\Session\Store');
		$app['session']->shouldReceive('flash')->once()->with('foo', 'bar');
		$redirect = $app->redirect('boom');
		$return = $redirect->with('foo', 'bar');
		$this->assertInstanceOf('Illuminate\Foundation\RedirectResponse', $return);
	}


	public function testRedirectWithInputFlashesToSession()
	{
		$app = new Application;
		$app['session'] = m::mock('Illuminate\Session\Store');
		$app['session']->shouldReceive('flashInput')->once()->with(array('foo' => 'bar'));
		$redirect = $app->redirect('boom');
		$return = $redirect->withInput(array('foo'=> 'bar'));
		$this->assertInstanceOf('Illuminate\Foundation\RedirectResponse', $return);
	}


	public function testPrepareRequestInjectsSession()
	{
		$app = new Application;
		$request = Illuminate\Foundation\Request::create('/', 'GET');
		$app['session'] = m::mock('Illuminate\Session\Store');
		$app->prepareRequest($request);
		$this->assertEquals($app['session'], $request->getSessionStore());
	}


	public function testInputCallsInputOnRequest()
	{
		$app = new Application;
		$request = m::mock('Illuminate\Foundation\Request');
		$request->shouldReceive('input')->once()->with('foo', 'bar')->andReturn('baz');
		$app['request'] = $request;
		$this->assertEquals('baz', $app->input('foo', 'bar'));
	}


	public function testOldCallsOldOnRequest()
	{
		$app = new Application;
		$request = m::mock('Illuminate\Foundation\Request');
		$request->shouldReceive('old')->once()->with('foo', 'bar')->andReturn('baz');
		$app['request'] = $request;
		$this->assertEquals('baz', $app->old('foo', 'bar'));
	}


	public function testShowMethodCallsRenderOnBlade()
	{
		$app = new Application;
		$app['blade'] = m::mock('Illuminate\Blade\Factory');
		$app['blade']->shouldReceive('render')->once()->with('foo', array('bar' => 'baz'))->andReturn('boom');
		$this->assertEquals('boom', $app->show('foo', array('bar' => 'baz')));
	}


	public function testRespondMethodReturnsResponse()
	{
		$app = new Application;
		$response = $app->respond('foo', 404, array('foo' => 'bar'));
		$this->assertEquals('foo', $response->getContent());
		$this->assertEquals(404, $response->getStatusCode());
		$this->assertEquals('bar', $response->headers->get('foo'));
	}

}