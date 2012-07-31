<?php

use Mockery as m;
use Illuminate\Foundation\Lightbulb;
use Illuminate\Foundation\Application;

class FunctionsTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}


	public function testPathHelper()
	{
		set_app($app = new Application);
		$app['request'] = m::mock('Illuminate\Foundation\Request');
		$app['request']->shouldReceive('getHttpHost')->andReturn('www.foo.com');
		$app['request']->shouldReceive('getBasePath')->andReturn('/web');
		$app['request']->shouldReceive('getScheme')->once()->andReturn('https');
		$this->assertEquals('https://www.foo.com/web/bar', path('bar'));
		$this->assertEquals('https://www.foo.com/web/bar', path('bar', true));
		$this->assertEquals('http://www.foo.com/web/bar', path('bar', false));
		$this->assertEquals('http://www.foo.com/web/bar', http_path('bar'));
		$this->assertEquals('https://www.foo.com/web/bar', https_path('bar'));
	}


	public function testCsrfToken()
	{
		set_app($app = new Application);
		$app['session'] = $this->getMock('Illuminate\Session\TokenProvider');
		$app['session']->expects($this->once())->method('getToken')->will($this->returnValue('foo'));
		$this->assertEquals('foo', csrf_token());
	}

}