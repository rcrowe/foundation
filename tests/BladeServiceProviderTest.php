<?php

use Mockery as m;

class BladeServiceProviderTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}


	public function testErrorsAreShared()
	{
		$app = new Illuminate\Foundation\Application;
		$app['session'] = m::mock('stdClass');
		$app['session']->shouldReceive('has')->once()->with('errors')->andReturn(true);
		$app['session']->shouldReceive('get')->once()->with('errors')->andReturn('foo');
		$app->register(new Illuminate\Foundation\Provider\BladeServiceProvider);
		$app['blade.loader'] = m::mock('Illuminate\Blade\Loader');
		$shared = $app['blade']->getShared();
		$this->assertEquals('foo', $shared['errors']);
	}

}