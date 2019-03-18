# Printf Parser

[![Latest Stable Version](https://poser.pugx.org/donatj/printf-parser/version)](https://packagist.org/packages/donatj/printf-parser)
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