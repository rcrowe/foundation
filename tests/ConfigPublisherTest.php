<?php

use Mockery as m;

class ConfigPublisherTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}


	public function testPackageConfigPublishing()
	{
		$pub = new Illuminate\Foundation\ConfigPublisher($files = m::mock('Illuminate\Filesystem'), __DIR__);
		$pub->setPackagePath(__DIR__.'/vendor');
		$files->shouldReceive('exists')->once()->with(__DIR__.'/vendor/foo/bar/src/config/bar.php')->andReturn(true);
		$files->shouldReceive('copy')->once()->with(__DIR__.'/vendor/foo/bar/src/config/bar.php', __DIR__.'/packages/foo/bar.php')->andReturn(true);

		$this->assertTrue($pub->publishPackage('foo/bar'));

		$pub = new Illuminate\Foundation\ConfigPublisher($files2 = m::mock('Illuminate\Filesystem'), __DIR__);
		$files2->shouldReceive('exists')->once()->with(__DIR__.'/custom-packages/foo/bar/src/config/bar.php')->andReturn(true);
		$files2->shouldReceive('copy')->once()->with(__DIR__.'/custom-packages/foo/bar/src/config/bar.php', __DIR__.'/packages/foo/bar.php')->andReturn(true);

		$this->assertTrue($pub->publishPackage('foo/bar', __DIR__.'/custom-packages'));
	}

}