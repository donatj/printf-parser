<?php

namespace donatj\Printf;

class LexemeCollection implements \ArrayAccess, \IteratorAggregate {

	/**
	 * @var Lexeme[]
	 */
	private $lexItems;

	/**
	 * LexemeCollection constructor.
	 */
	public function __construct( Lexeme ...$lexItems ) {
		$this->lexItems = $lexItems;
	}

	/**
	 * Retrieve the first invalid Lexeme or null if all are valid.
	 */
	public function getInvalid() : ?Lexeme {
		foreach( $this->lexItems as $lexItem ) {
			if( $lexItem->getLexItemType() === Lexeme::T_INVALID ) {
				return $lexItem;
			}
		}

		return null;
	}

	public function offsetSet( $offset, $value ) {
		if( $offset === null ) {
			$this->lexItems[] = $value;
		} else {
			$this->lexItems[$offset] = $value;
		}
	}

	public function offsetExists( $offset ) {
		return isset($this->lexItems[$offset]);
	}

	public function offsetUnset( $offset ) {
		unset($this->lexItems[$offset]);
	}

	public function offsetGet( $offset ) {
		return isset($this->lexItems[$offset]) ? $this->lexItems[$offset] : null;
	}

	public function getIterator() {
		return new \ArrayIterator($this->lexItems);
	}

}
