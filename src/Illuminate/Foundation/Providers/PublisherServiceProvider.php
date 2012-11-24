<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AssetPublisher;
use Illuminate\Foundation\Console\AssetPublishCommand;

class PublisherServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function register($app)
	{
		$this->registerAssetPublisher($app);

		$this->registerConfigPublisher($app);
	}

	/**
	 * Register the asset publisher service and command.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerAssetPublisher($app)
	{
		$this->registerAssetPublishCommand($app);

		$app['asset.publisher'] = $app->share(function($app)
		{
			$publicPath = $app['path.base'].'/public';

			// The asset "publisher" is responsible for moving package's assets into the
			// web accessible public directory of an application so they can actually
			// be served to the browser. Otherwise, they would be locked in vendor.
			$publisher = new AssetPublisher($app['files'], $publicPath);

			$publisher->setPackagePath($app['path.base'].'/vendor');

			return $publisher;
		});
	}

	/**
	 * Register the asset publish console command.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerAssetPublishCommand($app)
	{
		$app['command.asset.publish'] = $app->share(function($app)
		{
			return new AssetPublishCommand($app['asset.publisher']);
		});
	}

	/**
	 * Register the configuration publisher class and command.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerConfigPublisher($app)
	{
		$this->registerConfigPublishCommand($app);

		$app['config.publisher'] = $app->share(function($app)
		{
			$configPath = $app['path'].'/config';

			// Once we have created the configuration publisher, we will set the default
			// package path on the object so that it knows where to find the packages
			// that are installed for the application and can move them to the app.
			$publisher = new ConfigPublisher($app['files'], $configPath);

			$publisher->setPackagePath($app['path.base'].'/vendor');

			return $publisher;
		});
	}

	/**
	 * Register the configuration publish console command.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerConfigPublishCommand($app)
	{
		$app['command.config.publish'] = $app->share(function($app)
		{
			return new ConfigPublishCommand($app['config.publisher']);
		});
	}

}