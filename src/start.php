<?php

/*
|--------------------------------------------------------------------------
| Register Class Imports
|--------------------------------------------------------------------------
|
| Here we will just import a few classes that we need during the booting
| of the framework. These are mainly classes that involve loading the
| configuration files for the application, such as the file system.
|
*/

use Illuminate\Filesystem;
use Illuminate\Config\FileLoader as ConfigLoader;
use Illuminate\Config\Repository as ConfigRepository;

/*
|--------------------------------------------------------------------------
| Register Application Exception Handling
|--------------------------------------------------------------------------
|
| We will go ahead and register the application exception handling here
| which will provide a nice output of exception details and a stack
| trace in the event of exceptions during application execution.
|
*/

$app->startExceptionHandling();

/*
|--------------------------------------------------------------------------
| Register The Configuration Repository
|--------------------------------------------------------------------------
|
| The configuration repository is used to lazily load in the options for
| this application from the configuration files. The files are easily
| separated by their concerns so they do not become really crowded.
|
*/

$path = $app['path'].'/config';

$loader = new ConfigLoader(new Filesystem, $path);

$app['config'] = new ConfigRepository($loader, $app['env']);

/*
|--------------------------------------------------------------------------
| Load The Illuminate Facades
|--------------------------------------------------------------------------
|
| The facades provide a terser static interface over the various parts
| of the application, allowing their methods to be accessed through
| a mixtures of magic methods and facade derivatives. It's slick.
|
*/

Illuminate\Support\Facade::setFacadeApplication($app);

require_once __DIR__.'/facades.php';

/*
|--------------------------------------------------------------------------
| Register The Core Service Providers
|--------------------------------------------------------------------------
|
| The Illuminate core service providers register all of the core pieces
| of the Illuminate framework including session, caching, encryption
| and more. It's simply a convenient wrapper for the registration.
|
*/

foreach ($app['config']['app.providers'] as $provider => $options)
{
	$provider = '\\'.ltrim($provider, '\\');

	if (isset($options['defer']) and $options['defer'])
	{
		$app->deferredRegister($provider, $options['provides']);
	}
	else
	{
		$app->register(new $provider);
	}
}

/*
|--------------------------------------------------------------------------
| Load The Application Start Script
|--------------------------------------------------------------------------
|
| The start script gives us the application the opportunity to override
| any of the existing IoC bindings, as well as register its own new
| bindings for things like repositories, etc. We'll load it here.
|
*/

if (file_exists($path = $app['path'].'/start/production.php'))
{
	require $path;
}

if (file_exists($path = $app['path'].'/start/'.$app['env'].'.php'))
{
	require $path;
}

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| The Application routes are kept separate from the application starting
| just to keep the file a little cleaner. We'll go ahead and load in
| all of the routes now and return the application to the callers.
|
*/

require $app['path'].'/routes.php';