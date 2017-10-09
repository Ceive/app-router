<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Ceive\Routing;

/**
 * @Author: Alexey Kutuzov <lexus.1995@mail.ru>
 * Interface FactoryMethodInterface
 * @package Ceive\Routing
 */
interface FactoryMethodInterface{
	
	/**
	 * @param $definition
	 * @param FactoryDirector $director
	 * @return mixed
	 */
	public function create($definition, FactoryDirector $director);

}

