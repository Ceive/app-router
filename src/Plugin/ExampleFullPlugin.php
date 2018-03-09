<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing\Plugin;

use Ceive\Routing\Exception\Matching\MissingException;
use Ceive\Routing\Exception\Matching\SkipException;
use Ceive\Routing\Hierarchical\ConjunctionRoute;
use Ceive\Routing\Hierarchical\MatchingDecorator;
use Ceive\Routing\Matching;
use Ceive\Routing\Route;
use Ceive\Routing\Router;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class ExampleFullPlugin
 * @package Ceive\Routing\Plugin
 */
final class ExampleFullPlugin{
	/** @var  Router */
	protected $router;
	
	public function setRouter(Router $router){
		$this->router = $router;
	}
	
	/**
	 * @param Matching $matching
	 */
	public function onBegin(Matching $matching){
		
	}
	
	/**
	 * @param Matching $matching
	 * @param Route $route
	 */
	public function onCheckEnv(Route $route, Matching $matching){
		
	}
	
	/**
	 * @param Matching $matching
	 * @param Route $route
	 */
	public function onConformed(Route $route, Matching $matching){
		
	}
	
	/**
	 * @param Matching $matching
	 * @param Route $route
	 */
	public function onReached(Route $route, Matching $matching){
		
	}
	
	/**
	 * @param Matching $matching
	 * @param Route $route
	 */
	public function onFinish(Route $route, Matching $matching){
		
	}
	
	/**
	 * @param Route $route
	 * @param $params
	 */
	public function onPrepareRenderParams(Route $route, $params){
		
	}
	
	/**
	 * @param Matching $matching
	 * @param Route $route
	 * @param ConjunctionRoute $conjunctionRoute
	 * @param SkipException $exception
	 */
	public function onCatchSkip(Matching $matching, Route $route, ConjunctionRoute $conjunctionRoute, SkipException $exception){
		
	}
	
	/**
	 * @param ConjunctionRoute $route
	 * @param MissingException $exception
	 */
	public function onCatchMissing(ConjunctionRoute $route, MissingException $exception){
		if($route){
			$resolve = $route->{'on not_found'};
		}else{
			$resolve = $this->router->{'on not_found'};
		}
		
		$notFoundRoute = $this->router->create($resolve);
		$result = $notFoundRoute->match($exception->matching);
		
		return $result;
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
		if($exception instanceof \AccessDeniedRoute){
			$exception->getRoute();
			$exception->getMatching();
			$resolve = $route->{'on deny'};
			$deniedRoute = $this->router->create($resolve);
			$deniedRoute->match($matching);
			return true;
		}
		
		return false;
	}
	
}


