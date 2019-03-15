<?php

namespace donatj\Printf;

class StringScanner {

	private $pos = 0;

	/**
	 * @var string
	 */
	private $string;

	/**
	 * StringScanner constructor.
	 */
	public function __construct( string $string ) {
		$this->string = $string;
	}

	public function peek( $size = 1 ) : CharData {
		$str = substr($this->string, $this->pos, $size);

		return new CharData($str, strlen($str) < $size);
	}

	public function next() : CharData {
		$data = $this->peek();

		$this->pos += $data->length();

		return $data;
	}
}
