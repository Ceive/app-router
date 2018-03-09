<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing;


use Ceive\Routing\Hierarchical\ParentAwareInterface;
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
	
	public function query($query){
		if(!is_array($query)){
			$a = [];
			if(preg_match_all('@([^\w\[\]\(\)]*)(\w+)?(?:\#(\w+))?(?:\.([\w]+))?(?:\:\:?(\w+)(?:(\(([^\)]+)\))?)?(\[[^\]]+\])*)?@', $query, $m)){
				
				foreach($m[0] as $i => $global){
					
					$a[] = [
						'delimiter' => ($m[1][$i]?:null), // delimiter
						'tag'       => ($m[2][$i]?:null), // tagname
						'id'        => ($m[3][$i]?:null), // identifier
						'class'     => ($m[4][$i]?:null), // class
						'pseudo'    => ($m[5][$i]?[
							'name'      => ($m[5][$i]?:null),
							'arguments' => ($m[6][$i]?:null),
						]:null),
						'attributes' => ($m[7][$i]?:null)  // attributes
					];
					
				}
				
			}
			$query = $a;
			unset($a);
		}
		
		
		$chunk = array_shift($query);
		
		
		$delimiter = trim($chunk['delimiter']);
		
		$collection = [];
		$checker = $this->checker($chunk);
		if(!$delimiter){
			$collection = $this->findBy($checker);
		}else if($delimiter === '>'){
			$collection = $this->findBy($checker,1);
		}else if($delimiter === '~'){
			$collection = [];
			if($this instanceof ParentAwareInterface){
				foreach($this->getParent()->getRoutes() as $r){
					if($r !== $this){
						if(call_user_func($checker, $r)){
							$collection[] = $r;
						}
					}
				}
			}
		}else if($delimiter === '+'){
			$collection = [];
			if($this instanceof ParentAwareInterface){
				$c = false;
				foreach($this->getParent()->getRoutes() as $r){
					if($c){
						if(call_user_func($checker, $r)){
							$collection[] = $r;
						}
						$c = false;
					}elseif($r === $this){
						$c = true;
					}
				}
			}
		}
		
		if(!$query){
			return $collection;
		}
		$a = [];
		foreach($collection as $itm){
			$a = array_merge($a, $itm->query($query));
		}
		return $a;
	}
	
	/**
	 * @param $query
	 * @return \Closure
	 */
	protected function checker($query){
		return function(Route $route) use($query){
			if($route->getDefaultReference() === $query['class']){
				return true;
			}
			return false;
		};
	}
	
}
