<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Ceive\Routing\Simple;
use Ceive\Routing\Route\PatternResolver;


/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class SimplePatternResolver
 * @package Ceive\Routing
 */
class SimplePatternResolver implements PatternResolver{

	/** @var array  */
	protected $patterns = [];

	protected $path_delimiter = '__';
	
	
	
	/**
	 * @param $pattern
	 * @param $pattern_options
	 * @return array Высекание параметров из шаблона
	 * Высекание параметров из шаблона
	 */
	public function patternPlaceholders($pattern, $pattern_options = null){
		$key = md5(serialize([$pattern, $pattern_options]));
		if(!isset($this->patterns[$key])){
			preg_match_all('@\\\\|\\\\\(|\(\?\<([A-z]+(?:\w+))\>@m', $pattern, $m);
			$names = [];
			if($m[0]){
				foreach($m[0] as $i => $global_matched){
					$names[] = $m[1][$i];
				}
			}
			$this->patterns[$key] = $names;
		}
		return $this->patterns[$key];
	}

	/**
	 * @param $pattern
	 * @param $pattern_options
	 * @return mixed
	 */
	public function patternMetadata($pattern, $pattern_options = null){
		return $this->patternPlaceholders($pattern, $pattern_options);
	}


	/**
	 * @param array $params
	 * @param $pattern
	 * @param $pattern_options
	 * @return string
	 */
	public function patternRender(array $params, $pattern, $pattern_options = null){
		return preg_replace_callback('@\\\\|\\\\\\(|(\(\?\<([A-z]+\w*)\>(?R)*\))|[^()\\\\]+@smi',function($m) use($params){
			if(isset($m[1])){
				if(isset($params[$m[2]])){
					return $params[$m[2]];
				}else{
					// param not found
					return null;
				}
			}else{
				return $m[0];
			}
		}, $pattern );
	}


	/**
	 * @param $string
	 * @param $pattern
	 * @param $pattern_options
	 * @return array|bool
	 */
	public function patternMatch($string, $pattern, $pattern_options = null){
		if(preg_match('@^'.addcslashes($pattern,'@').'$@m', $string, $m)){
			$data = [];
			foreach($m as $name => $value){
				if(is_string($name)){
					$data[$name] = $value;
				}
			}
			return $data;
		}
		return false;
	}

	/**
	 * @param $string
	 * @param $pattern
	 * @param null $pattern_options
	 * @param null $received_at_start
	 * @return array|bool
	 */
	public function patternMatchStart($string, $pattern, $pattern_options = null, &$received_at_start = null){
		if(preg_match('@^'.addcslashes($pattern,'@').'@m', $string, $m)){
			$data = [];
			$received_at_start = $m[0];
			foreach($m as $name => $value){
				if(is_string($name)){
					$data[$name] = $value;
				}
			}
			return $data;
		}
		return false;
	}
	
	public function setPathDelimiter($delimiter = null){
		$this->path_delimiter = $delimiter;
		return $this;
	}
	
	public function getPathDelimiter(){
		return $this->path_delimiter;
	}
	
	/**
	 * @param $path
	 * @return array
	 */
	public function explode($path){
		if(is_array($path)){
			return $path;
		}
		return explode($this->path_delimiter, $path);
	}
	
	/**
	 * @param $path
	 * @return string
	 */
	public function join($path){
		if(is_string($path)){
			return $path;
		}else{
			return implode($this->path_delimiter, $path);
		}
	}
	
	
}


