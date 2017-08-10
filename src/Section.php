<?php
namespace quill;

class Section {
	/**
	 *
	 */
	use Attributes;
	
	/**
	 * 
	 * @var \quill\Document
	 */
	private $document;
	
	/**
	 *
	 * @var \quill\Element
	 */
	private $header;
	
	/**
	 * 
	 * @var \quill\Element
	 */
	private $body;
	
	/**
	 *
	 * @var \quill\Element
	 */
	private $footer;
	
	/**
	 * 
	 * @param \quill\Document $document
	 */
	public function __construct($document){
		self::initAttributes();
		$this->document	= $document;
		$this->header	= new RootElement('header');
		$this->body		= new RootElement('body');
		$this->footer	= new RootElement('footer');
	}
	
	/**
	 * 
	 * @return \quill\Document
	 */
	public function getDocument(){
		return $this->document;
	}
	
	/**
	 * 
	 * @return \quill\Element
	 */
	public function getHeader(){
		return $this->header;
	}
	
	/**
	 * 
	 * @return \quill\Element
	 */
	public function getBody(){
		return $this->body;
	}
	
	/**
	 * 
	 * @return \quill\Element
	 */
	public function getFooter(){
		return $this->footer;
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
		$writer->startElement('section');
		foreach($this->attributes as $name=>$value){
			$writer->writeAttribute($name, $value);
		}
		
		$this->header->save($writer);
		$this->body->save($writer);
		$this->footer->save($writer);
		
		$writer->fullEndElement();
		$writer->flush();
	}
}