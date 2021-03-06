<?php
namespace quill;

use csslib\query\Path;
use alf\Section;
use quill\bottles\Basic;
use quill\bottles\Image;
use quill\bottles\Block;

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
	 * @var Ink[]
	 */
	private $bottles;
	
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
		$this->bottles		= [
			'default'	=> new Basic(),
			'block'		=> new Block(),
			'image'		=> new Image()
		];
	}
	
	/**
	 * 
	 * @param string $name
	 * @return \quill\Ink
	 */
	public function getBottle($name){
		return $this->bottles[$name];
	}
	
	/**
	 * 
	 * @param string $name
	 * @param \quill\Ink $bottle
	 * @return \quill\Pen
	 */
	public function setBottle($name, $bottle){
		$this->bottles[$name];
		return $this;
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
		
		if(!$this->book->hasSize()) $this->book->setSize($width, $height);
		

		while($slice = $body->slice($height)){
			$canvas = $this->book->addPage($width, $height);
			//$slice->setPreferredHeight($height);
			$slice->render($canvas);
			// TODO reader header & footer
		}
		
		$canvas = $this->book->addPage($width, $height);
		//$body->setPreferredHeight($height);
		$body->render($canvas);
		// TODO reader header & footer
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
		
		$type = $this->path->getValue('-quill-type')->getText();
		if(!isset($this->bottles[$type])) throw new \Exception('Unknow Quill type `'.$type.'`');
		$inkt = $this->bottles[$type];
		
		if($inkt->isInline($this->path)){
			$this->flush($parent);
			if($container = $inkt->processs($this, $parent, $element, $this->path)){
				$this->children($container, $element, false);
				if($container->getContentWidth(false) === false){
					$container->pack(min($container->getCalulatedWidth(), $parent->getContentWidth()));
				}
			}
		}else{
			$this->trailing = true;
			$this->flush($parent);
			if($container = $inkt->processs($this, $parent, $element, $this->path)){
				$this->children($container, $element, true);
			}
			$this->preceding = true;
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
				if($this->buffer!==false) throw new \Exception('Unexpected text "'.$this->buffer->text.'"');
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