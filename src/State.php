<?php
namespace quill;

interface State {
	/**
	 * 
	 * @param \quill\Parser $parser
	 */
	public function open($parser, $name, $attributes);
	
	/**
	 * 
	 * @param \quill\Parser $parser
	 */
	public function close($parser, $name);
	
	/**
	 * 
	 * @param \quill\Parser $parser
	 * @param string $tagName
	 * @param array $attributes
	 * @return \quill\State
	 */
	public function element($parser, $name, $attributes);
	
	/**
	 * 
	 * @param \quill\Parser $parser
	 * @param string $value
	 */
	public function text($parser, $value);
	
	/**
	 * 
	 * @param \quill\Parser $parser
	 * @param string $vale
	 */
	public function comment($parser, $value);
}