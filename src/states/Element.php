<?php
namespace quill\states;

class Element extends DefaultState {
	/**
	 * 
	 * @var \quill\Element
	 */
	private $parent;
	
	/**
	 * 
	 * @param \quill\Element $parent
	 */
	public function __construct($parent){
		$this->parent = $parent;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \quill\states\DefaultState::element()
	 */
	public function element($parser, $name, $attributes){
		$this->parent->append($element =  new \quill\Element($name));
		$element->setAttributes($attributes);
		return new self($element);
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \quill\states\DefaultState::text()
	 */
	public function text($parser, $value){
		$this->parent->text($value);
	}
}