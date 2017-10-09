<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing\Tests;

use Ceive\Routing\Hierarchical\ConjunctionRoute;
use Ceive\Routing\Hierarchical\MatchingReached;
use Ceive\Routing\Route;
use Ceive\Routing\Simple\SimpleMatching;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class RouteLinkTest
 * @package Ceive\Routing\Tests
 *
 * route @alias
 *
 *
 */
class RouteLinkTest extends FactoryTest{
	
	public function setUp(){
		parent::setUp();
		$this->router->addRoute($this->factory->createRoute([
			'type'      => ConjunctionRoute::class,
			'pattern'   => '/management',
			'symbolic'  => true,
			'children'  => [
				clone $this->router->findFirstByReference('user.list')//todo: CSS preferred selector
			],
		]));
		$this->router->addRoute($this->factory->createRoute([
			'type'      => ConjunctionRoute::class,
			'pattern'   => '/site',
			'symbolic'  => true,
			'children'  => [
				clone $this->router->findFirstByReference('user.list')//todo: CSS preferred selector
			],
		]));
	}
	
	
	public function testRendering(){
		parent::testRendering();
		
		$base = $this->router->findFirstBy(function(Route $route){return $route->getPattern() === '/management';});
		
		
		$sub = $base->findFirstByReference('user.list');
		$result = $sub->render();
		$this->assertEquals('/management/users',$result);
		
		$sub = $base->findFirstByReference('user.update');
		$result = $sub->render(['user__id' => 1]);
		$this->assertEquals('/management/users/1/update',$result);
		
		$sub = $base->findFirstByReference('user.note.update');
		$result = $sub->render(['user__id' => 1,'note__id'=>211]);
		$this->assertEquals('/management/users/1/notes/211/update',$result);
		
		
	}
	
	public function testMatching(){
		parent::testMatching();
		
		$matching = new SimpleMatching('/management/users/2334');
		foreach($this->router->matchLoop($matching) as $routeSource){
			$reached = new MatchingReached($routeSource);
			$reached->way();
		}
		
		$matching = new SimpleMatching('/management/users/2334/notes/122');
		foreach($this->router->matchLoop($matching) as $routeSource){
			$reached = new MatchingReached($routeSource);
			$reached->way();
		}
		
		
		$matching = new SimpleMatching('/management/users/2334/notes/new');
		foreach($this->router->matchLoop($matching) as $routeSource){
			$reached = new MatchingReached($routeSource);
			$reached->way();
		}
	}
	
	public function testBinding(){
		
	}
	
	
}


