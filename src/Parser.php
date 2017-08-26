<?php
namespace quill;

use quill\states\Initial;

class Parser {
	private $parser;
	private $document;
	
	public function __construct($document){
		$this->document	= $document;
		
		$this->parser = new class($this) extends Sax {
			private $parser;
			private $stack;
			private $current;
			
			public function __construct($parser){
				parent::__construct();
				$this->parser	= $parser;
				$this->stack	= [];
				$this->current	= new \stdClass();
				$this->current->name	= false;
				$this->current->state	= new Initial();
			}
			
			public function start($name, $attributes){
				if($this->buffer !== false){
					$this->current->state->text($this->parser, $this->buffer);
					$this->buffer = false;
				}
				
				$next = new \stdClass();
				$next->name		= $name;
				$next->state	= $this->current->state->element($this->parser, $name, $attributes);
				if(!$next->state) throw new \Exception('Unexpected element "'.$name.'"');
				
				$this->stack[] = $this->current;
				$this->current = $next;
				
				$this->current->state->open($this->parser, $name, $attributes);
			}
			
			public function end($name){
				if($this->current->name === false) throw new \Exception('Unexpected close tag');
				if($this->current->name != $name) throw new \Exception('Unexpected close tag "'.$name.'" expected "'.$this->current->name.'"');
				
				if($this->buffer !== false){
					$this->current->state->text($this->parser, $this->buffer);
					$this->buffer = false;
				}
				
				$this->current->state->close($this->parser, $this->current->name);
				$this->current = array_pop($this->stack);
			}
			
			public function text($text){
				if($this->buffer===false){
					$this->buffer = $text;
				}else{
					$this->buffer.= $text;
				}
			}
			
			public function comments($text){
				if($this->buffer !== false){
					$this->current->state->text($this->parser, $buffer);
					$this->buffer = false;
				}
				$this->current->state->comment($this->parser, $text);
			}
			
			public function entity($text){
				$value = html_entity_decode("&$text;", ENT_QUOTES | ENT_HTML5, 'ISO-8859-1');
				if($value != $data){
					if($this->buffer === false){
						$this->buffer = $value;
					}else{
						$this->buffer.= $value;
					}
				}else{
					throw new \Exception('Failed to decode entity');
				}
			}
		};
	}
	
	public function parse($stream){
		$this->parser->parse($stream);
	}
	
	/**
	 * 
	 * @return \quill\Document
	 */
	public function getDocument(){
		return $this->document;
	}
}