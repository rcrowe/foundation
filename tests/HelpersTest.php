<?php

class HelpersTest extends Illuminate\Foundation\TestCase {

	public function testPathHelper()
	{
		$app = Illuminate\Foundation\LightSwitch::flip();
		$app->get('foo', function() {})->bind('bar');
		$this->assertEquals('/foo', path('bar'));
	}

}