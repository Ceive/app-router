<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing\Tests;

use Ceive\Routing\Exception\Rendering\MissingParametersException;
use Ceive\Routing\Exception\RenderingException;
use Ceive\Routing\Hierarchical\ConjunctionRoute;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class ErrorsTest
 * @package Ceive\Routing\Tests
 */
class ErrorsTest extends Base{
	
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
					'type' => ConjunctionRoute::class,
					'pattern' => '/notes',
					'action'    => 'user.note.list',
					'children'  => [[
						'type' => ConjunctionRoute::class,
						'pattern' => '/(?<note__id>[1-9]?[0-9]*)',
						'action'    => 'user.note.index',
						'objects'   => [
							'note' => 'Note'
						]
					]],
				]],
			]],
		]));
	}
	
	public function testNotPassedParameters(){
		$userRoute = $this->router->findFirstByReference('user.index');
		$noteRoute = $this->router->findFirstByReference('user.note.index');
		
		try{
			$e = null;
			$userRoute->render();// user__id
		}catch(MissingParametersException $e){}
		$this->assertInstanceOf(MissingParametersException::class, $e);
		
		try{
			$e = null;
			$noteRoute->render([
				'note' => ['id'=>1]
			]);// note__id, user__id
		}catch(MissingParametersException $e){}
		$this->assertInstanceOf(MissingParametersException::class, $e);
		
	}
	
	
	public function testWrongType(){
		$userRoute = $this->router->findFirstByReference('user.index');
		$noteRoute = $this->router->findFirstByReference('user.note.index');
		
		try{
			$e = null;
			$userRoute->render(['user__id' => 'string']);// user__id
		}catch(MissingParametersException $e){}
		$this->assertInstanceOf(MissingParametersException::class, $e);
		
		try{
			$e = null;
			$noteRoute->render([
				'note' => ['id'=>'de']
			]);// note__id, user__id
		}catch(MissingParametersException $e){}
		$this->assertInstanceOf(MissingParametersException::class, $e);
		
	}
}


