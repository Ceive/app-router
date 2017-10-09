<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing\Hierarchical;


use Ceive\Routing\Matching;
use Ceive\Routing\Route;
use Ceive\Routing\RouteAbstract;
use Ceive\Routing\Router;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class RouteDecorator
 * @package Ceive\Routing\Hierarchical
 */
class RouteDecorator implements Route, ParentAwareInterface{
	
	use RouteLeafTrait;
	
	/** @var RouteAbstract  */
	protected $wrapped;
	
	/**
	 * RouteDecorator constructor.
	 * @param ConjunctionRoute|null $parent
	 * @param RouteAbstract $wrapped
	 */
	public function __construct(ConjunctionRoute $parent = null, RouteAbstract $wrapped){
		$this->parent = $parent;
		$this->wrapped = $wrapped;
	}
	
	/**
	 * @param ConjunctionRoute $route
	 * @return $this
	 */
	public function setParent(ConjunctionRoute $route = null){
		$this->parent = $route;
		return $this;
	}
	
	/**
	 * @return ConjunctionRoute|null
	 */
	public function getParent(){
		return $this->parent;
	}
	
	/**
	 * @return Route
	 */
	public function getWrapped(){
		return $this->wrapped;
	}
	
	/**
	 * @param Router $router
	 * @return mixed
	 */
	public function setRouter(Router $router){
		return $this->wrapped->setRouter($router);
	}
	
	/**
	 * @return Router
	 */
	public function getRouter(){
		return $this->wrapped->getRouter();
	}
	
	/**
	 * @return array
	 */
	public function getOptions(){
		return $this->wrapped->getOptions();
	}
	
	/**
	 * @return null|string
	 */
	public function getPattern(){
		return $this->wrapped->getPattern();
	}
	
	/**
	 * @return array|null
	 */
	public function getPatternOptions(){
		return $this->wrapped->getPatternOptions();
	}
	
	/**
	 * @return array
	 */
	public function getPatternParams(){
		return $this->wrapped->getPatternParams();
	}
	
	/**
	 * @return array|mixed
	 */
	public function getDefaultReference(){
		return $this->wrapped->getDefaultReference();
	}
	
	/**
	 * @return array
	 */
	public function getDefaultParams(){
		return $this->wrapped->getDefaultParams();
	}
	
	
	/**
	 * @param Matching $matching
	 * @return Matching
	 */
	public function match(Matching $matching){
		
		
		$this->wrapped->match($matching);
		
		if($matching->getRoute() === $this->wrapped){
			$matching->setRoute($this);
		}
		
		return $matching;
	}
	
	/**
	 * @param array $options
	 * @return $this
	 */
	public function setOptions(array $options){
		$this->wrapped->setOptions($options);
		return $this;
	}
	
	/**
	 * @param null $params
	 * @return string
	 */
	protected function _render($params = null){
		return $this->wrapped->render($params);
	}
	
	
	public function __get($name){
		return $this->wrapped->__get($name);
	}
	
	public function __set($name, $value){
		$this->wrapped->__set($name, $value);
	}
	
	public function __isset($name){
		return $this->wrapped->__isset($name);
	}
	
	public function __unset($name){
		$this->wrapped->__unset($name);
	}
}


