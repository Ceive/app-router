<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Ceive\Routing\Hierarchical;


use Ceive\Routing\FactoryDirector;
use Ceive\Routing\FactoryMethodInterface;
use Ceive\Routing\Route;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class ConjunctionFactory
 * @package Ceive\Routing\Hierarchical
 */
class ConjunctionFactory implements FactoryMethodInterface{

    /**
     * @param array $definition
     * @param FactoryDirector $context
     * @return ConjunctionRoute
     * @throws \Ceive\Routing\RoutingException
     */
	public function create($definition, FactoryDirector $context){
		
		$p = isset($definition['pattern'])?$definition['pattern']:null;
		$po = isset($definition['pattern_options'])?$definition['pattern_options']:[];
		$a = isset($definition['action'])?$definition['action']:null;
		
		$relay = new ConjunctionRoute($a, $p, $po);
		$relay->setRouter($context->getRouter());
		
		$relay->setOptions(array_diff_key($definition,array_flip(['pattern','pattern_options','action','children'])));
		if(isset($definition['children'])){
			foreach($definition['children'] as $child){
				if($child instanceof Route){
					$relay->addRoute($child);
				}else{
					$relay->addRoute($context->createRoute($child));
				}
			}
		}
		return $relay;
	}
	
	public static function getName(){
		return ConjunctionRoute::class;
	}
}


