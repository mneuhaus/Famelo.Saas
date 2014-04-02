<?php
namespace Famelo\Saas\Csv;


/**
 */
class Reader extends \League\Csv\Reader {
	/**
	 * @var array
	 */
	protected $columns;

    public function validate($columns) {
        $this->columns = $this->fetchOne();
        foreach ($columns as $column) {
        	if (!in_array($column, $this->columns)) {
        		return FALSE;
        	}
        }
        return TRUE;
    }

    public function fetchAllAssoc() {
    	$rows = array();
    	foreach ($this->fetchAssoc($this->columns) as $key => $row) {
    		if ($key > 0) {
    			$rows[] = $row;
    		}
    	}
    	return $rows;
    }
}
