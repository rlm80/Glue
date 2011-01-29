<?php

namespace Glue\System\DB;

use \PDO;

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
		// Delete cache :
		\Glue\Core::clear_cache('db/');

		// Run tests :
		echo ("<pre>");
		self::create_test_tables();
		try {
			self::test_introspection();
			self::test_fragments();
			self::test_queries();
		}
		catch (\Exception $e) {
			self::drop_test_tables();
			throw $e;
		}
		self::drop_test_tables();
	}

	static private function create_test_tables() {
		self::drop_test_tables();

		// DB tables :
		db::cn()->exec("create table glintro (a integer auto_increment, b integer, c varchar(31) default 'test', d decimal(6,2) default 45, primary key(a, b))");
		db::cn()->exec("create table glusers (id integer auto_increment, login varchar(31), password varchar(31), primary key(id))");
		db::cn()->exec("create table glprofiles (id integer auto_increment, email varchar(255), primary key(id))");
		db::cn()->exec("create table glposts (id integer auto_increment, content text, gluser_id integer, primary key(id))");

		// Connection definitions :
		$dir	= \Glue\CLASSPATH_USER . 'db/connection/';
		$path	= $dir . 'test.php';
		$code = <<<'EOD'
<?php

  namespace Glue\User\DB;

  class Connection_Test extends \Glue\DB\Connectio_MySQL {
	protected $dbname = 'test';
	protected $username = 'root';
	protected $password = '';
  }
?>
EOD;
		@mkdir($dir, 777, true);
		file_put_contents($path, $code);
	}

	static private function drop_test_tables() {
		// Drop tables :
		try { db::cn()->exec("drop table glintro");		} catch (\Exception $e) {};
		try { db::cn()->exec("drop table glusers");		} catch (\Exception $e) {};
		try { db::cn()->exec("drop table glprofiles");	} catch (\Exception $e) {};
		try { db::cn()->exec("drop table glposts");		} catch (\Exception $e) {};

		// Delete view definitions :
		$dir	= \Glue\CLASSPATH_USER . 'db/table/';
		$path	= $dir . 'glpersons.php';
		@unlink($path);

		// Delete connection definition :
		$dir	= \Glue\CLASSPATH_USER . 'db/connection/';
		$path	= $dir . 'test.php';
		@unlink($path);
	}

	static private function test_introspection() {
		// Table list :
		$list = \Glue\DB\DB::cn()->table_list();
		sort($list);
		$tests['table list'] = array('glintro,glposts,glprofiles,glusers', implode(',', $list));

		// Table exists :
		$tests['table exists true'] = array(true, \Glue\DB\DB::cn()->table_exists('glposts'));
		$tests['table exists false'] = array(false, \Glue\DB\DB::cn()->table_exists('glpersonsqsdf'));

		// Tables :
		$tables = \Glue\DB\DB::cn()->tables();
		$tests['tables'] = array(4, count($tables));

		// Table data :
		$table = \Glue\DB\DB::cn()->table('glintro');

		// Name :
		$tests['table name'] = array('glintro', $table->name());

		// PK :
		$arr = array();
		foreach($table->pk() as $pkc)
			$arr[] = $pkc->name();
		sort($arr);
		$tests['table pk'] = array('a,b', implode(',', $arr));

		// Columns :
		$c = $table->column('a');
		$tests['a name'] = array('a', $c->name());
		$tests['a type'] = array('int', strtolower($c->type()));
		$tests['a nullable'] = array(false, $c->nullable());
		$tests['a maxlength'] = array(null, $c->maxlength());
		$tests['a precision'] = array(10, $c->precision());
		$tests['a scale'] = array(0, $c->scale());
		$tests['a default'] = array(null, $c->default());
		$tests['a auto'] = array(true, $c->auto());
		$tests['a phptype'] = array('integer', $c->phptype());

		$c = $table->column('b');
		$tests['b name'] = array('b', $c->name());
		$tests['b type'] = array('int', strtolower($c->type()));
		$tests['b nullable'] = array(false, $c->nullable());
		$tests['b maxlength'] = array(null, $c->maxlength());
		$tests['b precision'] = array(10, $c->precision());
		$tests['b scale'] = array(0, $c->scale());
		$tests['b default'] = array('0', $c->default());
		$tests['b auto'] = array(false, $c->auto());
		$tests['b phptype'] = array('integer', $c->phptype());

		$c = $table->column('c');
		$tests['c name'] = array('c', $c->name());
		$tests['c type'] = array('varchar', strtolower($c->type()));
		$tests['c nullable'] = array(true, $c->nullable());
		$tests['c maxlength'] = array(31, $c->maxlength());
		$tests['c precision'] = array(null, $c->precision());
		$tests['c scale'] = array(null, $c->scale());
		$tests['c default'] = array('test', $c->default());
		$tests['c auto'] = array(false, $c->auto());
		$tests['c phptype'] = array('string', $c->phptype());

		$c = $table->column('d');
		$tests['d name'] = array('d', $c->name());
		$tests['d type'] = array('decimal', strtolower($c->type()));
		$tests['d nullable'] = array(true, $c->nullable());
		$tests['d maxlength'] = array(null, $c->maxlength());
		$tests['d precision'] = array(6, $c->precision());
		$tests['d scale'] = array(2, $c->scale());
		$tests['d default'] = array('45.00', $c->default());
		$tests['d auto'] = array(false, $c->auto());
		$tests['d phptype'] = array('float', $c->phptype());

		// Connection list :
		$list = \Glue\DB\DB::connection_list();
		sort($list);
		$tests['connection list'] = array('default,test', implode(',', $list));

		// Connection exists :
		$tests['connection exists true'] = array(true, \Glue\DB\DB::connection_exists('test'));
		$tests['connection exists false'] = array(false, \Glue\DB\DB::connection_exists('testtt'));

		// Connections :
		$connections = \Glue\DB\DB::connections();
		$tests['connections'] = array(2, count($connections));

		// Checks :
		foreach($tests as $type => $data) {
			list($expected, $real) = $data;
			echo ("Testing introspection : " . $type . " ...");
			if ($expected === $real)
				echo "ok \n";
			else {
				echo '<b style="color:blue">error ! ' . $real . " doesn't match target " . $expected . "\n</b>";
				return false;
			}
		}
	}

	static private function test_fragments() {
		$tests = array(
			'value - string'	=> array(
					db::val("test'test"),
					"'test\'test'"
				),
			'value - integer' 	=> array(
					db::val(10),
					"10"
				),
			'value - array'	=> array(
					db::val(array("test'test", 10)),
					"('test\'test',10)"
				),
			'value - float'		=> array(
					db::val(10.5),
					"10.5"
				),
			'value - boolean'	=> array(
					db::val(false),
					"FALSE"
				),
			'value - null'		=> array(
					db::val(null),
					"NULL"
				),
			'template - simple' => array(
					db::sql("test template"),
					"test template"
				),
			'template - complex' => array(
					db::sql("test `q'sdf``a'zer``` testtest 'q`sdf''a`zer'''"),
					"test `q'sdf``a'zer``` testtest 'q`sdf\\'a`zer\\''"
				),
			'template - complex with replacements' => array(
					db::sql("? test ? `q'?sd!f``a'zer``` test ! test ? 'q`sdf''a`zer''' !", 'a', 'b', 'c', 'd', array('e','f')),
					"'a' test 'b' `q'?sd!f``a'zer``` test `c` test 'd' 'q`sdf\\'a`zer\\'' `e`.`f`"
				),
			'template - nested' => array(
					db::sql("? test ? ?", db::sql('toast'), db::sql('toast'), 10),
					"toast test toast 10"
				),
			'boolean - simple' => array(
					db::bool("'test' = ?", "qsdf")->or("'test' IN ?", array('azer', 'qsdf')),
					"('test' = 'qsdf') OR ('test' IN ('azer','qsdf'))"
				),
			'boolean - nested' => array(
					db::bool(db::bool("1=1")->or("2=2"))->and("3=3"),
					"((1=1) OR (2=2)) AND (3=3)"
				),
			'boolean - not' => array(
					db::bool("1=1")->not(),
					"NOT ((1=1))"
				),
			'boolean - not not' => array(
					db::bool("1=1")->not()->not(),
					"(1=1)"
				),
			'table' => array(
					$t = db::table('glusers', 'myalias'),
					"`glusers` AS `myalias`"
				),
			'template - columns' => array(
					db::sql("$t->id < $t->password qsdf"),
					"`myalias`.`id` < `myalias`.`password` qsdf"
				),
		);

		$join = db::join(db::table('glusers','t1'))
					->left(db::table('glprofiles','t2'))->on('?=?', 'test1', 'test2')->or('2=2')->and('3=3')
					->right(db::table('glposts','t3'))->on('1=1');
		$tests['join simple'] = array(
			$join,
			"`glusers` AS `t1` LEFT OUTER JOIN `glprofiles` AS `t2` ON ('test1'='test2') OR (2=2) AND (3=3) RIGHT OUTER JOIN `glposts` AS `t3` ON (1=1)"
		);

		$join2 = db::join(db::table('glusers','t3'))
					->left($join)->on('5=5');
		$tests['join nested'] = array(
			$join2,
			"`glusers` AS `t3` LEFT OUTER JOIN (`glusers` AS `t1` LEFT OUTER JOIN `glprofiles` AS `t2` ON ('test1'='test2') OR (2=2) AND (3=3) RIGHT OUTER JOIN `glposts` AS `t3` ON (1=1)) ON (5=5)"
		);

		$alias = db::table('glusers','myalias');
		$join3 = db::join(db::table('glprofiles','t3'))
					->left($alias)->on('1=1');
		$tests['join alias'] = array(
			$join3,
			"`glprofiles` AS `t3` LEFT OUTER JOIN `glusers` AS `myalias` ON (1=1)"
		);

		$orderby = new \Glue\DB\Fragment_Builder_Orderby();
		$orderby
			->orderby($t->login, array($t->password, \Glue\DB\DB::DESC))
			->orderby($t->email);
		$tests['orderby'] = array(
			$orderby,
			"(`myalias`.`login`) ASC, (`myalias`.`password`) DESC, (`myalias`.`email`) ASC"
		);

		$groupby = new \Glue\DB\Fragment_Builder_Groupby();
		$groupby
			->groupby($t->login, DB::sql("$t->password || 'qsdf'"))
			->groupby($t->email);
		$tests['groupby'] = array(
			$groupby,
			"(`myalias`.`login`), (`myalias`.`password` || 'qsdf'), (`myalias`.`email`)"
		);

		$select = new \Glue\DB\Fragment_Builder_SelectList();
		$select->columns($t->login, DB::sql($t->password))->columns(array($t->email, 'myemail'))->columns($t->login);
		$tests['select'] = array(
			$select,
			"(`myalias`.`login`) AS ```myalias``.``login```, (`myalias`.`password`), (`myalias`.`email`) AS `myemail`"
		);


		$select1 = db::select(array('users','test'))->where("1=1")->andwhere("2=2")->orwhere("3=3")->distinct();
		$tests['query select basic'] = array(
			$select1,
			"SELECT DISTINCT * FROM `users` AS `test` WHERE (1=1) AND (2=2) OR (3=3)"
		);

		$select2 = db::select(array('users','myusers'), $u)->where("$u->login = 'mylogin'");
		$tests['query select alias'] = array(
			$select2,
			"SELECT * FROM `users` AS `myusers` WHERE (`myusers`.`login` = 'mylogin')"
		);

		$select3 = db::select(array('users', null), $a)
						->left(array('users', 'myusers'), $b)
						->on("$a->login = $b->login");
		$tests['query select no alias'] = array(
			$select3,
			"SELECT * FROM `users` LEFT OUTER JOIN `users` AS `myusers` ON (`users`.`login` = `myusers`.`login`)"
		);

		$select4 = db::select(array('users', 'myusers'), $a)->orderby($a->login)->limit(30)->offset(20);
		$tests['query select limit offset'] = array(
			$select4,
			"SELECT * FROM `users` AS `myusers` ORDER BY (`myusers`.`login`) ASC LIMIT 30 OFFSET 20"
		);

		$select5 = db::select(array('users', 'myusers'), $a)->groupby($a->login, $a->password)->having("count(*) > 1")->orderby($a->login, $a->password)->columns($a->login, $a->password);
		$tests['query select group by having'] = array(
			$select5,
			"SELECT (`myusers`.`login`) AS ```myusers``.``login```, (`myusers`.`password`) AS ```myusers``.``password``` FROM `users` AS `myusers` GROUP BY (`myusers`.`login`), (`myusers`.`password`) HAVING (count(*) > 1) ORDER BY (`myusers`.`login`) ASC, (`myusers`.`password`) ASC"
		);

		$select5 = db::select(array('users', 'myusers'), $a)->groupby($a->login, $a->password)->having("count(*) > 1")->orderby($a->login, $a->password)->columns($a->login, $a->password);
		$tests['query select group by having'] = array(
			$select5,
			"SELECT (`myusers`.`login`) AS ```myusers``.``login```, (`myusers`.`password`) AS ```myusers``.``password``` FROM `users` AS `myusers` GROUP BY (`myusers`.`login`), (`myusers`.`password`) HAVING (count(*) > 1) ORDER BY (`myusers`.`login`) ASC, (`myusers`.`password`) ASC"
		);

		$select6 = db::select(array('users', 'a'), $a)->left(array('users', 'b'), $b)->on("1=1")->andon("2=2")->right(array('users', 'c'), $c)->on("3=3")->oron("4=4");
		$tests['query select andon oron'] = array(
			$select6,
			"SELECT * FROM `users` AS `a` LEFT OUTER JOIN `users` AS `b` ON (1=1) AND (2=2) RIGHT OUTER JOIN `users` AS `c` ON (3=3) OR (4=4)"
		);

		$delete1 = db::delete('users', $a)->where("$a->login = 'test'")->orderby($a->login)->limit(30)->offset(20);
		$tests['query delete'] = array(
			$delete1,
			"DELETE FROM `users` WHERE (`users`.`login` = 'test') ORDER BY (`users`.`login`) ASC LIMIT 30 OFFSET 20"
		);

		$update1 = db::update('users', $a)->set('login', 'test')->set('password', \Glue\DB\DB::sql(':pass'))->where("$a->login = 'test'")->orderby($a->login)->limit(30)->offset(20);
		$tests['query update'] = array(
			$update1,
			"UPDATE `users` SET `login` = ('test'), `password` = (:pass) WHERE (`users`.`login` = 'test') ORDER BY (`users`.`login`) ASC LIMIT 30 OFFSET 20"
		);

		$update2 = db::update('users', $a)
					->set(array(
						'login' => 'test',
						'password' => \Glue\DB\DB::sql(':pass')
					))
					->where("$a->login = 'test'")->orderby($a->login)->limit(30)->offset(20);
		$tests['query update array'] = array(
			$update2,
			"UPDATE `users` SET `login` = ('test'), `password` = (:pass) WHERE (`users`.`login` = 'test') ORDER BY (`users`.`login`) ASC LIMIT 30 OFFSET 20"
		);


		$insert1 = db::insert('users')->columns('login', 'password')->columns(array('id'))->values("test'1", "test'2", \Glue\DB\DB::sql(':test'))->values(array("a", "b", "c"), array("d", "e", "f"))->values(array(array("a", "b", "c"), array("d", "e", "f")));
		$tests['query insert'] = array(
			$insert1,
			"INSERT INTO `users` (`login`, `password`, `id`) VALUES ('test\'1','test\'2',:test),('a','b','c'),('d','e','f'),('a','b','c'),('d','e','f')"
		);

		// Checks :
		foreach($tests as $type => $data) {
			list($f, $target) = $data;
			echo ("Testing fragments : " . $type . " ...");
			if (db::cn()->compile($f) === $target)
				echo "ok \n";
			else {
				echo '<b style="color:blue">error ! ' . $f->sql() . " doesn't match target " . $target . "\n</b>";
				return false;
			}
		}

		return true;
	}

	private function test_queries() {
		// Test insert :
		$query = db::insert('glusers')->columns('login', 'password')
					->values('test1', 'test1')
					->values('test2', 'test2')
					->values('test3', 'test3');
		db::cn()->exec($query);

		// Test prepared insert :
		$query = db::insert('glprofiles')->columns('id', 'email')
					->values(null, 'test1')
					->values(null, 'test2')
					->values(null, 'test3');
		$stmt = db::cn()->prepare($query);
		$stmt->execute();

		// Test update :
		$query = db::update('glusers', $u)
					->set('password', db::select('glprofiles')->columns("count(*)"))
					->where("$u->login = ?", 'test2');
		db::cn()->exec($query);

		// Test Delete :
		$query = db::delete('glprofiles', $u)->where("$u->id = ?", 3);
		db::cn()->exec($query);

		// Test Select :
		$query = db::select('glusers', $a)
					->left('glusers', $b)->on("$a->id = $b->id")
					->where("$a->login LIKE ?", 'test%')
					->columns($a->id, $a->login, $b->id, $b->login)
					->where("$a->id = ?", 3)
					->groupby($a->id, $a->login, $b->id, $b->login)
					->orderby($a->id, $a->login, $b->id, $b->login)
					->limit(1)->offset(0);
		$stmt = db::cn()->query($query);

 
  // Create fragment :
  $f = db::values(1, 'test1')
         ->values(array(2, 'test2'))
         ->values(
           array(
             array(3, 'test3'),
             array(4, 'test4'),
           )
         );
  
  // Output SQL :
  echo db::cn()->compile( $f ); // (1,'test1'),(2,'test2'),(3,'test3'),(4,'test4')

		/*foreach($stmt as $row) {
			var_dump($row);
			var_dump($a->id($row));
		}*/

/*
		$query = db::select('glusers', $u)->columns($u->id, $u->login, $u->password);
		$statement = db::cn()->prepare($query);
		$statement->execute();
		while($res = $statement->fetch(PDO::FETCH_BOTH)) {
			//var_dump($u['login']);
			var_dump($res);
			echo $test;
		}*/

		/*
		$stmt = db::cn()->query("select login as login from glusers");
		//$stmt->bindColumn('login', $test);
		$stmt->setFetchMode(PDO::FETCH_BOTH);
		while($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
			print_r($res);
			//echo $test;
		}
		*/


		//$arr = $statement->fetchAll();
		//print_r($arr);

	}
}