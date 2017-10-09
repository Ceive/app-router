<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Ceive\Routing;


/**
 * @author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class Route
 * @package Kewo\Matching
 *
 * requested_url
 *  schema ~ https | http
 *  sub-domain
 *  domain
 *
 * request
 *  method ~ GET | POST | DELETE | PUT | OPTIONS | HEAD
 *  headers
 *      [~ slash separated parameters matching ~]
 *      accept              ~ text/html, text/*, application/json, image/png
 *      accept-language     ~ ru_RU, en-US; q=0.6
 *      accept-encoding     ~ utf-8
 *
 *
 *
 * scope:
 *  language
 *  output format
 *
 *
 *
 * params:
 *      key: '{get.param}'
 *
 *
 * Важно: Роут может указывать на принадлежность вложенному узлу, это особенно важно для контекста,
 * который будет сопровождать диспетчеризацию, для понимания в какой вложенный раздел попадает клиент
 *
 * Важно: Маршрут вполне часто является лицом CRUD действия для определенного Объекта
 *  поэтому нужно учитывать что шаблон может основываться на его аттрибутах и отношениях,
 *  могут быть соседние маршруты для других действий и действий над его отношениями
 *  или маршруты аналоги, Только в других скопах.
 *
 * Важно: Роут может генерировать ссылку, поэтому шаблон должен быть как проверочным,
 *  так и отрисовывающимся на базе входных параметров и объектов
 */
interface Route extends Matchable{
	
	/**
	 * @param Router $router
	 * @return mixed
	 */
	public function setRouter(Router $router);

	/**
	 * @return Router
	 */
	public function getRouter();
	
	/**
	 * @param Matching $matching
	 * @return Matching
	 */
	public function match(Matching $matching);

	/**
	 * @param  $params
	 * @return string
	 */
	public function render($params = null);
	
	/**
	 * @return string|null
	 */
	public function getPattern();
	
	/**
	 * @return array|null
	 */
	public function getPatternOptions();
	
	/**
	 * @return array
	 */
	public function getPatternParams();

	/**
	 * @return mixed
	 */
	public function getDefaultReference();

	/**
	 * @return array
	 */
	public function getDefaultParams();
	
	/**
	 * @return mixed
	 */
	public function getOptions();
	
	
	public function __get($name);
	public function __set($name, $value);
	public function __isset($name);
	public function __unset($name);
}


