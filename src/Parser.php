<?php

namespace donatj\Printf;

/**
 * Parser implements a PHP Printf compatible Printf string parser.
 *
 * @see https://www.php.net/manual/en/function.printf.php
 */
class Parser {

	/** @var Emitter */
	private $emitter;

	/**
	 * Parser constructor.
	 *
	 * @param \donatj\Printf\Emitter $emitter The given Emitter to emit Lexemes as parsed
	 */
	public function __construct( Emitter $emitter ) {
		$this->emitter = $emitter;
	}

	/**
	 * Parses a printf string and emit parsed lexemes to the configured Emitter
	 */
	public function parseStr( string $string ) : void {
		$lexer = new StringLexer($string);

		while( ($next = $lexer->next()) && !$next->isEof() ) {
			if( $next->getString() === '%' ) {
				if( $lexer->hasPrefix('%') ) {
					$this->emitter->emit(
						new Lexeme(Lexeme::T_LITERAL_STRING, '%', $lexer->pos())
					);
					$lexer->next();

					continue;
				}

				$this->lexSprintf($this->emitter, $lexer);

				continue;
			}

			$lexer->rewind();
			$this->lexString($this->emitter, $lexer);
		}
	}

	private function lexString( Emitter $emitter, StringLexer $lexer ) : void {
		$pos    = $lexer->pos();
		$buffer = '';
		while( ($next = $lexer->next()) && !$next->isEof() ) {
			if( $next->getString() === '%' ) {
				$lexer->rewind();
				break;
			}

			$buffer .= $next->getString();
		}

		$emitter->emit(
			new Lexeme(Lexeme::T_LITERAL_STRING, $buffer, $pos)
		);
	}

	private function lexSprintf( Emitter $emitter, StringLexer $lexer ) : void {
		$pos  = $lexer->pos();
		$next = $lexer->next();

		$arg           = null;
		$showPositive  = null;
		$padChar       = null;
		$padWidth      = null;
		$leftJustified = null;
		$precision     = null;

		if( $next->getString() !== '0' && ctype_digit($next->getString()) ) {
			$lexer->rewind();
			$int = $this->eatInt($lexer);
			if( $lexer->hasPrefix('$') ) {
				$lexer->next();
				$next = $lexer->next();
				$arg  = $int;
			}
		}

		// flag parsing
		for(;;) {
			switch( $next->getString() ) {
				case '0':
					$padChar = '0';
					$next    = $lexer->next();
					continue 2;
				case ' ':
					$padChar = ' ';
					$next    = $lexer->next();
					continue 2;
				case "'":
					$next    = $lexer->next();
					$padChar = $next->getString();
					$next    = $lexer->next();
					continue 2;
			}

			if( $next->getString() === '-' ) {
				$leftJustified = true;
				$next          = $lexer->next();
				continue;
			}

			if( $next->getString() === '+' ) {
				$showPositive = true;
				$next         = $lexer->next();
				continue;
			}

			break;
		}

		if( $padChar !== null ) {
			$lexer->rewind();
			$peek = $lexer->peek();
			if( ctype_digit($peek->getString()) ) {
				$padWidth = $this->eatInt($lexer);
			}

			$next = $lexer->next();
		}

		if( $next->getString() === '.' ) {
			if( ctype_digit($lexer->peek()->getString()) ) {
				$precision = $this->eatInt($lexer);
			}

			$next = $lexer->next();
		}

		$tType = Lexeme::T_INVALID;
		if( in_array($next->getString(), ArgumentLexeme::VALID_T_TYPES, true) ) {
			$tType = $next->getString();
		}

		$content = $lexer->substr($pos, $lexer->pos() - $pos);

		$emitter->emit(
			new ArgumentLexeme(
				$tType,
				$content,
				$pos,
				$arg,
				$showPositive,
				$padChar,
				$padWidth,
				$leftJustified,
				$precision
			)
		);
	}

	private function eatInt( StringLexer $lexer ) : string {
		$int = '';
		while( ($next = $lexer->next()) && !$next->isEof() ) {
			if( !ctype_digit($next->getString()) ) {
				$lexer->rewind();
				break;
			}

			$int .= $next->getString();
		}

		return $int;
	}

}
