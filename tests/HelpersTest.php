<?php

class HelpersTest extends Illuminate\Foundation\TestCase {

	public function testRouteHelper()
	{
		$app = Illuminate\Foundation\LightSwitch::flip();
		$app->get('foo', function() {})->bind('bar');
		$this->assertEquals('/foo', route('bar'));
	}

}