<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */

namespace Ceive\Routing\Method\Layout;

use Ceive\Routing\Method\Layout;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class Processing
 * @package Ceive\Routing\Method
 */
class Processing{
	
	/** @var  Layout */
	public $layout;
	
	/** @var  Processing|null */
	public $prev;
	
	/** @var string  */
	public $path;
	
	/** @var string */
	public $name;
	
	/** @var  string|null */
	public $content;
	
	/** @var array  */
	public $params = [];
	
	/** @var array  */
	public $data = [];
	
	public $sub;
	
	
	public function __construct($name, $path, Layout $layout, Processing $prev = null, $content = null, array $params = []){
		$this->name = $name;
		$this->path = $path;
		$this->prev = $prev;
		$this->content = $content;
		$this->params = $params;
		$this->layout = $layout;
	}
	
	public function process(){
		if(file_exists($this->path)){
			$this->_render();
		}
		return $this->content;
	}
	
	public function _render(){
		try{
			ob_start();
			$layout = $this->layout;
			$this->data = (include $this->path);
		}finally{
			$this->content = $content = ob_get_clean();
		}
	}
	
	/**
	 * @param $tpl
	 * @param array $params
	 * @param null $content
	 * @param array $data
	 * @return mixed|null
	 */
	public function inc($tpl, array $params = null, $content = null, &$data = []){
		$path = $this->layout->path($tpl, dirname($this->path));
		if(file_exists($path)){
			$this->sub = new Processing($tpl, $path, $this->layout, $this, $content, (array)$params);
			$this->sub->process();
			$data = $this->sub->data;
			return $this->sub->data;
		}else{
			return $this->sub = null;
		}
	}
	
}


