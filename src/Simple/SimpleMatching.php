<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Ceive\Routing\Simple;
use Ceive\Routing\MatchingAbstract;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class SimpleMatchingAbstract
 * @package Ceive\Routing\Matching
 */
class SimpleMatching extends MatchingAbstract{

	/** @var string  */
	private $path;

	/**
	 * SimpleMatchingAbstract constructor.
	 * @param string $path
	 */
	public function __construct($path){
		$this->path = $path;
		$this->proposed_path = $path;
	}

	/**
	 * @return string
	 */
	public function getPath(){
		return $this->path;
	}

}


