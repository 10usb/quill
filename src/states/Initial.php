<?php
namespace quill\states;

class Initial extends DefaultState {
	/**
	 * 
	 * {@inheritDoc}
	 * @see \quill\states\DefaultState::element()
	 */
	public function element($parser, $name, $attributes){
		if($name == 'document') return new Document();
		return false;
	}
}