<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Console\Migrations\Migrator;
use Illuminate\Database\Console\Migrations\ResetCommand;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Database\Console\Migrations\RollbackCommand;
use Illuminate\Database\Console\Migrations\DatabaseMigrationRepository;

class MigrationServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function register($app)
	{
		$this->registerRepository($app);

		$this->registerMigrator($app);

		$this->registerCommands($app);
	}

	/**
	 * Register the migration repository service.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerRepository($app)
	{
		$app['migration.repository'] = $app->share(function($app)
		{
			// The migration repository implementation is responsible for reading the
			// migrations that have already run from the data store and of helping
			// track each newly run migrations, as well as a rollback operation.
			$connection = function() use ($app)
			{
				return $app['db']->connection();
			};

			$table = $app['config']['database.migration.table'];

			return new DatabaseMigrationRepository($connection, $table);
		});
	}

	/**
	 * Register the migrator service.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerMigrator($app)
	{
		$app['migrator'] = $app->share(function($app)
		{
			return new Migrator($app['migration.repository'], $app['files']);
		});
	}

	/**
	 * Register all of the migration commands.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerCommands($app)
	{
		foreach (array('Migrate', 'Rollback', 'Reset') as $command)
		{
			$this->{'register'.$command.'Command'}($app);
		}
	}

	/**
	 * Register the "migrate" migration command.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerMigrateCommand($app)
	{
		$app['command.migrate'] = $app->share(function($app)
		{
			$path = $app['config']['database.migration.path'];

			return new MigrateCommand($app['migrator'], $path, $app['path'].'/vendor');
		});
	}

	/**
	 * Register the "rollback" migration command.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerRollbackCommand($app)
	{
		$app['command.migrate.rollback'] = $app->share(function($app)
		{
			return new RollbackCommand($app['migrator']);
		});
	}

	/**
	 * Register the "reset" migration command.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerResetCommand($app)
	{
		$app['command.migrate.reset'] = $app->share(function($app)
		{
			return new ResetCommand($app['migrator']);
		});
	}

}