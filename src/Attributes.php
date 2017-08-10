<?php
namespace quill;

trait Attributes {
	/**
	 *
	 * @var array
	 */
	private $attributes;
	
	/**
	 * 
	 */
	private function initAttributes(){
		$this->attributes	= [];
	}
	
	/**
	 *
	 * @param array $attributes
	 */
	public function setAttributes($attributes){
		$this->attributes = array_merge($this->attributes, $attributes);
	}
	
	/**
	 *
	 * @param string $name
	 * @return string
	 */
	public function getAttribute($name){
		return $this->attributes[$name];
	}
	
	/**
	 * 
	 * @return array
	 */
	public function getAttributes(){
		return $this->attributes;
	}
}