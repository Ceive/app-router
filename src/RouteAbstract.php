<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Ceive\Routing;

use Ceive\Routing\Exception\Matching\MissingException;
use Ceive\Routing\Exception\Matching\SkipException;
use Ceive\Routing\Exception\Matching\StabilizeException;
use Ceive\Routing\Exception\Rendering\MissingParameterException;
use Ceive\Routing\Exception\Rendering\MissingParametersException;
use Ceive\Routing\Exception\RenderingException;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class RouteAbstract
 * @package Ceive\Routing
 */
abstract class RouteAbstract implements Route{
	
	
	//TODO:  insertion scope variables to path dynamically {}
	
	/** @var  Router */
	protected $router;

	/** @var string */
	protected $pattern;

	/** @var  null|array */
	protected $pattern_options;

	/** @var mixed */
	protected $reference;

	/** @var array  */
	protected $params = [];
	
	/** @var array  */
	protected $options = [];
	
	/**
	 * SimpleRouteAbstract constructor.
	 * @param $pattern
	 * @param $pattern_options
	 * @param $reference
	 */
	public function __construct($reference, $pattern, $pattern_options = null){
		$this->pattern          = $pattern;
		$this->pattern_options  = $pattern_options;
		$this->reference        = $reference;
	}

	/**
	 * @param Router $router
	 * @return $this
	 */
	public function setRouter(Router $router){
		if($this->router !== $router){
			$this->router = $router;
		}
		return $this;
	}
	
	public function setOptions(array $options){
		$this->options = $options;
		return $this;
	}
	public function getOptions(){
		return $this->options;
	}

	/**
	 * @return Router
	 * @throws RoutingException
	 */
	public function getRouter(){
		if(!$this->router){
			throw new RoutingException('Router not be setup!');
		}
		return $this->router;
	}
	
	/**
	 * @param Matching $matching
	 * @return Matching
	 * @throws \Ceive\Routing\Exception\Matching\SkipException
	 */
	public function match(Matching $matching){

		$this->_doMatch($matching);

		if($matching->isReached()){
			$this->_checkEnv($matching);
			$this->_matchingConformed($matching);
			$this->_matchingReached($matching);
		}else{
			$matching->setConformed(false);
		}

		return $matching;
	}
	
	/**
	 * @param Matching $matching
	 * @return Matching
	 */
	protected function _doMatch(Matching $matching){
		$path = $matching->getProposedPath();
		if( false !== $params = $this->_matchPath($path, $received)){
			$matching->setConformed(true);
			$matching->setConformedPath($received);
			$matching->setRoute($this);
			$matching->setParams($params);
		}
		return $matching;
	}
	
	/**
	 * @param $params
	 * @return string
	 */
	public function render($params = null){
		$resolver = $this->getRouter()->getPatternResolver();
		$params = $this->_prepareRenderParams($params);
		$params = (array) $params;
		return $resolver->patternRender($params, $this->pattern, $this->pattern_options);
	}
	
	/**
	 * @return string|null
	 */
	public function getPattern(){
		return $this->pattern;
	}
	
	/**
	 * @return array|null
	 */
	public function getPatternOptions(){
		return $this->pattern_options;
	}
	
	
	/**
	 * @return array
	 * @throws RoutingException
	 */
	public function getPatternParams(){
		$pattern_resolver = $this->getRouter()->getPatternResolver();
		return $pattern_resolver->patternPlaceholders($this->pattern, $this->pattern_options);
	}

	/**
	 * @return mixed
	 */
	public function getDefaultReference(){
		return $this->reference;
	}

	/**
	 * @return array
	 */
	public function getDefaultParams(){
		return $this->params;
	}

	/**
	 * @param $path
	 * @param $matched_received
	 * @return array|false
	 * @throws RoutingException
	 */
	protected function _matchPath($path, &$matched_received = null){
		$resolver = $this->getRouter()->getPatternResolver();
		
		$pattern = $this->pattern;
		$pattern_options = $this->pattern_options;
		
		$data = $resolver->patternMatchStart($path, $pattern, $pattern_options, $matched_received);
		if($data === false){
			$matched_received = null;
		}
		return $data;
	}

