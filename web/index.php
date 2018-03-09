<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */


namespace Ceive\Routing;
include '../vendor/autoload.php';

use Ceive\Routing\Hierarchical\ConjunctionFactory;
use Ceive\Routing\Hierarchical\ConjunctionRoute;
use Ceive\Routing\Method\LayoutMethod;
use Ceive\Routing\Method\LocationMethod;
use Ceive\Routing\Plugin\BindingPlugin;
use Ceive\Routing\Plugin\NotFoundPlugin;
use Ceive\Routing\Plugin\PathModifierPlugin;
use Ceive\Routing\Plugin\ProcessPlugin;
use Ceive\Routing\Route\MyBindingAdapter;
use Ceive\Routing\Simple\SimpleMatching;
use Ceive\Routing\Simple\SimplePatternResolver;
use Ceive\Routing\Simple\SimpleRoute;
use Ceive\Routing\Simple\SimpleRouteFactory;
use Ceive\Routing\Simple\SimpleRouter;

$resolver   = new SimplePatternResolver();
$resolver->setPathDelimiter('__');

$router = new SimpleRouter($resolver);

class MyProcess extends ProcessPlugin{
	
	public function process(){
		$way = $this->matching->way();
		$layout = $this->matching->layout();
		
		echo $layout->render();
	}
	
}

$router->addPlugin(new MyProcess());
$router->addPlugin(new BindingPlugin());
$router->addPlugin(new PathModifierPlugin());
$router->addPlugin(new NotFoundPlugin());
$router->setMethod('location',new LocationMethod(), true );

$baseDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views';

$router->setMethod('layout',new LayoutMethod($baseDir,'main'));


$router->setBindingAdapter(new MyBindingAdapter());

$director = new FactoryDirector($router);

$director->setFactory(new ConjunctionFactory(),ConjunctionRoute::class);
$director->setFactory(new SimpleRouteFactory(),SimpleRoute::class);
$director->setDefault(SimpleRoute::class);

$fn = [
	'clearExtraSlashes' => function(Route $route, Matching $matching){
		$pp = $matching->getProposedPath();
		
		$pp = rtrim($pp,"\\/");
		
		//if($pp === '/') $pp = '';
		//else $pp = rtrim($pp,"\\/");
		
		$matching->setProposedPath($pp);
	}
];

$route = $director->createRoute([
	'pattern' 	=> '',
	'type' 		=> ConjunctionRoute::class,
	'location' 	=> [
		'title' => 'Главная',
	],
	'onBeforeMatch' => $fn['clearExtraSlashes'],
	'children' => [
		[
			'pattern' => '/users',
			'action' => 'user:list',
			'rules' => [[
				'http.method' => 'get',
			]],
			'location' => [
				'title' => 'Пользователи',
			],
			'view' => [
				'layout' => 'routes/site',
				'template' => 'routes/site'
			],
			'type' => ConjunctionRoute::class,
			
			'children' => [[
				'pattern' => '/(?<babi>\d+)/ba',
				'action' => 'user:bo',
			], [
				'pattern' => '/create',
				'action' => 'user:create',
				'form' => [
					'source' => 'http.post'
				]
			],[
				'pattern' => '/(?<user__id>\d+)',
				'action' => '#user.view',
				'type' => ConjunctionRoute::class,
				'static' => true,// если в базе данных не будет объекта с айди user__id то произойдет выброс 404
				'rules' => [
					'http.method' => 'get'
				],
				'output' => [ 'json', 'html' ],
				'view' => [
					'template' => 'routes/site/user',
					'layout' => [
						'base' => 'routes/site/user',
						'blocks' => [
							'avatar' => [
								'model/user/image',
								'scope' => [
									'image' => '{user.profile.avatar}'
								],
							],
						],
					],
				],
				'location' => [
					'title' => 'Табличка пользователя',
					'breadcrumb' => '{user.profile.getFullname(`Family N.S.`)}',
				],
				'objects'   => [
					'user' => 'UserClass'
				],
				
				'children'  => [ [
					'pattern' => '/update',
					'action' => 'user:update',
					'form' => [
						'source' => 'http.post'
					]
				], [
					'pattern' => '/delete',
					'action' => 'user:delete',
				], [
					'location' => [
						'breadcrumb' => 'Записи',
						'title' => 'Записи пользователя',
					],
					'pattern' => '/notes',
					'action' => 'user:note:list',
					'type' => ConjunctionRoute::class,
					
					'children'  => [ [
						
						'pattern' => '/(?<note__id>\d+)',
						'action' => 'user:note:read',
						'type' => ConjunctionRoute::class,
						'location' => [
							'breadcrumb' => '{note.title}',
							'title' => 'Запись пользователя',
						],
						'objects'   => [
							'note' => 'NoteClass'
						],
						'view' => [
							'template' => 'routes/site/user.note'
						],
						
						'children' => [ [
							'location' => [
								'title' => 'Редактирование',
							],
							'pattern' => '/update',
							'action' => 'user:note:update',
						], [
							'pattern' => '/delete',
							'action' => 'user:note:delete',
						] ],
					], [
						'pattern' => '/create',
						'action' => 'user:note:create',
					] ],
				] ],
			] ],
		]
	]
]);

$path = $_SERVER['REQUEST_URI'];
// встроить Работу с Объектами ORM, В маршрутизатор (CONVERTER)
$matching = new SimpleMatching($path);
$router->addRoute($route);

$router->process($matching);

__halt_compiler();

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
	
	$links = [$m->getElapsedPath()];
	
	while($m instanceof MatchingDecorator && $m = $m->getWrapped()){
		$a[] = $m->getReference();
		$links[] = $m->getElapsedPath();
	}
	foreach($a as $i=>&$ref){
		if($i>=1){
			$ref = "<a href='{$links[$i]}'><i>$ref</i></a>";
		}else{
			$ref = "<a href='{$links[$i]}'><b style='color: cadetblue;'>$ref</b></a>";
		}
	}
	
	echo implode(' / ', array_reverse($a));
	
}else{
	echo 'Not Reached';
}
function renderSubTree(ConjunctionRoute $route){
	$link = $route->render();
	echo '<a href="'.$link.'">'.$route->getDefaultReference().'</a>';
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

