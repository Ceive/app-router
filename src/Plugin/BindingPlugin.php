<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing\Plugin;

use Ceive\Routing\Exception\Matching\MissingException;
use Ceive\Routing\Exception\Matching\SkipException;
use Ceive\Routing\Exception\MatchingException;
use Ceive\Routing\Exception\Rendering\MissingParameterException;
use Ceive\Routing\Exception\Rendering\MissingParametersException;
use Ceive\Routing\Hierarchical\ConjunctionRoute;
use Ceive\Routing\Matching;
use Ceive\Routing\Route;
use Ceive\Routing\Router;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class BindingPlugin
 * @package Ceive\Routing\Tests
 *
 * @listener onBegin($matching);
 * @listener onConformed($matching, $route);
 * @listener onReached($matching, $route);
 * @listener onFinish($matching, $route);
 * @listener onPrepareRenderParams($route, $params);
 * @listener onCheckEnv($matching, $route);
 *
 */
class BindingPlugin{
	
	/** @var  Route\BindingAdapter */
	protected $binding_adapter;
	/** @var  Route\PatternResolver */
	protected $pattern_resolver;
	
	/** @var  Router */
	protected $router;
	
	/** @var  Route */
	protected $route;
	
	/** @var  Matching */
	protected $matching;
	
	public function setRouter(Router $router){
		if($this->router !== $router){
			$this->_setRouter($router);
		}
	}
	
	protected function _setRouter(Router $router){
		$this->router = $router;
		$this->binding_adapter = $router->getBindingAdapter();
		$this->pattern_resolver = $router->getPatternResolver();
	}
	
	/**
	 * @param Matching $matching
	 * @param Route $route
	 * @throws MatchingException
	 */
	public function onConformed(Matching $matching, Route $route){
		
		$patternParams = $params = array_replace((array)$route->getDefaultParams(), $matching->getParams());
		
		$bindings = $route->objects?:[];
		
		if($this->pattern_resolver->isEmbeddedPaths()){
			foreach($route->getPatternParams() as $placeholder){
				$chunks = $this->pattern_resolver->explode($placeholder);
				$containerKey = array_shift($chunks);
				#Binding
				if(isset($bindings[$containerKey])){
					$bindingRule = $bindings[$containerKey];
					$objectId = $patternParams[$placeholder];
					
					$relativePath = $this->pattern_resolver->join($chunks);
					try{
						#Fetch
						$object = $this->binding_adapter->load($relativePath,$objectId, $bindingRule, $patternParams, $placeholder);
						#Check
						$value = $this->_checkout($object, $patternParams, $containerKey, $placeholder);
					}catch(MatchingException $e){
						$e->matching = $matching;
						$e->route = $route;
						throw $e;
					}
					
					$params[ $containerKey ] = $value;
					unset($params[$placeholder], $value); // TODO: unset old used patternParam from $params
				}
			}
		}
		
		$matching->setParams($params, true);
		$matching->setOptions($route->getOptions(), true); // TODO: options
	}
	
	/**
	 * @param $object
	 * @param $pattern_params
	 * @param $container_key
	 * @param $placeholder
	 * @return mixed
	 * @throws MissingException
	 * @throws SkipException
	 */
	protected function _checkout($object, $pattern_params, $container_key, $placeholder){
		if(!$object){
			if($this->route->static){
				throw new MissingException();
			}else{
				throw new SkipException();
			}
		}
		return $object;
	}
	
	/**
	 * @param $params
	 * @param Route $route
	 * @return mixed
	 * @throws MissingParametersException
	 */
	public function onPrepareRenderParams(Route $route, $params){
		$this->route = $route;
		$router = $route->getRouter();
		$binder = $router->getBindingAdapter();
		
		$params = (array)$params;
		$placeholders = $this->pattern_resolver->patternPlaceholders($route->getPattern());
		$pattern_params = array_intersect_key($params, array_fill_keys($placeholders, null));
		
		$bindings = $route->objects?:[];
		$meta = [];
		foreach($placeholders as $param_key_def){
			$chunks = $this->pattern_resolver->explode($param_key_def);
			$container_key = $chunks[0];
			if(isset($bindings[$container_key])){
				$binding_rule = $bindings[$container_key];
				$meta[$param_key_def] = [$container_key, $binding_rule];
				if(isset($params[$container_key])){
					$object = $params[$container_key];
					array_shift($chunks);
					// todo bindingAdapter location is string or array
					$value = $binder->fetch($object, $chunks, $binding_rule, $pattern_params, $param_key_def);
					$params[ $param_key_def ] = $value;
					unset($params[$container_key], $value);
					
				}
			}else{
				$meta[$param_key_def] = null;
			}
		}
		
		$errors = [];
		foreach($meta as $paramKey => $alternativeBinding){
			if(!isset($params[$paramKey])){
				$errors[$paramKey] = $alternativeBinding;
			}
		}
		if($errors){
			$missing = [];
			foreach($errors as $paramKey => $alternative){
				$alternativeKey = $alternativeBinding = null;
				if($alternative){
					list($alternativeKey, $alternativeBinding) = $alternative;
				}
				$missing[] = new MissingParameterException($paramKey, $alternativeKey, $alternativeBinding);
			}
			throw new MissingParametersException($missing);
		}
		
		return $params;
	}
	
	/**
	 * @param Matching $matching
	 * @param Route $route
	 * @param ConjunctionRoute $conjunctionRoute
	 * @param \Exception $exception
	 * @return bool|null - catched
	 */
	public function onCatchException(Matching $matching, Route $route, ConjunctionRoute $conjunctionRoute, \Exception $exception){
		
		if($exception instanceof MissingException){
			
			
			
			//return true;
		}
		
		return false;
	}
}