	/**
	 * @param Matching $matching
	 */
	protected function _matchingConformed(Matching $matching){
		
		#Example binding dsi
		$b = [
			'classname' => 'UserClass',
			'from' => 'user_id',
			'to' => 'id',
		];
		
		$matching->setReference($this->reference);
		$this->getRouter()->fireEvent('conformed', [$this, $matching]);
		/*
		
		$pattern_params = $params = array_replace((array)$this->params, $matching->getParams());
		$bindings = isset($this->options['objects'])?$this->options['objects']:[];
		$delimiter = $this->getRouter()->getPatternResolver()->getPathDelimiter();
		
		if($delimiter){
			foreach($this->getPatternParams() as $param_key_def){
				$chunks = $this->_decompositePath($param_key_def, $delimiter);
				$container_key = $chunks[0];
				#Binding
				if(isset($bindings[$container_key])){
					$binding_rule = $bindings[$container_key];
					$object_id = $params[$param_key_def];
					$query_key = $this->_compositePath($chunks, $delimiter);
					#Fetch
					$object = $this->_fetchBinding($query_key,$object_id, $binding_rule, $pattern_params, $param_key_def);
					#Check
					$value = $this->_checkoutBinding($object, $pattern_params, $container_key, $param_key_def);
					
					$params[ $container_key ] = $value;
					unset($params[$param_key_def], $value);
				}
			}
		}
		
		$matching->setParams($params, true);*/
	}
	
	protected function _matchingFinish(Matching $matching){
		$this->getRouter()->fireEvent('finish', [$this, $matching]);
	}
	
	/**
	 * Подготовка массива параметров перед отрисовкой
	 * @param $params
	 * @return mixed
	 * @throws RenderingException
	 */
	protected function _prepareRenderParams($params){
		
		$params = $this->getRouter()->fireEvent('prepareRenderParams', [$this, $params]);
		$params = array_diff($params,[null,[],false,'']);
		return call_user_func_array('array_replace', $params);
		
		// todo Попробовать локальное определение с перескоком на родительские цепочки
		/*
		
		$errors = [];
		foreach($meta as $paramKey => $alternativeBinding){
			if(!isset($params[$paramKey])){
				$errors[$paramKey] = $alternativeBinding;
			}
		}
		if($errors){
			$missing = [];
			foreach($errors as $paramKey => $alternative){
				$alternativeKey = $alternativeBinding = null;
				if($alternative){
					list($alternativeKey, $alternativeBinding) = $alternative;
				}
				$missing[] =  new MissingParameterException($paramKey, $alternativeKey, $alternativeBinding);
			}
			throw new MissingParametersException($missing);
		}
		return $params;*/
		
	}
	
	/**
	 * @param $param
	 * @param $path_delimiter
	 * @return array
	 */
	protected function _decompositePath($param, $path_delimiter){
		return explode($path_delimiter,$param);
	}
	
	/**
	 * @param $chunks @see _decompositePath
	 * @param $path_delimiter - '/', '.' etc... @see PatternResolver::getPathDelimiter
	 * @return string without first path element(without container)
	 */
	protected function _compositePath($chunks, $path_delimiter){
		array_shift($chunks);
		return implode($path_delimiter, $chunks);
	}
	
	/**
	 * Выдача связанного объекта (Проверка и т.п)
	 * @param object $object
	 * @param array $pattern_params
	 * @param $container_key
	 * @param $param_key_def
	 * @return object
	 * @throws MissingException|SkipException
	 */
	protected function _checkoutBinding($object,array $pattern_params, $container_key, $param_key_def){
		if(!$object){
			if(isset($this->options['static']) && $this->options['static']){
				throw new MissingException();
			}else{
				throw new SkipException();
			}
		}
		return $object;
	}
	
	/**
	 * Обрабатывает метаданные связывания, и берет нужный объект из бд
	 * @param $field_path
	 * @param $field_value
	 * @param $binding_rule
	 * @param array $pattern_params
	 * @param null $full_path
	 * @return null
	 */
	protected function _fetchBinding($field_path, $field_value, $binding_rule, $pattern_params = [], $full_path = null){
		return $this->getRouter()->getBindingAdapter()->load($field_path, $field_value, $binding_rule, $pattern_params, $full_path);
	}
	
	/**
	 * Проверка окружения: выброс Skip в случае неудачи.
	 *
	 * @param Matching $matching
	 */
	protected function _checkEnv(Matching $matching){
		
	}
	
	/**
	 * @param Matching $matching
	 */
	protected function _matchingReached(Matching $matching){
		$this->getRouter()->fireEvent('reached', [$this, $matching]);
	}
	
	
	public function __get($name){
		if(isset($this->options[$name])){
			return $this->options[$name];
		}
		return null;
	}
	
	public function __isset($name){
		return isset($this->options[$name]);
	}
	
	public function __set($name, $value){
		$this->options[$name] = $value;
	}
	
	public function __unset($name){
		unset($this->options[$name]);
	}
}


