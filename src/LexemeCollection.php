<?php

namespace donatj\Printf;

class LexemeCollection {

	/**
	 * @var LexItem[]
	 */
	private $lexItems;

	/**
	 * LexemeCollection constructor.
	 */
	public function __construct( LexItem ...$lexItems ) {
		$this->lexItems = $lexItems;
	}

	/**
	 * Retrieve the first invalid Lexeme or null if all are valid.
	 */
	public function getInvalid() : ?LexItem {
		foreach( $this->lexItems as $lexItem ) {
			if( $lexItem->getLexItemType() === LexItem::T_INVALID ) {
				return $lexItem;
			}
		}

		return null;
	}
}
