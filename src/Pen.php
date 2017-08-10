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
	 * @param \quill\Repository $repository
	 * @param \quill\Book $book
	 */
	public function __construct($repository, $book){
		$this->repository	= $repository;
		$this->book			= $book;
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
		
		$this->element($body, $section->getBody());
		
		
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
				$this->children($parent->appendBlock(new Section($parent->getContentWidth())), $element);
			break;
			case 'inline':
				$this->children($parent, $element);
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
	private function children($parent, $element){
		foreach($element->getChildren() as $child){
			if($child instanceof Element){
				$this->element($parent, $child);
			}elseif($child instanceof Text){
				$font = $this->repository->getFont('Helvetica', 12);
				$color = $this->path->getValue('color');
				$parent->appendText($child->getValue(), $font, sprintf('#%02X%02X%02X', $color->getRed(), $color->getGreen(), $color->getBlue()));
			}else{
				throw new \Exception('Unknown node type');
			}
		}
	}
}