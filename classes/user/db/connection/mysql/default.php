<?php

namespace Glue\User\DB;

class Connection_MySQL_Default extends \Glue\DB\Connection_MySQL {
	public function __construct() {
		parent::__construct(
			'test',		// Database name.
			'root',		// Username.
			''			// Password.
		);
	}
}