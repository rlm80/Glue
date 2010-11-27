<?php

namespace Glue\User\DB;

class Database_MySQL_Default extends \Glue\DB\Database_MySQL {
	public function __construct() {
		parent::__construct(
			'test',		// Database name.
			'root',		// Username.
			''			// Password.
		);
	}
}