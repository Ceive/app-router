<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing;


use Ceive\Routing\Route;
use Ceive\Routing\RouteContainer;

trait RouteContainerTrait{
	
	/** @var  Route[]|RouteContainer[] */
	protected $routes = [];
	
	public function getRoutes(){
		return $this->routes;
	}
	
	
	/**
	 * @param Route $route
	 * @param bool $recursive
	 * @return bool
	 */
	public function isContains(Route $route, $recursive = true){
		$nextRecursive = is_numeric($recursive)?$recursive-1:$recursive;
		foreach($this->routes as $c){
			if($route === $c || ($c instanceof RouteContainer && $c->isContains($route, $nextRecursive))){
				return true;
			}
		}
		return false;
	}
	
	/**
	 * @param $reference
	 * @param bool $recursive
	 * @return RouteContainer[]|Route[]
	 */
	public function findByReference($reference, $recursive = true){
		$nextRecursive = is_numeric($recursive)?$recursive-1:$recursive;
		$contains = [];
		foreach($this->routes as $route){
			if($route->getDefaultReference() === $reference){
				$contains[] = $route;
			}elseif($recursive && $route instanceof RouteContainer){
				$contains = array_merge($contains, (array)$route->findByReference($reference, $nextRecursive));
			}
		}
		return $contains;
	}
	
	
	/**
	 * @param $reference
	 * @param bool $recursive
	 * @return RouteContainer|Route|null
	 */
	public function findFirstByReference($reference, $recursive = true){
		$nextRecursive = is_numeric($recursive)?$recursive-1:$recursive;
		foreach($this->routes as $route){
			if($route->getDefaultReference() === $reference){
				return $route;
			}elseif($recursive && $route instanceof RouteContainer && ($r = $route->findFirstByReference($reference, $nextRecursive))){
				return $r;
			}
		}
		return null;
	}
	
	
	/**
	 * @param callable $checker
	 * @param bool $recursive
	 * @return RouteContainer|Route|null
	 */
	public function findFirstBy(callable $checker, $recursive = true){
		$nextRecursive = is_numeric($recursive)?$recursive-1:$recursive;
		foreach($this->routes as $route){
			if(call_user_func($checker, $route)){
				return $route;
			}elseif($recursive && $route instanceof RouteContainer && ($r = $route->findFirstBy($checker, $nextRecursive))){
				return $r;
			}
		}
		return null;
	}
	
	
	/**
	 * @param callable $checker
	 * @param bool $recursive
	 * @return RouteContainer[]|Route[]
	 */
	public function findBy(callable $checker, $recursive = true){
		$nextRecursive = is_numeric($recursive)?$recursive-1:$recursive;
		$contains = [];
		foreach($this->routes as $route){
			if(call_user_func($checker, $route)){
				$contains[] = $route;
			}elseif($recursive && $route instanceof RouteContainer){
				$contains = array_merge($contains, (array)$route->findBy($checker, $nextRecursive));
			}
		}
		return $contains;
	}
	
	
}
