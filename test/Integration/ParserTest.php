<?php

namespace Integration;

use donatj\Printf\ArgumentLexeme;
use donatj\Printf\Emitter;
use donatj\Printf\Lexeme;
use donatj\Printf\LexemeEmitter;
use donatj\Printf\Parser;
use donatj\Printf\Printer;
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
					$s = "[{$lexItem->getLexItemType()}={$lexItem->getVal()}:{$lexItem->getPos()}||{$lexItem->getArg()}|pos:{$lexItem->getShowPositive()}|{$lexItem->getPadChar()}|{$lexItem->getPadWidth()}|left:{$lexItem->getLeftJustified()}|{$lexItem->getPrecision()}";
					if( $lexItem->getWidthArgumentIndex() !== null ) {
						$s .= "|w:{$lexItem->getWidthArgumentIndex()}";
					}

					if( $lexItem->getPrecisionArgumentIndex() !== null ) {
						$s .= "|p:{$lexItem->getPrecisionArgumentIndex()}";
					}

					$this->serialized .= $s . ']';
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

			'test %h lowercase decimal dot' => [
				'%h',
				'[h=h:1|||pos:|||left:|]',
				true,
			],

			'test %H uppercase decimal dot' => [
				'%H',
				'[H=H:1|||pos:|||left:|]',
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

			'dynamic width' => [
				'%*s %*3$s %1$*2$s',
				'[s=*s:1|||pos:|||left:||w:0][!= :3][s=*3$s:5|||pos:|||left:||w:3][!= :9][s=1$*2$s:11||1|pos:|||left:||w:2]',
				true,
			],

			'dynamic precision implicit' => [
				'%.*f',
				'[f=.*f:1|||pos:|||left:||p:0]',
				true,
			],

			'dynamic width and precision implicit' => [
				'%*.*f',
				'[f=*.*f:1|||pos:|||left:||w:0|p:0]',
				true,
			],

			'dynamic width positional' => [
				'%2$*3$s',
				'[s=2$*3$s:1||2|pos:|||left:||w:3]',
				true,
			],

			'dynamic precision positional' => [
				'%2$.*3$f',
				'[f=2$.*3$f:1||2|pos:|||left:||p:3]',
				true,
			],

			'dynamic width and precision positional' => [
				'%2$*3$.*4$f',
				'[f=2$*3$.*4$f:1||2|pos:|||left:||w:3|p:4]',
				true,
			],

			'dynamic width zero positional index rejected' => [
				'%*0$s',
				'[=*0:1|||pos:|||left:||w:0][!=$s:3]',
				false,
			],

			'dynamic precision zero positional index rejected' => [
				'%.*0$f',
				'[=.*0:1|||pos:|||left:||p:0][!=$f:4]',
				false,
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
			'gaps in required indexes' => [
				'%1$s %5$d',
				[ [ArgumentLexeme::ARG_TYPE_STRING], ' ', [ArgumentLexeme::ARG_TYPE_INT] ],
				[ 1 => ArgumentLexeme::ARG_TYPE_STRING, 5 => ArgumentLexeme::ARG_TYPE_INT ],
				false,
			],
			'dynamic width implicit' => [
				'%*s',
				[ [ ArgumentLexeme::ARG_TYPE_STRING ] ],
				[ 1 => ArgumentLexeme::ARG_TYPE_INT, 2 => ArgumentLexeme::ARG_TYPE_STRING ],
				false,
			],
			'dynamic precision implicit' => [
				'%.*f',
				[ [ ArgumentLexeme::ARG_TYPE_DOUBLE ] ],
				[ 1 => ArgumentLexeme::ARG_TYPE_INT, 2 => ArgumentLexeme::ARG_TYPE_DOUBLE ],
				false,
			],
			'dynamic width and precision implicit' => [
				'%*.*f',
				[ [ ArgumentLexeme::ARG_TYPE_DOUBLE ] ],
				[ 1 => ArgumentLexeme::ARG_TYPE_INT, 2 => ArgumentLexeme::ARG_TYPE_INT, 3 => ArgumentLexeme::ARG_TYPE_DOUBLE ],
				false,
			],
			'dynamic width positional' => [
				'%2$*3$s',
				[ [ ArgumentLexeme::ARG_TYPE_STRING ] ],
				[ 2 => ArgumentLexeme::ARG_TYPE_STRING, 3 => ArgumentLexeme::ARG_TYPE_INT ],
				false,
			],
			'dynamic precision positional' => [
				'%2$.*3$f',
				[ [ ArgumentLexeme::ARG_TYPE_DOUBLE ] ],
				[ 2 => ArgumentLexeme::ARG_TYPE_DOUBLE, 3 => ArgumentLexeme::ARG_TYPE_INT ],
				false,
			],
			'dynamic width and precision positional' => [
				'%2$*3$.*4$f',
				[ [ ArgumentLexeme::ARG_TYPE_DOUBLE ] ],
				[ 2 => ArgumentLexeme::ARG_TYPE_DOUBLE, 3 => ArgumentLexeme::ARG_TYPE_INT, 4 => ArgumentLexeme::ARG_TYPE_INT ],
				false,
			],

			// These just ingrain the current behavior of the parser to avoid breaking compatibility. In a major release
			// these should be changed such that an index could contain more than a single type, gross as that is
			'overlapping types - last type wins 1' => [
				'%1$s %1$d',
				[ [ArgumentLexeme::ARG_TYPE_STRING], ' ', [ArgumentLexeme::ARG_TYPE_INT] ],
				[ 1 => ArgumentLexeme::ARG_TYPE_INT ],
				false,
			],
			'overlapping types - last type wins 2' => [
				'%1$d %1$s',
				[ [ArgumentLexeme::ARG_TYPE_INT], ' ', [ArgumentLexeme::ARG_TYPE_STRING] ],
				[ 1 => ArgumentLexeme::ARG_TYPE_STRING ],
				false,
			],
			'overlapping types - multiple overwrites' => [
				'%*s %2$*1$f %1$*2$f',
				[ [ArgumentLexeme::ARG_TYPE_STRING], ' ', [ ArgumentLexeme::ARG_TYPE_DOUBLE ], ' ', [ ArgumentLexeme::ARG_TYPE_DOUBLE ] ],
				[ 1 => ArgumentLexeme::ARG_TYPE_DOUBLE, ArgumentLexeme::ARG_TYPE_INT ],
				false,
			],
		];
	}

	/**
	 * @return \Generator<int|string,array<string,string>>
	 */
	public static function printProvider() : \Generator {
		foreach( self::parseStringProvider() as $input => $args ) {
			if($args[2] === false) {
				continue;
			}

			$canonical = $args[0];
			if(is_string($args[2])) {
				$canonical = $args[2];
			}

			// @phpstan-ignore generator.valueType (phpstan knows $args[0] is string, but insists it's an int here)
			yield $input => [
				$args[0],
				$canonical,
			];
		}
	}

	/**
	 * @dataProvider printProvider
	 */
	public function testPrinter(string $input, string $expected) : void {
		$emitter = new LexemeEmitter;
		$parser  = new Parser($emitter);
		$parser->parseStr($input);

		$printer = new Printer;
		$actual  = $printer->print($emitter->getLexemes());

		try {
			$this->assertSame($expected, $actual, sprintf('"%s" != "%s"', $actual, $expected));
		}catch( \Exception $e ) {
			$this->fail("Printer threw an exception for input: {$input}\nException message: " . $e->getMessage());
		}
	}

}
