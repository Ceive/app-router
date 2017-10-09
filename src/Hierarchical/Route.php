<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Ceive\Routing\Hierarchical;

/**
 * @Author: Alexey Kutuzov <lexus.1995@mail.ru>
 * Interface Route
 * @package Ceive\Routing\Hierarchical
 * 02.08 Решение о getParent Leaf Routes
 *
 * Matching: Нужен localPattern, localPatternOptions localOptions
 * Rendering: Нужен getParent или [o,o,o,*](Location)
 *
 * Методы которые не зависят от вложенности но используют методы зависимые от нее (аргументы методов остаются неизменными)
 *  match(Matching $matching)
 *  render(array $parameters)
 *  [
 *      'object' => <o>,
 *      //'object.id' => 123,
 *      '#query' => [
 *          'param1' => 'value1
 *      ],
 * --if(route use post parameters)----Generating-Request(REST request; CustomRequest; XmlHTTPRequest, AJAX)-----------------------
 *      '#headers' => [
 *          'header-first' => 'value-first'
 *      ],
 *      '#post' => [
 *          'param1' => 'value1
 *      ],
 *      '#body' => [
 *          'param1' => 'value1
 *      ]
 *  ]
 * Методы которые зависят от вложенности их вызов может иметь локальный вывод или же наследованный образец
 *   getPattern();            [local;extended]
 *   getPatternOptions();     [local;extended]
 *   getOptions();            [???]
 *   getDefaultParams();      [local;extended]
 *   getDefaultReference();   [???]
 */
interface Route{
	
	public function getParent();
	
	public function getLocation();
	
	public function getExtendedPattern();
	
	public function getExtendedPatternParams();
	
	public function getExtendedOptions($key);
	
}

