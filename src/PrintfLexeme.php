<?php

namespace donatj\Printf;

class PrintfLexeme extends Lexeme {

	public const T_INT_AS_BINARY      = 'b'; // b - the argument is treated as an integer and presented as a binary number.
	public const T_INT_AS_CHARACTER   = 'c'; // c - the argument is treated as an integer and presented as the character with that ASCII value.
	public const T_INT                = 'd'; // d - the argument is treated as an integer and presented as a (signed) decimal number.
	public const T_DOUBLE_AS_SCI      = 'e'; // e - the argument is treated as scientific notation (e.g. 1.2e+2). The precision specifier stands for the number of digits after the decimal point since PHP 5.2.1. In earlier versions, it was taken as number of significant digits (one less).
	public const T_DOUBLE_AS_SCI_CAP  = 'E'; // E - like %e but uses uppercase letter (e.g. 1.2E+2).
	public const T_FLOAT_LOCALE       = 'f'; // f - the argument is treated as a float and presented as a floating-point number (locale aware).
	public const T_FLOAT_NO_LOCALE    = 'F'; // F - the argument is treated as a float and presented as a floating-point number (non-locale aware). Available since PHP 5.0.3.
	public const T_FLOAT_AUTO_SCI     = 'g'; // g - shorter of %e and %f.
	public const T_FLOAT_AUTO_SCI_CAP = 'G'; // G - shorter of %E and %F.
	public const T_INT_AS_OCTAL       = 'o'; // o - the argument is treated as an integer and presented as an octal number.
	public const T_STRING             = 's'; // s - the argument is treated as and presented as a string.
	public const T_INT_UNSIGNED       = 'u'; // u - the argument is treated as an integer and presented as an unsigned decimal number.
	public const T_INT_HEX            = 'x'; // x - the argument is treated as an integer and presented as a hexadecimal number (with lowercase letters).
	public const T_INT_HEX_CAP        = 'X'; // X - the argument is treated as an integer and presented as a hexadecimal number (with uppercase letters).

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

	public const ARG_TYPE_MISSING = '';
	public const ARG_TYPE_INT     = 'int';
	public const ARG_TYPE_DOUBLE  = 'float';
	public const ARG_TYPE_STRING  = 'string';

	/** @var int[] string    s */
	public const STRING_TYPES = [ self::T_STRING ];

	/** @var int[] integer    d, u, c, o, x, X, b */
	public const INTEGER_TYPES = [
		self::T_INT, self::T_INT_UNSIGNED,
		self::T_INT_AS_CHARACTER, self::T_INT_AS_OCTAL,
		self::T_INT_HEX, self::T_INT_HEX_CAP,
		self::T_INT_AS_BINARY,
	];

	/** @var int[] double    g, G, e, E, f, F */
	public const DOUBLE_TYPES = [
		self::T_FLOAT_AUTO_SCI, self::T_FLOAT_AUTO_SCI_CAP,
		self::T_DOUBLE_AS_SCI, self::T_DOUBLE_AS_SCI_CAP,
		self::T_FLOAT_LOCALE, self::T_FLOAT_NO_LOCALE,
	];

	private $arg;
	private $showPositive;
	private $padChar;
	private $padWidth;
	private $leftJustified;
	private $precision;

	public function __construct(
		string $lexItemType, string $val, int $pos,
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

	/**
	 * Returns based on the type of argument one of the following
	 *
	 *   PrintfLexeme::ARG_TYPE_MISSING
	 *   PrintfLexeme::ARG_TYPE_INT
	 *   PrintfLexeme::ARG_TYPE_DOUBLE
	 *   PrintfLexeme::ARG_TYPE_STRING
	 */
	public function argType() : string {
		if( in_array($this->lexItemType, self::INTEGER_TYPES, true) ) {
			return self::ARG_TYPE_INT;
		}

		if( in_array($this->lexItemType, self::DOUBLE_TYPES, true) ) {
			return self::ARG_TYPE_DOUBLE;
		}

		if( in_array($this->lexItemType, self::STRING_TYPES, true) ) {
			return self::ARG_TYPE_STRING;
		}

		return self::ARG_TYPE_MISSING;
	}

}
