<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing\Hierarchical;

/**
 * @Author: Alexey Kutuzov <lexus.1995@mail.ru>
 * Interface ParentAwareInterface
 * @package Ceive\Routing\Hierarchical
 */
interface ParentAwareInterface{
	
	/**
	 * @param ConjunctionRoute $route
	 * @return mixed
	 */
	public function setParent(ConjunctionRoute $route);
	
	/**
	 * @return ConjunctionRoute
	 */
	public function getParent();
	
}

