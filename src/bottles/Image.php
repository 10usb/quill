<?php
namespace quill\bottles;

use quill\Ink;

class Image implements Ink {
	/**
	 * 
	 * {@inheritDoc}
	 * @see \quill\Ink::isInline()
	 */
	public function isInline($path){
		return $path->getValue('display') != 'block';
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \quill\Ink::processs()
	 */
	public function processs($pen, $parent, $element, $path){
		$source = $element->getAttribute('src');
		$style = [];
		
		if($value = $path->getValue('margin-top')) $style['margin-top'] = $value->getMeasurement('pt');
		if($value = $path->getValue('margin-right')) $style['margin-right'] = $value->getMeasurement('pt');
		if($value = $path->getValue('margin-bottom')) $style['margin-bottom'] = $value->getMeasurement('pt');
		if($value = $path->getValue('margin-left')) $style['margin-left'] = $value->getMeasurement('pt');
		
		if($value = $path->getValue('width')){
			$style['width']	= $value->getMeasurement('pt');
		}else{
			$style['width']	= $element->getAttribute('width');
		}
		
		if($value = $path->getValue('width')){
			$style['height']	= $value->getMeasurement('pt');
		}else{
			$style['height']	= $element->getAttribute('height');
		}
		
		if($this->isInline($path)){
			$parent->appendInline(new \alf\Image($source, $style));
		}else{
			$parent->appendBlock(new \alf\Image($source, $style));
		}
		return false;
		
	}
}