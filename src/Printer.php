<?php

namespace donatj\Printf;

/**
 * Printer takes an iterable of Lexemes and produces a printf string.
 */
class Printer {

	/**
	 * @param iterable<Lexeme> $lexemes This can be a LexemeCollection
	 */
	public function print( iterable $lexemes ) : string {
		$out = '';
		foreach( $lexemes as $lexeme ) {
			$type  = $lexeme->getLexItemType();
			$value = $lexeme->getVal();
			if( $type === Lexeme::T_INVALID ) {
				throw new \RuntimeException(sprintf("print failed - invalid lexeme: '%s' at: %d", $lexeme->getVal(), $lexeme->getPos()));
			}

			if( $type === Lexeme::T_LITERAL_STRING ) {
				if( $value === '%' ) {
					$out .= '%%';
				} else {
					$out .= $lexeme->getVal();
				}

				continue;
			}

			if( !$lexeme instanceof ArgumentLexeme ) {
				throw new \RuntimeException(sprintf("print failed - unhandled lexeme: '%s' at: %d", $lexeme->getVal(), $lexeme->getPos()));
			}

			$out .= '%';
			$out .= $lexeme->getArg() ? $lexeme->getArg() . '$' : '';

			// flags in canonical order
			if($lexeme->getPadChar() !== null) {
				if( $lexeme->getPadChar() !== '0' && $lexeme->getPadChar() !== ' ' ) {
					$out .= "'";
				}

				$out .= $lexeme->getPadChar();
			}

			$out .= $lexeme->getLeftJustified() ? '-' : '';
			$out .= $lexeme->getShowPositive() ? '+' : '';
			$out .= $lexeme->getPadWidth();
			$out .= $lexeme->getPrecision() !== null ? '.' . $lexeme->getPrecision() : '';
			$out .= $type;
		}

		return $out;
	}

}
