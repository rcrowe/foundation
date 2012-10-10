<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Validation\Factory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\DatabasePresenceVerifier;

class ValidatorServiceProvider extends ServiceProvider {

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
	public function register($app)
	{
		$this->registerPresenceVerifier($app);

		$app['validator'] = $app->share(function($app)
		{
			$validator = new Factory($app['translator']);

			// The validation presence verifier is responsible for determing the existence
			// of values in a given data collection, typically a relational database or
			// other persistent data stores. And it is used to check for uniqueness.
			if (isset($app['validation.presence']))
			{
				$validator->setPresenceVerifier($app['validation.presence']);
			}

			return $validator;
		});
	}

	/**
	 * Register the database presence verifier.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerPresenceVerifier($app)
	{
		$app['validation.presence'] = $app->share(function($app)
		{
			return new DatabasePresenceVerifier($app['db']->connection());
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function getProvidedServices()
	{
		return array('validator', 'validation.presence');
	}

}