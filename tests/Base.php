<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing\Tests;

use Ceive\Routing\FactoryDirector;
use Ceive\Routing\Hierarchical\ConjunctionFactory;
use Ceive\Routing\Hierarchical\ConjunctionRoute;
use Ceive\Routing\Plugin\BindingPlugin;
use Ceive\Routing\Route\MyBindingAdapter;
use Ceive\Routing\Router;
use Ceive\Routing\RouterAbstract;
use Ceive\Routing\Simple\SimplePatternResolver;
use Ceive\Routing\Simple\SimpleRoute;
use Ceive\Routing\Simple\SimpleRouteFactory;
use Ceive\Routing\Simple\SimpleRouter;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class BaseRouterTest
 * @package Ceive\Routing\Tests
 */
class Base extends \PHPUnit_Framework_TestCase{
	
	/** @var  FactoryDirector */
	public $factory;
	
	/** @var  RouterAbstract */
	public $router;
	
	public function setUp(){
		return $this->initial();
	}
	
	/**
	 *
	 */
	public function initial(){
		$resolver = new SimplePatternResolver();
		$resolver->setPathDelimiter('__');
		
		$this->router = new SimpleRouter($resolver);
		
		$binding_adapter = new MyBindingAdapter();
		
		$this->router->setBindingAdapter($binding_adapter);
		$this->router->addPlugin(new BindingPlugin());
		
		$this->factory = new FactoryDirector($this->router);
		
		$this->factory->setFactory(new ConjunctionFactory(),ConjunctionRoute::class);
		$this->factory->setFactory(new SimpleRouteFactory(),SimpleRoute::class);
		$this->factory->setDefault(SimpleRoute::class);
		return $this->factory;
	}
}


