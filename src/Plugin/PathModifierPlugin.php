<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing\Plugin;


use Ceive\Routing\Matching;
use Ceive\Routing\Route;

class PathModifierPlugin{
	
	public function onBeforeMatch(Route $route, Matching $matching){
		$p = $route->onBeforeMatch;
		if(is_callable($p)){
			call_user_func($p, $route, $matching);
		}
	}
	
}


