<?php

namespace donatj\Printf;

class PrintfLexItem extends LexItem {

	public const T_INT_AS_BINARY      = 101; // b - the argument is treated as an integer and presented as a binary number.
	public const T_INT_AS_CHARACTER   = 102; // c - the argument is treated as an integer and presented as the character with that ASCII value.
	public const T_INT                = 103; // d - the argument is treated as an integer and presented as a (signed) decimal number.
	public const T_DOUBLE_AS_SCI      = 104; // e - the argument is treated as scientific notation (e.g. 1.2e+2). The precision specifier stands for the number of digits after the decimal point since PHP 5.2.1. In earlier versions, it was taken as number of significant digits (one less).
	public const T_DOUBLE_AS_SCI_CAP  = 105; // E - like %e but uses uppercase letter (e.g. 1.2E+2).
	public const T_FLOAT_LOCALE       = 106; // f - the argument is treated as a float and presented as a floating-point number (locale aware).
	public const T_FLOAT_NO_LOCALE    = 107; // F - the argument is treated as a float and presented as a floating-point number (non-locale aware). Available since PHP 5.0.3.
	public const T_FLOAT_AUTO_SCI     = 108; // g - shorter of %e and %f.
	public const T_FLOAT_AUTO_SCI_CAP = 109; // G - shorter of %E and %F.
	public const T_INT_AS_OCTAL       = 110; // o - the argument is treated as an integer and presented as an octal number.
	public const T_STRING             = 111; // s - the argument is treated as and presented as a string.
	public const T_INT_UNSIGNED       = 112; // u - the argument is treated as an integer and presented as an unsigned decimal number.
	public const T_INT_HEX            = 113; // x - the argument is treated as an integer and presented as a hexadecimal number (with lowercase letters).
	public const T_INT_HEX_CAP        = 114; // X - the argument is treated as an integer and presented as a hexadecimal number (with uppercase letters).

	public const CHAR_MAP = [
		'b' => self::T_INT_AS_BINARY,
		'c' => self::T_INT_AS_CHARACTER,
		'd' => self::T_INT,
		'e' => self::T_DOUBLE_AS_SCI,
		'E' => self::T_DOUBLE_AS_SCI_CAP,
		'f' => self::T_FLOAT_LOCALE,
		'F' => self::T_FLOAT_NO_LOCALE,
		'g' => self::T_FLOAT_AUTO_SCI,
		'G' => self::T_FLOAT_AUTO_SCI_CAP,
		'o' => self::T_INT_AS_OCTAL,
		's' => self::T_STRING,
		'u' => self::T_INT_UNSIGNED,
		'x' => self::T_INT_HEX,
		'X' => self::T_INT_HEX_CAP,
	];

	private $arg;
	private $showPositive;
	private $padChar;
	private $padWidth;
	private $leftJustified;
	private $precision;

	public function __construct( int $lexItemType, string $val, int $pos,
		?int $arg, ?bool $showPositive, ?string $padChar, ?int $padWidth, ?bool $leftJustified, ?int $precision
	) {
		parent::__construct($lexItemType, $val, $pos);
		$this->arg           = $arg;
		$this->showPositive  = $showPositive;
		$this->padChar       = $padChar;
		$this->padWidth      = $padWidth;
		$this->leftJustified = $leftJustified;
		$this->precision     = $precision;
	}

	public function getArg() : ?int {
		return $this->arg;
	}

	public function getShowPositive() : ?bool {
		return $this->showPositive;
	}

	public function getPadChar() : ?string {
		return $this->padChar;
	}

	public function getPadWidth() : ?int {
		return $this->padWidth;
	}

	public function getLeftJustified() : ?bool {
		return $this->leftJustified;
	}

	public function getPrecision() : ?int {
		return $this->precision;
	}

}
