<?php
namespace quill\states;

use quill\State;

abstract class DefaultState implements State {
	/**
	 *
	 * {@inheritDoc}
	 * @see \quill\State::open()
	 */
	public function open($parser, $name, $attributes){
	}
	
	/**
	 *
	 * {@inheritDoc}
	 * @see \quill\State::close()
	 */
	public function close($parser, $name){
	}
	
	/**
	 *
	 * {@inheritDoc}
	 * @see \quill\State::element()
	 */
	public function element($parser, $name, $attributes){
		return $this;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 * @see \quill\State::text()
	 */
	public function text($parser, $value){
	}
	
	/**
	 *
	 * {@inheritDoc}
	 * @see \quill\State::comment()
	 */
	public function comment($parser, $value){
	}
}