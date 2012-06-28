<?php

/*
|--------------------------------------------------------------------------
| Load The Environment Configuration
|--------------------------------------------------------------------------
|
| You may specify a config for each environment. The default config will be
| included on every request and the environment config gives a chance to
| customize the application such as tweaking each service's options.
|
*/

$config = array();

if (file_exists($path = $appPath.'/config/production.php'))
{
	$config = require $path;
}

if (file_exists($path = $appPath.'/config/'.$app['env'].'.php'))
{
	$config = array_merge($config, require $path);
}

/*
|--------------------------------------------------------------------------
| Set The Application Configuration
|--------------------------------------------------------------------------
|
| Now that we have the configuration array, we can set the values on the
| application instance using "dot" syntax. This will make the options
| available to all of the services that will also to be registered.
|
*/

foreach ($config as $key => $value)
{
	$app[$key] = $value;
}

/*
|--------------------------------------------------------------------------
| Register The Core Service Provider
|--------------------------------------------------------------------------
|
| The Illuminate core service provider registers all of the core pieces
| of the Illuminate framework including session, caching, encryption
| and more. It's simply a convenient wrapper for the registration.
|
*/

use Illuminate\Foundation\CoreServiceProvider;

$app->register(new CoreServiceProvider);

/*
|--------------------------------------------------------------------------
| Load The Application Translation Messages
|--------------------------------------------------------------------------
|
| Here we'll load all of the language messages for the application, which
| are all stored in a single language file. The translator service is
| automatically registered for us via the core services provider.
|
*/

$messages = require $appPath.'/lang.php';

$domains = array();

if (isset($app['translator.domains']))
{
	$domains = $app['translator.domains'];
}

$app['translator.domains'] = array_merge($domains, compact('messages'));

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

if (file_exists($path = $appPath.'/start/production.php'))
{
	require $path;
}

if (file_exists($path = $appPath.'/start/'.$app['env'].'.php'))
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
| all of the routes now and return the application to the caller.
|
*/

require $appPath.'/routes.php';