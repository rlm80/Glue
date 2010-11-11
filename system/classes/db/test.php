<?php

namespace Glue\DB;

use PDOException;

/**
 * Test class.
 * 
 * You may call the functions of this class to ensure everything is working properly.
 * 
 * @package Glue
 * @author Régis Lemaigre
 * @license MIT
 */

class Test {
	static public function run_tests() {
		echo ("<pre>");
		self::create_test_tables();
		try {
			self::test_fragments();
			self::test_queries();
		}
		catch (Exception $e) {
			self::drop_test_tables();
			throw $e;
		}
		self::drop_test_tables();
	}

	static private function create_test_tables() {
		self::drop_test_tables();
		db::db()->exec("create table glusers (id integer auto_increment, login varchar(31), password varchar(31), primary key(id))");
		db::db()->exec("create table glprofiles (id integer auto_increment, email varchar(255), primary key(id))");
		db::db()->exec("create table glposts (id integer auto_increment, content text, gluser_id integer, primary key(id))");
	}

	static private function drop_test_tables() {
		try { db::db()->exec("drop table glusers");		} catch (PDOException $e) {};
		try { db::db()->exec("drop table glprofiles");	} catch (PDOException $e) {};
		try { db::db()->exec("drop table glposts");		} catch (PDOException $e) {};
	}

