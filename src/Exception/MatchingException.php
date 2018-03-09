<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Ceive\Routing\Exception;

use Ceive\Routing\Matching;
use Ceive\Routing\Route;
use Ceive\Routing\RoutingException;

class MatchingException extends RoutingException{
	
	/** @var  Matching */
	public $matching;
	
	/** @var  Route */
	public $route;
	
}


