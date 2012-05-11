<?php

use Illuminate\Foundation\Application;

class RoutingTest extends Illuminate\Foundation\TestCase {

	public function testAutoSlashes()
	{
		$app = new Application;
		$controller = $app->get('something', function() {});
		$this->assertEquals('/something', $controller->getRoute()->getPattern());
	}


	public function testMethodIsSetCorrectly()
	{
		$app = new Application;
		$controller = $app->get('something', function() {});
		$this->assertEquals('GET', $controller->getRoute()->getRequirement('_method'));
		$controller = $app->post('something', function() {});
		$this->assertEquals('POST', $controller->getRoute()->getRequirement('_method'));
		$controller = $app->put('something', function() {});
		$this->assertEquals('PUT', $controller->getRoute()->getRequirement('_method'));
		$controller = $app->delete('something', function() {});
		$this->assertEquals('DELETE', $controller->getRoute()->getRequirement('_method'));
		$controller = $app->get('something', function() {});
	}


	public function testRouteControllerIsSet()
	{
		$app = new Application;
		$controller = $app->get('something', function() { return 'foo'; });
		$callable = $controller->getRoute()->getDefault('_controller');
		$this->assertEquals('foo', $callable());
	}


	public function testMatchShortcut()
	{
		$app = new Application;
		$controller = $app->match('something', array('on' => 'get|post', function () {}));
		$this->assertEquals('GET|POST', $controller->getRoute()->getRequirement('_method'));
	}


	public function testHttpsShortcut()
	{
		$app = new Application;
		$controller = $app->get('something', array('https' => true, function() {}));
		$this->assertEquals('https', $controller->getRoute()->getRequirement('_scheme'));
		$controller = $app->get('something', array('https' => false, function() {}));
		$this->assertEquals('http', $controller->getRoute()->getRequirement('_scheme'));
	}


	public function testMiddlewareShortcut()
	{
		$app = new Application;
		$app->middleware('auth', function() { return 'auth!'; });
		$controller = $app->get('something', array('before' => 'auth', function() {}));
		$middlewares = $controller->getRoute()->getDefault('_middlewares');
		$this->assertEquals('auth!', $middlewares[0]());
	}


	public function testMultiMiddlewareShortcut()
	{
		$app = new Application;
		$app->middleware('foo', function() { return 'foo!'; });
		$app->middleware('bar', function() { return 'bar!'; });
		$controller = $app->get('something', array('before' => 'foo|bar', function() {}));
		$middlewares = $controller->getRoute()->getDefault('_middlewares');
		$this->assertEquals('foo!', $middlewares[0]());
		$this->assertEquals('bar!', $middlewares[1]());	
	}


	public function testNameShortcut()
	{
		$app = new Application;
		$controller = $app->get('something', array('as' => 'foo', function() {}));
		$this->assertEquals('foo', $controller->getRouteName());
	}


	public function testRouteGrouping()
	{
		$app = new Application;
		$app->middleware('auth', function() { return 'foo!'; });
		$app->group(array('before' => 'auth'), function($app)
		{
			$app->get('foo', function() {});
			$app->get('bar', function() {});
			$app->get('baz', array('before' => null, function() {}));
		});
		$routes = array_values($app['controllers']->flush()->all());
		$firstMiddlewares = $routes[0]->getDefault('_middlewares');
		$this->assertEquals('foo!', $firstMiddlewares[0]());
		$secondMiddlewares = $routes[1]->getDefault('_middlewares');
		$this->assertEquals('foo!', $secondMiddlewares[0]());
		$thirdMiddlewares = $routes[2]->getDefault('_middlewares');
		$this->assertEquals(0, count($thirdMiddlewares));
	}


	public function testPoundSignShortcut()
	{
		$app = new Application;
		$controller = $app->get('foo/{#:bar}', function() {});
		$this->assertEquals('/foo/{bar}', $controller->getRoute()->getPattern());
		$this->assertEquals('\\d+', $controller->getRoute()->getRequirement('bar'));
	}

}