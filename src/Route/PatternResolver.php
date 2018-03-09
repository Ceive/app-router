<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Ceive\Routing\Route;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class PatternResolver
 * @package Kewo\Matching
 */
interface PatternResolver{

	/**
	 * @param $pattern
	 * @param $pattern_options
	 * @return array Высекание параметров из шаблона
	 * Высекание параметров из шаблона
	 */
	public function patternPlaceholders($pattern, $pattern_options = null);

	/**
	 * @param $pattern
	 * @param $pattern_options
	 * @return mixed
	 */
	public function patternMetadata($pattern, $pattern_options = null);

	/**
	 * @param array $params
	 * @param $pattern
	 * @param $pattern_options
	 * @return string
	 */
	public function patternRender(array $params, $pattern, $pattern_options = null);



	/**
	 * @param $string
	 * @param $pattern
	 * @param $pattern_options
	 * @return array|bool|false
	 */
	public function patternMatch($string, $pattern, $pattern_options = null);

	/**
	 * @param $string
	 * @param $pattern
	 * @param $pattern_options
	 * @param $received_at_start
	 * @return array|bool|false
	 */
	public function patternMatchStart($string, $pattern, $pattern_options = null, &$received_at_start = null);
	
	/**
	 * @return string|false
	 */
	public function getPathDelimiter();
	
	/**
	 * @param $path
	 * @return array
	 */
	public function explode($path);
	
	/**
	 * @return mixed
	 */
	public function isEmbeddedPaths();
	
	/**
	 * @param $path
	 * @return string
	 */
	public function join($path);
}


