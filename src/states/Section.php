<?php
namespace quill\states;

class Section extends DefaultState {
	/**
	 * 
	 * @var \quill\Section
	 */
	private $section;
	
	/**
	 *
	 * {@inheritDoc}
	 * @see \quill\states\DefaultState::open()
	 */
	public function open($parser, $name, $attributes){
		$this->section = $parser->getDocument()->addSection();
		$this->section->setAttributes($attributes);
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \quill\states\DefaultState::element()
	 */
	public function element($parser, $name, $attributes){
		switch($name){
			case 'header': return new Element($this->section->getHeader());
			case 'body': return new Element($this->section->getBody());
			case 'footer': return new Element($this->section->getFooter());
		}
		return false;
	}
}