<?php
/**
 * 基类
 * @author Administrator
 *
 */
class Ebay_EbayLibTradingNew extends Ebay_EbayLibTrading {
	protected $_config = array (
			'token' => '',
			'devid' => '',
			'appid' => '',
			'certid' => '',
			'serverurl' => '',
			'version' => '823',
			'siteid' => '0' 
	);
	public function __construct($config = array()) {
		$this->_config = array_merge ( $this->_config, $config );
		// print_r($this->_config);
	}
	
	/**
	 * 入口方法
	 *
	 * @param unknown_type $callName        	
	 * @param unknown_type $param        	
	 * @return Ambigous <NULL, multitype:>
	 */
	public function request($callName, $param) {
		$requestXml = $this->getXmlContent ( $callName, $param );
// 		header('Content-Type:text/xml');
// 		echo $requestXml;exit;
		// print_r($this->_config);exit;
		$session = new eBaySession ( $this->_config ['token'], $this->_config ['devid'], $this->_config ['appid'], $this->_config ['appid'], $this->_config ['serverurl'], $this->_config ['version'], $this->_config ['siteid'], $callName );
		$responseXml = $session->sendHttpRequest ( $requestXml );
		// echo $responseXml;exit;
		$data = XML_unserialize ( $responseXml );
		// print_r($data);exit;
		return $data;
	}
	public function getXmlContent($callName, $arr) {
		$xml = new SimpleXMLElement ( "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<{$callName}Request xmlns=\"urn:ebay:apis:eBLBaseComponents\"></{$callName}Request>" );
		
		// function call to convert array to xml
		$this->array2xml ( $arr, $xml );
		
		// 2. output xml
		$xml = $xml->asXML ();
		$xml = trim ( $xml );
		return $xml;
	}
	/**
	 * 数组转xml
	 * @param unknown_type $info
	 * @param unknown_type $xml
	 * @throws Exception
	 */
	protected function array2xml($info, &$xml) {
		foreach ( $info as $key => $value ) {
			if (is_array ( $value )) {
				$keys = array_keys ( $value );
				// 前后加空格
				$keys_str = ' ' . implode ( ' ', $keys ) . ' ';
				$is_numeric_arr = false;
				if (preg_match ( '/^[0-9 ]+$/', $keys_str )) { // 数组的键为数字
					$is_numeric_arr = true;
				} else {
					if (preg_match ( '/ [0-9]+ /', $keys_str ) && preg_match ( '/[^0-9]+/', $keys_str )) { // 数组的键为数字和非数字混合
						throw new Exception ( '数组格式不正确' );
					}
				}
				// var_dump($contain_int_key);
				// echo $keys_str;exit;
				// print_r($keys);exit;
				if ($is_numeric_arr) {
					// echo $key;exit;
					foreach ( $value as $v ) {
						$xml->addChild ( "{$key}", $v );
					}
				} else {
					$subnode = $xml->addChild ( "{$key}" );
					
					$this->array2xml ( $value, $subnode );
				}
			} else {
				if (preg_match ( '/^[0-9]+$/', $key )) {
					// $xml->addChild ( "//", htmlspecialchars ( "$value" ) );
				} else {
					$xml->addChild ( "{$key}", htmlspecialchars ( "$value" ) );
				}
			}
		}
	}
}