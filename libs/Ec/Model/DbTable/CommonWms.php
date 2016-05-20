<?php
class Ec_Model_DbTable_CommonWms extends Zend_Db_Table {
	
// 	protected $_schema = 'wp';
	protected $_sequence = true;
	public function __construct($config = array(), $definition = null)
	{		
		$dbprefix = Zend_Registry::get('dbprefix');//表前缀
		$this->_name=$dbprefix.$this->_name;//带有前缀的数据表。
		
		$wms_db = Zend_Registry::get('wms_db');//Wsm 数据库名
		$this->_schema=$wms_db;
		parent::__construct($config);

	}
	public function replace($data) {
	    // get the columns for the table
	    $tableInfo = $this->info();
	    $tableColumns = $tableInfo['cols'];
	
	    // columns submitted for insert
	    $dataColumns = array_keys($data);
	
	    // intersection of table and insert cols
	    $valueColumns = array_intersect($tableColumns, $dataColumns);
	    sort($valueColumns);
	
	    // generate SQL statement
	    $cols = '';
	    $vals = '';
	    foreach($valueColumns as $col) {
	        $cols .= $this->getAdapter()->quoteIdentifier($col) . ',';
	        $vals .=	(get_class($data[$col]) == 'Zend_Db_Expr')
	        ? $data[$col]->__toString()
	        : $this->getAdapter()->quoteInto('?', $data[$col]);
	        $vals .= ',';
	    }
	    $cols = rtrim($cols, ',');
	    $vals = rtrim($vals, ',');
	    $sql = 'REPLACE INTO ' . $this->_name . ' (' . $cols . ') VALUES (' . $vals . ');';
	
	
	    return $this->_db->query($sql);
	
	}
}