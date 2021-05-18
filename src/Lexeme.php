<?php

namespace donatj\Printf;

/**
 * Lexeme represents a "basic" component of a printf string - either Literal Strings "!" or Invalid Lexemes
 */
class Lexeme {

	public const T_INVALID        = '';
	public const T_LITERAL_STRING = '!';

	/** @var string */
	protected $lexItemType;
	/** @var string */
	protected $val;
	/** @var int */
	protected $pos;

	/**
	 * LexItem constructor.
	 */
	public function __construct( string $lexItemType, string $val, int $pos ) {
		$this->lexItemType = $lexItemType;
		$this->val         = $val;
		$this->pos         = $pos;
	}

	/**
	 * The type of the printf Lexeme
	 */
	public function getLexItemType() : string {
		return $this->lexItemType;
	}

	/**
	 * The text of the lexeme
	 */
	public function getVal() : string {
		return $this->val;
	}

	/**
	 * The string position of the given lexeme
	 */
	public function getPos() : int {
		return $this->pos;
	}

}
