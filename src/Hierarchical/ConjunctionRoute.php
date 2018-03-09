<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Ceive\Routing\Hierarchical;

use Ceive\Routing\Exception\Matching\MissingException;
use Ceive\Routing\Exception\Matching\SkipException;
use Ceive\Routing\Matching;
use Ceive\Routing\Route;
use Ceive\Routing\RouteAbstract;
use Ceive\Routing\RouteContainer;
use Ceive\Routing\RoutingException;
use Ceive\Routing\RouteContainerTrait;


// TODO сделать ConjunctionRoute наследником Router т.к в обоих используется агрегация дочерних маршрутов
// TODO перенести в Router методы поиска маршрутов

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class ConjunctionRoute
 * @package Ceive\Routing\Hierarchical
 *
 * kwd: Channel(Канал), Relay(Реле), ConjunctionRoute(Связка)
 *
 * @property Route[]|RouteContainer[] $routes
 */
class ConjunctionRoute extends RouteAbstract implements ParentAwareInterface, RouteContainer{

	use RouteLeafTrait;
	use RouteContainerTrait;
	
	/**
	 * Означает что он не может быть Reached, и на него не может быть ссылки.
	 * Делегирует обработку дочерним маршрутам при сопоставлении себя.
	 * @var bool
	 */
	protected $is_symbolic = false;
	
	
	/**
	 * @param Matching $matching
	 * @return Matching
	 * @throws \Exception
	 */
	public function match(Matching $matching){
		
		
		$this->_doMatch($matching);

		if(!$matching->isConformed()){
			$matching->reset();
			return $matching;
		}
		
		$this->_checkEnv($matching);
		
		if($matching->isReached() || !$this->routes){
			
			if($this->is_symbolic){
				return $matching->setConformed(false);
			}else{
				$this->_matchingConformed($matching);
				$this->_matchingReached($matching);
				return $matching;
			}
			
		}else{
			$this->_matchingConformed($matching);
		}
		
		$result = null;
		$decorator = $this->decorateMatching($matching);
		try{
			foreach($this->routes as $route){
				try{
					try{
						$result = $route->match($decorator);
						if($result->isConformed()){
							break;
						}
						$result = null;
					}catch(\Exception $e){
						if(!$this->_catchException($e, $route, $decorator)){
							throw $e;
						}
					}
				}catch (SkipException $e){
					$this->_catchSkip($e, $route, $decorator);
				}
			}
			if(!$result && !$decorator->isReached()){
				$missing = new MissingException();
				$missing->route = $this;
				$missing->matching = $decorator;
				throw $missing;
			}
		}catch(MissingException $missing){
			$result = $this->_catchMissing($missing);
		}
		
		if($decorator->isReached() || ($result instanceof MatchingDecorator && $result->isReached())){
			
			$result = $result?$result:$decorator;
			
			//$result->apply();// Вызывается для декоратора
			
			$this->_matchingFinish($result);
			return $result;
		}
		
		
		
		$matching->reset();
		return $matching;
		
	}
	
	/**
	 * @param $path
	 * @param null $matched_received
	 * @return array|bool|false
	 * @throws RoutingException
	 */
	protected function _matchPath($path, &$matched_received = null){
		$resolver = $this->getRouter()->getPatternResolver();
		$data = $resolver->patternMatchStart($path, $this->pattern, $this->pattern_options, $matched_received);
		if($data === false){
			$matched_received = null;
		}
		return $data;
	}
	
	/**
	 * @param $matching
	 * @return MatchingDecorator
	 */
	protected function decorateMatching($matching){
		return new MatchingDecorator($matching);
	}
	
	/**
	 * @param Route $route
	 * @return RouteDecorator
	 */
	protected function decorateRoute(Route $route){
		return new RouteDecorator($this, $route);
	}
	
	/**
	 * @param Route $route
	 * @return $this
	 */
	public function addRoute(Route $route){
		if($route instanceof ConjunctionRoute){
			$route->setParent($this);
			$this->routes[] = $r = $route;
		}else{
			$this->routes[] = $r = $this->decorateRoute($route);
		}
		
		$this->getRouter()->registerRoute($r);
		
		return $this;
	}
	
	public function __clone(){
		foreach($this->routes as $i => $route){
			$route = clone $route;
			if($route instanceof ParentAwareInterface){
				$route->setParent($this);
			}
			$this->routes[$i] = $route;
		}
	}
	
