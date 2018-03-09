<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing\Method;


use Ceive\Routing\Hierarchical\MatchingReached;
use Ceive\Routing\Router;

class LocationMethod{
	
	/***
	 * @param MatchingReached $matching
	 * @return Location|null
	 */
	public function run(MatchingReached $matching){
		$location = null;
		$a = [];
		foreach($matching->way() as $m){
			$l = $m->getOption('location');
			$a[] = $location = new Location($a, (array)$l, $m);
		}
		return $location;
	}
	
	
	
}


