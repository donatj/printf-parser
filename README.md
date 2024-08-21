# Printf Parser

[![Latest Stable Version](https://poser.pugx.org/donatj/printf-parser/version)](https://packagist.org/packages/donatj/printf-parser)
[![Total Downloads](https://poser.pugx.org/donatj/printf-parser/downloads)](https://packagist.org/packages/donatj/printf-parser)
[![License](https://poser.pugx.org/donatj/printf-parser/license)](https://packagist.org/packages/donatj/printf-parser)
[![ci.yml](https://github.com/donatj/printf-parser/actions/workflows/ci.yml/badge.svg)](https://github.com/donatj/printf-parser/actions/workflows/ci.yml)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/donatj/printf-parser/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/donatj/printf-parser)
[![Code Coverage](https://scrutinizer-ci.com/g/donatj/printf-parser/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/donatj/printf-parser)


PHP printf-syntax compatible printf string parser.

Parses printf strings into a stream of lexemes.


## Requirements

- **php**: >=7.2

## Installing

Install the latest version with:

```bash
composer require 'donatj/printf-parser'
```

## Example

Here is a simple example:

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

$emitter = new \donatj\Printf\LexemeEmitter;
$parser  = new \donatj\Printf\Parser($emitter);

$parser->parseStr('percent of %s: %d%%');

$lexemes = $emitter->getLexemes();

foreach( $lexemes as $lexeme ) {
	echo $lexeme->getLexItemType() . ' -> ';
	echo var_export($lexeme->getVal(), true);

	if( $lexeme instanceof \donatj\Printf\ArgumentLexeme ) {
		echo ' arg type: ' . $lexeme->argType();
	}

	echo PHP_EOL;
}

```

Output:

```
! -> 'percent of '
s -> 's' arg type: string
! -> ': '
d -> 'd' arg type: int
! -> '%'
```

## Documentation

### Class: \donatj\Printf\Parser

Parser implements a PHP Printf compatible Printf string parser.

#### Method: Parser->__construct

```php
function __construct(\donatj\Printf\Emitter $emitter)
```

Parser constructor.

##### Parameters:

- ***\donatj\Printf\Emitter*** `$emitter` - The given Emitter to emit Lexemes as parsed

---

#### Method: Parser->parseStr

```php
function parseStr(string $string) : void
```

Parses a printf string and emit parsed lexemes to the configured Emitter

### Class: \donatj\Printf\LexemeEmitter

---

#### Method: LexemeEmitter->getLexemes

```php
function getLexemes() : \donatj\Printf\LexemeCollection
```

Return the Lexemes received by the emitter as an immutable LexemeCollection

### Class: \donatj\Printf\LexemeCollection

LexemeCollection is an immutable iterable collection of Lexemes with ArrayAccess

---

#### Method: LexemeCollection->getInvalid

```php
function getInvalid() : ?\donatj\Printf\Lexeme
```

Retrieve the first invalid Lexeme or null if all are valid.  
  
This is useful for checking if a printf string parsed without error.

---

#### Method: LexemeCollection->toArray

```php
function toArray() : array
```

Get the LexemeCollection as an ordered array of Lexemes

##### Returns:

- ***\donatj\Printf\Lexeme[]***

---

#### Method: LexemeCollection->argTypes

```php
function argTypes() : array
```

##### Returns the list of expected arguments a 1-indexed map of the following

```  
ArgumentLexeme::ARG_TYPE_MISSING  
ArgumentLexeme::ARG_TYPE_INT  
ArgumentLexeme::ARG_TYPE_DOUBLE  
ArgumentLexeme::ARG_TYPE_STRING  
```

##### Returns:

- ***string[]***

### Class: \donatj\Printf\Lexeme

Lexeme represents a "basic" component of a printf string - either Literal Strings "!" or Invalid Lexemes

```php
<?php
namespace donatj\Printf;

class Lexeme {
	public const T_INVALID = '';
	public const T_LITERAL_STRING = '!';
}
```

#### Method: Lexeme->__construct

```php
function __construct(string $lexItemType, string $val, int $pos)
```

LexItem constructor.

---

#### Method: Lexeme->getLexItemType

```php
function getLexItemType() : string
```

The type of the printf Lexeme

---

#### Method: Lexeme->getVal

```php
function getVal() : string
```

The text of the lexeme

---

#### Method: Lexeme->getPos

```php
function getPos() : int
```

The string position of the given lexeme

### Class: \donatj\Printf\ArgumentLexeme

```php
<?php
namespace donatj\Printf;

class ArgumentLexeme {
	/** @var string the argument is treated as an integer and presented as a binary number. */
	public const T_INT_AS_BINARY = 'b';
	/** @var string the argument is treated as an integer and presented as the character with that ASCII value. */
	public const T_INT_AS_CHARACTER = 'c';
	/** @var string the argument is treated as an integer and presented as a (signed) decimal number. */
	public const T_INT = 'd';
	/** @var string the argument is treated as scientific notation (e.g. 1.2e+2). The precision specifier stands for the
number of digits after the decimal point since PHP 5.2.1. In earlier versions, it was taken as number of
significant digits (one less). */
	public const T_DOUBLE_AS_SCI = 'e';
	/** @var string like %e but uses uppercase letter (e.g. 1.2E+2). */
	public const T_DOUBLE_AS_SCI_CAP = 'E';
	/** @var string the argument is treated as a float and presented as a floating-point number (locale aware). */
	public const T_FLOAT_LOCALE = 'f';
	/** @var string the argument is treated as a float and presented as a floating-point number (non-locale aware).
Available since PHP 5.0.3. */
	public const T_FLOAT_NO_LOCALE = 'F';
	/** @var string shorter of %e and %f. */
	public const T_FLOAT_AUTO_SCI = 'g';
	/** @var string shorter of %E and %F. */
	public const T_FLOAT_AUTO_SCI_CAP = 'G';
	/** @var string the argument is treated as an integer and presented as an octal number. */
	public const T_INT_AS_OCTAL = 'o';
	/** @var string the argument is treated as and presented as a string. */
	public const T_STRING = 's';
	/** @var string the argument is treated as an integer and presented as an unsigned decimal number. */
	public const T_INT_UNSIGNED = 'u';
	/** @var string the argument is treated as an integer and presented as a hexadecimal number (with lowercase letters). */
	public const T_INT_HEX = 'x';
	/** @var string the argument is treated as an integer and presented as a hexadecimal number (with uppercase letters). */
	public const T_INT_HEX_CAP = 'X';
	public const VALID_T_TYPES = [self::T_INT_AS_BINARY, self::T_INT_AS_CHARACTER, self::T_INT, self::T_DOUBLE_AS_SCI, self::T_DOUBLE_AS_SCI_CAP, self::T_FLOAT_LOCALE, self::T_FLOAT_NO_LOCALE, self::T_FLOAT_AUTO_SCI, self::T_FLOAT_AUTO_SCI_CAP, self::T_INT_AS_OCTAL, self::T_STRING, self::T_INT_UNSIGNED, self::T_INT_HEX, self::T_INT_HEX_CAP];
	public const ARG_TYPE_MISSING = '';
	public const ARG_TYPE_INT = 'int';
	public const ARG_TYPE_DOUBLE = 'float';
	public const ARG_TYPE_STRING = 'string';
	/** @var string[] string    s */
	public const STRING_TYPES = [self::T_STRING];
	/** @var string[] integer    d, u, c, o, x, X, b */
	public const INTEGER_TYPES = [self::T_INT, self::T_INT_UNSIGNED, self::T_INT_AS_CHARACTER, self::T_INT_AS_OCTAL, self::T_INT_HEX, self::T_INT_HEX_CAP, self::T_INT_AS_BINARY];
	/** @var string[] double    g, G, e, E, f, F */
	public const DOUBLE_TYPES = [self::T_FLOAT_AUTO_SCI, self::T_FLOAT_AUTO_SCI_CAP, self::T_DOUBLE_AS_SCI, self::T_DOUBLE_AS_SCI_CAP, self::T_FLOAT_LOCALE, self::T_FLOAT_NO_LOCALE];
	public const T_INVALID = '';
	public const T_LITERAL_STRING = '!';
}
```

#### Method: ArgumentLexeme->__construct

```php
function __construct(string $lexItemType, string $val, int $pos, ?int $arg, bool $showPositive, ?string $padChar, ?int $padWidth, bool $leftJustified, ?int $precision)
```

ArgumentLexeme constructor.

LexItem constructor.

---

#### Method: ArgumentLexeme->getArg

```php
function getArg() : ?int
```

The position specifier, such as `%3$s` would return 3 and `%s` would return null

##### Returns:

- ***int*** | ***null*** - null on unspecified

---

#### Method: ArgumentLexeme->getShowPositive

```php
function getShowPositive() : bool
```

Is the "Prefix positive numbers with a plus sign +" flag enabled

---

#### Method: ArgumentLexeme->getPadChar

```php
function getPadChar() : ?string
```

Specified pad character flag

##### Returns:

- ***string*** | ***null*** - null on unspecified

---

#### Method: ArgumentLexeme->getPadWidth

```php
function getPadWidth() : ?int
```

Specified pad width

##### Returns:

- ***int*** | ***null*** - null on unspecified

---

#### Method: ArgumentLexeme->getLeftJustified

```php
function getLeftJustified() : bool
```

Is left-justification flag enabled?

---

#### Method: ArgumentLexeme->getPrecision

```php
function getPrecision() : ?int
```

The Lexeme's indicated precision.

##### Returns:

- ***int*** | ***null*** - null on unspecified

---

#### Method: ArgumentLexeme->argType

```php
function argType() : string
```

Returns based on the type of argument one of the following  
  
ArgumentLexeme::ARG_TYPE_MISSING  
ArgumentLexeme::ARG_TYPE_INT  
ArgumentLexeme::ARG_TYPE_DOUBLE  
ArgumentLexeme::ARG_TYPE_STRING

---

#### Method: ArgumentLexeme->getLexItemType

```php
function getLexItemType() : string
```

The type of the printf Lexeme

---

#### Method: ArgumentLexeme->getVal

```php
function getVal() : string
```

The text of the lexeme

---

#### Method: ArgumentLexeme->getPos

```php
function getPos() : int
```

The string position of the given lexeme