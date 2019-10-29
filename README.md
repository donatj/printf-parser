# Printf Parser

[![Latest Stable Version](https://poser.pugx.org/donatj/printf-parser/version)](https://packagist.org/packages/donatj/printf-parser)
[![Total Downloads](https://poser.pugx.org/donatj/printf-parser/downloads)](https://packagist.org/packages/donatj/printf-parser)
[![License](https://poser.pugx.org/donatj/printf-parser/license)](https://packagist.org/packages/donatj/printf-parser)
[![Build Status](https://travis-ci.org/donatj/printf-parser.svg?branch=master)](https://travis-ci.org/donatj/printf-parser)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/donatj/printf-parser/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/donatj/printf-parser)
[![Code Coverage](https://scrutinizer-ci.com/g/donatj/printf-parser/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/donatj/printf-parser)


Parses printf style strings, e.g. `cats: %d` into inspectable lexemes.


## Requirements

- **php**: >=7.1

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

$emitter = new \donatj\Printf\LexemeEmitter();
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

#### Method: LexemeEmitter->getLexemes

```php
function getLexemes() : \donatj\Printf\donatj\Printf\LexemeCollection
```

Return the Lexemes received by the emitter as an immutable LexemeCollection

### Class: \donatj\Printf\LexemeCollection

LexemeCollection is an immutable iterable collection of Lexemes with ArrayAccess

#### Method: LexemeCollection->getInvalid

```php
function getInvalid() : ?\donatj\Printf\donatj\Printf\Lexeme
```

Retrieve the first invalid Lexeme or null if all are valid.  
  
This is useful for checking if a printf string parsed without error.

---

#### Method: LexemeCollection->toArray

```php
function toArray() : array
```

Get the LexemeCollection as an Array

##### Returns:

- ***\donatj\Printf\Lexeme[]***

---

#### Method: LexemeCollection->argTypes

```php
function argTypes() : array
```

##### Returns the list of expected arguments a 1-indexed map of the following

```  
PrintfLexeme::ARG_TYPE_MISSING  
PrintfLexeme::ARG_TYPE_INT  
PrintfLexeme::ARG_TYPE_DOUBLE  
PrintfLexeme::ARG_TYPE_STRING  
```

##### Returns:

- ***string[]***