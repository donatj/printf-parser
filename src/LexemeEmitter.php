<?php

namespace donatj\Printf;

class LexemeEmitter {

	/** @var Lexeme[] */
	private $lexemes = [];

	/**
	 * @internal This is for use by the Parser
	 */
	public function emit( Lexeme $lexItem ) {
		$this->lexemes[] = $lexItem;
	}

	public function getLexemes() : LexemeCollection {
		return new LexemeCollection(...$this->lexemes);
	}

}
