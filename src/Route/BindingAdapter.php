<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Ceive\Routing\Route;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Interface BindingAdapter
 * @package Ceive\Routing\Route
 * @TODO: implement OrmAdapter interface and classes
 * @TODO: BindingAdapter: Мост между Параметрами маршрутов и связывание их на определенные схемы сторонней ORM (Реализация адаптера)
 */
interface BindingAdapter{
	
	/**
	 * @param $field_path
	 * @param $field_value
	 * @param $binding_rule
	 * @param array $pattern_params
	 * @param null $full_path
	 * @return mixed
	 */
	public function load($field_path, $field_value, $binding_rule, $pattern_params = [], $full_path = null);
	
	/**
	 * @param $object
	 * @param $field_path
	 * @param $binding_rule
	 * @param array $pattern_params
	 * @param null $full_path
	 * @return mixed
	 */
	public function fetch($object, $field_path, $binding_rule, $pattern_params = [], $full_path = null);
	
}


