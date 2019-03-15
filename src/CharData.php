<?php

namespace donatj\Printf;

class CharData {

	/**
	 * @var string
	 */
	private $string;
	/**
	 * @var bool
	 */
	private $eof;

	public function __construct( string $string, bool $eof ) {
		$this->string = $string;
		$this->eof    = $eof;
	}

	/**
	 * @return string
	 */
	public function getString() : string {
		return $this->string;
	}

	/**
	 * @return bool
	 */
	public function isEof() : bool {
		return $this->eof;
	}

	public function length() : int {
		return strlen($this->string);
	}
}
