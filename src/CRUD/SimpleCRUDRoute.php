<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Kewodoa\Routing\CRUDConjunction;

use Ceive\Routing\Hierarchical\ConjunctionRoute;
use Ceive\Routing\Simple\SimpleRoute;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class SimpleCRUDRoute
 * @package Kewodoa\Routing\CRUDConjunction
 */
class SimpleCRUDRoute extends ConjunctionRoute{

    /**
     * SimpleRouteAbstract constructor.
     * L
     *   C
     *   R
     *     U
     *     D
     *
     * @param array $config
     *
     *
     * * * * Now * * *
     * @throws \Ceive\Routing\RoutingException
     * @Listing
     * GET /list (read list) ?offset ?limit
     *
     * @Creating
     * GET /list/new (form)
     * POST /list/new
     *
     * @Updating
     * GET /list/[id]/update (form)
     * POST /list/[id]/update
     *
     * @Deleting
     * GET /list/[id]/delete
     *
     *
     * * * * Rest FULL * * *
     * @Listing
     * GET /list (read list) ?offset ?limit
     *
     * @Creating
     * GET /list/new  (FORM); similar to /list/new GET
     * POST /list;  similar to /list/new POST
     *
     * @Updating
     * GET /list/[id]/update (FORM);
     * POST|PATCH /list/[id] ;
     *
     * @Deleting
     * DELETE /list/[id]
     */
	public function __construct(array $config){
		
		$config = array_replace([
			'class'     => null,
			'many'      => null,
			'create'    => 'new',
			'update'    => 'update',
			'delete'    => 'delete',
			'addition' => [],
		],$config);
		
		$prefix = $config['class'] . '.';
		
		
		$actionList     = $prefix . 'list';
		$actionCreate   = $prefix . 'create';
		$actionRead     = $prefix . 'index';
		$actionUpdate   = $prefix . 'update';
		$actionDelete   = $prefix . 'delete';
		
		
		$pathList     = '/list';
		$pathCreate   = '/new';
		$pathRead     = '/(?<obj_id>\d+)';
		$pathUpdate   = '/update';
		$pathDelete   = '/delete';
		
		
		parent::__construct($actionList, $pathList, null);
		
		$objectRoute = new ConjunctionRoute($actionRead, $pathRead);
		
		$objectRoute->addRoute(new SimpleRoute($actionUpdate, $pathUpdate));
		$objectRoute->addRoute(new SimpleRoute($actionDelete, $pathDelete));
		
		$this->addRoute($objectRoute);
		$this->addRoute(new SimpleRoute($actionCreate, $pathCreate));
		
		foreach($config['append'] as $route){
			$objectRoute->addRoute($route);
		}
	}
	
}


