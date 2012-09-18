<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Pagination\Environment;

class PaginationServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function register(Application $app)
	{
		$app['paginator'] = $app->share(function($app)
		{
			$view = $app['view']->driver();

			$paginator = new Environment($app['request'], $view, $app['translator']);

			$paginator->setViewName($app['config']['view.pagination']);

			return $paginator;
		});
	}

}