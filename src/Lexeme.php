<?php

namespace donatj\Printf;

class Lexeme {

	public const T_INVALID        = '';
	public const T_LITERAL_STRING = '!';

	/**
	 * @var string
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
	public function __construct( string $lexItemType, string $val, int $pos ) {
		$this->lexItemType = $lexItemType;
		$this->val         = $val;
		$this->pos         = $pos;
	}

	public function getLexItemType() : string {
		return $this->lexItemType;
	}

	public function getVal() : string {
		return $this->val;
	}

	public function getPos() : int {
		return $this->pos;
	}

}
