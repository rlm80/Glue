<?php

namespace Glue\DB;

use PDOStatement;

/**
 * Statement class.
 *
 * PDOStatement extension that adds automatic type casting.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Statement extends PDOStatement {
    /**
     * @var Database PDO instance that spawned this statement. Automatically
     * 						passed by PDO to the constructor of this class when a new
     * 						statement is created.
     */
    protected $db;

    /**
     * @var array Array of formatters that will be used to type cast column values.
     */
    protected $formatters;

    /**
     * @var integer Last insert id right after this statement was executed.
     */
    protected $last_insert_id;
    
    /**
     * @var array Column - variable bindings.
     */
    protected $bindings;
    
    /**
     * @var array Column - variable bindings, to be registered after next call to execute.
     */
    protected $delayed_bindings = array();    

    /**
     * Constructor.
     *
     * @param Database $db
     */
    protected function __construct($db) {
        $this->db = $db;
    }

    /**
     * Binds a formatter to a column of the result set.
     *
     * @param string $column The column (name or integer index).
     * @param Formatter $formatter A Formatter instance, or a PHP type as a string.
     */
    public function bindFormatter($column, Formatter $formatter = null) {
    	if (isset($formatter))
    		$this->formatters[$column] = $formatter;
    	else
    		unset($this->formatters[$column]);
    }
    
    /**
     * PDOStatement::bindColumn() override, to keep track of all columns bindings so that proper formatting
     * can be applied after each call to fetch with FETCH_BOUND.
     */
    public function bindColumn($column, &$param) {
    	$this->bindings[] = array(
    		'column' => $column,
    		'param' => &$param
    	);
    	return parent::bindColumn($column, $param);
    }
    
    /**
     * Bindings delayed after next call to execute().
     */
    public function bindColumnDelayed($column, &$param) {
    	$this->delayed_bindings[] = array(
    		'column' => $column,
    		'param' => &$param
    	);
    }    

    /**
     * Redefined to store the last insert id, so it can be retrieved by calling lastInsertId() on this object.
     *
     * @see PDOStatement::execute()
     */
    public function execute($input_parameters = array()) {
    	// Execute statement :
    	$return = parent::execute($input_parameters);
    	
    	// Register delayed bindings :
    	foreach($this->delayed_bindings as $binding)
    		parent::bindColumn($binding['column'], $binding['param']);
    	$this->delayed_bindings = array();
    	
    	// Store last insert id :
    	$this->last_insert_id = $this->db->lastInsertId();
    	
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