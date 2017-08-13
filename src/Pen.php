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
				$style = [];
				if($value = $this->path->getValue('margin-left')) $style['margin-left'] = $value->getMeasurement('pt');
				if($value = $this->path->getValue('margin-right')) $style['margin-right'] = $value->getMeasurement('pt');
				if($value = $this->path->getValue('margin-top')) $style['margin-top'] = $value->getMeasurement('pt');
				if($value = $this->path->getValue('margin-bottom')) $style['margin-bottom'] = $value->getMeasurement('pt');
				
				if($value = $this->path->getValue('padding-left')) $style['padding-left'] = $value->getMeasurement('pt');
				if($value = $this->path->getValue('padding-right')) $style['padding-right'] = $value->getMeasurement('pt');
				if($value = $this->path->getValue('padding-top')) $style['padding-top'] = $value->getMeasurement('pt');
				if($value = $this->path->getValue('padding-bottom')) $style['padding-bottom'] = $value->getMeasurement('pt');
				
				
				if($value = $this->path->getValue('background-color')) $style['background-color'] = $value;

				$this->children($parent->appendBlock(new Section($parent->getContentWidth(), $style)), $element, true);
				$this->preceding = true;
			break;
			case 'inline':
				$this->flush($parent);
				$this->children($parent, $element, false);
			break;
			default: throw new \Exception('Unknown display type "'.$this->path->getValue('display').'"');
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
				$this->buffer = new \stdClass();
				$this->buffer->text		= $child->getValue();
				$this->buffer->style	= $this->path->getState();
			}else{
				throw new \Exception('Unknown node type');
			}
		}
		
		if($block) $this->trailing = true;
		$this->flush($parent);
	}
	
	/**
	 * 
	 * @param \alf\Container $parent
	 * @return NULL|boolean
	 */
	private function flush($parent){
		if($this->buffer === false) return null;
		
		$text = $this->preceding ? ($this->trailing ? trim($this->buffer->text) : ltrim($this->buffer->text)) : ($this->trailing ? rtrim($this->buffer->text) : $this->buffer->text);
		$style =  $this->buffer->style;
		
		$this->buffer		= false;
		$this->preceding	= false;
		$this->trailing		= false;
		
		if($text){
			$name	= $style->getValue('font-family')->getText();
			$size	= $style->getValue('font-size')->getMeasurement('pt');
			$italic	= $style->getValue('font-style')->getText() == 'italic';
			$bold	= $style->getValue('font-weight')->getText() == 'bold';
			
			$font	= $this->repository->getFont($name, $size, $italic, $bold);
			$color	= $style->getValue('font-color');
			
			
			if($value = $style->getValue('line-height')){
				$lineHeight = $value->getMeasurement('pt');
			}else{
				$lineHeight = false;
			}
			
			$parent->appendText($text, $font, $color, $lineHeight);
			return true;
		}
		return false;
	}
}