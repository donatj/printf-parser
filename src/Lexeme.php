<?php

namespace donatj\Printf;

class Lexeme {

	public const T_INVALID        = 0;
	public const T_LITERAL_STRING = 1;

	/**
	 * @var int
	 */
	private $lexItemType;
	/**
	 * @var string
	 */
	private $val;
	/**
	 * @var int
	 */
	private $pos;

	/**
	 * LexItem constructor.
	 */
	public function __construct( int $lexItemType, string $val, int $pos ) {
		$this->lexItemType = $lexItemType;
		$this->val         = $val;
		$this->pos         = $pos;
	}

	public function getLexItemType() : int {
		return $this->lexItemType;
	}

	public function getVal() : string {
		return $this->val;
	}

	public function getPos() : int {
		return $this->pos;
	}

}
