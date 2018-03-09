<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Ceive\Routing;

use Ceive\Routing\Exception\Matching\MissingException;
use Ceive\Routing\Exception\Matching\SkipException;
use Ceive\Routing\Exception\Rendering\InvalidParametersException;
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
		$this->getRouter()->fireEvent('beforeMatch',[$this, $matching]);
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
		$result = $resolver->patternRender($params, $this->pattern, $this->pattern_options);
		$this->_checkRendered($result);
		return $result;
	}
	
	/**
	 * @param $result
	 * @throws InvalidParametersException
	 */
	protected function _checkRendered($result){
		if($this->_matchPath($result)===false){
			throw new InvalidParametersException('Check params please: '.implode($this->getPatternParams()));
		}
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
		$this->getRouter()->fireEvent('conformed', [$matching, $this]);
	}
	
	protected function _matchingFinish(Matching $matching){
		$this->getRouter()->fireEvent('finish', [$matching, $this]);
	}
	
	/**
	 * Подготовка массива параметров перед отрисовкой
	 * @param $params
	 * @return mixed
	 * @throws RenderingException
	 */
	protected function _prepareRenderParams($params){
		$collection = $this->getRouter()->fireEvent('prepareRenderParams', [$this, $params]);
		$collection = array_diff($collection,[null,false,'']);
		return call_user_func_array('array_replace', $collection);
	}
	
	/**
	 * Проверка окружения: выброс Skip в случае неудачи.
	 *
	 * @param Matching $matching
	 */
	protected function _checkEnv(Matching $matching){
		$this->getRouter()->fireEvent('checkEnv',[$matching, $this]);
	}
	
	/**
	 * @param Matching $matching
	 */
	protected function _matchingReached(Matching $matching){
		$matching->reached();
		//$this->getRouter()->fireEvent('reached', [$this, $matching]);
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


