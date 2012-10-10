<?php

use Mockery as m;

class ApplicationManifestTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}


	public function testManifestIsGeneratedWhenItDoesntAlreadyExist()
	{
		$files = m::mock('Illuminate\Filesystem');
		$app = m::mock('Illuminate\Foundation\Application[buildManifest,registerFromManifest]');
		$files->shouldReceive('exists')->once()->with('foo')->andReturn(false);
		$app->shouldReceive('buildManifest')->once()->with($files, 'foo', array('services'))->andReturn(array('manifest'));
		$app->shouldReceive('registerFromManifest')->once()->with(array('manifest'));
		$app['config'] = array('app.providers' => array('services'));

		$app->registerServices($files, 'foo');
	}


	public function testManifestIsGeneratedWhenNewServicesExistInConfig()
	{
		$files = m::mock('Illuminate\Filesystem');
		$app = m::mock('Illuminate\Foundation\Application[buildManifest,registerFromManifest]');
		$files->shouldReceive('exists')->once()->with('foo')->andReturn(true);
		$files->shouldReceive('get')->once()->with('foo')->andReturn(serialize(array('providers' => 'different.services')));
		$app->shouldReceive('buildManifest')->once()->with($files, 'foo', array('services'))->andReturn(array('manifest'));
		$app->shouldReceive('registerFromManifest')->once()->with(array('manifest'));
		$app['config'] = array('app.providers' => array('services'));

		$app->registerServices($files, 'foo');
	}


	public function testManifestIsNotModifiedIfItExistsAndNoNewServicesAreAdded()
	{
		$files = m::mock('Illuminate\Filesystem');
		$app = m::mock('Illuminate\Foundation\Application[buildManifest,registerFromManifest]');
		$files->shouldReceive('exists')->once()->with('foo')->andReturn(true);
		$files->shouldReceive('get')->once()->with('foo')->andReturn(serialize(array('providers' => 'services')));
		$app->shouldReceive('buildManifest')->never();
		$app->shouldReceive('registerFromManifest')->once()->with(array('providers' => 'services'));
		$app['config'] = array('app.providers' => 'services');

		$app->registerServices($files, 'foo');
	}

}