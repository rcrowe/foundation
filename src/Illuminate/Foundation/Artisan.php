<?php namespace Illuminate\Foundation;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Illuminate\Console\Application as ConsoleApplication;

class Artisan {

	/**
	 * The application instance.
	 *
	 * @var Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * The Artisan console instance.
	 *
	 * @var  Illuminate\Console\Application
	 */
	protected $artisan;

	/**
	 * Create a new Artisan command runner instance.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	/**
	 * Run an Artisan console command by name.
	 *
	 * @param  string  $command
	 * @param  array   $parameters
	 * @param  Symfony\Component\Console\Output\OutputInterface  $output
	 * @return void
	 */
	public function run($command, array $parameters = array(), OutputInterface $output = null)
	{
		$artisan = $this->getArtisan();

		$output = $output ?: new NullOutput;

		return $artisan->find($command)->run(new ArrayInput($parameters), $output);
	}

	/**
	 * Get the Artisan console instance.
	 *
	 * @return Illuminate\Console\Application
	 */
	protected function getArtisan()
	{
		if ( ! is_null($this->artisan)) return $this->artisan;

		return $this->artisan = ConsoleApplication::start($this->app);
	}

}