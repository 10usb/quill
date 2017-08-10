<?php
namespace quill\states;

use csslib\parsers\Parser;

class Style extends DefaultState {
	/**
	 * 
	 * {@inheritDoc}
	 * @see \quill\states\DefaultState::element()
	 */
	public function comment($parser, $value){
		$parser = new Parser($parser->getDocument()->getStylesheet()->addSegment('embedded'));
		$parser->setSource($value);
		$parser->parse();
	}
}