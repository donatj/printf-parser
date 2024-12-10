<?php

namespace donatj\Printf;

class CharData {

	private string $string;
	private bool $eof;

	public function __construct( string $string, bool $eof ) {
		$this->string = $string;
		$this->eof    = $eof;
	}

	public function getString() : string {
		return $this->string;
	}

	public function isEof() : bool {
		return $this->eof;
	}

	public function length() : int {
		return strlen($this->string);
	}

}
