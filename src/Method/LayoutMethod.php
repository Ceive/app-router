<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing\Method;

use Ceive\Routing\Hierarchical\MatchingReached;
use Ceive\Routing\Matching;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class LayoutMethod
 * @package Ceive\Routing\Method
 */
class LayoutMethod{
	
	protected $main;
	
	protected $baseDirname;
	
	/**
	 * LayoutMethod constructor.
	 * @param $baseDirname
	 * @param null $main
	 */
	public function __construct($baseDirname, $main = null){
		$this->main = $main;
		$this->baseDirname = $baseDirname;
	}
	
	/**
	 * @param MatchingReached $matching
	 * @return Layout
	 */
	public function run(MatchingReached $matching){
		$layouts = [];
		$lastLayout = null;
		
		$this->_layouts($layouts, $this->main, $matching, $matching, $lastLayout);
		
		$way = $matching->way();
		$count = count($way);
		
		foreach($way as $i => $m){
			$view = $m->getOption('view');
			if($view){
				if(!is_string($view)){
					$view = array_replace([
						'layout'     => null, // layout for contains
						'container'  => null, // wrapper for children
						'template'   => null, // render self
					],(array)$view);
					$template   = $view['template'];
					$layout     = $view['layout'];
					$container  = $view['container'];
				}else{
					$template   = $view;
					$layout     = null;
					$container  = null;
				}
				
				$this->_layouts($layouts, $layout, $m,$matching, $lastLayout);
				if($i == $count - 1){
					$this->_layouts($layouts, $template, $m, $matching, $lastLayout);
				}else if($i == $count - 2){
					$this->_layouts($layouts, $container, $m, $matching, $lastLayout);
				}
			}
		}
		return $lastLayout;
	}
	
	/**
	 * @param Layout[] $layouts
	 * @param $tpl
	 * @param Matching $matching
	 * @param MatchingReached $reached
	 * @param Layout|null $lastLayout
	 * @return array
	 */
	protected function _layouts(array &$layouts = [], $tpl, Matching $matching, MatchingReached $reached, Layout &$lastLayout = null){
		if($tpl){
			if(!is_array($tpl)){
				$tpl = [$tpl];
			}
			foreach($tpl as $t){
				if(is_string($t)){
					foreach($layouts as $l){
						if($l->template === $t){
							continue(2);
						}
					}
					$layouts[] = $lastLayout = new Layout($t, $this, $matching, $reached, $lastLayout);
				}else if(is_array($t)){
					$this->_layouts($layouts, $t, $matching, $reached, $lastLayout);
				}
			}
			return $layouts;
		}
		return $layouts;
	}
	
	public function getBaseDirname(){
		return $this->baseDirname;
	}
	
}


