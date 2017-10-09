<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing\Hierarchical;

use Ceive\Routing\Exception\Rendering\MissingParametersException;
use Ceive\Routing\RouteAbstract;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class RouteLeaf
 * @package Ceive\Routing\Hierarchical
 */
class RouteLeaf extends RouteAbstract implements ParentAwareInterface{
	
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
	 * @return array
	 */
	public function getOptions(){
		if(!$this->parent) return  parent::getOptions();
		return array_replace( $this->parent->getOptions() , parent::getOptions());
	}
	
	/**
	 * @return null|string
	 */
	public function getPattern(){
		if(!$this->parent) return  parent::getPattern();
		return implode([$this->parent->getPattern(), parent::getPattern()]);
	}
	
	/**
	 * @return array|null
	 */
	public function getPatternOptions(){
		if(!$this->parent) return  parent::getPatternOptions();
		return array_replace( $this->parent->getPatternOptions() , parent::getPatternOptions());
	}
	
	/**
	 * @return array
	 */
	public function getPatternParams(){
		if(!$this->parent) return  parent::getPatternParams();
		
		$parent = $this->parent->getPatternParams();
		$self = parent::getPatternParams();
		
		return array_merge( $parent , $self);
	}
	
	/**
	 * @return array|mixed
	 */
	public function getDefaultReference(){
		if(!$this->parent) return  parent::getDefaultReference();
		return $this->getRouter()->extendReference($this->parent->getDefaultReference(), parent::getDefaultReference());
	}
	
	/**
	 * @return array
	 */
	public function getDefaultParams(){
		if(!$this->parent) return  parent::getDefaultParams();
		return array_replace( $this->parent->getDefaultParams() , parent::getDefaultParams());
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
	protected function _render($params = null){
		return parent::render($params);
	}
	
	/**
	 * @return null|string
	 */
	public function getOwnPattern(){
		return parent::getPattern();
	}
	
	/**
	 * @return array|null
	 */
	public function getOwnPatternOptions(){
		return parent::getPatternOptions();
	}
	
	/**
	 * @return array|null
	 */
	public function getOwnReference(){
		return parent::getDefaultReference();
	}
	
	/**
	 * @return array|null
	 */
	public function getOwnPatternParams(){
		return parent::getPatternParams();
	}
	
	/**
	 * @return array|mixed
	 */
	public function getOwnOptions(){
		return parent::getOptions();
	}
	
}


