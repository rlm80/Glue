<?php

namespace Glue\System\DB;

use PDOStatement, PDO;

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
     * @var integer Stores default fetch mode set for this statement, as PHP doesn't make this information available....
     */
    protected $default_fetch_mode = PDO::FETCH_ASSOC;

    /**
     * Constructor.
     *
     * @param Database $db
     */
    protected function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Overriden to store default fetch mode locally.
     * 
     * @param integer $mode
     */
    public function setFetchMode($mode) {
    	$this->default_fetch_mode = $mode;
    	return parent::setFetchMode($mode);
    }
    
    /**
     * Returns current default fetch mode.
     * 
     * @return integer
     */
    public function getFetchMode() {
    	return $this->default_fetch_mode;
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
     * can be applied after each call to fetch.
     */
    public function bindColumn($column, &$param) {
    	// Get parameters array :
    	$args = func_get_args();
    	$args[1] =& $param;
    	
    	// Store binding to apply formatting after each call to fetch :
    	$this->bindings[] = array(
    		'column' => $column,
    		'param' => &$param
    	);
    	
    	// Reproduce call on parent method (we do it this way because PDOStatement::bindColumn doesn't document
    	// the default values of its parameters...) :
    	return call_user_func_array('parent::bindColumn', $args);
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
     * Redefined to store the last insert id, so that it can be retrieved by calling lastInsertId() on this object.
     *
     * @see PDOStatement::execute()
     */
    public function execute($input_parameters = array()) {
    	// Execute statement :
    	$return = parent::execute($input_parameters);
    	
    	// Register delayed bindings :
    	foreach($this->delayed_bindings as $binding)
    		$this->bindColumn($binding['column'], $binding['param']);
    	$this->delayed_bindings = array();
    	
    	// Store last insert id :
    	$this->last_insert_id = $this->db->lastInsertId();
    	
    	return $return;
    }
    
    public function fetch($fetch_mode = null, $cursor_orientation = PDO::FETCH_ORI_NEXT, $cursor_offset = 0) {
    	// Execute fetch :
    	$return = parent::fetch($fetch_mode, $cursor_orientation, $cursor_offset);
    	
    	// If $return is FALSE, this means we have reached the end of the result set :
    	if ($return === FALSE)
    		return FALSE;
    	
    	// Get real fetch mode :
    	if ( ! isset($fetch_mode))
    		$fetch_mode = $this->getFetchMode();
    		
    	// Depending on the fetch mode, type cast values appropriately :
    	if ($fetch_mode === PDO::FETCH_BOUND) {
    		// For all registered bindings, look for a formatter and apply formatting to bound variable :
    		foreach($this->bindings as $binding) {
    			$column = $binding['column'];
    			$param =& $binding['param'];
    			if (isset($this->formatters[$column])) {
    				$formatter	= $this->formatters[$column];
    				$param		= $formatter->format($param);
    			}
    		}
    	}
    	elseif ($fetch_mode === PDO::FETCH_ASSOC || $fetch_mode === PDO::FETCH_BOTH || $fetch_mode === PDO::FETCH_NUM) {
    	    // For all items in fetched array, look for a formatter and apply formatting :
    		foreach($return as $column => $value) {
    			// Formatter key :
    			if (is_numeric($column))
    				$key = $column + 1;
    			else 
    				$key = $column;
    			
    			// Format :
    			if (isset($this->formatters[$key])) {
    				$formatter			= $this->formatters[$key];
    				$return[$column]	= $formatter->format($value);
    			}
    		}    		
    	}
    	else {
    		// Other fetch modes are not supported yet. Feel free to submit patches....
    	} 
    	

    	// Return result :
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