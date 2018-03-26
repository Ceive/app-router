<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing\CRUD;


use Ceive\Routing\FactoryDirector;
use Ceive\Routing\FactoryMethodInterface;
use Ceive\Routing\Hierarchical\ConjunctionRoute;
use Ceive\Routing\Route;
use Ceive\Routing\Simple\SimpleRoute;
use Kewodoa\Routing\CRUDConjunction\SimpleCRUDRoute;

class SimpleCRUDFactory implements FactoryMethodInterface{
	
	/**
	 * @param $definition
	 * @param FactoryDirector $director
	 * @return mixed
	 */
	public function create($definition, FactoryDirector $director){
		$definition = array_replace([
			
			'class'     => null,
			'alias'     => 'object',
			
			'list'      => 'list',
			'create'    => 'new',
			'update'    => 'update',
			'delete'    => 'delete',
			
			'children'  => [],
			'append'    => [],
			
			'in create' => null,
			'in update' => null,
			'in index' => null,
			'in delete' => null,
			
			'as create' => null,
			'as update' => null,
			'as index' => null,
			'as delete' => null,
		],$definition);
		
		$prefix = $definition['class'] . '.';
		
		
		$actionList     = $prefix . 'list';
		$actionCreate   = $prefix . 'create';
		$actionRead     = $prefix . 'index';
		$actionUpdate   = $prefix . 'update';
		$actionDelete   = $prefix . 'delete';
		
		
		$pathRead     = "/(<{$definition['alias']}__id>[1-9][0-9]*)";
		
		$pathList     = '/'.$definition['list'];
		$pathCreate   = '/'.$definition['new'];
		$pathUpdate   = '/'.$definition['update'];
		$pathDelete   = '/'.$definition['delete'];
		
		
		$route = new ConjunctionRoute($actionList, $pathList, null);
		$route->setRouter($director->getRouter());
		$route->setOptions(array_diff_key($definition,array_flip([
			'class','list','create','update','delete','children','append',
			'in create','in update','in index','in delete',
			'as create','as update','as index','as delete',
		])));
		
		/** @var ConjunctionRoute $indexRoute */
		$indexRoute = $director->createRoute(array_replace([
			'type' => ConjunctionRoute::class,
			'action' => $actionRead,
			'pattern' => $pathRead,
			'children' => $definition['in index']?:[]
		], $definition['as index']?:[]));
		
		$updateRoute = $director->createRoute(array_replace([
			'action' => $actionUpdate,
			'pattern' => $pathUpdate,
			'children' => $definition['in update']?:[]
		], $definition['as update']?:[]));
		
		$deleteRoute = $director->createRoute(array_replace([
			'action' => $actionDelete,
			'pattern' => $pathDelete,
			'children' => $definition['in delete']?:[]
		], $definition['as delete']?:[]));
		
		$createRoute = $director->createRoute(array_replace([
			'action' => $actionCreate,
			'pattern' => $pathCreate,
			'children' => $definition['in create']?:[]
		], $definition['as create']?:[]));
		
		$indexRoute->addRoute($updateRoute);
		$indexRoute->addRoute($deleteRoute);
		$route->addRoute($indexRoute);
		$route->addRoute($createRoute);
		
		foreach($definition['children'] as $r){
			$indexRoute->addRoute($r instanceof Route?$r:$director->createRoute($r));
		}
		foreach($definition['append'] as $r){
			$route->addRoute($r instanceof Route?$r:$director->createRoute($r));
		}
		return $route;
	}
}


