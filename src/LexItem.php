<?php

namespace donatj\Printf;

class LexItem {

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
	 *
	 * @param int    $lexItemType
	 * @param string $val
	 * @param int    $pos
	 */
	public function __construct( int $lexItemType, string $val, int $pos ) {
		$this->lexItemType = $lexItemType;
		$this->val         = $val;
		$this->pos         = $pos;
	}

	/**
	 * @return int
	 */
	public function getLexItemType() : int {
		return $this->lexItemType;
	}

	/**
	 * @return string
	 */
	public function getVal() : string {
		return $this->val;
	}

	/**
	 * @return int
	 */
	public function getPos() : int {
		return $this->pos;
	}

}
