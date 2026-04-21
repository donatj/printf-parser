<?php

namespace donatj\Printf;

/**
 * Printer takes an iterable of Lexemes and produces a printf string.
 */
class Printer {

	/**
	 * Builds a canonical printf-format string from lexemes
	 *
	 * @param iterable<Lexeme> $lexemes This can be a LexemeCollection
	 * @throws \RuntimeException if an invalid or unrecognized lexeme is encountered
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

			if( $lexeme->getWidthArgumentIndex() !== null ) {
				$out .= $lexeme->getWidthArgumentIndex() === ArgumentLexeme::ARG_INDEX_IMPLICIT
					? '*'
					: '*' . $lexeme->getWidthArgumentIndex() . '$';
			} else {
				$out .= $lexeme->getPadWidth();
			}

			if( $lexeme->getPrecisionArgumentIndex() !== null ) {
				$out .= $lexeme->getPrecisionArgumentIndex() === ArgumentLexeme::ARG_INDEX_IMPLICIT
					? '.*'
					: '.*' . $lexeme->getPrecisionArgumentIndex() . '$';
			} else {
				$out .= $lexeme->getPrecision() !== null ? '.' . $lexeme->getPrecision() : '';
			}

			$out .= $lexeme->getLongModifier() ? 'l' : '';
			$out .= $type;
		}

		return $out;
	}

}
