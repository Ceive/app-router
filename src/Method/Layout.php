<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing\Method;

use Ceive\Routing\Hierarchical\MatchingReached;
use Ceive\Routing\Matching;
use Ceive\Routing\Method\Layout\Processing;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class Layout
 * @package Ceive\Routing\Method
 */
class Layout{
	
	/** @var  LayoutMethod */
	public $method;
	
	/**
	 * @var Layout
	 */
	public $parent;
	
	/**
	 * @var MatchingReached
	 */
	public $reached;
	/**
	 * @var Matching
	 */
	public $matching;
	
	/**
	 * @var string
	 */
	public $template;
	
	
	/** @var  Layout|null */
	public $prev;
	
	/** @var bool  */
	public $break = false;
	
	
	
	public function __construct($template, LayoutMethod $method, Matching $matching, MatchingReached $reached, Layout $parent = null){
		
		$this->matching = $matching;
		$this->parent = $parent;
		$this->template = $template;
		$this->reached = $reached;
		$this->method = $method;
		$this->path = $this->path($this->template);
	}
	
	
	/**
	 * @param null $content
	 * @param Layout $prev
	 * @return null|string
	 */
	public function render($content = null, Layout $prev = null){
		$this->prev  = $prev;
		$this->break = false;
		
		if($this->template){
			$params = array_replace(
				$this->matching->getOptions(),
				$this->matching->getParams()
			);
			$params['*'] = $content;
			
			$operation = new Processing($this->template, $this->path, $this, null, $content, $params);
			$content = $operation->process();
		}
		
		if($this->break){
			return $content;
		}
		
		if($this->parent){
			return $this->parent->render($content, $this);
		}else{
			return $content;
		}
	}
	
	public function getBaseDirname(){
		return $this->method->getBaseDirname();
	}
	
	public function path($tpl, $base = null){
		
		$baseDir = $this->getBaseDirname();
		
		if(in_array(substr($tpl, 0, 1), ['/','\\'])){
			$base = $baseDir;
		}else if(!$base){
			$base = $baseDir;
		}
		
		$tplPath = realpath( rtrim($base,'\/') . DIRECTORY_SEPARATOR . ltrim($tpl,'\/') . '.phtml' );
		if($tplPath && strpos($tplPath, $baseDir) !== false){
			return $tplPath;
		}
		return null;
	}
}


