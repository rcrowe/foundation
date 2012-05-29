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


	public function testTranslationHelpers()
	{
		$app = Illuminate\Foundation\LightSwitch::flip();
		$app['translator'] = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
		$app['translator']->expects($this->once())->method('trans')->with($this->equalTo('message.key'), $this->equalTo(array('foo' => 'bar')), $this->equalTo('domain'), $this->equalTo('locale'));
		trans('message.key', array('foo' => 'bar'), 'domain', 'locale');

		$app['translator']->expects($this->once())->method('transChoice')->with($this->equalTo('message.key'), $this->equalTo(1), $this->equalTo(array('foo' => 'bar')), $this->equalTo('domain'), $this->equalTo('locale'));
		transChoice('message.key', 1, array('foo' => 'bar'), 'domain', 'locale');
	}

}