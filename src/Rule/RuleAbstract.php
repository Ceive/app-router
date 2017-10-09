<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Ceive\Routing\Rule;
use Ceive\Routing\Matching;
use Kewodoa\Routing\Rule\Rule;

/**
 * TODO: реализовать управляющий контекст для правил RuleAbstract.
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class RuleAbstract
 * @package Ceive\Routing\Route
 */
class RuleAbstract implements Rule{
	
	const MODE_STRICT       = 'strict';
	const MODE_STABILIZED   = 'stabilized';
	const MODE_SKIP         = null;
	
	protected $condition;
	
	//TODO: Переделать, сделать LogicCondition {operand} {operator} {operand}
	/** Path to value in context
	 * request.query.key
	 * scope.weekday
	 * request.post.key
	 * request.params.key
	 * request.header.Content-Type
	 * @var mixed
	 */
	public $key;
	
	/** Expected value of returns from path
	 * @var mixed
	 */
	public $expected;
	
	/**
	 * Строгая реакция на не соответствие
	 * @var bool
	 */
	public $strict = false;
	
	/** Стабилизация в случае не соответствия
	 * @var bool
	 */
	public $stabilized = false;
	
	/**
	 * @param Matching $matching
	 */
	public function match(Matching $matching){
		$value = $this->fetchValue($matching, $this->key);
		if(!$this->check( $value , $this->expected)){
			if($this->strict){
				$matching->unexpectedRequest([
					$this->key => [
						'invalid_value'  => $value,
						'expected_value' => $this->expected,
					]
				]);
			}elseif($this->stabilized){
				$matching->stabilizeRequestWith([
					$this->key => $this->expected
				]);
			}else{
				$matching->skip();
			}
		}
	}
	
	/**
	 * @param Matching $matching
	 * @param $key
	 * @return mixed
	 */
	public function fetchValue(Matching $matching, $key){
		return $matching->getEnv($key);
	}
	
	/**
	 * Метод проверки значений
	 * @param $value
	 * @param $expected
	 * @return bool
	 */
	public function check($value, $expected){
		return $value === $expected;
	}
	
	/**
	 * Режим поведения
	 * @param $mode
	 * @return $this
	 */
	public function setMode($mode = null){
		$this->strict = $mode===self::MODE_STRICT;
		$this->stabilized = $mode===self::MODE_STABILIZED;
		return $this;
	}
	
}


