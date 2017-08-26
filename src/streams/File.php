<?php
namespace quill\streams;

class File {
	private $path;
	private $handle;
	
	/**
	 * 
	 * @param string $path
	 */
	public function __construct($path){
		$this->path		= $path;
		$this->handle	= fopen($path, 'r+b');
	}
	
	/**
	 * 
	 * @param integer $length
	 * @return string
	 */
	public function read($length){
		if(feof($this->handle)) return false;
		return fread($this->handle, $length);
	}
}