<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing\Method;
use Ceive\Routing\Matching;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class Location
 * @package Ceive\Routing\Method
 */
class Location{
	
	/** @var Location  */
	protected $parent;
	
	/** @var Matching  */
	protected $matching;
	
	/** @var  string */
	protected $title;
	
	/** @var  string */
	protected $menu;
	
	/** @var  string */
	protected $breadcrumb;
	
	/** @var  string */
	protected $description;
	
	protected $way = [];
	
	/**
	 * Location constructor.
	 * @param Location[] $way
	 * @param array $config
	 * @param Matching|null $matching
	 */
	public function __construct(array $way, $config = [],Matching $matching = null){
		$this->parent = $way? $way[count($way)-1] : null;
		$this->way = array_merge($way, [$this]);
		$this->matching = $matching;
		foreach(array_replace(array_diff_key(get_object_vars($this),array_flip(['parent','way'])),$config) as $k => $v){
			$this->{$k} = $v;
		}
		
	}
	
	/**
	 * @return Location
	 */
	public function getParent(){
		return $this->parent;
	}
	
	/***
	 * @return string
	 */
	public function getTitle(){
		return $this->title;
	}
	
	/**
	 * @return string
	 */
	public function getMenu(){
		return $this->menu?:$this->title;
	}
	
	/**
	 * @return string
	 */
	public function getBreadcrumb(){
		return $this->breadcrumb?:$this->title;
	}
	
	/**
	 * @return string
	 */
	public function getDescription(){
		return $this->description;
	}
	
	public function getLink(){
		return $this->matching->getElapsedPath();
	}
	
	/**
	 * @return Location[]
	 */
	public function way(){
		return $this->way;
	}
	
}


