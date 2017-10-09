<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */
namespace Ceive\Routing;

use Ceive\Routing\Exception\Matching\SkipException;
use Ceive\Routing\Route\BindingAdapter;
use Ceive\Routing\Route\PatternResolver;
use Ceive\Routing\Simple\SimpleMatching;
use Ceive\Routing\RouteContainerTrait;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class RouterAbstract
 * @package Ceive\Routing
 */
abstract class RouterAbstract implements Router, RouteContainer{
	
	use RouteContainerTrait;
	
	/** @var  Route[]  */
	protected $registered_routes = [];
	
	/** @var  PatternResolver */
	protected $pattern_resolver;

	/** @var  BindingAdapter */
	protected $binding_adapter;

	/**
	 * RouterAbstract constructor.
	 * @param PatternResolver $resolver
	 */
	public function __construct(PatternResolver $resolver){
		$this->pattern_resolver = $resolver;
	}

	/**
	 * @return PatternResolver
	 */
	public function getPatternResolver(){
		return $this->pattern_resolver;
	}


	/**
	 * @param Matching $matching
	 * @return \Generator|Matching[]
	 */
	public function matchLoop(Matching $matching){
		foreach($this->routes as $route){
			try{
				$match = $route->match($matching);
				if($match->isConformed()){
					yield $match;
				}
			}catch (SkipException $skipping){
				$matching->reset();
			}
		}
	}

	/**
	 * @param Matching $matching
	 * @return Matching
	 */
	public function match(Matching $matching){
		$this->fireEvent('begin', [$matching] );
		foreach($this->routes as $route){
			try{
				$route->match($matching);
				if($matching->isConformed()){
					return $matching;
				}else{
					$matching->reset();
				}
			}catch (SkipException $skipping){
				$matching->reset();
			}
		}
		return $matching;
	}
	
	
	/**
	 * @param Matching $matching
	 * @return void
	 */
	public function process(Matching $matching){
		foreach($this->matchLoop($matching) as $result){}
	}
	
	/**
	 * @param \Ceive\Routing\Route $route
	 * @return $this
	 */
	public function addRoute(Route $route){
		$route->setRouter($this);
		if(!in_array($route, $this->routes, true)){
			$this->routes[] = $route;
		}
		if(!in_array($route, $this->registered_routes, true)){
			$this->registered_routes[] = $route;
		}
		return $this;
	}
	
	/**
	 * @param Route $route
	 * @return $this
	 */
	public function registerRoute(Route $route){
		$route->setRouter($this);
		if(!in_array($route, $this->registered_routes, true)){
			$this->registered_routes[] = $route;
		}
		return $this;
	}
	
	/**
	 * @param callable $collector
	 * @return Route[]
	 */
	public function findRegisteredRoute(callable $collector){
		$a = [];
		foreach($this->registered_routes as $route){
			if(call_user_func($collector, $route) === true){
				$a[] = $route;
			}
		}
		return $a;
	}
	
	/**
	 * @return Route[]
	 */
	public function getRoutes(){
		return array_values($this->routes);
	}
	
	/**
	 * TODO: MIND
	 * ИСП. ЕСЛИ: Единица Вычисления создается в Маршрутизаторе
	 * @param $path
	 * @return SimpleMatching
	 */
	public function factoryMatching($path){
		return new SimpleMatching($path);
	}
	
	/**
	 * @param BindingAdapter $adapter
	 * @return $this
	 */
	public function setBindingAdapter(BindingAdapter $adapter){
		$this->binding_adapter = $adapter;
		return $this;
	}
	
	public function getBindingAdapter(){
		return $this->binding_adapter;
	}
	
	public function extendReference($ancestor, $successor){
		return $successor;
	}
	
	public $stack = [];
	
	/** @var  object[] */
	protected $plugins = [];
	
	/**
	 * @param $plugin
	 * @return $this
	 */
	public function addPlugin($plugin){
		if(is_object($plugin)){
			$this->plugins[] = $plugin;
		}
		return $this;
	}
	
	/**
	 * @param $name
	 * @param array $arguments
	 * @param bool $stoppable
	 * @return array|bool
	 */
	public function fireEvent($name, array $arguments, $stoppable = false){
		$method = 'on'.$name;
		$results = [];
		foreach($this->plugins as $plugin){
			if(method_exists($plugin, 'setRouter')){
				$results[] = call_user_func([$plugin, 'setRouter'], $this);
			}
			if(method_exists($plugin, $method)){
				$result = call_user_func_array([$plugin, $method], $arguments);
				if($stoppable && $result === false){
					return false;
				}
				$results[] = $result;
			}
		}
		return $results;
		//$this->stack[] = [$name, $route, $matching];
		
	}
	
	public function extra($method, Matching $matching, array $arguments){
		// TODO: Implement extra() method.
	}
	
	
}