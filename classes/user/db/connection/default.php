<?php

namespace Glue\User\DB;

class Connection_Default extends \Glue\DB\Connection_MySQL {
	protected $dbname = 'test';
	protected $username = 'root';
	protected $password = '';
}