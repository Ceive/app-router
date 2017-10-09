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
 * Class InvalidParameterValueException
 * @package Ceive\Routing\Exception\Rendering
 */
class InvalidParameterValueException extends RenderingException{
	
	/** @var string  */
	protected $parameter;
	/** @var string  */
	protected $type;
	/** @var bool */
	protected $optional;
	
	/**
	 * MissingParameterException constructor.
	 * @param string $parameter
	 * @param string $type
	 * @param bool $optional
	 */
	public function __construct($parameter, $type = null, $optional = false){
		
		$this->parameter    = $parameter;
		$this->type = $type;
		$this->optional = $optional;
		
		parent::__construct('invalid parameter '.$parameter.' type:'.$type.';'.($optional?' --optional;':''));
	}
	
	public function getParameter(){
		return $this->parameter;
	}
	
	public function getType(){
		return $this->type;
	}
	
	public function isOptional(){
		return $this->optional;
	}
	
}


