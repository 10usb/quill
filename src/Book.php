<?php
namespace quill;

interface Book {
	/**
	 * 
	 * @param \quill\Info $info
	 */
	public function setInfo($info);
	
	/**
	 * @return boolean
	 */
	public function hasSize();
	
	/**
	 * 
	 * @param number $width
	 * @param number $height
	 */
	public function setSize($width, $height);
	
	/**
	 * 
	 * @param number|boolean $width
	 * @param number|boolean $height
	 * @return \alf\Canvas
	 */
	public function addPage($width = false, $height = false);
}