<?php
namespace quill;

use csslib\values\Color;
use csslib\values\Measurement;
use csslib\values\Name;

class Translator implements \csslib\query\Translator {
	/**
	 * 
	 * {@inheritDoc}
	 * @see \csslib\query\Translator::getValue()
	 */
	public function getValue($chain, $document, $key){
		$value = null;
		
		switch($key){
			case 'font-family':
			case 'font-size':
			case 'font-style':
			case 'font-weight':
			case 'font-color':
			case 'width':
			case 'height':
			case 'line-height':
				if($property = $chain->getProperty($key)){
					$value = $property->getValueList(0)->getValue(0);
					if($value=='inherit') $value = $this->getValue($chain->getParent(), $document, $key);
				}
			break;
			case '-quill-type':
			case 'display':
				if($property = $chain->getProperty($key)){
					return $property->getValueList(0)->getValue(0);
				}
			break;
			
			case 'page-margin-top': $this->getBoxValue($chain, $value, $key, 'page-margin', 0, 0); break;
			case 'page-margin-right': $this->getBoxValue($chain, $value, $key, 'page-margin', 1, 1); break;
			case 'page-margin-bottom': $this->getBoxValue($chain, $value, $key, 'page-margin', 2, 0); break;
			case 'page-margin-left': $this->getBoxValue($chain, $value, $key, 'page-margin', 3, 1); break;
			
			case 'margin-top': $this->getBoxValue($chain, $value, $key, 'margin', 0, 0); break;
			case 'margin-right': $this->getBoxValue($chain, $value, $key, 'margin', 1, 1); break;
			case 'margin-bottom': $this->getBoxValue($chain, $value, $key, 'margin', 2, 0); break;
			case 'margin-left': $this->getBoxValue($chain, $value, $key, 'margin', 3, 1); break;
			
			case 'padding-top': $this->getBoxValue($chain, $value, $key, 'padding', 0, 0); break;
			case 'padding-right': $this->getBoxValue($chain, $value, $key, 'padding', 1, 1); break;
			case 'padding-bottom': $this->getBoxValue($chain, $value, $key, 'padding', 2, 0); break;
			case 'padding-left': $this->getBoxValue($chain, $value, $key, 'padding', 3, 1); break;
			
			
			case 'background-color':
				if($property = $chain->getProperty(['background', $key])){
					if($property->getName() == $key){
						$value = $property->getValueList(0)->getValue(0);
						if($value=='inherit') $value = $this->getValue($chain->getParent(), $document, $key);
					}else{
						$list = $property->getValueList(0);
						for($index = 0; $index < $list->getCount(); $index++){
							if($list->getValue($index) instanceof Color){
								$value = $list->getValue($index);
								break;
							}
						}
					}
				}
			break;
			case 'border-top-width':
			case 'border-right-width':
			case 'border-bottom-width':
			case 'border-left-width':
				if($property = $chain->getProperty(['border', 'border-width', substr($key, 0, -6), $key])){
					if($property->getName() == $key){
						$value = $property->getValueList(0)->getValue(0);
						if($value=='inherit') $value = $this->getValue($chain->getParent(), $document, $key);
					}else{
						$list = $property->getValueList(0);
						for($index = 0; $index < $list->getCount(); $index++){
							if($list->getValue($index) instanceof Measurement){
								$value = $list->getValue($index);
								break;
							}
						}
					}
				}
			break;
			case 'border-top-style':
			case 'border-right-style':
			case 'border-bottom-style':
			case 'border-left-style':
				if($property = $chain->getProperty(['border', 'border-style', substr($key, 0, -6), $key])){
					if($property->getName() == $key){
						$value = $property->getValueList(0)->getValue(0);
						if($value=='inherit') $value = $this->getValue($chain->getParent(), $document, $key);
					}else{
						$list = $property->getValueList(0);
						for($index = 0; $index < $list->getCount(); $index++){
							if($list->getValue($index) instanceof Name){
								$value = $list->getValue($index);
								break;
							}
						}
					}
				}
			break;
			case 'border-top-color':
			case 'border-right-color':
			case 'border-bottom-color':
			case 'border-left-color':
				if($property = $chain->getProperty(['border', 'border-color', substr($key, 0, -6), $key])){
					if($property->getName() == $key){
						$value = $property->getValueList(0)->getValue(0);
						if($value=='inherit') $value = $this->getValue($chain->getParent(), $document, $key);
					}else{
						$list = $property->getValueList(0);
						for($index = 0; $index < $list->getCount(); $index++){
							if($list->getValue($index) instanceof Color){
								$value = $list->getValue($index);
								break;
							}
						}
					}
				}
			break;
			default: throw new \Exception("Unknow property '$key'");
		}
		
		return $value;
	}
	
	private function getBoxValue($chain, &$value, $key, $group, $index4, $index2){
		$property = $chain->getProperty([$group, $key], $match);
		if($property){
			if($match == $key){
				$value = $property->getValueList(0)->getValue(0);
			}elseif($property->getValueList(0)->getCount()==4){
				$value = $property->getValueList(0)->getValue($index4);
			}elseif($property->getValueList(0)->getCount()==2){
				$value = $property->getValueList(0)->getValue($index2);
			}elseif($property->getValueList(0)->getCount()==1){
				$value = $property->getValueList(0)->getValue(0);
			}
			if($value=='inherit') $value = $this->getValue($chain->getParent(), $document, $key);
		}
	}
}