<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing\Tests;

use Ceive\Routing\Hierarchical\ConjunctionRoute;
/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class BaseRouterTest
 * @package Ceive\Routing\Tests
 */
class BaseRouterTest extends Base{
	
	public function setUp(){
		$factory = parent::setUp();
		
		$this->router->addRoute($factory->createRoute([
			'type'      => ConjunctionRoute::class,
			'pattern'   => '/users',
			'action'    => 'user.list',
			'children'  => [[
				'type' => ConjunctionRoute::class,
				'pattern' => '/(?<user__id>[1-9]?[0-9]*)',
				'action'    => 'user.index',
				'objects'   => [
					'user' => 'User'
				],
				'children'  => [[
					'pattern' => '/update',
					'action'    => 'user.update',
				],[
					'pattern' => '/delete',
					'action'    => 'user.delete',
				],[
					'type' => ConjunctionRoute::class,
					'pattern' => '/notes',
					'action'    => 'user.note.list',
					'children'  => [[
						'type' => ConjunctionRoute::class,
						'pattern' => '/(?<note__id>[1-9]?[0-9]*)',
						'action'    => 'user.note.index',
						'objects'   => [
							'note' => 'Note'
						],
						'children'  => [[
							'pattern' => '/update',
							'action'    => 'user.note.update',
						],[
							'pattern' => '/delete',
							'action'    => 'user.note.delete',
						]]
					],[
						'pattern' => '/new',
						'action' => 'user.note.create',
						'ok' => [[
							'type'  => 'flash-message',
							'after' => '.message'
						],[
							'type'  => 'redirect',
							'deffer'=> '5 sec',
							'to'    => 'route: & -> closest(pattern="/notes") -> [action="user.note.index"] --save-params'
						]],
						'on' => []
					]],
				]],
			],[
				'pattern'   => '/new',
				'action'    => 'user.create'
			]],
		]));
		return $factory;
	}
	
}


