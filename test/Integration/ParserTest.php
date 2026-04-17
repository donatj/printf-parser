<?php

namespace Integration;

use donatj\Printf\ArgumentLexeme;
use donatj\Printf\Emitter;
use donatj\Printf\Lexeme;
use donatj\Printf\LexemeEmitter;
use donatj\Printf\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase {

	/**
	 * @param bool|string $valid
	 * @dataProvider parseStringProvider
	 */
	public function testParsing( string $input, string $serialized, $valid ) : void {
		$lexemeEmitter = new LexemeEmitter;
		$emitter = new class($lexemeEmitter) implements Emitter {

			public Emitter $lexemeEmitter;
			public string $serialized = '';
			public function __construct( Emitter $emitter ) {
 $this->lexemeEmitter = $emitter; }

			public function emit( Lexeme $lexItem ) : void {
				$this->lexemeEmitter->emit($lexItem);
				if( $lexItem instanceof ArgumentLexeme ) {
					$this->serialized .= "[{$lexItem->getLexItemType()}={$lexItem->getVal()}:{$lexItem->getPos()}||{$lexItem->getArg()}|pos:{$lexItem->getShowPositive()}|{$lexItem->getPadChar()}|{$lexItem->getPadWidth()}|left:{$lexItem->getLeftJustified()}|{$lexItem->getPrecision()}]";
				} else {
					$this->serialized .= "[{$lexItem->getLexItemType()}={$lexItem->getVal()}:{$lexItem->getPos()}]";
				}
			}

		};

		(new Parser($emitter))->parseStr($input);
		$this->assertSame($serialized, $emitter->serialized);

		$invalid = $lexemeEmitter->getLexemes()->getInvalid();
		$this->assertSame(
			($invalid === null),
			!($valid === false),
			sprintf(
				'Expected validity: %s, but got %s. Invalid lexeme: %s',
				($valid === false) ? 'invalid' : 'valid',
				($invalid === null) ? 'valid' : 'invalid',
				($invalid !== null) ? sprintf("'%s' at position %d", $invalid->getVal(), $invalid->getPos()) : 'none'
			));
	}

	/**
	 * @return array<array{string,string,bool|string}> the string to test, its summarized parse, canonical form/true if already canonical/false if invalid
	 */
	public static function parseStringProvider() : array {
		return [
			[ '%% foo %%', '[!=%:1][!= foo :2][!=%:8]', true ],
			[ '%11d%+22d%-33d', '[d=11d:1|||pos:||11|left:|][d=+22d:5|||pos:1||22|left:|][d=-33d:10|||pos:||33|left:1|]', true ],
			[ 'What %%%f percent', '[!=What :0][!=%:6][f=f:8|||pos:|||left:|][!= percent:9]', true ],
			// test all padding types
			[ '%012d % 12d %\'x12d', '[d=012d:1|||pos:|0|12|left:|][!= :5][d= 12d:7|||pos:| |12|left:|][!= :11][d=\'x12d:13|||pos:|x|12|left:|]', true ],
			[ 'foo%sbar', '[!=foo:0][s=s:4|||pos:|||left:|][!=bar:5]', true ],
			[ 'f%1$\'x-10d soup', '[!=f:0][d=1$\'x-10d:2||1|pos:|x|10|left:1|][!= soup:10]', true ],
			[
				'%s %d foo %15$\'c10.2c SOUP',
				'[s=s:1|||pos:|||left:|][!= :2][d=d:4|||pos:|||left:|][!= foo :5][c=15$\'c10.2c:11||15|pos:|c|10|left:|2][!= SOUP:21]',
				true,
			],
			[
				'this %% is my %s to %1$\'x10d parse %2$s longer string %15$s',
				'[!=this :0][!=%:6][!= is my :7][s=s:15|||pos:|||left:|][!= to :16][d=1$\'x10d:21||1|pos:|x|10|left:|][!= parse :28][s=2$s:36||2|pos:|||left:|][!= longer string :39][s=15$s:55||15|pos:|||left:|]',
				true,
			],

			'test positional arguments' => [
				'%g %2$s-%1$f %u',
				'[g=g:1|||pos:|||left:|][!= :2][s=2$s:4||2|pos:|||left:|][!=-:7][f=1$f:9||1|pos:|||left:|][!= :12][u=u:14|||pos:|||left:|]',
				true,
			],

			'invalid string handling' => [
				'100%',
				'[!=100:0][=:4|||pos:|||left:|]',
				false,
			],

			'eof mid padding parse' => [ '%+10', '[=+10:1|||pos:1||10|left:|]', false],
			'eof mid padding parse no flags' => [ '%10', '[=10:1|||pos:||10|left:|]', false],

			'test positive argument' => [
				'%+10d %-+2d %+-2d',
				'[d=+10d:1|||pos:1||10|left:|][!= :5][d=-+2d:7|||pos:1||2|left:1|][!= :11][d=+-2d:13|||pos:1||2|left:1|]',
				'%+10d %-+2d %-+2d',
			],

			'handle dumb flag parsing' => [
				"%---+++---+-'x10d",
				'[d=---+++---+-\'x10d:1|||pos:1|x|10|left:1|]',
				"%'x-+10d",
			],
		];
	}

	/**
	 * @param array<array{string}|string> $expectedParts
	 * @param array<int,string>           $args
	 * @dataProvider printfWithTypeProvider
	 */
	public function testArgTypeLookup( string $input, array $expectedParts, array $args, bool $invalid = false ) : void {
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

		if( $invalid ) {
			$this->assertInstanceOf(Lexeme::class, $lexemes->getInvalid());
		} else {
			$this->assertNull($lexemes->getInvalid());
		}

		$this->assertSame($args, $lexemes->argTypes());
	}

	/**
	 * @return array<array{string,array<array{string}|string>,array<int,string>,bool}>
	 */
	public static function printfWithTypeProvider() : array {
		return [
			[ 'no args', [ 'no args' ], [], false ],
			[
				'cats on roofs %f',
				[ 'cats on roofs ', [ ArgumentLexeme::ARG_TYPE_DOUBLE ] ],
				[ 1 => ArgumentLexeme::ARG_TYPE_DOUBLE ],
				false,
			],
			[
				'percent of %s: %d%%',
				[ 'percent of ', [ ArgumentLexeme::ARG_TYPE_STRING ], ': ', [ ArgumentLexeme::ARG_TYPE_INT ], '%' ],
				[ 1 => ArgumentLexeme::ARG_TYPE_STRING, ArgumentLexeme::ARG_TYPE_INT ],
				false,
			],

			// Invalid
			[
				'100%',
				[ '100', [ ArgumentLexeme::ARG_TYPE_MISSING ] ],
				[ 1 => ArgumentLexeme::ARG_TYPE_MISSING ],
				true,
			],
			[
				'%d %s %',
				[ [ ArgumentLexeme::ARG_TYPE_INT ], ' ', [ ArgumentLexeme::ARG_TYPE_STRING ], ' ', [ ArgumentLexeme::ARG_TYPE_MISSING ] ],
				[ 1 => ArgumentLexeme::ARG_TYPE_INT, ArgumentLexeme::ARG_TYPE_STRING, ArgumentLexeme::ARG_TYPE_MISSING ],
				true,
			],
		];
	}

}
