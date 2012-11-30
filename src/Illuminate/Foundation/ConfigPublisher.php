<?php namespace Illuminate\Foundation;

use Illuminate\Filesystem;

class ConfigPublisher {

	/**
	 * The filesystem instance.
	 *
	 * @var Illuminate\Filesystem
	 */
	protected $files;

	/**
	 * The destination of the config files.
	 *
	 * @var string
	 */
	protected $publishPath;

	/**
	 * The path to the application's packages.
	 *
	 * @var string
	 */
	protected $packagePath;

	/**
	 * Create a new configuration publisher instance.
	 *
	 * @param  Illuminate\Filesystem  $files
	 * @param  string  $publishPath
	 * @return void
	 */
	public function __construct(Filesystem $files, $publishPath)
	{
		$this->files = $files;
		$this->publishPath = $publishPath;
	}

	/**
	 * Publish the configuration files for a package.
	 *
	 * @param  string  $package
	 * @param  string  $packagePath
	 * @return void
	 */
	public function publishPackage($package, $packagePath = null)
	{
		list($vendor, $name) = explode('/', $package);

		// First we will figure out the source of the package's configuration location
		// which we do by convention. Once we have that we will move the files over
		// to the "main" configuration directory for this particular application.
		$path = $packagePath ?: $this->packagePath;

		$source = $this->getSource($package, $name, $path);

		$destination = $this->publishPath."/packages/{$package}";

		// We need to create the destination directory if it doesn't exist so we will
		// actually be able to write the published configuration file to disk else
		// we will get a file not found error when trying to publish the config.
		$this->makeDestination($destination);

		return $this->files->copyDirectory($source, $destination);
	}

	/**
	 * Get the source configuration directory to publish.
	 *
	 * @param  string  $package
	 * @param  string  $name
	 * @param  string  $packagePath
	 * @return string
	 */
	protected function getSource($package, $name, $packagePath)
	{
		$source = $packagePath."/{$package}/src/config";

		if ( ! $this->files->isDirectory($source))
		{
			throw new \InvalidArgumentException("Configuration not found.");
		}

		return $source;
	}

	/**
	 * Create the destination directory if it doesn't exist.
	 *
	 * @param  string  $destination
	 * @return void
	 */
	protected function makeDestination($destination)
	{
		if ( ! $this->files->isDirectory($destination))
		{
			$this->files->makeDirectory($destination, 0777, true);
		}
	}

	/**
	 * Set the default package path.
	 *
	 * @param  string  $packagePath
	 * @return void
	 */
	public function setPackagePath($packagePath)
	{
		$this->packagePath = $packagePath;
	}

}
