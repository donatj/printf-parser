<?php

namespace donatj\Printf;

class LexemeEmitter {

	/** @var LexItem[] */
	private $lexemes = [];

	/**
	 * @internal This is for use by the Parser
	 */
	public function emit( LexItem $lexItem ) {
		$this->lexemes[] = $lexItem;
	}

	public function getLexemes() : LexemeCollection {
		return new LexemeCollection(...$this->lexemes);
	}

}
