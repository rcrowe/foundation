<?php namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Foundation\ConfigPublisher;
use Symfony\Component\Console\Input\InputArgument;

class ConfigPublishCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'config:publish';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = "Publish a package's configuration to the application";

	/**
	 * The asset publisher instance.
	 *
	 * @var Illuminate\Foundation\AssetPublisher
	 */
	protected $config;

	/**
	 * Create a new configuration publish command instance.
	 *
	 * @param  Illuminate\Foundation\ConfigPublisher  $config
	 * @return void
	 */
	public function __construct(ConfigPublisher $config)
	{
		parent::__construct();

		$this->config = $config;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$package = $this->input->getArgument('package');

		$this->config->publishPackage($package);

		$this->output->writeln('<info>Configuration published for package:</info> '.$package);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('package', InputArgument::REQUIRED, 'The name of package to publish'),
		);
	}

}