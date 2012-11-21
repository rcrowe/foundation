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
		$this->registerAssetPublishCommand($app);

		$app['asset.publisher'] = $app->share(function($app)
		{
			$publisher = new AssetPublisher($app['files'], $app['path.base'].'/public');

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

}