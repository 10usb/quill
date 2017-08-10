<?php
namespace quill;

class Info {
	/**
	 * 
	 * @var array
	 */
	private $data;
	
	/**
	 * 
	 */
	public function __construct(){
		$this->data	= [];
	}
	
	public function get($name){
		return $this->data[$name];
	}
	
	public function set($name, $value){
		$this->data[$name] = $value;
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
			$writer->setIndentString("\t");
			$writer->setIndent(true);
		}
		$writer->startElement('info');
		
		foreach($this->data as $key=>$value){
			$writer->writeElement($key, $value);
		}
		
		$writer->endElement();
		$writer->flush();
	}
}