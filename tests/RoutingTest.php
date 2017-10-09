<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Ceive\Routing\Tests;


use Ceive\Routing\Simple\SimpleMatching;
use Ceive\Routing\Simple\SimplePatternResolver;
use Ceive\Routing\Simple\SimpleRoute;
use Ceive\Routing\Simple\SimpleRouter;

class RoutingTest extends \PHPUnit_Framework_TestCase{

	/** @var  SimpleRoute */
	protected $route;

	/** @var  SimpleRouter */
	protected $router;


	/**
	 *
	 */
	public function setUp(){
		$this->route = new SimpleRoute('user:update',
			/** @lang text */
			'/users/(?<user_id>[1-9][0-9]*)/update'
		);

		$this->router = new SimpleRouter(new SimplePatternResolver());
		$this->router->addRoute($this->route);// not named
		$this->router->addRoute($this->route);// named to my_name
	}

	/**
	 * @expectedException
	 */
	public function testRoute(){
		$this->assertEquals($this->route->getRouter(), $this->router);
		$this->assertEquals($this->route->getDefaultReference(), 'user:update');
		$this->assertEquals($this->route->getPatternParams(), [ 'user_id' ]);
	}
	
	public function testRenderRoute(){
		// В SimplePatternResolver и SimpleRoute не проводится проверка сопоставления по входному шаблону, просто подстановка вместо плейсхолдера
		$this->assertEquals($this->route->render(['user_id' => 27]), '/users/27/update' );
		$this->assertEquals($this->route->render(['user_id' => '...']), '/users/.../update' );
		$this->assertEquals($this->route->render(['user_id' => null]), '/users//update' );
		$this->assertEquals($this->route->render([]), '/users//update' );
	}


	public function testMatching(){
		$matching = new SimpleMatching('/users/88/update');

		$this->assertEquals($matching->isConformed(), false);

		$matching = $this->router->match($matching);

		$this->assertEquals($matching->isConformed(), true);
		$this->assertEquals($matching->getReference(), 'user:update');
		$this->assertEquals($matching->getParams(), [
			'user_id' => '88' // Да да это строка, т.к в простом маршруте никаких приведений типов не проводится
		]);
	}
}


