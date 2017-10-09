<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing\Tests;


use Ceive\Routing\FactoryDirector;
use Ceive\Routing\Hierarchical\ConjunctionFactory;
use Ceive\Routing\Hierarchical\ConjunctionRoute;
use Ceive\Routing\Hierarchical\MatchingReached;
use Ceive\Routing\Route\MyBindingAdapter;
use Ceive\Routing\Simple\SimpleMatching;
use Ceive\Routing\Simple\SimplePatternResolver;
use Ceive\Routing\Simple\SimpleRoute;
use Ceive\Routing\Simple\SimpleRouteFactory;
use Ceive\Routing\Simple\SimpleRouter;

class FactoryTest extends BaseRouterTest{
	
	public function testFactory(){
		/**
		 *
		 * Были сделаны Декораторы Leaf-Маршрутов RouteDecorator - который обеспечивает каждый Лист в коньюнкции - родителем, при этом так-же сама коньюнкция имеет родителя
		 * получается что леафы можно использовать без клонирования TODO: сделать пулл маршрутов в Роутере, для хранения по ключу псевдонима
		 * посредством использования @alias псевдонима, по нему будет происходить выборка из пулла маршрутов,а после уже декорация
		 * следует учитывать что ConjunctionRoute::addRoute оборачивает маршрут только если он не наследован ParentAwareInterface,
		 * иначе же произойдет смена родителя у добавляемого маршрута,
		 * поэтому при попытке уподобления свойств Коньюнкции или Декеоратора следует их клонировать перед подачей в addRoute
		 *
		 * золотое правило для этой реализации: TODO: линейные стандартные маршруты можно использовать в разных местах одновременно
		 * а нелинейные и ParentAware следует клонировать
		 *
		 */
	}
	
	public function testRendering(){
		
		$sub = $this->router->findFirstByReference('user.list');
		$result = $sub->render();
		$this->assertEquals('/users',$result);
		
		$sub = $this->router->findFirstByReference('user.index');
		$result = $sub->render(['user__id' => 1]);
		$this->assertEquals('/users/1',$result);
		
		$sub = $this->router->findFirstByReference('user.update');
		$result = $sub->render(['user__id' => 1]);
		$this->assertEquals('/users/1/update',$result);
		
		$sub = $this->router->findFirstByReference('user.note.update');
		$result = $sub->render(['user__id' => 1,'note__id'=>211]);
		$this->assertEquals('/users/1/notes/211/update',$result);
		
		
	}
	
	
	public function testRenderingWithObjects(){
		
		$user = [
			'id' => 1
		];
		$note = [
			'id' => 211
		];
		
		$sub = $this->router->findFirstByReference('user.list');
		$result = $sub->render();
		$this->assertEquals('/users',$result);
		
		
		$sub = $this->router->findFirstByReference('user.update');
		$result = $sub->render(['user' => $user]);
		$this->assertEquals('/users/1/update',$result);
		
		
		$sub = $this->router->findFirstByReference('user.note.update');
		$result = $sub->render(['user' => $user,'note'=>$note]);
		$this->assertEquals('/users/1/notes/211/update',$result);
		
		
	}
	
	public function testMatching(){
		$matching = new SimpleMatching('/users/2334');
		foreach($this->router->matchLoop($matching) as $routeSource){
			$reached = new MatchingReached($routeSource);
			$reached->way();
		}
		
		$matching = new SimpleMatching('/users/2334/notes/122/delete');
		foreach($this->router->matchLoop($matching) as $routeSource){
			$reached = new MatchingReached($routeSource);
			$reached->way();
		}
		
		
	}
	
}


