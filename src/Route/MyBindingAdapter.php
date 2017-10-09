<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Ceive\Routing\Route;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class MyBindingAdapter
 * @package Ceive\Routing\Route
 */
class MyBindingAdapter implements BindingAdapter{
	
	/**
	 * @param $field_path
	 * @param $field_value
	 * @param $binding_rule
	 * @param array $pattern_params
	 * @param null $full_path
	 * @return null
	 */
	public function load($field_path, $field_value, $binding_rule, $pattern_params = [], $full_path = null){
		// можно это инкапсулировать в объекты Binding;, объект будет обращаться к Router за адаптером Orm(selectById)
		$classname = null;
		if(is_string($binding_rule)){
			$classname = $binding_rule;
			$criteria = null;
		}elseif(is_array($binding_rule)){
			if(isset($binding_rule['class'])){
				$classname = $binding_rule['class'];
			}
			if(isset($binding_rule['criteria'])){
				$criteria = $binding_rule['criteria'];
			}
		}
		
		// полный путь
		// полный разделенный путь
		// путь до поля
		// разделенный путь до поля
		
		
		$object = new \stdClass();
		$object->{$field_path} = $field_value;
		$object->classname = $classname;
		return $object;
	}
	
	
	public function _prepareCriteria($binding_rule){
		$criteria   = null;
		$schema = null;
		if(is_array($binding_rule)){
			if(isset($binding_rule['schema'])){
				$schema = $binding_rule['schema'];
			}
			if(isset($binding_rule['criteria'])){
				$criteria = $binding_rule['criteria'];
			}
		}else{
			// is string
			$schema = $binding_rule;
		}
		return [$schema, $criteria];
	}
	
	/**
	 * @param $object
	 * @param $field_path
	 * @param $binding_rule
	 * @param array $pattern_params
	 * @param null $full_path
	 * @return mixed
	 * @internal param $location
	 */
	public function fetch($object, $field_path, $binding_rule, $pattern_params = [], $full_path = null){
		
		$chunk = array_shift($field_path);
		
		$value = null;
		if(is_object($object)){
			if(isset($object->{$chunk})){
				$value = $object->{$chunk};
			}elseif(method_exists($object,'__get')){
				$value = $object->{$chunk};
			}elseif(method_exists($object, 'get' . $chunk )){
				$value = call_user_func([$object, 'get' . $chunk]);
			}elseif($object instanceof \ArrayAccess){
				if($object->offsetExists($chunk)){
					$value = $object->offsetGet($chunk);
				}
			}
		}elseif(is_array($object)){
			if(isset($object[$chunk])){
				$value = $object[$chunk];
			}
		}
		
		
		if($field_path){
			return $this->fetch($value, $field_path, null);
		}else{
			return $value;
		}
	}
	
}


