<?php

namespace donatj\Printf;

interface Emitter {

	/**
	 * @param \donatj\Printf\Lexeme $lexItem
	 * @internal This is for use by the Parser
	 */
	public function emit( Lexeme $lexItem ) : void;

}
