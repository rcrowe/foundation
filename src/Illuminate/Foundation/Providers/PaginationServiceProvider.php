<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Pagination\Environment;
use Illuminate\Support\ServiceProvider;

class PaginationServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function register($app)
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