<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing\Hierarchical;

use Ceive\Routing\Matching;
use Ceive\Routing\MatchingAbstract;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class MatchingReached
 * @package Ceive\Routing\Hierarchical
 */
class MatchingReached extends MatchingDecorator{
	
	protected $root;
	
	/** @var  Route[] */
	protected $routes_chain = [];
	
	/** @var  Matching[] */
	protected $matching_chain = [];
	
	/** @var  string */
	protected $elapsed_path;
	
	/**
	 * MatchingReached constructor.
	 * @param Matching $matching
	 */
	public function __construct(Matching $matching){
		parent::__construct($matching);
		$this->depth = 0;
		$this->root = null;
		$this->reference = $matching->getReference();
		$this->route = $matching->getRoute();
		
		$this->conformed = $this->isConformed();
		$this->conformed_path = $this->getConformedPath();
		$this->proposed_path = $this->getProposedPath();
		
		if($matching instanceof MatchingDecorator){
			$this->root = $matching->getRootWrapped();
			$this->depth = $matching->getDepth();
		}
		$this->nest();
	}
	
	/**
	 * @return Matching
	 */
	public function getRootWrapped(){
		if(!$this->root){
			$this->root = parent::getRootWrapped();
		}
		return $this->root;
	}
	
	/**
	 * @return mixed
	 */
	public function getReference(){
		return $this->wrapped->getReference();
	}
	
	/**
	 * @return bool
	 */
	public function isReached(){
		return $this->wrapped->isReached();
	}
	
	/**
	 * @return bool
	 */
	public function isConformed(){
		return $this->wrapped->isConformed();
	}
	
	/**
	 * @return string
	 */
	public function getPath(){
		return $this->getRootWrapped()->getPath();
	}
	/**
	 * @return string
	 */
	public function getProposedPath(){
		return $this->getRootWrapped()->getPath();
	}
	
	/**
	 * @return string
	 */
	public function getConformedPath(){
		return $this->getRootWrapped()->getPath();
	}
	
	protected function nest(){
		
		$params = [];
		$options = [];
		$routes = [];
		$way = [];
		
		$this->_eachWrapped(function(Matching $matching) use (&$params, &$options, &$routes, &$way){
			$params = array_replace($matching->getParams(), $params);
			$options = array_replace($matching->getOptions(), $options);
			$routes[] = $matching->getRoute();
			$way[] = $matching;
		});
		
		$this->routes_chain = array_reverse($routes);
		$this->matching_chain = array_reverse($way);
		$this->params = $params;
		$this->options = $options;
		
		$this->getElapsedPath();
		
	}
	
	/**
	 * @param callable $collector
	 * @return array
	 */
	protected function _eachWrapped(callable $collector){
		$m = $this;
		$a = [];
		while($m instanceof MatchingDecorator && $m = $m->getWrapped()){
			$a[] = call_user_func($collector, $m);
		}
		return $a;
	}
	
	public function getElapsedPath(){
		if($this->elapsed_path === null){
			$this->elapsed_path = $this->getWrapped()->getElapsedPath();
		}
		return $this->elapsed_path;
	}
	
	/**
	 * @param bool $reverse
	 * @return Matching[]
	 */
	public function way($reverse = false){
		return $reverse? array_reverse($this->matching_chain):$this->matching_chain;
	}
	
	public function runtimeColumn($key){
		$a = [];
		foreach($this->way() as $matching){
			$a[] = $matching->{$key};
		}
		return $a;
	}
	
}


