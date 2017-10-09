<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing\Exception\Rendering;


use Ceive\Routing\Exception\RenderingException;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class MissingParameterException
 * @package Ceive\Routing\Exception\Rendering
 */
class MissingParameterException extends RenderingException{
	
	protected $parameter;
	
	protected $bind_as;
	
	protected $bind_rule;
	
	/**
	 * MissingParameterException constructor.
	 * @param string $parameter
	 * @param null $bindAs
	 * @param null $bindRule
	 * @internal param null $binding
	 */
	public function __construct($parameter, $bindAs = null, $bindRule = null){
		
		$this->parameter    = $parameter;
		$this->bind_as      = $bindAs;
		$this->bind_rule    = $bindRule;
		
		$m = '"'.$parameter.'"';
		if($bindAs){
			$m.= '(use alternate: "'.$bindAs.'; '.print_r($bindRule, true).'")';
		}
		parent::__construct($m);
	}
	
	public function getParameter(){
		return $this->parameter;
	}
	
	public function getBindingAlias(){
		return $this->bind_as;
	}
	
	public function getBindingRule(){
		return $this->bind_rule;
	}
	
}


