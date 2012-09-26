<?php

use Mockery as m;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;

class FunctionsTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}


	public function testPathHelper()
	{
		set_app($app = new Application);
		$app['request'] = Request::create('http://www.foo.com', 'GET');
		$this->assertEquals('http://www.foo.com/bar', path('bar'));
		$this->assertEquals('https://www.foo.com/bar', path('bar', true));
		$this->assertEquals('https://www.foo.com/bar', secure_path('bar'));
	}


	public function testRouteHelper()
	{
		set_app($app = new Application);
		$app['request'] = Request::create('http://www.foo.com', 'GET');
		$app['router']->get('foo', array('as' => 'bar', function() {}));
		$this->assertEquals('http://www.foo.com/foo', route('bar'));
	}


	public function testCsrfToken()
	{
		set_app($app = new Application);
		$app['session'] = $this->getMock('Illuminate\Session\TokenProvider');
		$app['session']->expects($this->once())->method('getToken')->will($this->returnValue('foo'));
		$this->assertEquals('foo', csrf_token());
	}

}