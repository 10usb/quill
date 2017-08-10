<?php
namespace quill\states;

class Contents extends DefaultState {
	/**
	 * 
	 * {@inheritDoc}
	 * @see \quill\states\DefaultState::open()
	 */
	public function open($parser, $name, $attributes){
		$parser->getDocument()->setAttributes($attributes);
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \quill\states\DefaultState::element()
	 */
	public function element($parser, $name, $attributes){
		if($name == 'section') return new Section();
		return null;
	}
}