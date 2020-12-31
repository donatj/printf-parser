<?php

namespace donatj\Printf;

/**
 * LexemeCollection is an immutable iterable collection of Lexemes with ArrayAccess
 */
class LexemeCollection implements \ArrayAccess, \IteratorAggregate {

	/**
	 * @var Lexeme[]
	 */
	private $lexItems;

	/**
	 * LexemeCollection constructor.
	 *
	 * @internal
	 */
	public function __construct( Lexeme ...$lexItems ) {
		$this->lexItems = $lexItems;
	}

	/**
	 * Retrieve the first invalid Lexeme or null if all are valid.
	 *
	 * This is useful for checking if a printf string parsed without error.
	 */
	public function getInvalid() : ?Lexeme {
		foreach( $this->lexItems as $lexItem ) {
			if( $lexItem->getLexItemType() === Lexeme::T_INVALID ) {
				return $lexItem;
			}
		}

		return null;
	}

	/**
	 * @internal
	 */
	public function offsetSet( $offset, $value ) {
		if( $offset === null ) {
			$this->lexItems[] = $value;
		} else {
			$this->lexItems[$offset] = $value;
		}
	}

	/**
	 * @internal
	 */
	public function offsetExists( $offset ) {
		return isset($this->lexItems[$offset]);
	}

	/**
	 * @internal
	 */
	public function offsetUnset( $offset ) {
		unset($this->lexItems[$offset]);
	}

	/**
	 * @internal
	 */
	public function offsetGet( $offset ) {
		return isset($this->lexItems[$offset]) ? $this->lexItems[$offset] : null;
	}

	/**
	 * @internal
	 */
	public function getIterator() {
		return new \ArrayIterator($this->lexItems);
	}

	/**
	 * Get the LexemeCollection as an ordered array of Lexemes
	 *
	 * @return Lexeme[]
	 */
	public function toArray() : array {
		return $this->lexItems;
	}

	/**
	 * Returns the list of expected arguments a 1-indexed map of the following:
	 *
	 * ```
	 * PrintfLexeme::ARG_TYPE_MISSING
	 * PrintfLexeme::ARG_TYPE_INT
	 * PrintfLexeme::ARG_TYPE_DOUBLE
	 * PrintfLexeme::ARG_TYPE_STRING
	 * ```
	 *
	 * @return string[]
	 */
	public function argTypes() : array {
		$noNumInc = 1;
		$args     = [];
		foreach( $this->lexItems as $item ) {
			if( $item instanceof ArgumentLexeme ) {
				$type = $item->argType();

				if( $item->getArg() !== null ) {
					$args[$item->getArg()] = $type;
				} else {
					$args[$noNumInc] = $type;
					$noNumInc++;
				}
			}
		}

		return $args;
	}

}
