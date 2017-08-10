<?php
namespace quill;

class RootElement extends Element {
	/**
	 * 
	 * {@inheritDoc}
	 * @see \quill\Node::isFixed()
	 */
	public function isFixed(){
		return true;
	}
}