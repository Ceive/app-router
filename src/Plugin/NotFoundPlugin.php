<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing\Plugin;
use Ceive\Routing\Exception\Matching\MissingException;
use Ceive\Routing\Route;
use Ceive\Routing\Router;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class NotFoundPlugin
 * @package Ceive\Routing\Plugin
 */
class NotFoundPlugin{

	/** @var  Route */
	protected $route;
	
	public function __construct(){
		
		//$this->route = new NotFoundRoute();
		
	}
	
	public function onCatchMissing(MissingException $missing, Route $route){
		//return $this->route;
	}
	
}


