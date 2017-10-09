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
	
	public function setParent(ConjunctionRoute $route);
	
	public function getParent();
	
}

