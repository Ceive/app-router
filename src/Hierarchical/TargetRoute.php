<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Ceive\Routing\Hierarchical;


use Ceive\Routing\Route;
use Ceive\Routing\RouteAbstract;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class TargetRoute
 * @package Ceive\Routing\Hierarchical
 */
class TargetRoute extends RouteAbstract{
	
	/**
	 * @var ConjunctionRoute
	 */
	protected $base;
	
	/**
	 * @var Route
	 */
	protected $route;
	
	/**
	 * @var ConjunctionRoute[]|Route[]
	 */
	protected $location = [];
	
	/**
	 * TargetRoute constructor.
	 * @param Route $route
	 * @param ConjunctionRoute $base TODO локация может строится и относительно Router
	 * @throws \Exception
	 */
	public function __construct(Route $route, ConjunctionRoute $base){
		
		$this->route = $route;
		$this->base = $base;
		
		$this->location = $base->locate($base);
		
		if(empty($this->location)){
			throw new \Exception(
				'Route "'.$route->getDefaultReference().
				'" is not contains in ConjunctionRoute "'.
				$base->getDefaultReference().'"'
			);
		}
		
		$pattern = [];
		$pattern_options = [];
		$default_params = [];
		
		foreach($this->location as $point){
			$pattern[]          = $point->getPattern();
			$pattern_options    = array_replace_recursive($pattern_options , $point->getPatternOptions());
			$default_params     = array_replace_recursive($default_params, $point->getDefaultParams());
		}
		
		parent::__construct($route->getDefaultReference(), implode($pattern), $pattern_options);
		
		$this->params = $default_params;
		
		
	}
	
	/**
	 * @return ConjunctionRoute[]|Route[]
	 */
	public function locate(){
		return $this->location;
	}
	
	/**
	 * @experimental
	 * @param string|integer $valueColumnKey
	 * @param string|integer|null $indexColumnKey
	 * @return array
	 */
	public function locateOption($valueColumnKey, $indexColumnKey = null){
		$a = [];
		foreach($this->location as $point){
			$a[] = $point->options;
		}
		return array_column($a,$valueColumnKey,$indexColumnKey);
	}
	
}


