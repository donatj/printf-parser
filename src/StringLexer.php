<?php

namespace donatj\Printf;

class StringLexer {

	private int $pos = 0;

	private string $string;

	/**
	 * StringScanner constructor.
	 */
	public function __construct( string $string ) {
		$this->string = $string;
	}

	public function peek( int $size = 1 ) : CharData {
		$str = substr($this->string, $this->pos, $size);

		return new CharData($str, strlen($str) < $size);
	}

	public function pos() : int {
		return $this->pos;
	}

	public function next() : CharData {
		$data = $this->peek();

		$this->pos += $data->length();

		return $data;
	}

	public function rewind() : void {
		$this->pos--;
		if( $this->pos < 0 ) {
			//@todo custom exception
			throw new \RuntimeException('cannot rewind');
		}
	}

	public function hasPrefix( string $prefix ) : bool {
		$peek = $this->peek(strlen($prefix));

		return $peek->getString() === $prefix;
	}

	public function substr( int $start, int $length ) : string {
		return substr($this->string, $start, $length);
	}

}
