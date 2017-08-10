<?php
namespace quill\states;

use quill\State;

class Debug implements State {
	/**
	 * 
	 * {@inheritDoc}
	 * @see \quill\State::open()
	 */
	public function open($parser, $name, $attributes){
		printf("open: %s => %s\n", $name, json_encode($attributes));
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \quill\State::close()
	 */
	public function close($parser, $name){
		printf("close: %s\n", $name);
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \quill\State::element()
	 */
	public function element($parser, $name, $attributes){
		printf("element: %s => %s\n", $name, json_encode($attributes));
		return $this;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \quill\State::text()
	 */
	public function text($parser, $value){
		printf("text: %s\n", $value);
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \quill\State::comment()
	 */
	public function comment($parser, $value){
		printf("comment: %s\n", $value);
	}
}