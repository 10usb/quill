<?php
namespace quill\bottles;

use quill\Ink;

class Basic implements Ink {
	/**
	 * 
	 * {@inheritDoc}
	 * @see \quill\Ink::isInline()
	 */
	public function isInline($path){
		return !in_array($path->getValue('display')->getText(), ['block', 'table']);
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \quill\Ink::processs()
	 */
	public function processs($pen, $parent, $element, $path){
		switch($path->getValue('display')){
			case 'block': return $pen->getBottle('block')->processs($pen, $parent, $element, $path);
			case 'inline': return $parent;
			case 'inline-block': return $pen->getBottle('block')->processs($pen, $parent, $element, $path);
			default: throw new \Exception('Unknown display type "'.$path->getValue('display').'"');
		}
	}
}