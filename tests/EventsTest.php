<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing\Tests;


use Ceive\Routing\Hierarchical\ConjunctionRoute;
use Ceive\Routing\Matching;
use Ceive\Routing\Plugin\BindingPlugin;
use Ceive\Routing\Simple\SimpleMatching;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class EventsTest
 * @package Ceive\Routing\Tests
 */
class EventsTest extends Base{
	
	
	public function setUp(){
		$factory = parent::setUp();
		
		$this->router->addRoute($factory->createRoute([
			'type' => ConjunctionRoute::class,
			'pattern' => '/users',
			'action' => 'user.list',
			'on conformed' => function(Matching $matching){},
			'on reached' => function(Matching $matching){},
			'children' => [[
				'type' => ConjunctionRoute::class,
				'pattern' => '/(?<user__id>[1-9]?[0-9]*)',
				'action' => 'user.index',
				'objects' => [
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
						'action' => 'user.note.create'
					]],
				]],
			],[
				'pattern'   => '/new',
				'action'    => 'user.create'
			]],
		]));
	}
	
	public function testA(){
		$this->router->addPlugin(new BindingPlugin());
		$this->router->stack = [];
		$m = new SimpleMatching('/users/555/notes/555/delete');
		
		$this->router->process($m);
		
		$e = $this->router->stack;
		$a = [];
		foreach($e as list($name, $route, $matching)){
			$a[] = [$name, $route->getPattern(), $route->getDefaultReference(), $matching->getConformedPath()];
		}
		
		$e =$e;
		
	}
	
}


