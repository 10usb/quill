<?php
namespace quill;

interface Ink {
	/**
	 * 
	 * @param \csslib\query\Path $path
	 * @return boolean
	 */
	public function isInline($path);
	
	/**
	 * 
	 * @param \quill\Pen $pen
	 * @param \alf\Container $parent
	 * @param \quill\Element $element
	 * @param \csslib\query\Path $path
	 * @return \alf\Container
	 */
	public function processs($pen, $parent, $element, $path);
}