	static private function test_fragments() {
		$tests = array(
			'value - string'	=> array(
					db::value("test'test"),
					"'test\'test'"
				),
			'value - integer' 	=> array(
					db::value(10),
					"10"
				),
			'value - array'	=> array(
					db::value(array("test'test", 10)),
					"('test\'test',10)"
				),
			'value - float'		=> array(
					db::value(10.5),
					"10.5"
				),
			'value - boolean'	=> array(
					db::value(false),
					"FALSE"
				),
			'value - null'		=> array(
					db::value(null),
					"NULL"
				),
			'template - no replacements' => array(
					db::template("test template"),
					"test template"
				),
			'template - two replacements' => array(
					db::template("? test ? template", "test'test", 10),
					"'test\'test' test 10 template"
				),
			'boolean - simple' => array(
					db::bool("'test' = ?", "qsdf")->or("'test' IN ?", array('azer', 'qsdf'))->root(),
					"('test' = 'qsdf') OR ('test' IN ('azer','qsdf'))"
				),
			'boolean - nested' => array(
					db::bool(db::bool("1=1")->or("2=2"))->and("3=3")->root(),
					"((1=1) OR (2=2)) AND (3=3)"
				),
			'table' => array(
					$t = db::alias('glusers', 'myalias'),
					"`glusers` AS `myalias`"
				),
			'column' => array(
					$t->login,
					"`myalias`.`login`"
				),
		);

		$get = new Fragment_Builder_Get(null);
		$get
			->and($t->login)
			->and($t->password)
			->and($t->login)->as('mylogin')
			->and($t->login)
			->and('?', 'test')
			->and('?', 'test')
			->root();
		$tests['get'] = array(
			$get,
			"`myalias`.`login` AS `login`, `myalias`.`password` AS `password`, `myalias`.`login` AS `mylogin`, `myalias`.`login` AS `login3`, ('test') AS `computed`, ('test') AS `computed2`"
		);

		$orderby = new Fragment_Builder_Orderby(null);
		$orderby
			->and($t->login)
			->and($t->password)
			->and($t->login)->asc()
			->and('?', 'test')->desc()
			->root();
		$tests['orderby'] = array(
			$orderby,
			"`myalias`.`login`, `myalias`.`password`, `myalias`.`login` ASC, ('test') DESC"
		);

		$join = db::join('glusers')->as('t1')
					->left('glprofiles')->as('t2')->on('?=?', 'test1', 'test2')->or('2=2')->and('3=3')
					->right('glposts')->as('t3')->on('1=1')->root();
		$tests['join simple'] = array(
			$join,
			"`glusers` AS `t1` LEFT OUTER JOIN `glprofiles` AS `t2` ON ('test1'='test2') OR (2=2) AND (3=3) RIGHT OUTER JOIN `glposts` AS `t3` ON (1=1)"
		);

		$join2 = db::join('glusers')->as('t3')
					->left($join)->on('5=5')->root();
		$tests['join nested'] = array(
			$join2,
			"`glusers` AS `t3` LEFT OUTER JOIN (`glusers` AS `t1` LEFT OUTER JOIN `glprofiles` AS `t2` ON ('test1'='test2') OR (2=2) AND (3=3) RIGHT OUTER JOIN `glposts` AS `t3` ON (1=1)) ON (5=5)"
		);

		$alias = db::alias('glusers','myalias');
		$join3 = db::join('glprofiles')->as('t3')
					->left($alias)->on('1=1')->root();
		$tests['join alias'] = array(
			$join3,
			"`glprofiles` AS `t3` LEFT OUTER JOIN `glusers` AS `myalias` ON (1=1)"
		);

		$select1 = db::select('glusers')->as('test')->where("1=1")->and("2=2")->or("3=3")->andnot("4=4")->ornot("5=5")->root();
		$tests['query select basic'] = array(
			$select1,
			"SELECT * FROM `glusers` AS `test` WHERE (1=1) AND (2=2) OR (3=3) AND NOT (4=4) OR NOT (5=5)"
		);

		$select2 = db::select('glusers', $u)->as('myusers')->where("$u->login = 'mylogin'")->root();
		$tests['query select alias'] = array(
			$select2,
			"SELECT * FROM `glusers` AS `myusers` WHERE (`myusers`.`login` = 'mylogin')"
		);

		$select3 = db::select('glusers', $a)->left('glusers', $b)->as('myusers')->on("$a->login = $b->login")->root();
		$tests['query select no alias'] = array(
			$select3,
			"SELECT * FROM `glusers` LEFT OUTER JOIN `glusers` AS `myusers` ON (`glusers`.`login` = `myusers`.`login`)"
		);

		$select4 = db::select('glusers', $a)->as('myusers')->orderby($a->login)->asc()->limit(30)->offset(20)->root();
		$tests['query select limit offset'] = array(
			$select4,
			"SELECT * FROM `glusers` AS `myusers` ORDER BY `myusers`.`login` ASC LIMIT 30 OFFSET 20"
		);

		$select5 = db::select('glusers', $a)->as('myusers')->groupby($a->login)->and($a->password)->having("count(*) > 1")->orderby($a->login)->and($a->password)->get($a->login)->and($a->password)->root();
		$tests['query select group by having'] = array(
			$select5,
			"SELECT `myusers`.`login` AS `login`, `myusers`.`password` AS `password` FROM `glusers` AS `myusers` GROUP BY `myusers`.`login`, `myusers`.`password` HAVING (count(*) > 1) ORDER BY `myusers`.`login`, `myusers`.`password`"
		);

		$delete1 = db::delete('glusers', $a)->where("$a->login = 'test'")->root();
		$tests['query delete'] = array(
			$delete1,
			"DELETE FROM `glusers` WHERE (`glusers`.`login` = 'test')"
		);

		$update1 = db::update('glusers', $a)->set($a->login, 'test')->and($a->password, 'test')->where("$a->login = 'test'")->root();
		$tests['query update'] = array(
			$update1,
			"UPDATE `glusers` SET `glusers`.`login` = 'test', `glusers`.`password` = 'test' WHERE (`glusers`.`login` = 'test')"
		);

		$insert1 = db::insert('glusers', $a)->columns($a->login, $a->password)->and($a->id)->values("test'1", "test'2")->and(1, 2)->root();
		$tests['query insert'] = array(
			$insert1,
			"INSERT INTO `glusers` (`login`, `password`, `id`) VALUES ('test\'1','test\'2'),(1,2)"
		);

		// Checks :
		foreach($tests as $type => $data) {
			list($f, $target) = $data;
			echo ("Testing fragments : " . $type . " ...");
			if ($f->sql() === $target)
				echo "ok \n";
			else {
				echo "error ! " . $f->sql() . " doesn't match target " . $target . "\n";
				return false;
			}
		}

		return true;
	}

	private function test_queries() {
		$statement = db::insert('glusers', $u)
						->columns($u->login, $u->password)
						->values('test1', 'test1')
							->and('test2', 'test2')
							->and('test3', 'test3')
						->prepare();
		$statement->execute();
	}
}