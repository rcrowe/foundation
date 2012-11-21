<?php namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Foundation\AssetPublisher;
use Symfony\Component\Console\Input\InputArgument;

class AssetPublishCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'asset:publish';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = "Publish a package's assets to the public directory";

	/**
	 * The asset publisher instance.
	 *
	 * @var Illuminate\Foundation\AssetPublisher
	 */
	protected $assets;

	/**
	 * Create a new package publish command instance.
	 *
	 * @param  Illuminate\Foundation\AssetPublisher  $assets
	 * @return void
	 */
	public function __construct(AssetPublisher $assets)
	{
		parent::__construct();

		$this->assets = $assets;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$package = $this->input->getArgument('package');

		$this->assets->publishPackage($package);

		$this->output->writeln('<info>Assets published for package:</info> '.$package);
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