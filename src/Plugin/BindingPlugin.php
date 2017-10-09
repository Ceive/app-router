<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing\Plugin;

use Ceive\Routing\Exception\Matching\MissingException;
use Ceive\Routing\Exception\Matching\SkipException;
use Ceive\Routing\Exception\Rendering\MissingParameterException;
use Ceive\Routing\Exception\Rendering\MissingParametersException;
use Ceive\Routing\Matching;
use Ceive\Routing\Route;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class BindingPlugin
 * @package Ceive\Routing\Tests
 */
class BindingPlugin{
	
	/** @var  Route\BindingAdapter */
	protected $binding_adapter;
	
	/** @var  Route */
	protected $route;
	
	/** @var  Matching */
	protected $matching;
	
	/**
	 * @param Matching $matching
	 * @param Route $route
	 */
	public function onConformed(Matching $matching, Route $route){
		$this->binding_adapter = $route->getRouter()->getBindingAdapter();
		$resolver = $route->getRouter()->getPatternResolver();
		
		$pattern_params = $params = array_replace(
			(array)$route->getDefaultParams(),
			$matching->getParams()
		);
		
		$bindings = $route->objects?:[];
		$delimiter = $resolver->getPathDelimiter();
		if($delimiter){
			foreach($route->getPatternParams() as $placeholder){
				$chunks = $resolver->explode($placeholder);
				$container_key = array_shift($chunks);
				#Binding
				if(isset($bindings[$container_key])){
					$binding_rule = $bindings[$container_key];
					$object_id = $pattern_params[$placeholder];
					
					$query_key = $resolver->join($chunks);
					#Fetch
					$object = $this->binding_adapter->load($query_key,$object_id, $binding_rule, $pattern_params, $placeholder);
					#Check
					$value = $this->_checkout($object, $pattern_params, $container_key, $placeholder);
					
					$params[ $container_key ] = $value;
					unset($params[$placeholder], $value);
				}
			}
		}
		
		$matching->setParams($params, true);
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
	 * @param Matching $matching
	 * @param Route $route
	 */
	public function onReached(Matching $matching, Route $route){
		$a = [];
	}
	
	/**
	 * @param Matching $matching
	 * @param Route $route
	 */
	public function onFinish(Matching $matching, Route $route){
		$a = [];
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
		$resolver = $router->getPatternResolver();
		$binder = $router->getBindingAdapter();
		
		$params = (array)$params;
		$placeholders = $resolver->patternPlaceholders($route->getPattern());
		$pattern_params = array_intersect_key($params, array_fill_keys($placeholders, null));
		
		$bindings = $route->objects?:[];
		$meta = [];
		foreach($placeholders as $param_key_def){
			$chunks = $resolver->explode($param_key_def);
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
				$meta[$param_key_def]=null;
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
				$missing[] =  new MissingParameterException($paramKey, $alternativeKey, $alternativeBinding);
			}
			throw new MissingParametersException($missing);
		}
		
		return $params;
	}
}


