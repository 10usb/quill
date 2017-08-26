<?php
namespace quill;

abstract class Sax {
	private $buffer;
	private $length;
	private $state;
	
	public function __construct($length = 1024){
		$this->buffer	= '';
		$this->length	= $length;
		$this->state	= 0;
	}
	
	/**
	 * 
	 * @param \quill\streams\File $stream
	 */
	public function parse($stream){
		$this->buffer = $stream->read($this->length);
		do {
			switch($this->state){
				case 0;
					$limit = 100;
					while($limit-- && !preg_match('/^([^<&]*)([<&])/s', $this->buffer, $matches)){
						$read = $stream->read($this->length);
						if($read === false) return strlen($this->buffer);
						$this->buffer.= $read;
					}
					if($limit <= 0) throw new \Exception('Unexpected end of document');
					
					if($matches[1]) $this->text($matches[1]);
					
					if($matches[2] == '&'){
						$this->buffer = substr($this->buffer, strlen($matches[0]));
						$this->state = 1;
					}elseif($matches[2] == '<'){
						$this->buffer = substr($this->buffer, strlen($matches[0]));
						$this->state = 2;
					}else{
						throw new \Exception('Unexpected "'.$matches[2].'" expected a < at "'.substr($this->buffer, 0, 50).'..."');
					}
				break;
				case 1:			
					$limit = 100;
					while($limit-- && !preg_match('/^([^<>&\'"\s;]+);/', $this->buffer, $matches)){
						$read = $stream->read($this->length);
						if($read === false) return strlen($this->buffer);
						$this->buffer.= $read;
					}
					if($limit <= 0) throw new \Exception('Unexpected end of document');
					
					$this->text($this->entity($matches[1]));
					
					$this->buffer = substr($this->buffer, strlen($matches[0]));
					$this->state = 0;
				break;
				case 2:
					$limit = 100;
					while($limit-- && !preg_match('/^(?:(?:\!--(.+?)--)|(?:\!\[CDATA\[(.+?)\]\])|(?:\?([^<>&\'" ]+)(.+?)\?>)|(?:([^<>&\'"\s\/]+)(\s(?:\s*[^<>&\'"\s\/]+(?:="[^"]*")?)*)?(\/?)>)|(?:\/([^<>&\'"\s\/]+)>))/s', $this->buffer, $matches)){
						$read = $stream->read($this->length);
						if($read === false) return strlen($this->buffer);
						$this->buffer.= $read;
					}
					if($limit <= 0) throw new \Exception('Unexpected end of document');
					
					if($matches[1]){
						$this->comments($matches[1]);
					}elseif($matches[2]){
						$this->cdata($matches[2]);
					}elseif($matches[3]){
						$this->pi($matches[3], $matches[4]);
					}elseif($matches[5]){
						$attributes = [];
						if($matches[6]){
							if(preg_match_all('/([^<>&\'"\s\/]+)(?:="([^"]*)"| |$)/s', $matches[6], $submatches, PREG_SET_ORDER)){
								foreach($submatches as $match){
									$attributes[($match[1])] = $match[2] ? $match[2] : null;
								}
							}
						}
						$this->start($matches[5], $attributes);
						if($matches[7]) $this->end($matches[5]);
					}elseif($matches[8]){
						$this->end($matches[8]);
					}else{
						throw new \Exception('Oops');
					}
					
					$this->buffer = substr($this->buffer, strlen($matches[0]));
					$this->state = 0;
				break;
			}
			
			if(strlen($this->buffer) <= 0){
				$read = $stream->read($this->length);
				if($read === false) return 0;
				$this->buffer.= $read;
			}
		}while(strlen($this->buffer) > 0);
	}
	
	public abstract function start($name, $attributes);
	
	public abstract function end($name);
	
	public abstract function text($text);
	
	public function comments($text){}
	
	public function entity($text){
		return $text;
	}
	
	public function cdata($text){
		$this->text($text);
	}
	
	public function pi($target, $data){}
}