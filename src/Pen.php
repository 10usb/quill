<?php
namespace quill;

use csslib\query\Path;
use alf\Section;

class Pen {
	/**
	 * 
	 * @var \quill\Repository
	 */
	private $repository;
	
	/**
	 * 
	 * @var \quill\Book
	 */
	private $book;
	
	/**
	 * 
	 * @var \csslib\query\Path
	 */
	private $path;
	
	/**
	 * 
	 * @var string
	 */
	private $buffer;
	
	/**
	 *
	 * @var boolean
	 */
	private $preceding;
	
	/**
	 *
	 * @var boolean
	 */
	private $trailing;
	
	/**
	 * 
	 * @param \quill\Repository $repository
	 * @param \quill\Book $book
	 */
	public function __construct($repository, $book){
		$this->repository	= $repository;
		$this->book			= $book;
		$this->buffer		= false;
		$this->preceding	= false;
		$this->trailing		= false;
	}
	
	/**
	 * 
	 * @param \quill\Section $section
	 */
	public function write($section){
		$this->path = new Path($section->getDocument()->getStylesheet(), new Translator());
		
		$selector = $this->path->push()->setTagName('section');
		foreach(array_merge($section->getDocument()->getAttributes(), $section->getAttributes()) as $key=>$value){
			if($key=='class'){
				$selector->addClass($value);
			}elseif($key=='id'){
				$selector->setIdentification($value);
			}else{
				$selector->addAttribute($key, $value);
			}
		}
		
		$body = new Section($width = $this->path->getValue('width')->getMeasurement('pt'), [
			'padding-left' => $this->path->getValue('page-margin-left')->getMeasurement('pt'),
			'padding-top' => $this->path->getValue('page-margin-top')->getMeasurement('pt'),
			'padding-right' => $this->path->getValue('page-margin-right')->getMeasurement('pt'),
			'padding-bottom' => $this->path->getValue('page-margin-bottom')->getMeasurement('pt')
		], Section::BORDER_BOX);
		
		$height = $this->path->getValue('height')->getMeasurement('pt');
		
		$this->element($body, $section->getBody(), true);
		
		
		if(isset($_GET['debug'])){
			$body->render(new \pdfcreator\DebugCanvas());
			return;
		}
		
		if(!$this->book->hasSize()) $this->book->setSize($width, $height);
		/*
		 // Slice the body into pages
		 $pageHeight = $catalog->getHeight();
		 while($slice = $body->slice($pageHeight)){
		 $page = $file->getCatalog()->addPage();
		 $slice->render(new PDFCanvas($page->getCanvas()));
		 }
		 $page = $catalog->addPage();
		 $slice->render(new PDFCanvas($page->getCanvas()));
		 */
		$canvas = $this->book->addPage($width, $height);
		$body->render($canvas);
	}
	
	/**
	 * 
	 * @param \alf\Container $parent
	 * @param \quill\Element $element
	 */
	private function element($parent, $element){
		$selector = $this->path->push()->setTagName($element->getTagName());
		foreach($element->getAttributes() as $key=>$value){
			if($key=='class'){
				$selector->addClass($value);
			}elseif($key=='id'){
				$selector->setIdentification($value);
			}else{
				$selector->addAttribute($key, $value);
			}
		}
		
		switch($this->path->getValue('display')){
			case 'block':
				$this->trailing = true;
				$this->flush($parent);
				$this->children($parent->appendBlock(new Section($parent->getContentWidth())), $element, true);
				$this->preceding = true;
			break;
			case 'inline':
				$this->flush($parent);
				$this->children($parent, $element, false);
			break;
			default: throw new \Exception('Unknown display type');
		}
		
		$this->path->pop();
	}
	
	/**
	 * 
	 * @param \alf\Container $parent
	 * @param \quill\Element $element
	 */
	private function children($parent, $element, $block){
		if($block) $this->preceding = true;
		
		foreach($element->getChildren() as $child){
			if($child instanceof Element){
				$this->element($parent, $child);
			}elseif($child instanceof Text){
				if($this->buffer!==false) throw new \Exception('Unexpected text "'.$this->buffer.'"');
				$this->buffer		= $child->getValue();
				$this->bufferFont	= $this->repository->getFont('Helvetica', 12);
				$this->bufferColor	= $this->path->getValue('color');
			}else{
				throw new \Exception('Unknown node type');
			}
		}
		
		if($block) $this->trailing = true;
		$this->flush($parent);
	}
	
	/**
	 * 
	 * @return NULL|boolean
	 */
	private function flush($parent){
		if($this->buffer === false) return null;
		
		$text = $this->preceding ? ($this->trailing ? trim($this->buffer) : ltrim($this->buffer)) : ($this->trailing ? rtrim($this->buffer) : $this->buffer);
		
		$this->buffer		= false;
		$this->preceding	= false;
		$this->trailing		= false;
		
		if($text){
			$parent->appendText($text, $this->bufferFont, sprintf('#%02X%02X%02X', $this->bufferColor->getRed(), $this->bufferColor->getGreen(), $this->bufferColor->getBlue()));
			return true;
		}
		return false;
	}
}