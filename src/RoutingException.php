<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Ceive\Routing;
use Ceive\Routing\Exception\PatternException;
use Ceive\Routing\Exception\RenderingException;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class RoutingException
 * @package Kewo\Matching
 */
class RoutingException extends \Exception{
	

	/**
	 * @param $pattern
	 * @param $message
	 * @return PatternException
	 */
	public static function badPattern($pattern, $message){
		return new PatternException("Pattern '{$pattern}' have incorrect definition. {$message}");
	}

	/**
	 * @param $pattern
	 * @param $options
	 * @param $message
	 * @return PatternException
	 */
	public static function badPatternOptions($pattern, $options, $message){
		return new PatternException("Pattern '{$pattern}', incorrect options. {$message}. Options: " . var_export($options, true));
	}

	/**
	 * @param $parameter_name
	 * @return RenderingException
	 */
	public static function renderingParamMissing($parameter_name){
		return new RenderingException("Not passed parameter '{$parameter_name}', is missing");
	}

	/**
	 * @param Route $route
	 * @param RoutingException $exception
	 * @return RoutingException
	 */
	public static function wrapRouteException(Route $route, RoutingException $exception){
		return $exception;
	}
	
}


