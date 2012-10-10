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
		$app = m::mock('Illuminate\Foundation\Application[compileManifest,registerFromManifest]');
		$files->shouldReceive('exists')->once()->with('foo')->andReturn(false);
		$app->shouldReceive('compileManifest')->once()->with($files, 'foo', array('services'))->andReturn(array('manifest'));
		$app->shouldReceive('registerFromManifest')->once()->with(array('manifest'));
		$app['config'] = array('app.providers' => array('services'));

		$app->registerServices($files, 'foo');
	}


	public function testManifestIsGeneratedWhenNewServicesExistInConfig()
	{
		$files = m::mock('Illuminate\Filesystem');
		$app = m::mock('Illuminate\Foundation\Application[compileManifest,registerFromManifest]');
		$files->shouldReceive('exists')->once()->with('foo')->andReturn(true);
		$files->shouldReceive('get')->once()->with('foo')->andReturn(serialize(array('providers' => 'different.services')));
		$app->shouldReceive('compileManifest')->once()->with($files, 'foo', array('services'))->andReturn(array('manifest'));
		$app->shouldReceive('registerFromManifest')->once()->with(array('manifest'));
		$app['config'] = array('app.providers' => array('services'));

		$app->registerServices($files, 'foo');
	}


	public function testManifestIsNotModifiedIfItExistsAndNoNewServicesAreAdded()
	{
		$files = m::mock('Illuminate\Filesystem');
		$app = m::mock('Illuminate\Foundation\Application[compileManifest,registerFromManifest]');
		$files->shouldReceive('exists')->once()->with('foo')->andReturn(true);
		$files->shouldReceive('get')->once()->with('foo')->andReturn(serialize(array('providers' => 'services')));
		$app->shouldReceive('compileManifest')->never();
		$app->shouldReceive('registerFromManifest')->once()->with(array('providers' => 'services'));
		$app['config'] = array('app.providers' => 'services');

		$app->registerServices($files, 'foo');
	}


	public function testManifestIsCompiledCorrectly()
	{
		$app = new Illuminate\Foundation\Application;
		$manifest = array('providers' => array('ApplicationManifestTestProviderStub'), 'manifest' => array('ApplicationManifestTestProviderStub' => array('defer' => true, 'provides' => array('provides.list'))));
		$files = m::mock('Illuminate\Filesystem');
		$files->shouldReceive('put')->once()->with('foo', serialize($manifest));

		$this->assertEquals($manifest, $app->compileManifest($files, 'foo', array('ApplicationManifestTestProviderStub')));
	}


	public function testServicesCanBeRegisteredFromManifest()
	{
		$app = m::mock('Illuminate\Foundation\Application[register,deferredRegister]');
		$manifest = array('manifest' => array('provider' => array('defer' => true, 'provides' => array('deferred.provide')), 'ApplicationManifestTestProviderStub' => array('defer' => false, 'provides' => array())));
		$app->shouldReceive('deferredRegister')->once()->with('provider', array('deferred.provide'));
		$app->shouldReceive('register')->once()->with(m::type('ApplicationManifestTestProviderStub'));
		$app->registerFromManifest($manifest);
	}

}

class ApplicationManifestTestProviderStub extends Illuminate\Support\ServiceProvider {
	/**
	 * Indicates if the service provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register the service provider.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function register($app) {}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function getProvidedServices()
	{
		return array('provides.list');
	}
}