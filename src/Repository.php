<?php
namespace quill;

interface Repository {
	/**
	 * 
	 * @param string $name
	 * @param number $size
	 * @return \alf\Font
	 */
	public function getFont($name, $size);
}