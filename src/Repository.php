<?php
namespace quill;

interface Repository {
	/**
	 * 
	 * @param string $name
	 * @param number $size
	 * @param booleam $italic
	 * @param booleam $bold
	 * @return \alf\Font
	 */
	public function getFont($name, $size, $italic = false, $bold = false);
}