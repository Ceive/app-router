<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing\Hierarchical;


use Ceive\Routing\Exception\Rendering\MissingParametersException;
/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class RouteLeafTrait
 * @package Ceive\Routing\Hierarchical
 */
trait RouteLeafTrait{
	
	/** @var  ConjunctionRoute|null */
	protected $parent;
	
	/**
	 * @param ConjunctionRoute $parent
	 * @return $this
	 */
	public function setParent(ConjunctionRoute $parent){
		$this->parent = $parent;
		return $this;
	}
	
	/**
	 * @return ConjunctionRoute
	 */
	public function getParent(){
		return $this->parent;
	}
	
	/**
	 * @param null $params
	 * @return string
	 * @throws MissingParametersException
	 */
	public function render($params = null){
		$errors = [];
		$e = null;
		
		$self = '';
		$parent = '';
		
		try{
			$self = $this->_render($params);
		}catch(MissingParametersException $e){
			$errors = $e->getParameters();
		}
		
		
		if($this->parent){
			try{
				$parent = $this->parent->render($params);
				if($e){
					throw $e;
				}
				
			}catch(MissingParametersException $e1){
				if($e1 !== $e){
					$errors = array_merge($errors, $e1->getParameters());
				}else throw $e;
			}
		}
		
		if($errors){
			throw new MissingParametersException($errors);
		}
		
		return $parent . $self;
	}
	
	/**
	 * @param null $params
	 * @return string
	 */
	abstract protected function _render($params = null);
	
	
}

