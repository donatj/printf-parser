<?php

require __DIR__ . '/../vendor/autoload.php';

$emitter = new \donatj\Printf\LexemeEmitter;
$parser  = new \donatj\Printf\Parser($emitter);

$parser->parseStr('name: %s, score: %10.2f, rank: %1$d');

$lexemes = $emitter->getLexemes();

foreach( $lexemes as $lexeme ) {
	echo $lexeme->getLexItemType() . ' -> ';
	echo var_export($lexeme->getVal(), true);

	if( $lexeme instanceof \donatj\Printf\ArgumentLexeme ) {
		echo ' [type: ' . $lexeme->argType();

		if( $lexeme->getPadWidth() !== null ) {
			echo ', width: ' . $lexeme->getPadWidth();
		}

		if( $lexeme->getPrecision() !== null ) {
			echo ', precision: ' . $lexeme->getPrecision();
		}

		if( $lexeme->getArg() !== null ) {
			echo ', position: ' . $lexeme->getArg();
		}

		echo ']';
	}

	echo PHP_EOL;
}
