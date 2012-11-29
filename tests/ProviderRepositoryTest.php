<?php

use Mockery as m;

class ProviderRepositoryTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}


	public function testServicesAreRegisteredWhenManifestIsNotRecompiled()
	{
		$repo = $this->getMock('Illuminate\Foundation\ProviderRepository', array('loadManifest', 'shouldRecompile', 'createProvider'), array(m::mock('Illuminate\Filesystem')));
		$repo->expects($this->once())->method('loadManifest')->will($this->returnValue(array('eager' => array('foo'), 'deferred' => array('deferred'))));
		$repo->expects($this->once())->method('shouldRecompile')->will($this->returnValue(false));
		$app = m::mock('Illuminate\Foundation\Application[register]');
		$provider = m::mock('Illuminate\Support\ServiceProvider');
		$repo->expects($this->once())->method('createProvider')->with($this->equalTo($app), $this->equalTo('foo'))->will($this->returnValue($provider));
		$app->shouldReceive('register')->once()->with($provider);
		$app->shouldReceive('setDeferredServices')->once()->with(array('deferred'));

		$repo->load($app, array());
	}


	public function testManifestIsProperlyRecompiled()
	{
		$repo = m::mock('Illuminate\Foundation\ProviderRepository[createProvider,loadManifest,writeManifest,shouldRecompile]', array(m::mock('Illumiante\Filesystem')));
		$app = m::mock('Illuminate\Foundation\Application');

		$repo->shouldReceive('loadManifest')->once()->andReturn(array('eager' => array(), 'deferred' => array('deferred')));
		$repo->shouldReceive('shouldRecompile')->once()->andReturn(true);

		// foo mock is just a deferred provider
		$repo->shouldReceive('createProvider')->once()->with($app, 'foo')->andReturn($fooMock = m::mock('StdClass'));
		$fooMock->shouldReceive('isDeferred')->once()->andReturn(true);
		$fooMock->shouldReceive('provides')->once()->andReturn(array('foo.provides1', 'foo.provides2'));

		// bar mock is added to eagers since it's not reserved
		$repo->shouldReceive('createProvider')->once()->with($app, 'bar')->andReturn($barMock = m::mock('Illuminate\Support\ServiceProvider'));
		$barMock->shouldReceive('isDeferred')->once()->andReturn(false);
		$repo->shouldReceive('writeManifest')->once()->andReturnUsing(function($app, $manifest) { return $manifest; });

		// bar mock should be registered with the application since it's eager
		$repo->shouldReceive('createProvider')->once()->with($app, 'bar')->andReturn($barMock);
		$app->shouldReceive('register')->once()->with($barMock);

		// the deferred should be set on the application
		$app->shouldReceive('setDeferredServices')->once()->with(array('foo.provides1' => 'foo', 'foo.provides2' => 'foo'));

		$manifest = $repo->load($app, array('foo', 'bar'));
	}

}