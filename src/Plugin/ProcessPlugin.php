<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing\Plugin;
use Ceive\Routing\Exception\Matching\SkipException;
use Ceive\Routing\Hierarchical\MatchingReached;
use Ceive\Routing\Matching;
use Ceive\Routing\Route;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class ProcessPlugin
 * @package Ceive\Routing\Plugin
 * @TODO View Configuration Switchers
 */
class ProcessPlugin{
	
	/** @var  MatchingReached */
	protected $matching;
	
	/** @var  Route */
	protected $route;
	
	/**
	 * @param Matching $matching
	 * @param Route $route
	 * @throws SkipException
	 */
	public function onReached(Matching $matching, Route $route){
		$this->matching = new MatchingReached($matching);
		$this->route = $route;
		try{
			$this->process();
		}catch(SkipException $e){
			throw $e;
		}
		
	}
	
	/**
	 * @param Matching $matching
	 * @param Route $route
	 * @return mixed
	 * @throws SkipException
	 */
	public function onConformed(Matching $matching, Route $route){
		$onConformed = $route->{'on conformed'};
		try{
			if(isset($route->{'on conformed'}) && is_callable($route->{'on conformed'})){
				return call_user_func($route->{'on conformed'}, $matching, $route);
			}
		}catch(SkipException $e){
			throw $e;
		}
		
	}
	
	public function process(){
		
	}
	
}


