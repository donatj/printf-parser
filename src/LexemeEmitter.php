<?php

namespace donatj\Printf;

class LexemeEmitter implements Emitter {

	/** @var Lexeme[] */
	private array $lexemes = [];

	/**
	 * @internal This is for use by the Parser
	 */
	public function emit( Lexeme $lexItem ) : void {
		$this->lexemes[] = $lexItem;
	}

	/**
	 * Return the Lexemes received by the emitter as an immutable LexemeCollection
	 */
	public function getLexemes() : LexemeCollection {
		return new LexemeCollection(...$this->lexemes);
	}

}
