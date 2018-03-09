<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing\Plugin;
use Ceive\Routing\Exception\Matching\SkipException;
use Ceive\Routing\Hierarchical\ConjunctionRoute;
use Ceive\Routing\Hierarchical\MatchingReached;
use Ceive\Routing\Matching;
use Ceive\Routing\Route;
use Ceive\Routing\Router;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class AccessPlugin
 * @package Ceive\Routing\Plugin
 * @Example
 */
class AccessPlugin{
	
	public $matching;
	
	public $route;
	
	public $router;
	
	public $denied_matching;
	public $denied_matching_after;
	
	/**
	 * @param Router $router
	 * @return $this
	 */
	public function setRouter(Router $router){
		$this->router = $router;
		return $this;
	}
	
	public function onBegin(){
		$this->denied_matching = null;
		$this->denied_matching_after = false;
	}
	
	/**
	 * @param Matching $matching
	 * @param Route $route
	 * @param ConjunctionRoute $conjunctionRoute
	 * @param \Exception $exception
	 * @return bool|null - catched
	 */
	public function onCatchException(Matching $matching, Route $route, ConjunctionRoute $conjunctionRoute, \Exception $exception){
		
		/**
		 * Example Access plugin
		 */
		if($exception instanceof \AccessDeniedRoute && ($this->denied_matching_after || $matching === $this->denied_matching)){
			$this->denied_matching_after = true;
			//$exception->getRoute();
			//$exception->getMatching();
			$resolve = $route->{'on deny'};
			if($resolve){
				$this->router->resolveForward($resolve);
				//$deniedRoute = $this->router->create($resolve);
				//$deniedRoute->match($matching);
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * @param Matching $matching
	 * @param Route $route
	 * @throws \Exception
	 */
	public function onReached(Matching $matching, Route $route){
		$this->matching = new MatchingReached($matching);
		$this->route = $route;
		
		$this->denied_matching = null;
		$this->denied_matching_after = false;
		
		foreach($this->matching->way(true) as $m){
			if($m->hasRuntime('denied')){
				$this->denied_matching = $m;
			}
		}
		
		if($this->denied_matching){
			throw new \Exception();
		}
		
	}
	
	public function onConformed(Matching $matching, Route $route){
		$access = $route->access;
		if(is_callable($access)){
			if(!call_user_func($access, $matching, $route)){
				$matching->setRuntime('denied', true);
			}
		}
	}
	
}


