<?php
namespace quill;

use csslib\formatters\Pretty;

class Document {
	/**
	 *
	 */
	use Attributes;
	
	/**
	 * 
	 * @var \quill\Info
	 */
	private $info;
	
	/**
	 * 
	 * @var \csslib\Document
	 */
	private $stylesheet;
	
	/**
	 * 
	 * @var Section[]
	 */
	private $sections;
	
	public function __construct(){
		self::initAttributes();
		$this->info			= new Info();
		$this->stylesheet	= new \csslib\Document();
		$this->sections		= [];
	}
	
	/**
	 * 
	 * @return \quill\Info
	 */
	public function getInfo(){
		return $this->info;
	}
	
	/**
	 * 
	 * @return \csslib\Document
	 */
	public function getStylesheet(){
		return $this->stylesheet;
	}
	
	/**
	 * 
	 * @return \quill\Section
	 */
	public function addSection(){
		return $this->sections[] = new Section($this);
	}
	
	/**
	 * 
	 * @return \quill\Section[]
	 */
	public function getSections(){
		return $this->sections;
	}
	
	/**
	 * 
	 * @param string $filename
	 */
	public function save($uri = 'php://output'){
		$writer = new \XMLWriter();
		$writer->openUri($uri);
		//$writer->setIndentString("\t");
		$writer->setIndent(true);
		
		$writer->startDocument('1.0', 'UTF-8');
		$writer->writeRaw('<!DOCTYPE document SYSTEM "pdf.dtd">'."\n");
		$writer->startElement('document');
		$this->info->save($writer);
		
		foreach($this->stylesheet->getSegments() as $index=>$segment){
			if($segment->getName()!='user-agent'){
				$formatter = new Pretty();
				$writer->startElement('style');
				$writer->writeComment("\n".trim($formatter->format($segment))."\n");
				$writer->endElement();
			}
		}
		
		$writer->startElement('contents');
		foreach($this->attributes as $name=>$value){
			$writer->writeAttribute($name, $value);
		}
		
		foreach($this->sections as $section) $section->save($writer);
		
		$writer->fullEndElement();
		$writer->endElement();
		$writer->flush();
	}
}