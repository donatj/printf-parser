<?php

namespace donatj\Printf;

/**
 * LexemeCollection is an immutable iterable collection of Lexemes with ArrayAccess
 *
 * @implements \IteratorAggregate<\donatj\Printf\Lexeme>
 * @implements \ArrayAccess<int,\donatj\Printf\Lexeme>
 */
class LexemeCollection implements \ArrayAccess, \IteratorAggregate {

	/** @var Lexeme[] */
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
	public function offsetSet( $offset, $value ) : void {
		if( $offset === null ) {
			$this->lexItems[] = $value;
		} else {
			$this->lexItems[$offset] = $value;
		}
	}

	/**
	 * @internal
	 */
	public function offsetExists( $offset ) : bool {
		return isset($this->lexItems[$offset]);
	}

	/**
	 * @internal
	 */
	public function offsetUnset( $offset ) : void {
		unset($this->lexItems[$offset]);
	}

	/**
	 * @internal
	 */
	public function offsetGet( $offset ) : ?Lexeme {
		return $this->lexItems[$offset] ?? null;
	}

	/**
	 * @return \ArrayIterator<int,\donatj\Printf\Lexeme>
	 * @internal
	 */
	public function getIterator() : \ArrayIterator {
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
	 * ArgumentLexeme::ARG_TYPE_MISSING
	 * ArgumentLexeme::ARG_TYPE_INT
	 * ArgumentLexeme::ARG_TYPE_DOUBLE
	 * ArgumentLexeme::ARG_TYPE_STRING
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
