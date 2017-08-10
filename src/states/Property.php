<?php
namespace quill\states;

class Property extends DefaultState {
	/**
	 *
	 * @var string
	 */
	private $value;
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \quill\states\DefaultState::element()
	 */
	public function open($parser, $name, $attributes){
		$this->value	= '';
	}
	
	public function text($parser, $value){
		$this->value.= $value;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 * @see \quill\states\DefaultState::close()
	 */
	public function close($parser, $name){
		$parser->getDocument()->getInfo()->set($name, $this->value);
	}
}