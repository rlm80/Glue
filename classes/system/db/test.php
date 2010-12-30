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
			//self::test_queries();
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

		// View definitions :
		$dir	= \Glue\CLASSPATH_USER . 'db/table/default/';
		$path	= $dir . 'glpersons.php';
		$code = <<<'EOD'
<?php

  namespace Glue\User\DB;

  class Table_Default_Glpersons extends \Glue\DB\Table {

    protected function init_name() {
      return 'glusers';
    }

    public function _get_column_alias(\Glue\DB\Column $column) {
      if ($column->name() === 'login')
        return 'name';
      return parent::_get_column_alias($column); // Any other column identifier is unchanged
    }
  }
?>
EOD;
		@mkdir($dir, 777, true);
		file_put_contents($path, $code);
		
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
		$tests['table list'] = array('glintro,glpersons,glposts,glprofiles,glusers', implode(',', $list));

		// Table exists :
		$tests['table exists true'] = array(true, \Glue\DB\DB::cn()->table_exists('glpersons'));
		$tests['table exists false'] = array(false, \Glue\DB\DB::cn()->table_exists('glpersonsqsdf'));

		// Tables :
		$tables = \Glue\DB\DB::cn()->tables();
		$tests['tables'] = array(5, count($tables));

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

		$c = $table->column('b');
		$tests['b name'] = array('b', $c->name());
		$tests['b type'] = array('int', strtolower($c->type()));
		$tests['b nullable'] = array(false, $c->nullable());
		$tests['b maxlength'] = array(null, $c->maxlength());
		$tests['b precision'] = array(10, $c->precision());
		$tests['b scale'] = array(0, $c->scale());
		$tests['b default'] = array(0, $c->default());
		$tests['b auto'] = array(false, $c->auto());

		$c = $table->column('c');
		$tests['c name'] = array('c', $c->name());
		$tests['c type'] = array('varchar', strtolower($c->type()));
		$tests['c nullable'] = array(true, $c->nullable());
		$tests['c maxlength'] = array(31, $c->maxlength());
		$tests['c precision'] = array(null, $c->precision());
		$tests['c scale'] = array(null, $c->scale());
		$tests['c default'] = array('test', $c->default());
		$tests['c auto'] = array(false, $c->auto());

		$c = $table->column('d');
		$tests['d name'] = array('d', $c->name());
		$tests['d type'] = array('decimal', strtolower($c->type()));
		$tests['d nullable'] = array(true, $c->nullable());
		$tests['d maxlength'] = array(null, $c->maxlength());
		$tests['d precision'] = array(6, $c->precision());
		$tests['d scale'] = array(2, $c->scale());
		$tests['d default'] = array(45.0, $c->default());
		$tests['d auto'] = array(false, $c->auto());
		
		// Views :
		$v = \Glue\DB\DB::cn()->table('glpersons');
		$tests['view name'] = array('glusers', $v->name());
		$tests['view alias'] = array('glpersons', $v->alias());
		$tests['view column name'] = array('login', $v->column('name')->name());
		$tests['view column alias'] = array('name', $v->column('name')->alias());
		
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
				echo "error ! " . $real . " doesn't match target " . $expected . "\n";
				return false;
			}
		}
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
			'template - nested' => array(
					db::template("? test ?", db::template('toast'), db::template('toast')),
					"toast test toast"
				),
			'boolean - simple' => array(
					db::bool("'test' = ?", "qsdf")->or("'test' IN ?", array('azer', 'qsdf'))->root(),
					"('test' = 'qsdf') OR ('test' IN ('azer','qsdf'))"
				),
			'boolean - nested' => array(
					db::bool(db::bool("1=1")->or("2=2"))->and("3=3")->root(),
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
					db::template("$t->id < $t->password qsdf"),
					"`myalias`.`id` < `myalias`.`password` qsdf"
				),
		);
		
		$orderby = new Fragment_Builder_Orderby();
		$orderby
			->asc($t->login)
			->asc($t->password)
			->desc($t->login)
			->desc('?', 'test');
		$tests['orderby'] = array(
			$orderby,
			"`myalias`.`login` ASC, `myalias`.`password` ASC, `myalias`.`login` DESC, ('test') DESC"
		);
				
/*
		$get = new Fragment_Builder_SelectList(null);
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
*/
		$join = db::join(db::table('glusers','t1'))
					->left(db::table('glprofiles','t2'))->on('?=?', 'test1', 'test2')->or('2=2')->and('3=3')
					->right(db::table('glposts','t3'))->on('1=1')->root();
		$tests['join simple'] = array(
			$join,
			"`glusers` AS `t1` LEFT OUTER JOIN `glprofiles` AS `t2` ON ('test1'='test2') OR (2=2) AND (3=3) RIGHT OUTER JOIN `glposts` AS `t3` ON (1=1)"
		);

		$join2 = db::join(db::table('glusers','t3'))
					->left($join)->on('5=5')->root();
		$tests['join nested'] = array(
			$join2,
			"`glusers` AS `t3` LEFT OUTER JOIN (`glusers` AS `t1` LEFT OUTER JOIN `glprofiles` AS `t2` ON ('test1'='test2') OR (2=2) AND (3=3) RIGHT OUTER JOIN `glposts` AS `t3` ON (1=1)) ON (5=5)"
		);

		$alias = db::table('glusers','myalias');
		$join3 = db::join(db::table('glprofiles','t3'))
					->left($alias)->on('1=1')->root();
		$tests['join alias'] = array(
			$join3,
			"`glprofiles` AS `t3` LEFT OUTER JOIN `glusers` AS `myalias` ON (1=1)"
		);
/*
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

		$select5 = db::select('glusers', $a)->as('myusers')->groupby($a->login)->and($a->password)->having("count(*) > 1")->orderby($a->login)->and($a->password)->columns($a->login)->and($a->password)->root();
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
*/
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

		//print_r($statement); die;

		$statement = db::select('glusers', $u)
						->columns($u->id)->and($u->login)->and($u->password)
						->prepare();
		$statement->execute();

		$statement->bindColumn(1, $test);

		while($res = $statement->fetch(PDO::FETCH_BOTH)) {
			//var_dump($u['login']);
			var_dump($res);
			echo $test;
		}

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