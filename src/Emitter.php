<?php

namespace donatj\Printf;

interface Emitter {

	/**
	 * @internal This is for use by the Parser
	 */
	public function emit( Lexeme $lexItem );
}
