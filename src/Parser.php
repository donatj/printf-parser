<?php

namespace donatj\Printf;

class Parser {

	/**
	 * @var \donatj\Printf\LexemeEmitter
	 */
	private $emitter;

	/**
	 * Parser constructor.
	 *
	 * @param \donatj\Printf\LexemeEmitter $emitter
	 */
	public function __construct( LexemeEmitter $emitter ) {
		$this->emitter = $emitter;
	}

	public function parseStr( string $string ) : void {
		$lexer = new StringLexer($string);

		for(;;) {
			$next = $lexer->next();
			if( $next->isEof() ) {
				return;
			}

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

	private function lexString( LexemeEmitter $emitter, StringLexer $lexer ) : void {
		$pos    = $lexer->pos();
		$buffer = '';
		for( ;;) {
			$next = $lexer->next();
			if( $next->isEof() ) {
				break;
			}

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

	private function lexSprintf( LexemeEmitter $emitter, StringLexer $lexer ) : void {
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

		switch( $next->getString() ) {
			case '0':
				$padChar = '0';
				$next    = $lexer->next();
				break;
			case ' ':
				$padChar = ' ';
				$next    = $lexer->next();
				break;
			case "'":
				$next    = $lexer->next();
				$padChar = $next->getString();
				$next    = $lexer->next();
		}

		if( $next->getString() === '-' ) {
			$leftJustified = true;
			$next          = $lexer->next();
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
		if( isset(PrintfLexeme::CHAR_MAP[$next->getString()]) ) {
			$tType = PrintfLexeme::CHAR_MAP[$next->getString()];
		}

		$content = $lexer->substr($pos, $lexer->pos() - $pos);

		$emitter->emit(
			new PrintfLexeme(
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
		for( ;;) {
			$next = $lexer->next();
			if( !ctype_digit($next->getString()) ) {
				$lexer->rewind();
				break;
			}

			$int .= $next->getString();
		}

		return $int;
	}

}
