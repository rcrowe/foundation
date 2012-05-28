<?php

class FunctionsTest extends Illuminate\Foundation\TestCase {

	public function testRouteHelper()
	{
		$app = Illuminate\Foundation\LightSwitch::flip();
		$app->register(new Silex\Provider\UrlGeneratorServiceProvider);
		$app->get('foo', function() {})->bind('bar');
		$this->assertEquals('/foo', route('bar'));

		$app->get('bar/{baz}', function() {})->bind('zoom');
		$app->flush();
		$this->assertEquals('/bar/taylor', route('zoom', array('baz' => 'taylor')));
		unset($GLOBALS['__illuminate.app']);
	}

}