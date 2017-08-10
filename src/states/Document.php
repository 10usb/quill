<?php
namespace quill\states;

class Document extends DefaultState {
	/**
	 * 
	 * {@inheritDoc}
	 * @see \quill\states\DefaultState::element()
	 */
	public function element($parser, $name, $attributes){
		switch($name){
			case 'info': return new Info();
			case 'style': return new Style();
			case 'contents': return new Contents();
		}
		return false;
	}
}