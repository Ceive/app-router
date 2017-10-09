<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing;

/**
 * @Author: Alexey Kutuzov <lexus.1995@mail.ru>
 * Interface RouteContainer
 * @package Ceive\Routing
 */
interface RouteContainer{
	
	/**
	 * @return Route[]
	 */
	public function getRoutes();
	
	/**
	 * @param Route $route
	 * @param bool $recursive
	 * @return bool
	 */
	public function isContains(Route $route, $recursive = true);
	
	/**
	 * @param $reference
	 * @param bool $recursive
	 * @return Route[]|RouteContainer[]
	 */
	public function findByReference($reference, $recursive = true);
	
	
	/**
	 * @param $reference
	 * @param bool $recursive
	 * @return Route|RouteContainer[]
	 */
	public function findFirstByReference($reference, $recursive = true);
	
	
	/**
	 * @param callable $checker
	 * @param bool $recursive
	 * @return Route|RouteContainer|null
	 */
	public function findFirstBy(callable $checker, $recursive = true);
	
	
	/**
	 * @param callable $checker
	 * @param bool $recursive
	 * @return Route[]|RouteContainer[]
	 */
	public function findBy(callable $checker, $recursive = true);
	
}

