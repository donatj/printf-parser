<?php

namespace Integration;

use donatj\Printf\ArgumentLexeme;
use donatj\Printf\Emitter;
use donatj\Printf\Lexeme;
use donatj\Printf\LexemeEmitter;
use donatj\Printf\Parser;

class ParserTest extends \PHPUnit\Framework\TestCase {

	/**
	 * @dataProvider parseStringProvider
	 */
	public function testParsing( string $input, string $serialized ) : void {
		$emitter = new class implements Emitter {

			/** @var string */
			public $serialized = '';

			public function emit( Lexeme $lexItem ) : void {
				if( $lexItem instanceof ArgumentLexeme ) {
					$this->serialized .= "[{$lexItem->getLexItemType()}={$lexItem->getVal()}:{$lexItem->getPos()}||{$lexItem->getArg()}|pos:{$lexItem->getShowPositive()}|{$lexItem->getPadChar()}|{$lexItem->getPadWidth()}|left:{$lexItem->getLeftJustified()}|{$lexItem->getPrecision()}]";
				} else {
					$this->serialized .= "[{$lexItem->getLexItemType()}={$lexItem->getVal()}:{$lexItem->getPos()}]";
				}
			}

		};

		(new Parser($emitter))->parseStr($input);
		$this->assertSame($serialized, $emitter->serialized);
	}

	/**
	 * @return array<array{string,string}>
	 */
	public static function parseStringProvider() : array {
		return [

			[ 'What %%%f percent', '[!=What :0][!=%:6][f=f:8|||pos:|||left:|][!= percent:9]' ],
			// test all padding types
			[ '%012d % 12d %\'x12d', '[d=012d:1|||pos:|0|12|left:|][!= :5][d= 12d:7|||pos:| |12|left:|][!= :11][d=\'x12d:13|||pos:|x|12|left:|]' ],
			[ 'foo%sbar', '[!=foo:0][s=s:4|||pos:|||left:|][!=bar:5]' ],
			[ 'f%1$\'x-10d soup', '[!=f:0][d=1$\'x-10d:2||1|pos:|x|10|left:1|][!= soup:10]' ],
			[
				'%s %d foo %15$\'c10.2c SOUP',
				'[s=s:1|||pos:|||left:|][!= :2][d=d:4|||pos:|||left:|][!= foo :5][c=15$\'c10.2c:11||15|pos:|c|10|left:|2][!= SOUP:21]',
			],
			[
				'this %% is my %s to %1$\'x10d parse %2$s longer string %15$s',
				'[!=this :0][!=%:6][!= is my :7][s=s:15|||pos:|||left:|][!= to :16][d=1$\'x10d:21||1|pos:|x|10|left:|][!= parse :28][s=2$s:36||2|pos:|||left:|][!= longer string :39][s=15$s:55||15|pos:|||left:|]',
			],

			'test positional arguments' => [
				'%g %2$s-%1$f %u',
				'[g=g:1|||pos:|||left:|][!= :2][s=2$s:4||2|pos:|||left:|][!=-:7][f=1$f:9||1|pos:|||left:|][!= :12][u=u:14|||pos:|||left:|]',
			],

			'invalid string handling' => [ '100%', '[!=100:0][=:4|||pos:|||left:|]' ],

			'test positive argument' => [ '%+10d %-+2d %+-2d', '[=+1:1|||pos:1|||left:|][!=0d :3][=-+2:7|||pos:1|||left:1|][!=d :10][=+-2:13|||pos:1|||left:1|][!=d:16]' ],

			'handle dumb flag parsing' => [ "%---+++---+-'x10d", '[d=---+++---+-\'x10d:1|||pos:1|x|10|left:1|]' ],
		];
	}

	/**
	 * @dataProvider printfWithTypeProvider
	 */
	public function testArgTypeLookup( string $input, array $expectedParts ) : void {
		$emitter = new LexemeEmitter;
		$parser  = new Parser($emitter);

		$parser->parseStr($input);

		$lexemes = $emitter->getLexemes();

		$parts = [];
		foreach( $lexemes as $lexeme ) {
			if( $lexeme instanceof ArgumentLexeme ) {
				$parts[] = [ $lexeme->argType() ];
			} else {
				$parts[] = $lexeme->getVal();
			}
		}

		$this->assertSame($expectedParts, $parts);
	}

	public static function printfWithTypeProvider() : array {
		return [
			[ 'no args', [ 'no args' ] ],
			[ 'cats on roofs %f', [ 'cats on roofs ', [ ArgumentLexeme::ARG_TYPE_DOUBLE ] ] ],
			[ 'percent of %s: %d%%', [ 'percent of ', [ ArgumentLexeme::ARG_TYPE_STRING ], ': ', [ ArgumentLexeme::ARG_TYPE_INT ], '%' ] ],
		];
	}

}
