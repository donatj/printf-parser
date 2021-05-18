<?php

namespace Integration;

use donatj\Printf\ArgumentLexeme;
use donatj\Printf\Emitter;
use donatj\Printf\Lexeme;
use donatj\Printf\Parser;

class ParserTest extends \PHPUnit\Framework\TestCase {

	/**
	 * @dataProvider parseStringProvider
	 */
	public function testParsing( $input, $serialized ) : void {
		$emitter = new class implements Emitter {

			public $serialized = '';

			public function emit( Lexeme $lexItem ) : void {
				if( $lexItem instanceof ArgumentLexeme ) {
					$showPositive  = (int)($lexItem->getShowPositive() ?? -1);
					$leftJustified = (int)($lexItem->getLeftJustified() ?? -1);
					$this->serialized .= "[{$lexItem->getLexItemType()}={$lexItem->getVal()}:{$lexItem->getPos()}||{$lexItem->getArg()}|pos:{$showPositive}|{$lexItem->getPadChar()}|{$lexItem->getPadWidth()}|left:{$leftJustified}|{$lexItem->getPrecision()}]";
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

			[ 'What %%%f percent', '[!=What :0][!=%:6][f=f:8|||pos:0|||left:-1|][!= percent:9]' ],
			// test all padding types
			[ '%012d % 12d %\'x12d', '[d=012d:1|||pos:0|0|12|left:-1|][!= :5][d= 12d:7|||pos:0| |12|left:-1|][!= :11][d=\'x12d:13|||pos:0|x|12|left:-1|]' ],
			[ 'foo%sbar', '[!=foo:0][s=s:4|||pos:0|||left:-1|][!=bar:5]' ],
			[ 'f%1$\'x-10d soup', '[!=f:0][d=1$\'x-10d:2||1|pos:0|x|10|left:1|][!= soup:10]' ],
			[
				'%s %d foo %15$\'c10.2c SOUP',
				'[s=s:1|||pos:0|||left:-1|][!= :2][d=d:4|||pos:0|||left:-1|][!= foo :5][c=15$\'c10.2c:11||15|pos:0|c|10|left:-1|2][!= SOUP:21]',
			],
			[
				'this %% is my %s to %1$\'x10d parse %2$s longer string %15$s',
				'[!=this :0][!=%:6][!= is my :7][s=s:15|||pos:0|||left:-1|][!= to :16][d=1$\'x10d:21||1|pos:0|x|10|left:-1|][!= parse :28][s=2$s:36||2|pos:0|||left:-1|][!= longer string :39][s=15$s:55||15|pos:0|||left:-1|]',
			],


			'test positional arguments' => [
				'%g %2$s-%1$f %u',
				'[g=g:1|||pos:0|||left:-1|][!= :2][s=2$s:4||2|pos:0|||left:-1|][!=-:7][f=1$f:9||1|pos:0|||left:-1|][!= :12][u=u:14|||pos:0|||left:-1|]',
			],


			'invalid string handling' => [ '100%', '[!=100:0][=:4|||pos:0|||left:-1|]' ],

			'test positive argument' => ['%+10d %-+2d %+-2d', '[=+1:1|||pos:1|||left:-1|][!=0d :3][=-+2:7|||pos:1|||left:1|][!=d :10][=+-2:13|||pos:1|||left:1|][!=d:16]'],

			'handle dumb flag parsing' => [ "%---+++---+-'x10d", '[d=---+++---+-\'x10d:1|||pos:1|x|10|left:1|]' ],
		];
	}

}
