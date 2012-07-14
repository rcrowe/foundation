<?php

use Mockery as m;
use Illuminate\Foundation\Lightbulb;
use Illuminate\Foundation\Application;

class FunctionsTest extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass()
	{
		Illuminate\Foundation\Lightbulb::on();
	}


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


	public function testRouteHelper()
	{
		set_app($app = new Application);
		$app->register(new Silex\Provider\UrlGeneratorServiceProvider);
		$app->get('foo', function() {})->bind('bar');
		$this->assertEquals('/foo', route('bar'));

		$app->get('bar/{baz}', function() {})->bind('zoom');
		$app->flush();
		$this->assertEquals('/bar/taylor', route('zoom', array('baz' => 'taylor')));
		unset($GLOBALS['__illuminate.app']);
	}


	public function testTranslationHelpers()
	{
		set_app($app = new Application);
		$app['translator'] = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
		$app['translator']->expects($this->once())->method('trans')->with($this->equalTo('message.key'), $this->equalTo(array('foo' => 'bar')), $this->equalTo('domain'), $this->equalTo('locale'));
		trans('message.key', array('foo' => 'bar'), 'domain', 'locale');

		$app['translator']->expects($this->once())->method('transChoice')->with($this->equalTo('message.key'), $this->equalTo(1), $this->equalTo(array('foo' => 'bar')), $this->equalTo('domain'), $this->equalTo('locale'));
		transChoice('message.key', 1, array('foo' => 'bar'), 'domain', 'locale');
	}


	public function testCsrfToken()
	{
		set_app($app = new Application);
		$app['session'] = $this->getMock('Illuminate\Session\TokenProvider');
		$app['session']->expects($this->once())->method('getToken')->will($this->returnValue('foo'));
		$this->assertEquals('foo', csrf_token());
	}


	public function testArrayDot()
	{
		$array = array_dot(array('name' => 'taylor', 'languages' => array('php' => true)));
		$this->assertEquals($array, array('name' => 'taylor', 'languages.php' => true));
	}


	public function testStrIs()
	{
		$this->assertTrue(str_is('*.dev', 'localhost.dev'));
		$this->assertTrue(str_is('a', 'a'));
		$this->assertTrue(str_is('*dev*', 'localhost.dev'));
		$this->assertFalse(str_is('*something', 'foobar'));
		$this->assertFalse(str_is('foo', 'bar'));
	}


	public function testValue()
	{
		$this->assertEquals('foo', value('foo'));
		$this->assertEquals('foo', value(function() { return 'foo'; }));
	}

}