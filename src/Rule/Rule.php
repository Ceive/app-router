<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Kewodoa\Routing\Rule;
use Ceive\Routing\Matching;

/**
 * @Author: Alexey Kutuzov <lexus.1995@mail.ru>
 * Interface RuleAbstract
 * @package Kewodoa\Routing\RuleAbstract
 */
interface Rule{
	
	/**
	 * @param Matching $matching
	 */
	public function match(Matching $matching);
	
}

