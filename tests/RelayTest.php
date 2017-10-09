<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Ceive\Routing\Tests;

use Ceive\Routing\Hierarchical\ConjunctionRoute;
use Ceive\Routing\Route;
use Ceive\Routing\Simple\SimpleMatching;
use Ceive\Routing\Simple\SimplePatternResolver;
use Ceive\Routing\Simple\SimpleRoute;
use Ceive\Routing\Simple\SimpleRouter;

class RelayTest extends \PHPUnit_Framework_TestCase{
	
	/** @var  ConjunctionRoute */
	protected $route;
	
	/** @var  SimpleRouter */
	protected $router;
	
	
	/**
	 *
	 */
	public function setUp(){
		// /users/{user.id}/notes/{note.id}
		$this->router = $router = new SimpleRouter(new SimplePatternResolver());
		
		// TODO смысл поиска маршрута? зачем искать маршруты? может их помечать alias
		$this->route = (new ConjunctionRoute('user:list','/users'))
			->setRouter($router)
			->addRoute(
				(new SimpleRoute('user:create','/create'))->setRouter($router)
			)
			->addRoute(
				(new ConjunctionRoute('user:view','/(?<uid>\d+)')) // matched destination on '/users/91'
				->setRouter($router)
					->addRoute( (new SimpleRoute('user:update', '/update'))->setRouter($router) )
					->addRoute( (new SimpleRoute('user:delete', '/delete'))->setRouter($router) )
					->addRoute(
						(new ConjunctionRoute('user:note:list','/notes'))
							->setRouter($router)
							->addRoute(
								(new ConjunctionRoute('user:note:view','/(?<note_id>\d+)'))
									->setRouter($router)
									->addRoute( (new SimpleRoute('user:note:update', '/update'))->setRouter($router) )
									->addRoute( (new SimpleRoute('user:note:delete', '/delete'))->setRouter($router) )
							)
							->addRoute(
								(new SimpleRoute('user:note:add', '/add'))->setRouter($router)
							)
							->addRoute(
								(new SimpleRoute('user:note:delete', '/delete'))->setRouter($router)
							)
					)
			)->addRoute(
				(new SimpleRoute('user:bo','/(?<babi>\d+)/ba'))->setRouter($router)
			);
		
		
		// Placeholder worker
		
		
	}
	
	/**
	 * @return Route
	 */
	public function getRoute(){
		// TODO: RouteConjunction: поиск вложенных маршрутов
		// Поиск вложенных маршрутов
		return $this->route->findFirstByReference('user:update');
	}
	
	
	/**
	 * @expectedException
	 */
	public function testSearch(){
		$route = $this->route->findFirstByReference('user:update');
		
		$this->assertNotEmpty($route);
		
		$this->assertInstanceOf(Route::class, $route);
		
		$this->assertEquals($route->getDefaultReference(), 'user:update');
	}
	
	
	/**
	 * @expectedException
	 */
	public function testRoute(){
		
		$route = $this->getRoute();
		$this->assertEquals($route->getRouter(), $this->router);
		
		
		
		$location = $this->route->locate($route);
		
		$extended = [
			'pattern' => [],
			'params' => [],
		];
		
		foreach($location as $a){
			$extended['pattern'][] = $a->getPattern();
			$extended['params'] = array_replace($extended['params'],$a->getPatternParams());
		}
		$extended['pattern'] = implode('',$extended['pattern']);
		
		$this->assertEquals($extended['pattern'], '/users/(?<uid>\d+)/update');
		
		//$this->assertEquals($route->getPattern(), 'users/(?<uid>\d+)/update');
		
		$this->assertEquals($route->getDefaultReference(), 'user:update');
		
		$this->assertEquals($extended['params'], [ 'uid' ]);
	}
	
	public function testRenderRoute(){
		// В SimplePatternResolver и SimpleRoute не проводится проверка сопоставления по входному шаблону, просто подстановка вместо плейсхолдера
		$this->assertEquals($this->getRoute()->render(['user_id' => 27]), '/users/27/update' );
		$this->assertEquals($this->getRoute()->render(['user_id' => '...']), '/users/.../update' );
		$this->assertEquals($this->getRoute()->render(['user_id' => null]), '/users//update' );
		$this->assertEquals($this->getRoute()->render([]), '/users//update' );
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


