<?php

namespace Glue\System\DB;

use \PDOStatement, \PDO;

/**
 * Statement class.
 *
 * PDOStatement extension.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Statement extends PDOStatement {
    /**
     * @var \Glue\DB\Connection PDO instance that spawned this statement.
     */
    protected $cn;

    /**
     * @var integer Last insert id right after this statement was executed.
     */
    protected $last_insert_id;

    /**
     * Constructor.
     *
     * @param \Glue\DB\Connection $cn
     */
    protected function __construct(\Glue\DB\Connection $cn) {
       $this->cn = $cn;
    }

    /**
     * Redefined to store the last insert id, so that it can be retrieved by calling lastInsertId() on this object.
     *
     * @see PDOStatement::execute()
     */
    public function execute($input_parameters = null) {
    	$return = parent::execute($input_parameters);
    	$this->last_insert_id = $this->cn->lastInsertId();
    	return $return;
    }

    /**
     * Returns last insert id right after this statement was executed.
     *
     * @return integer
     */
    public function lastInsertId() {
    	return $this->last_insert_id;
    }
}