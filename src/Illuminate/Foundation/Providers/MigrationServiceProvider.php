<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Console\Migrations\Migrator;
use Illuminate\Database\Console\Migrations\MakeCommand;
use Illuminate\Database\Console\Migrations\ResetCommand;
use Illuminate\Database\Console\Migrations\InstallCommand;
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
		$commands = array('Migrate', 'Rollback', 'Reset', 'Install', 'Make');

		// We'll simply spin through the list of commands that are migration related
		// and register each one of them with an application container. They will
		// be resolved in the Artisan start file and registered on the console.
		foreach ($commands as $command)
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

	/**
	 * Register the "install" migration command.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerInstallCommand($app)
	{
		$app['command.migrate.install'] = $app->share(function($app)
		{
			return new InstallCommand($app['migration.repository']);
		});
	}

	/**
	 * Register the "install" migration command.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerMakeCommand($app)
	{
		$app['migration.creator'] = $app->share(function($app)
		{
			return new MigrationCreator($app['files']);
		});

		// Once we have the migration creator registered, we will create the command
		// and inject the creator. The creator is responsible for the actual file
		// creation of the migrations, and may be extended by these developers.
		$app['command.migrate.make'] = $app->share(function($app)
		{
			$path = $app['config']['db.migration.path'];

			return new MakeCommand($app['migration.creator'], $path);
		});
	}

}