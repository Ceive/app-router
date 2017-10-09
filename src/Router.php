<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Ceive\Routing;
use Ceive\Routing\Route\BindingAdapter;
use Ceive\Routing\Route\PatternResolver;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class Router
 * @package Kewo\Matching
 *
 * Каждый Маршрут - это пирс для клиента
 *
 * Важно понимать, что роутер способен на:
 * Восприятие структуры разделения приложений
 * Вытаскивать данные из запроса на вход в контроллер
 * Формировать ссылку на некий контроллер
 *
 *
 *
 *
 *
 * Роутер подготавливает и вытаскивает из внешнего запроса input для передачи его в диспетчеризацию
 *
 * @todo Способ разбора шаблонов
 * @todo Компиляция шаблонов и нативных проверок
 * @todo Делегирование input для MCA системы
 * @todo Предоставление информации для генерации URL и ссылок на диспетчеризации
 * @todo
 */
interface Router extends Matchable{

	/**
	 * @return PatternResolver
	 */
	public function getPatternResolver();
	
	/**
	 * @todo Необходимо поведение при отсутствии адаптера
	 * @return BindingAdapter
	 */
	public function getBindingAdapter();
	
	/**
	 * @param Matching $routing
	 * @return \Generator|Matching[]
	 */
	public function matchLoop(Matching $routing);

	/**
	 * @param Matching $matching
	 * @return Matching
	 */
	public function match(Matching $matching);
	
	/**
	 * @param $getDefaultReference
	 * @param $getDefaultReference1
	 * @return mixed
	 */
	public function extendReference($getDefaultReference, $getDefaultReference1);
	
	/**
	 * @param Route $route
	 * @return $this
	 */
	public function registerRoute(Route $route);
	
	/**
	 * @param $name
	 * @param array $arguments
	 * @return mixed
	 */
	public function fireEvent($name, array $arguments);
	
	/**
	 * @param $method
	 * @param Matching $matching
	 * @param $arguments
	 * @return mixed
	 * @internal param $this
	 */
	public function extra($method, Matching $matching, array $arguments);
	
}


