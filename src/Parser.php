<?php

namespace donatj\Printf;

class Parser {

	/**
	 * @var \donatj\Printf\Emitter
	 */
	private $emitter;

	/**
	 * Parser constructor.
	 *
	 * @param \donatj\Printf\Emitter $emitter
	 */
	public function __construct( Emitter $emitter ) {
		$this->emitter = $emitter;
	}

	public function parseStr( string $string ) : void {
		$lexer = new StringLexer($string);
		for( ; ; ) {
			$next = $lexer->next();
			if( $next->isEof() ) {
				return;
			}

			if( $next->getString() === '%' ) {
				if( $lexer->hasPrefix('%') ) {
					$this->emitter->emit(
						new LexItem(LexItem::T_LITERAL_STRING, '%', $lexer->pos())
					);
				}

				$this->lexSprintf($this->emitter, $lexer);
			}

			$lexer->rewind();
			$this->lexString($this->emitter, $lexer);
		}
	}

	private function lexString( Emitter $emitter, StringLexer $lexer ) : void {
		$pos    = $lexer->pos();
		$buffer = '';
		for( ; ; ) {
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
			new LexItem(LexItem::T_LITERAL_STRING, $buffer, $pos)
		);
	}

	private function lexSprintf( Emitter $emitter, StringLexer $lexer ) : void {
		$next = $lexer->next();

		$arg           = null;
		$showPositive  = null;
		$padChar       = null;
		$padWidth      = null;
		$leftJustified = null;
		$width         = null;

		if( $next->getString() !== '0' && ctype_digit($next->getString()) ) {
			$lexer->rewind();
			$int = $this->eatInt($lexer);
			if( $lexer->hasPrefix('$') ) {
				$lexer->next();
				$arg = $int;
				drop('arg', $arg);
			}
		}

		switch( $next->getString() ) {
			case '0';
				$padChar = '0';
				break;
			case ' ':
				$padChar = ' ';
				break;
			case "'":
				$next    = $lexer->next();
				$padChar = $next->getString();
		}
	}

	private function eatInt( StringLexer $lexer ) : string {
		$int = '';
		for( ; ; ) {
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
