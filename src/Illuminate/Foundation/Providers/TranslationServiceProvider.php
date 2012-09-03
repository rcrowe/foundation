<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;

class TranslationServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function register(Application $app)
	{
		$this->registerLoader($app);

		$app['translator'] = $app->share(function($app)
		{
			$config = $app['config']['app'];

			// When registering the translator component, we'll need to set the default
			// locale as well as the fallback locale. So, we'll grab the application
			// configuration so we can easily get both of these values from there.
			$locales = $config['locales'];

			$fallback = $config['fallback_locale'];

			$trans = new Translator($locales, $config['locale'], $fallback);

			$trans->loadTranslations($app['translation.loader']);

			return $trans;
		});
	}

	/**
	 * Register the translation line loader.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerLoader($app)
	{
		$app['translation.loader'] = $app->share(function($app)
		{
			return new FileLoader($app['files'], $app['config']['app.locale_path']);
		});
	}

}