<?php

namespace Integration;

use donatj\Printf\Emitter;
use donatj\Printf\Lexeme;
use donatj\Printf\Parser;
use donatj\Printf\PrintfLexeme;

class ParserTest extends \PHPUnit\Framework\TestCase {

	/**
	 * @dataProvider parseStringProvider
	 */
	public function testParsing( $input, $serialized ) : void {
		$emitter = new class implements Emitter {

			public $serialized = '';

			public function emit( Lexeme $lexItem ) {
				if( $lexItem instanceof PrintfLexeme ) {
					$this->serialized .= "[{$lexItem->getLexItemType()}={$lexItem->getVal()}:{$lexItem->getPos()}||{$lexItem->getArg()}|{$lexItem->getShowPositive()}|{$lexItem->getPadChar()}|{$lexItem->getPadWidth()}|{$lexItem->getLeftJustified()}|{$lexItem->getPrecision()}]";
				} else {
					$this->serialized .= "[{$lexItem->getLexItemType()}={$lexItem->getVal()}:{$lexItem->getPos()}]";
				}
			}
		};

		(new Parser($emitter))->parseStr($input);
		$this->assertSame($serialized, $emitter->serialized);
	}

	public function parseStringProvider() : array {
		return [

			[ 'What %%%f percent', '[!=What :0][!=%:6][f=f:8|||||||][!= percent:9]' ],
			// test all padding types
			[ '%012d % 12d %\'x12d', '[d=012d:1||||0|12||][!= :5][d= 12d:7|||| |12||][!= :11][d=\'x12d:13||||x|12||]' ],
			[ 'foo%sbar', '[!=foo:0][s=s:4|||||||][!=bar:5]' ],
			[ 'f%1$\'x-10d soup', '[!=f:0][d=1$\'x-10d:2||1||x|10|1|][!= soup:10]' ],
			[
				'%s %d foo %15$\'c10.2c SOUP',
				'[s=s:1|||||||][!= :2][d=d:4|||||||][!= foo :5][c=15$\'c10.2c:11||15||c|10||2][!= SOUP:21]',
			],
			[
				'this %% is my %s to %1$\'x10d parse %2$s longer string %15$s',
				'[!=this :0][!=%:6][!= is my :7][s=s:15|||||||][!= to :16][d=1$\'x10d:21||1||x|10||][!= parse :28][s=2$s:36||2|||||][!= longer string :39][s=15$s:55||15|||||]',
			],

			// test positional arguments
			[
				'%g %2$s-%1$f %u',
				'[g=g:1|||||||][!= :2][s=2$s:4||2|||||][!=-:7][f=1$f:9||1|||||][!= :12][u=u:14|||||||]',
			],

			// invalid string handling
			[ '100%', '[!=100:0][=:4|||||||]' ],
		];
	}

}
