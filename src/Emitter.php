<?php

namespace donatj\Printf;

class Emitter {

	public function emit( LexItem $lexItem ) {
		print_r($lexItem);
	}

}
