<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */


namespace Ceive\Routing;
include '../vendor/autoload.php';

use Ceive\Routing\Hierarchical\ConjunctionFactory;
use Ceive\Routing\Hierarchical\ConjunctionRoute;
use Ceive\Routing\Hierarchical\MatchingDecorator;
use Ceive\Routing\Route\BindingAdapter;
use Ceive\Routing\Route\MyBindingAdapter;
use Ceive\Routing\Simple\SimpleMatching;
use Ceive\Routing\Simple\SimplePatternResolver;
use Ceive\Routing\Simple\SimpleRoute;
use Ceive\Routing\Simple\SimpleRouteFactory;
use Ceive\Routing\Simple\SimpleRouter;

$resolver   = new SimplePatternResolver();
$resolver->setPathDelimiter('__');

$router     = new SimpleRouter($resolver);

$router->setBindingAdapter(new MyBindingAdapter());

$director = new FactoryDirector($router);

$director->setFactory(new ConjunctionFactory(),ConjunctionRoute::class);
$director->setFactory(new SimpleRouteFactory(),SimpleRoute::class);
$director->setDefault(SimpleRoute::class);

/*
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
*/
$route = $director->createRoute([
	'pattern' => '/users',
	'action' => 'user:list',
	'rules' => [[
		'http.method' => 'get',
	]],
	'type' => ConjunctionRoute::class,
	'children' => [
		[
			'pattern'   => '/(?<babi>\d+)/ba',
			'action'    => 'user:bo',
		], [
			'pattern'   => '/create',
			'action'    => 'user:create',
			'form'      => [
				'source' => 'http.post'
			]
		],[
			'pattern'   => '/(?<user__id>\d+)',
			'action'    => '#user.view',
			'type'      => ConjunctionRoute::class,
			'static'    => true,// если в базе данных не будет объекта с айди user__id то произойдет выброс 404
			'rules'     => [
				'http.method' => 'get'
			],
			'output'    => [ 'json', 'html' ],
			'objects'   => [
				'user' => 'UserClass'
			],
			'children'  => [
				[
					'pattern'   => '/update',
					'action'    => 'user:update',
					'form'      => [ 'source' => 'http.post' ]
				], [
					'pattern'   => '/delete',
					'action'    => 'user:delete',
				], [
					'pattern'   => '/notes',
					'action'    => 'user:note:list',
					'type'      => ConjunctionRoute::class,
					'children'  => [
						[
							'pattern' => '/(?<note__id>\d+)',
							'action' => 'user:note:read',
							'type' => ConjunctionRoute::class,
							'children' => [
								[
									'pattern' => '/update',
									'action' => 'user:note:update',
								], [
									'pattern' => '/delete',
									'action' => 'user:note:delete',
								]
							],
						], [
							'pattern' => '/create',
							'action' => 'user:note:create',
						]
					],
				]
			],
		]
	],
]);

$path = $_SERVER['REQUEST_URI'];
// встроить Работу с Объектами ORM, В маршрутизатор (CONVERTER)
$matching = new SimpleMatching($path);
$router->addRoute($route);
$generator = $router->matchLoop($matching);
foreach($generator as $match){
	if($match->isReached()){
		$matching = $match;
		$params     = $match->getParams();      // Аргументы: Параметры которые были высечены из запроса
		$reference  = $match->getReference();   // Ссылка на определенное действие
		$route = $match->getRoute();            // Маршрут
		echo '<pre>';
		print_r([
			'action' => $reference,
			'arguments' => $params,
			'options' => $route->getOptions()
		]);
		echo '</pre>';
		
	}
	
}
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
	echo 'Is not reached';
}
/**
 * из Jungle:
 *
 * php-text-templaflect - Шаблоны
 *
 * HTTP запрос.
 * Для форм:
 *      POST параметры запроса, переходят в Route->getParam(field_name)
 *      Редактирование.
 *      Создание.
 * При удалении - Редирект на коллекцию объектов того же типа (L - list action)
 *
 *
 */