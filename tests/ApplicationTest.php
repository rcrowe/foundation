<?php

use Mockery as m;
use Illuminate\Foundation\Request;
use Illuminate\Foundation\Lightbulb;
use Illuminate\Foundation\Application;

class ApplicationTest extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass()
	{
		Lightbulb::on();
	}


	public function tearDown()
	{
		m::close();
	}


	public function testBasicRoutingIntegration()
	{
		$app = new Application;
		$app['router']->get('/foo', function() { return 'bar'; });
		$app['request'] = Request::create('/foo');
		$response = $app->run();
		$this->assertEquals('bar', $response->getContent());
	}


	public function testEnvironmenetDetection()
	{
		$app = new Application;
		$app['request'] = m::mock('Symfony\Component\HttpFoundation\Request');
		$app['request']->shouldReceive('getHost')->andReturn('foo');
		$app->detectEnvironment(array(
			'local'   => array('localhost')
		));
		$this->assertEquals('default', $app['env']);

		$app = new Application;
		$app['request'] = m::mock('Symfony\Component\HttpFoundation\Request');
		$app['request']->shouldReceive('getHost')->andReturn('localhost');
		$app->detectEnvironment(array(
			'local'   => array('localhost')
		));
		$this->assertEquals('local', $app['env']);
	}

	/**
	 * @expectedException Illuminate\Session\TokenMismatchException
	 */
	public function testCsrfMiddlewareThrowsException()
	{
		$app = new Application;
		$app->register(new Illuminate\Foundation\Provider\SessionServiceProvider);
		$app['session'] = m::mock('Illuminate\Session\Store');
		$app['session']->shouldReceive('getToken')->once()->andReturn('foo');
		$app['request'] = Symfony\Component\HttpFoundation\Request::create('/', 'GET', array('csrf_token' => 'bar'));
		$middleware = $app->getMiddleware('csrf');
		$middleware();
	}


	public function testCsrfMiddlewareDoesntThrowWhenMatch()
	{
		$app = new Application;
		$app->register(new Illuminate\Foundation\Provider\SessionServiceProvider);
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


	public function testShowMethodCallsRenderOnBlade()
	{
		$app = new Application;
		$app['blade'] = m::mock('Illuminate\Blade\Factory');
		$app['blade']->shouldReceive('show')->once()->with('foo', array('bar' => 'baz'))->andReturn('boom');
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