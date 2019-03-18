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
