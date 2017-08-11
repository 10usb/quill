<?php
namespace quill;

class Translator implements \csslib\query\Translator {
	/**
	 * 
	 * {@inheritDoc}
	 * @see \csslib\query\Translator::getValue()
	 */
	public function getValue($chain, $document, $key){
		$property = $chain->getProperty($key);
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
				if($property){
					$value = $property->getValueList(0)->getValue(0);
					if($value=='inherit') $value = $this->getValue($chain->getParent(), $document, $key);
				}
			break;
			case 'display':
				if($property){
					return $property->getValueList(0)->getValue(0);
				}
			break;
			case 'page-margin-top':
				$property = $chain->getProperty('page-margin');
				if($property){
					if($property->getValueList(0)->getCount()==4){
						$value = $property->getValueList(0)->getValue(0);
					}elseif($property->getValueList(0)->getCount()==2){
						$value = $property->getValueList(0)->getValue(0);
					}elseif($property->getValueList(0)->getCount()==1){
						$value = $property->getValueList(0)->getValue(0);
					}
					if($value=='inherit') $value = $this->getValue($chain->getParent(), $document, $key);
				}
			break;
			case 'page-margin-right':
				$property = $chain->getProperty('page-margin');
				if($property){
					if($property->getValueList(0)->getCount()==4){
						$value = $property->getValueList(0)->getValue(1);
					}elseif($property->getValueList(0)->getCount()==2){
						$value = $property->getValueList(0)->getValue(1);
					}elseif($property->getValueList(0)->getCount()==1){
						$value = $property->getValueList(0)->getValue(0);
					}
					if($value=='inherit') $value = $this->getValue($chain->getParent(), $document, $key);
				}
			break;
			case 'page-margin-bottom':
				$property = $chain->getProperty('page-margin');
				if($property){
					if($property->getValueList(0)->getCount()==4){
						$value = $property->getValueList(0)->getValue(2);
					}elseif($property->getValueList(0)->getCount()==2){
						$value = $property->getValueList(0)->getValue(0);
					}elseif($property->getValueList(0)->getCount()==1){
						$value = $property->getValueList(0)->getValue(0);
					}
					if($value=='inherit') $value = $this->getValue($chain->getParent(), $document, $key);
				}
			break;
			case 'page-margin-left':
				$property = $chain->getProperty('page-margin');
				if($property){
					if($property->getValueList(0)->getCount()==4){
						$value = $property->getValueList(0)->getValue(3);
					}elseif($property->getValueList(0)->getCount()==2){
						$value = $property->getValueList(0)->getValue(1);
					}elseif($property->getValueList(0)->getCount()==1){
						$value = $property->getValueList(0)->getValue(0);
					}
					if($value=='inherit') $value = $this->getValue($chain->getParent(), $document, $key);
				}
			break;
			default: throw new \Exception("Unknow property '$key'");
		}
		
		return $value;
	}
}