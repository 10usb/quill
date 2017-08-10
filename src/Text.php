<?php
namespace quill;

class Text extends Node {
	/**
	 * 
	 * @var string
	 */
	private $value;
	
	/**
	 * 
	 * @param string $value
	 */
	public function __construct($value){
		parent::__construct();
		$this->value = $value;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getValue(){
		return $this->value;
	}
	
	/**
	 *
	 * @param string $filename
	 */
	public function save($uri = 'php://output'){
		if($uri instanceof \XMLWriter){
			$writer = $uri;
		}else{
			$writer = new \XMLWriter();
			$writer->openUri($uri);
		}
		$writer->text($this->value);
	}
}