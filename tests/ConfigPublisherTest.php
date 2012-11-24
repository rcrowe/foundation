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
		$files->shouldReceive('copyDirectory')->once()->with(__DIR__.'/vendor/foo/src/config', __DIR__.'/packages/foo')->andReturn(true);

		$this->assertTrue($pub->publishPackage('foo'));

		$pub = new Illuminate\Foundation\ConfigPublisher($files2 = m::mock('Illuminate\Filesystem'), __DIR__);
		$files2->shouldReceive('copyDirectory')->once()->with(__DIR__.'/custom-packages/foo/src/config', __DIR__.'/packages/foo')->andReturn(true);

		$this->assertTrue($pub->publishPackage('foo', __DIR__.'/custom-packages'));
	}

}