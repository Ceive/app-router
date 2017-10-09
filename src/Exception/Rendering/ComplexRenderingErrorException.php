<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing\Exception\Rendering;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class ComplexRenderingErrorException
 * @package Ceive\Routing\Exception\Rendering
 */
class ComplexRenderingErrorException{
	
	/**
	 * @var InvalidParameterValueException[]
	 */
	public $invalid_parameters = [];
	
	/**
	 * @var MissingParameterException[]
	 */
	public $missing_parameters = [];
	
	/**
	 * ComplexRenderingErrorException constructor.
	 * @param array $invalid
	 * @param array $missing
	 */
	public function __construct(array $invalid, array $missing){
		$this->invalid_parameters = $invalid;
		$this->missing_parameters = $missing;
	}
	
}


