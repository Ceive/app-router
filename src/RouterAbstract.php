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
use Exception;

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
	 * Router As Plugin
	 * @var array
	 */
	public $pluginPriorities = [
		// router
		'onBegin'                   => 0,
		// route
		'beforeMatch'               => 0,
		'onCheckEnv'                => 0,
		'onConformed'               => 0,
		'onReached'                 => 0,
		'onFinish'                  => 100000,
		'onPrepareRenderParams'     => 0,
		// in iteration (conjunction and router)
		'onCatchSkip'               => 0,
		'onCatchException'          => 0,
		// after iterations (conjunction and router)
		'notFoundChild'             => 0,
	];
	
	/** @var  object[] */
	protected $plugins = [];
	
	protected $plugins_by_priority = [];
	
	/** @var  object[]|callable[] */
	protected $methods = [];
	
	/** @var  mixed[] */
	protected $methods_cache = [];
	
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
	 * @return Matching
	 */
	public function match(Matching $matching){
		$this->resetMethodCache();
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
	 * @return \Generator|Matching[]
	 */
	public function matchLoop(Matching $matching){
		$this->resetMethodCache();
		$this->fireEvent('begin', [$matching] );
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
	
	/**
	 * @param $plugin
	 * @return $this
	 */
	public function addPlugin($plugin){
		if(is_object($plugin)){
			$this->plugins[] = $plugin;
			if(method_exists($plugin, 'attachRouter')){
				call_user_func([$plugin, 'attachRouter'], $this);
			}
		}
		return $this;
	}
	/**
	 * @param $plugin
	 * @return $this
	 */
	public function removePlugin($plugin){
		if(is_object($plugin)){
			$i = array_search($plugin, $this->plugins, true);
			if($i !== false){
				array_splice($this->plugins, $i, 1);
				if(method_exists($plugin, 'detachRouter')){
					call_user_func([$plugin, 'detachRouter'], $this);
				}
			}
		}
		return $this;
	}
	
	
	/**
	 * @param $name
	 * @param array $arguments
	 * @param bool $stoppable
	 * @return array|bool
	 *
	 * * * router
	 * @listener onBegin($matching);
	 *
	 * * * route
	 * @listener onCheckEnv($matching, $route);
	 * @listener onConformed($matching, $route);
	 * @listener onReached($matching, $route);
	 * @listener onFinish($matching, $route);
	 * @listener onPrepareRenderParams($route, $params);
	 *
	 * * * in iteration (conjunction and router)
	 * @listener onCatchSkip($matching, $route, $conjunctionRoute, $exception);
	 * @listener onCatchException($matching, $route, $conjunctionRoute, $exception);
	 *
	 * * * after iterations (conjunction and router)
	 * @listener notFoundChild($matching, $parentRoute=null);
	 *
	 */
	public function fireEvent($name, array $arguments, $stoppable = false){
		$method = 'on'.$name;
		$results = [];
		
		foreach( $this->_getPluginsOrdered($name) as $plugin){
			if(method_exists($plugin, $method)){
				if(method_exists($plugin, 'setRouter')){
					$results[] = call_user_func([$plugin, 'setRouter'], $this);
				}
				$result = call_user_func_array([$plugin, $method], $arguments);
				if($stoppable && $result === false){
					return false;
				}
				$results[] = $result;
			}
		}
		return $results;
	}
	
	/**
	 * @param $name
	 * @param array $arguments
	 * @param bool $modeFirst
	 * @return array|bool
	 */
	public function fireCollector($name, array $arguments, $modeFirst = false){
		$method = 'on'.$name;
		$results = [];
		foreach( $this->_getPluginsOrdered($name) as $plugin){
			if(method_exists($plugin, $method)){
				if(method_exists($plugin, 'setRouter')){
					$results[] = call_user_func([$plugin, 'setRouter'], $this);
				}
				$result = call_user_func_array([$plugin, $method], $arguments);
				if($modeFirst && $result){
					return $result;
				}
				$results[] = $result;
			}
		}
		
		if($modeFirst){
			return null;
		}
		
		return $results;
	}
	
	/**
	 * @param $event
	 * @return mixed|\object[]
	 */
	protected function _getPluginsOrdered($event){
		if(!isset($this->plugins_by_priority[$event])){
			$plugins = $this->plugins;
			array_unshift($plugins, $this);
			usort($plugins, function($a, $b) use($event){
				$a = $this->_fetchPluginPriority($a, $event);
				$b = $this->_fetchPluginPriority($b, $event);
				if($a == $b) return 0;
				return $a > $b?1:-1;
			});
			$this->plugins_by_priority[$event] = $plugins;
		}else{
			$plugins = $this->plugins_by_priority[$event];
		}
		return $plugins;
	}
	
	/**
	 * @param $plugin
	 * @param $event
	 * @return int|mixed
	 */
	protected function _fetchPluginPriority($plugin, $event){
		if(property_exists($plugin,'pluginPriorities') && $plugin->pluginPriorities){
			if(is_array($plugin->pluginPriorities)){
				return !empty($plugin->pluginPriorities[$event])?$plugin->pluginPriorities[$event]:0;
			}
			return $plugin->pluginPriorities?:0;
		}else if(method_exists($plugin, 'getPluginPriority')){
			return call_user_func([$plugin, 'getPluginPriority'], $event)?:0;
		}
		return 0;
	}
	
	/**
	 * @param $key
	 * @param $method
	 * @param bool $cached
	 * @return $this
	 */
	public function setMethod($key, $method, $cached = false){
		$this->methods[$key] = [$method, $cached];
		return $this;
	}
	
	/**
	 * @param null $key
	 * @return $this
	 */
	public function resetMethodCache($key = null){
		if($key === null){
			$this->methods_cache = [];
		}else{
			unset($this->methods_cache[$key]);
		}
		return $this;
	}
	
	/**
	 * @param $key
	 * @param Matching $matching
	 * @param array $arguments
	 * @return mixed
	 * @throws \Exception
	 */
	public function method($key, Matching $matching, array $arguments){
		if(array_key_exists($key, $this->methods_cache) ){
			return $this->methods_cache[$key];
		}
		if(isset($this->methods[$key])){
			list($method, $cached) = $this->methods[$key];
			$arguments = array_merge([$matching], $arguments);
			if(is_callable($method)){
				$result = call_user_func_array($method, $arguments);
			}else if(is_object($method)){
				if(method_exists($method, 'setRouter')){
					$result = call_user_func([$method, 'setRouter'], $this);
				}
				if(method_exists($method, 'run')){
					$result = call_user_func_array([$method, 'run'], $arguments);
				}
			}
			if(isset($result)){
				if($cached)$this->methods_cache[$key] = $result;
				return $result;
			}
		}
		throw new Exception('Method "'.$key.'" not found');
	}
	
	
	public function setNotFound(){
		[
			'pattern' => '/users',
			'not_conformed' => [
				'action' => ''
			],
			'children' => [[
				'pattern' => '/{user.id}'
			],[
				'pattern' => '/new'
			]]
		];
	}
	
}