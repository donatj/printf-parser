<?php

namespace donatj\Printf;

class Lexeme {

	public const T_INVALID        = 0;
	public const T_LITERAL_STRING = 1;

	/**
	 * @var int
	 */
	protected $lexItemType;
	/**
	 * @var string
	 */
	protected $val;
	/**
	 * @var int
	 */
	protected $pos;

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
