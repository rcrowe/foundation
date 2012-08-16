<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Routing\Router;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Foundation\Redirector;
use Illuminate\Foundation\Application;

class RoutingServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function register(Application $app)
	{
		$app['router'] = $app->share(function() { return new Router; });

		$this->registerUrlGenerator($app);

		$this->registerRedirector($app);
	}

	/**
	 * Register the URL generator service.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerUrlGenerator($app)
	{
		$app['url.generator'] = $app->share(function($app)
		{
			// The URL generator needs the route collection that exists on the router.
			// Keep in mind this is an object, so we're passing by references here
			// and all the registered routes will be available to the generator.
			$routes = $app['router']->getRoutes();

			return new UrlGenerator($routes, $app['request']);
		});
	}

	/**
	 * Register the Redirector service.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerRedirector($app)
	{
		$app['redirect'] = $app->share(function($app)
		{
			return new Redirector($app['url.generator']);
		});
	}

}