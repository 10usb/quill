<?php
namespace quill;

abstract class Node {
	/**
	 * 
	 * @var \quill\Element
	 */
	protected $parent;
	
	/**
	 * 
	 */
	public function __construct(){
		$this->parent	= null;
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function isFixed(){
		return false;
	}
	
	/**
	 * 
	 * @return \quill\Element
	 */
	public function getParent(){
		return $this->parent;
	}
	
	/**
	 *
	 * @param string $filename
	 */
	public abstract function save($uri = 'php://output');
}