<?php
namespace quill\bottles;

use quill\Ink;
use alf\Section;

class Block implements Ink {
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
		$style = [];
		if($value = $path->getValue('margin-top')) $style['margin-top'] = $value->getMeasurement('pt');
		if($value = $path->getValue('margin-right')) $style['margin-right'] = $value->getMeasurement('pt');
		if($value = $path->getValue('margin-bottom')) $style['margin-bottom'] = $value->getMeasurement('pt');
		if($value = $path->getValue('margin-left')) $style['margin-left'] = $value->getMeasurement('pt');
		
		if($value = $path->getValue('padding-top')) $style['padding-top'] = $value->getMeasurement('pt');
		if($value = $path->getValue('padding-right')) $style['padding-right'] = $value->getMeasurement('pt');
		if($value = $path->getValue('padding-bottom')) $style['padding-bottom'] = $value->getMeasurement('pt');
		if($value = $path->getValue('padding-left')) $style['padding-left'] = $value->getMeasurement('pt');
		
		
		if($value = $path->getValue('background-color')) $style['background-color'] = $value;
		
		if($value = $path->getValue('border-top-width')) $style['border-top-width'] = $value->getMeasurement('pt');
		if($value = $path->getValue('border-top-style')) $style['border-top-style'] = $value;
		if($value = $path->getValue('border-top-color')) $style['border-top-color'] = $value;
		
		if($value = $path->getValue('border-right-width')) $style['border-right-width'] = $value->getMeasurement('pt');
		if($value = $path->getValue('border-right-style')) $style['border-right-style'] = $value;
		if($value = $path->getValue('border-right-color')) $style['border-right-color'] = $value;
		
		if($value = $path->getValue('border-bottom-width')) $style['border-bottom-width'] = $value->getMeasurement('pt');
		if($value = $path->getValue('border-bottom-style')) $style['border-bottom-style'] = $value;
		if($value = $path->getValue('border-bottom-color')) $style['border-bottom-color'] = $value;
		
		if($value = $path->getValue('border-left-width')) $style['border-left-width'] = $value->getMeasurement('pt');
		if($value = $path->getValue('border-left-style')) $style['border-left-style'] = $value;
		if($value = $path->getValue('border-left-color')) $style['border-left-color'] = $value;
		
		
		if($this->isInline($path)){
			return $parent->appendInline(new Section(false, $style));
		}
		return $parent->appendBlock(new Section($parent->getContentWidth(false), $style));
	}
}