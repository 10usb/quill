<?php
namespace quill;

use quill\states\Initial;

class Parser {
	private $parser;
	private $stack;
	private $current;
	private $document;
	
	public function __construct($document){
		$this->document	= $document;
		$this->stack	= [];
		$this->current	= new \stdClass();
		$this->current->name	= false;
		$this->current->state	= new Initial();
		
	}
	
	public function parse($data){
		$parser = xml_parser_create('UTF-8');
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
		
		$buffer = false;
		xml_set_element_handler($parser, function($parser, $name, $attributes) use (&$buffer) {
			if($buffer !== false){
				$this->current->state->text($this, $buffer);
				$buffer = false;
			}
			
			$next = new \stdClass();
			$next->name		= $name;
			$next->state	= $this->current->state->element($this, $name, $attributes);
			if(!$next->state) throw new \Exception('Unexpected element "'.$name.'"');
			
			$this->stack[] = $this->current;
			$this->current = $next;
			
			$this->current->state->open($this, $name, $attributes);
		}, function($parser, $name) use (&$buffer) {
			if($this->current->name === false) throw new \Exception('Unexpected close tag');
			if($this->current->name != $name) throw new \Exception('Unexpected close tag "'.$name.'" expected "'.$this->current->name.'"');
			
			if($buffer !== false){
				$this->current->state->text($this, $buffer);
				$buffer = false;
			}
			
			$this->current->state->close($this, $this->current->name);
			$this->current = array_pop($this->stack);
		});
		
		xml_set_character_data_handler($parser, function($parser, $data) use (&$buffer) {
			if($buffer===false){
				$buffer = $data;
			}else{
				$buffer.= $data;
			}
		});
		
		xml_set_default_handler($parser, function($parser, $data) use (&$buffer) {
			if(preg_match('/^\&[a-z0-9]+\;$/i', $data)){
				$value = html_entity_decode($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
				if($value!=$data){
					if($buffer===false){
						$buffer = $value;
					}else{
						$buffer.= $value;
					}
				}else{
					throw new \Exception('Failed to decode entity');
				}
			}elseif(preg_match('/^\<\!\-\-(.+)\-\-\>/is', $data, $matches)){
				if($buffer !== false){
					$this->current->state->text($this, $buffer);
					$buffer = false;
				}
				$this->current->state->comment($this, $matches[1]);
			}else{
				throw new \Exception('Unsupported format');
			}
		});
		
		xml_parse($parser, $data);
		return xml_parser_free($parser);
	}
	
	/**
	 * 
	 * @return \quill\Document
	 */
	public function getDocument(){
		return $this->document;
	}
}