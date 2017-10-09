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
 * Class MissingParametersException
 * @package Ceive\Routing\Exception\Rendering
 */
class MissingParametersException extends RenderingException{
	
	/** @var  MissingParameterException[] */
	protected $parameters = [];
	
	/**
	 * MissingParametersException constructor.
	 * @param MissingParameterException[] $parameters
	 */
	public function __construct(array $parameters){
		$this->parameters = $parameters;
		$m = [];
		foreach($this->parameters as $g){
			$m[] = $g->getMessage();
		}
		parent::__construct('Missing Parameters: '.implode(",\r\n",$m));
	}
	
	/**
	 * @return MissingParameterException[]
	 */
	public function getParameters(){
		return $this->parameters;
	}
	
	/**
	 * @return array[]
	 */
	public function getParamsWithBindings(){
		$a = [];
		foreach($this->parameters as $param){
			$a[$param->getParameter()] = ($param->getBindingAlias()?[$param->getBindingAlias(), $param->getBindingRule()]:null);
		}
		return $a;
	}
	
	
}


