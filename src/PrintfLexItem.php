<?php

namespace donatj\Printf;

class PrintfLexItem extends LexItem {

	private $arg;
	private $showPositive;
	private $padChar;
	private $padWidth;
	private $leftJustified;
	private $width;

	public function __construct( int $lexItemType, string $val, int $pos,
		?int $arg, ?bool $showPositive, ?string $padChar, ?int $padWidth, ?bool $leftJustified, ?int $width
	) {
		parent::__construct($lexItemType, $val, $pos);
		$this->arg           = $arg;
		$this->showPositive  = $showPositive;
		$this->padChar       = $padChar;
		$this->padWidth      = $padWidth;
		$this->leftJustified = $leftJustified;
		$this->width         = $width;
	}

	/**
	 * @return int|null
	 */
	public function getArg() : ?int {
		return $this->arg;
	}

	/**
	 * @return bool|null
	 */
	public function getShowPositive() : ?bool {
		return $this->showPositive;
	}

	/**
	 * @return string|null
	 */
	public function getPadChar() : ?string {
		return $this->padChar;
	}

	/**
	 * @return int|null
	 */
	public function getPadWidth() : ?int {
		return $this->padWidth;
	}

	/**
	 * @return bool|null
	 */
	public function getLeftJustified() : ?bool {
		return $this->leftJustified;
	}

	/**
	 * @return int|null
	 */
	public function getWidth() : ?int {
		return $this->width;
	}

}
