<?php
namespace quill\states;

class Info extends DefaultState {
	/**
	 * 
	 * {@inheritDoc}
	 * @see \quill\states\DefaultState::element()
	 */
	public function element($parser, $name, $attributes){
		return new Property();
	}
}