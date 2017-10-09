<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */


namespace Ceive\Routing;
include '../vendor/autoload.php';

use Ceive\Routing\Hierarchical\ConjunctionRoute;
use Ceive\Routing\Hierarchical\MatchingDecorator;
use Ceive\Routing\Simple\SimpleMatching;
use Ceive\Routing\Simple\SimplePatternResolver;
use Ceive\Routing\Simple\SimpleRoute;
use Ceive\Routing\Simple\SimpleRouter;

$resolver   = new SimplePatternResolver();
$router     = new SimpleRouter($resolver);

// Не забыть: В данный момент матч может возвращять иной объект Matching , в связи с необходимостью проведения живой совпавшей цепи для реализации Locations в будущем
$route = (new ConjunctionRoute('user:list','/users'))
	->setRouter($router)
	->addRoute(
		(new SimpleRoute('user:create','/create'))->setRouter($router)
	)
	->addRoute(
		(new ConjunctionRoute('user:view','/(?<uid>\d+)')) // matched destination on '/users/91'
		->setRouter($router)
			->addRoute( (new SimpleRoute('user:update', '/update'))->setRouter($router) )
			->addRoute( (new SimpleRoute('user:delete', '/delete'))->setRouter($router) )
			->addRoute(
				(new ConjunctionRoute('user:note:list','/notes'))
					->setRouter($router)
					->addRoute(
						(new ConjunctionRoute('user:note:view','/(?<note_id>\d+)'))
							->setRouter($router)
							->addRoute( (new SimpleRoute('user:note:update', '/update'))->setRouter($router) )
							->addRoute( (new SimpleRoute('user:note:delete', '/delete'))->setRouter($router) )
					)
					->addRoute(
						(new SimpleRoute('user:note:add', '/add'))->setRouter($router)
					)
					->addRoute(
						(new SimpleRoute('user:note:delete', '/delete'))->setRouter($router)
					)
			)
	)->addRoute(
		(new SimpleRoute('user:bo','/(?<babi>\d+)/ba'))->setRouter($router)
	);

/**
 * Поиск по дереву маршрутов! (От общего к частному)
 * Отображение ссылки на маршрут. (От частного к общему)
 * Процессинг данных запроса (От общего к частному со сбором данных верхнего уровня)
 *
 */
$p = '/users/91/notes/87/update';
$p1 = '/users/91/ba';
$n = '/y/t/d/s';
$matching = new SimpleMatching($n);

$matching = $route->match($matching);


//$router->render('index:index:index','admin');

$reached = $matching->isReached();
if($reached){
	
	/**
	 * Получение простых хлебных крошек на основе пути маршрутов (ИЕРАРХИЯ)
	 */
	
	$m = $matching;
	$a = [$m->getReference()];
	while($m instanceof MatchingDecorator && $m = $m->getWrapped()){
		$a[] = $m->getReference();
	}
	
	$last = count($a)-1;
	$a = array_reverse($a);
	foreach($a as $i=>&$ref){
		if($i<$last){
			$ref = "<i>$ref</i>";
		}else{
			$ref = "<b style='color: cadetblue;'>$ref</b>";
		}
	}
	
	echo implode(' / ', $a);
	
}else{
	
}


$way = $route->findWayTo(function(Route $route){
	return $route->getDefaultReference() === 'user:note:update';
});
// !!Добавить проверку отсутствующих параметров
// !!Выборка относительных объектов для выборки значения другого параметра, но нужно учесть что родительский маршрут будет рендериться
$parameters = [
	'uid' => 57,
	'note_id' => 89
];
$way = array_reverse($way);
$link = 'http://www.fff.ru';
foreach($way as $chunk){
	$link.=$chunk->render($parameters);
}
echo $link;
/**
 * Нужно суммировать параметры в конечной операции РЕНДЕРИНГА
 *
 */



/**
 *
 * 1. Рендеринг конечных и срединных маршрутов относительно своих Родителей
 *      можно использовать интерфейс Router для родителей и выставлять setRouter(ConjunctionRoute<Router||Route>).
 *      router::render();
 *      router::match() -> match path and cut if need
 * 2. Рендеринг по ссылке и параметрам
 * 3. Проверка контекста, Проверка среды, Проверка запроса.
 * 4. Поведение при достижении (Reached)
 * 5. Проброс (Next)
 *
 */
/**
 * Нужно принять решение о том как реализовывать получение "Родительского маршрута"
 * Важно для Отрисовки из конкретного маршрута (Получение родительских отрезков путей result_path)
 */