	/**
	 * @param callable $checker
	 * @return Route[]|ConjunctionRoute[]
	 */
	public function findWithLocations(callable $checker){
		$contains = $this->findBy($checker);
		// TODO: low-priority: Профилактика избыточности циклов
		$a = [];
		foreach($contains as $route){
			$route_path = $this->locate($route);
			$a[] = $route_path;
		}
		return [$contains, $a];
	}
	
	
	/**
	 * @param Route $route
	 *
	 * @return Route[]|ConjunctionRoute[] - from current to target route
	 */
	public function locate(Route $route){
		$to_high = [];
		$this->_findWay($route,$to_high);
		
		// Специальный объект "Цепочка вложенности - путь до целевого маршрута"
		//return new NestingPath(array_reverse($to_high));
		
		return array_reverse($to_high);
	}
	
	/**
	 * @todo locateBy: У нескольких Маршрутов возможны одинаковые reference или другие атрибуты.
	 * @todo locateTo: Одна reference для нескольких маршрутов. Какой искомый из них маршрут будет рендериться? как обозначить нужный? контекст или другие атрибуты
	 * Поиск пути до вложенного маршрута (глубокий поиск)
	 * @param callable $checker - function(Route $route, Route[] $before_routes){return true || false;}
	 *
	 * @return Route[]|ConjunctionRoute[] - from[0] current to target[+] route
	 */
	public function locateBy(callable $checker){
		$to_high = [];
		$this->_findWayBy($checker,$to_high);
		
		// Специальный объект "Цепочка вложенности - путь до целевого маршрута"
		//return new NestingPath(array_reverse($to_high));
		
		return array_reverse($to_high);
	}
	
	/**
	 * @param Route $route
	 * @param array $way
	 * @param array $behind
	 * @param bool $_self
	 * @return bool
	 *
	 * $way == [
	 *      0 => Route(Target checker conformed),
	 *      1 => Route(Container),
	 *      2 => Route(Container),
	 *      3 => Route(Container),
	 *      4 => Route(MainContainer)
	 * ]
	 */
	private function _findWay(Route $route, &$way = [], $behind = [], $_self = true){
		if($_self && $route === $this){
			$way[] = $this;
			return true;
		}
		$behind[] = $this;
		foreach($this->routes as $c){
			if($route === $c){
				$way[] = $c;
				$way[] = $this;
				return true;
			}else if($c instanceof ConjunctionRoute && $c->_findWay($route, $way, $behind, false)){
				$way[] = $this;
				return true;
			}
		}
		return false;
	}
	
	/**
	 * @see findWayTo for more
	 *
	 * @param callable $checker
	 * @param array $way
	 * @param array $behind
	 * @param bool $_self
	 * @return bool $way == [
	 *
	 * $way == [
	 *      0 => Route(Target checker conformed),
	 *      1 => Route(Container),
	 *      2 => Route(Container),
	 *      3 => Route(Container),
	 *      4 => Route(MainContainer)
	 * ]
	 *
	 */
	private function _findWayBy(callable $checker, &$way = [], $behind = [], $_self = true){
		
		if($_self && call_user_func($checker, $this, $behind)){
			$way[] = $this;
			return true;
		}
		
		$behind[] = $this;
		foreach( $this->routes as $c ){
			if(call_user_func($checker, $c, $behind)){
				$way[] = $c;
				$way[] = $this;
				return true;
			}elseif($c instanceof ConjunctionRoute && $c->_findWayBy($checker, $way, $behind, false)){
				$way[] = $this;
				return true;
			}
		}
		return false;
		
	}
	
	/**
	 * @param null $params
	 * @return string
	 */
	protected function _render($params = null){
		return parent::render($params);
	}
	
	/**
	 * @param MatchingDecorator|Matching $matching
	 * @return Route|null
	 */
	protected function _notFoundChild(MatchingDecorator $matching){
		return $this->getRouter()->fireCollector('notFoundChild', [$matching, $this], true);
		
	}
	
	/**
	 * @param SkipException $e
	 * @param Route $route
	 * @param MatchingDecorator|Matching $matching
	 */
	protected function _catchSkip(SkipException $e, Route $route, MatchingDecorator $matching){
		$this->getRouter()->fireEvent('catchSkip', [$matching, $route, $this, $e]);
	}
	
	/**
	 * @param $missing
	 * @return array|mixed
	 */
	protected function _catchMissing($missing){
		return $this->getRouter()->fireCollector('catchMissing', [$missing, $this], true);
	}
	
	/**
	 * @param \Exception $e
	 * @param Route $route
	 * @param MatchingDecorator $matching
	 * @return bool catched
	 */
	protected function _catchException(\Exception $e, Route $route, MatchingDecorator $matching){
		$results = $this->getRouter()->fireEvent('catchException', [$matching, $route, $this, $e]);
		return in_array(true, $results, true);
	}
	
	
	
}


