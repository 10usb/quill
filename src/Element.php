<?php
namespace quill;

class Element extends Node {
	/**
	 * 
	 */
	use Attributes;
	
	/**
	 * 
	 * @var string
	 */
	private $tagName;
	
	/**
	 * 
	 * @var \quill\Node[]
	 */
	private $children;
	
	/**
	 * 
	 * @param string $tagName
	 */
	public function __construct($tagName){
		parent::__construct();
		self::initAttributes();
		
		if(preg_match('/\s+|[\<\>\=]+/', $tagName)) throw new \Exception('Invalid tag name');
		
		$this->tagName		= $tagName;
		$this->children		= [];
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getTagName(){
		return $this->tagName;
	}
	
	/**
	 * 
	 * @return \quill\Node[]
	 */
	public function getChildren(){
		return $this->children;
	}
	
	/**
	 * 
	 * @param string $tagName
	 * @return \quill\Element[]
	 */
	public function getChildrenByTagName($tagName){
		$children = [];
		foreach($this->children as $child){
			if($child instanceof Element && $child->getTagName() == $tagName){
				$children[] = $child;
			}
		}
		return $children;
	}
	
	/**
	 * 
	 * @param \quill\Node $child
	 */
	public function append($child){
		if(!$child instanceof Node) throw new \Exception('Unexpected value expected a Node');
		if($child->isFixed()) throw new \Exception('Node is fixed');
		if($child->parent!==null) $child->parent->remove($child);
		
		$child->parent	= $this;
		$this->children[] = $child;
	}
	
	/**
	 * 
	 * @param \quill\Node $child
	 */
	public function remove($child){
		if(!$child instanceof Node) throw new \Exception('Unexpected value expected a Node');
		if(($index = array_search($this->children, $child)) === false) throw new \Exception('Not a child of mine!');
		unset($this->children[$index]);
		$this->children = array_values($this->children);
	}
	
	/**
	 * 
	 * @param string $tagName
	 * @param string $text
	 * @return \quill\Element
	 */
	public function write($tagName, $text = ''){
		$this->append($node= new Element($tagName));
		if($text) $node->text($text);
		return $node;
	}
	
	/**
	 * 
	 * @param string $value
	 * @return \quill\Text
	 */
	public function text($value){
		$this->append($node = new Text($value));
		return $node;
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
		$writer->startElement($this->tagName);
		foreach($this->attributes as $name=>$value){
			$writer->writeAttribute($name, $value);
		}
		if($this->children){
			foreach($this->children as $child) $child->save($writer);
		}
		
		$writer->endElement();
	}
}