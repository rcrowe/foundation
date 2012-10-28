<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AssetPublisher;
use Illuminate\Foundation\Console\PackagePublishCommand;

class PublisherServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function register($app)
	{
		$this->registerPackagePublishCommand($app);

		$app['asset.publisher'] = $app->share(function($app)
		{
			return new AssetPublisher($app['files'], $app['path.base'].'/vendor');
		});
	}

	/**
	 * Register the package publish console command.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerPackagePublishCommand($app)
	{
		$app['command.package.publish'] = $app->share(function($app)
		{
			return new PackagePublishCommand($app['asset.publisher']);
		});
	}

}