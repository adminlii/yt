<?php
if(!defined('IN_SYS')) {
	die('Access Denied');
}

class MiniLog {
	private static $_instance;
	private $_path;
	private $_pid;
	private $_handleArr;
	
	function __construct($path) {
		$this->_path = $path;
		$this->_pid = getmypid();		
	}
	
	private function __clone() {}
	
	public static function instance($path = '/tmp/') {
		if(!(self::$_instance instanceof self)) {
			self::$_instance = new self($path);
		}		
		return self::$_instance;
	}
	
	private	function getHandle($fileName) {
		if($this->_handleArr[$fileName]) {
			return $this->_handleArr[$fileName];
		}
		date_default_timezone_set('PRC');

		switch(LOG_TYPE){
			case 'DAY_APPEND':
				$fileNameSuffix = date('Ymd', time());
				$handle = fopen($this->_path . $fileName . $fileNameSuffix . ".log", 'a');
				break;
			case 'EVERYTIME_WRITE':
				list($usec, $sec) = explode(" ", microtime());
				$fileNameSuffix = date('Ymd-His', $sec);
				$fileNameSuffix .= '-'.round($usec * 1000);
				$handle = fopen($this->_path . $fileName . $fileNameSuffix . ".log", 'w');	
				break;
			case 'DAY_WRITE':
			default:
				$fileNameSuffix = date('Ymd', time());
				$handle = fopen($this->_path . $fileName . $fileNameSuffix . ".log", 'w');
				break;
		}

		$this->_handleArr[$fileName] = $handle;
		return $handle;
	}
	
	public function log($fileName, $message) {
		$handle = $this->getHandle($fileName);
		list($usec, $sec) = explode(" ", microtime());
		$time = date('Y-m-d H:i:s', $sec);
		$time .= ' '.round($usec * 1000);
		$firstLine = "**********************"."[$time] [$this->_pid]"."**********************";
		fwrite($handle, "$firstLine\n$message\n");
		return true;
	}
	
	function __destruct(){
		foreach ($this->_handleArr as $key => $item) {
			if($item) {
				fclose($item);
			}
		}
	}
}

?>