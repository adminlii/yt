<?php
require_once 'XmlHandle.php';
class Common_ZjsEdi { 
	public function getVerifyData($params = array()) {
		$rdm1 = $params['rdm1']; // 随机数
		$rdm2 = $params['rdm2'];// 随机数
		$clientFlag = $params['clientFlag']; // 客户标识
		$xml = $params['xml']; // 请求报文
		$strSeed = $params['strSeed']; // 客户密钥
		$strConst = $params['strConst']; // 常量值 
		
		$str = $rdm1 . $clientFlag . $xml . $strSeed . $strConst . $rdm2;
// 		$byteStr = $this->getBytes ( $str );
// 		$strMd5 = md5 ( $this->toStr ( $byteStr ) );		
// 		$rs =  $rdm1 . substr ( $strMd5, 7, 21 ) . $rdm2;
		$rs =  $rdm1 .  substr( strtolower(md5($str)), 7, 21 ) . $rdm2;
		
		return $rs;
	}

	protected function array2xml($info, &$xml)
	{
		foreach($info as $key => $value){
			if(is_array($value)){
				if(is_numeric($key)){
					$key = array_pop(array_keys($value));
					$value = array_pop($value);
				}
				$subnode = $xml->addChild("{$key}");
				$this->array2xml($value, $subnode);
			}else{
				$xml->addChild("{$key}", htmlspecialchars("$value"));
			}
		}
	} 
	
	public function getXmlContent($arr,$root='ROOT'){
		// creating object of SimpleXMLElement
		
		$xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><{$root}></{$root}>");
		
	
		// function call to convert array to xml
		$this->array2xml($arr, $xml);
	
		// 2. output xml
		$xml = $xml->asXML();
		$xml = trim($xml); 
		return $xml;
	}
	public function OrderXML($clientFlag,$xml,$verifyData){
		
	}

	public function BatchOrderXML($clientFlag,$xmlArr,$verifyData){
	
	} 
	public   function demo(){
		$rdm1 = "01ab"; // 随机数
		$rdm2 = "02zy"; // 随机数
		$clientFlag = "宅急送"; // 客户标识
		$xml = "<zjs>001</zjs>"; // 请求报文
		$strSeed = "01234abcd"; // 客户密钥
		$strConst = "000000"; // 常量值
		                      // 验证数据= 01ab14948b21d74442d9eadd302zy
		
		$str = $rdm1 . $clientFlag . $xml . $strSeed . $strConst . $rdm2;
		$byteStr = $this->getBytes ( $str );
		$strMd5 = strtolower(md5 ( $str ));
		return $rdm1 . substr ( $strMd5, 7, 21 ) . $rdm2;
	
		
	}
}