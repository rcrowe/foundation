<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\LightSwitch;

class ModelBindingTest extends Silex\WebTestCase {

	public static function setUpBeforeClass()
	{
		LightSwitch::flip();
	}


	public function testClosureModelBinder()
	{
		$this->app->addModelBinder('user', function($id) { return $id.'!'; });
		$this->app->get('something/{user}', function($user) { return $user; });
		$client = $this->createClient();
		$crawler = $client->request('GET', '/something/taylor');
		$this->assertEquals('taylor!', $client->getResponse()->getContent());
	}


	public function testCustomModelBinder()
	{
		$this->app->addModelBinder('user', 'ModelBindingTestBinderStub');
		$this->app->get('something/{user}', function($user) { return $user; });
		$client = $this->createClient();
		$crawler = $client->request('GET', '/something/taylor');
		$this->assertEquals('taylor!', $client->getResponse()->getContent());	
	}


	public function testMultiBinderRegistration()
	{
		$this->app->addModelBinders(array('user' => 'foo', 'order' => 'bar'));
		$this->assertEquals('foo', $this->app['controllers']->binders['user']);
		$this->assertEquals('bar', $this->app['controllers']->binders['order']);
	}


	public function createApplication()
	{
		$app = new Application;
		$app['ioc'] = new ModelBindingTestIoCStub;
		return $app;
	}

}

class ModelBindingTestIoCStub {

	public function resolve($class)
	{
		if ($class !== 'ModelBindingTestBinderStub')
		{
			throw new Exception("Invalid IoC argument.");
		}

		return new ModelBindingTestBinderStub;
	}

}

class ModelBindingTestBinderStub implements Illuminate\Foundation\ModelBinderInterface {

	public function resolveBinding($id, Symfony\Component\HttpFoundation\Request $request)
	{
		return $id.'!';
	}

}