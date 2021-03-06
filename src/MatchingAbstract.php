<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Ceive\Routing;
use Ceive\Routing\Exception\Matching\SkipException;
use Ceive\Routing\Hierarchical\MatchingReached;


/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class MatchingAbstract
 * @package Ceive\Routing
 *
 * @ProposedPath Путь который предлагается для вычисления
 * @ConformedPath Путь, который достигнут (Это окончательный путь)
 *
 * @property mixed $proposedPath
 * @property mixed $conformedPath
 * @property mixed $params
 * @property mixed $reference
 * @property mixed $path
 * @property mixed $route
 *
 *
 */
abstract class MatchingAbstract implements Matching{

	//TODO: MIND: Use current domain name, with scheme in $proposed_path
	
	/** @var  Route */
	protected $route;

	/** @var array  */
	protected $options = [];

	/** @var  array */
	protected $params = [];

	/** @var  mixed */
	protected $reference;// todo action
	
	/** @var  bool */
	protected $conformed = false;

	/** @var  string|null */
	protected $conformed_path;

	/** @var  string|null */
	protected $proposed_path;
	
	/** @var array  */
	protected $runtime_params = [];

	/**
	 * @return mixed
	 * Ссылка на действие
	 */
	public function getReference(){
		return $this->reference;
	}

	/**
	 * @return array
	 */
	public function getParams(){
		return $this->params;
	}

	/**
	 * @return Route
	 */
	public function getRoute(){
		return $this->route;
	}

	/**
	 * @param \Ceive\Routing\Route $route
	 * @return $this
	 */
	public function setRoute(Route $route){
		$this->route = $route;
		return $this;
	}

	/**
	 * @param array $params
	 * @param bool|false $merge
	 * @return $this
	 */
	public function setParams(array $params, $merge = true){
		$this->params = $merge? array_replace($this->params, $params) : $params;
		return $this;
	}

	/**
	 * @param $reference
	 * @return $this
	 */
	public function setReference($reference){
		$this->reference = $reference;
		return $this;
	}

	/**
	 * @param bool|true $conformed
	 * @return $this
	 */
	public function setConformed($conformed = true){
		$this->conformed = $conformed !== false;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isConformed(){
		return $this->conformed;
	}

	/**
	 * @return bool
	 */
	public function isReached(){
		return $this->conformed && $this->proposed_path === $this->conformed_path;
	}


	/**
	 * @throws SkipException
	 */
	public function skip(){
		throw new SkipException();
	}

	/**
	 * @param $key
	 * @param $value
	 * @return $this
	 */
	public function setOption($key, $value){
		$this->options[$key] = $value;
		return $this;
	}

	/**
	 * @param $key
	 * @param null $default
	 * @return null
	 */
	public function getOption($key, $default = null){
		return isset($this->options[$key])?$this->options[$key]:null;
	}
	
	/**
	 * @param array $options
	 * @param bool|true $merge
	 * @return $this
	 */
	public function setOptions(array $options, $merge = true){
		$this->options = $merge?array_replace($this->options, $options):$options;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getOptions(){
		return $this->options;
	}


	/**
	 * @return void
	 */
	public function reset(){
		$this->route          = null;
		$this->reference      = null;
		$this->params         = [];
		$this->options        = [];
		$this->conformed_path = null;
		$this->conformed      = false;
	}

	/**
	 * @param string $received
	 * @return $this
	 */
	public function setConformedPath($received){
		$this->conformed_path = $received;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getConformedPath(){
		return $this->conformed_path;
	}

	/**
	 * @param $path
	 * @return $this|void
	 */
	public function setProposedPath($path){
		$this->proposed_path = $path;
	}

	/**
	 * @return string
	 */
	public function getProposedPath(){
		return $this->proposed_path;
	}
	
	
	/**
	 * @todo unexpected Request for route
	 * @param array $data
	 * @return mixed|void
	 */
	public function unexpectedRequest(array $data){
		foreach($data as $key => $value){
			$current    = $value['invalid_value'];
			$preferred  = $value['expected_value'];
			
			
		}
	}
	
	/**
	 * @todo stabilizeRequestWith for route
	 * @param array $data
	 * @return mixed|void
	 */
	public function stabilizeRequestWith(array $data){
		foreach($data as $key => $value){
			
		}
	}
	
	public function getEnv($key){
		// todo getEnv
	}
	
	public function setEnv($key, $value){
		// todo setEnv
	}
	
	
	/**
	 * @param $method
	 * @param $arguments
	 * @return mixed
	 */
	public function __call($method, array $arguments = []){
		return $this->getRoute()->getRouter()->method($method, $this, $arguments);
	}
	
	
	public function reached(){
		$this->getRoute()->getRouter()->fireEvent('reached', [$this, $this->getRoute()]);
	}
	
	public function __get($name){
		
		$result = $this->getRuntime($name);
		
		if($result !== null){
			return $result;
		}
		
		switch($name){
			case 'proposedPath':  return $this->getProposedPath(); break;
			case 'conformedPath': return $this->getConformedPath(); break;
			case 'elapsedPath':   return $this->getElapsedPath(); break;
			case 'params':        return $this->getParams(); break;
			case 'reference':     return $this->getReference(); break;
			case 'path':          return $this->getPath(); break;
			case 'route':         return $this->getRoute(); break;
		}
		
		return null;
	}
	
	public function __set($name, $value){
		$this->setRuntime($name, $value);
	}
	
	public function __isset($name){
		return $this->hasRuntime($name);
	}
	
	public function __unset($name){
		$this->removeRuntime($name);
	}
	
	/**
	 * @return string
	 */
	public function getElapsedPath(){
		return $this->getConformedPath();
	}
	
	public function setRuntimeParams(array $params, $merge = false){
		$this->runtime_params = $merge?array_replace($this->runtime_params, $params):$params;
		return $this;
	}
	
	public function getRuntimeParams(){
		return $this->runtime_params;
	}
	
	
	public function getRuntime($paramKey){
		if(isset($this->runtime_params[$paramKey])){
			return $this->runtime_params[$paramKey];
		}
		return null;
	}
	
	public function setRuntime($paramKey, $value){
		$this->runtime_params[$paramKey] = $value;
		return $this;
	}
	
	public function hasRuntime($paramKey){
		return isset($this->runtime_params[$paramKey]);
	}
	
	public function removeRuntime($paramKey){
		unset($this->runtime_params[$paramKey]);
		return $this;
	}
	
	
}