<?php
/**
 * 数据同步类
 * @author Administrator
 *
 */
class Common_Sync {
	
	private $_client = null;
	private $_domain = '';
	private $_abnormal = 1;
	
	/**
	 * @desc 设置EC Client
	 */
	public function setEcClient()
	{
		$config = Zend_Registry::get('config')->toArray();
		$this->_domain = $config['reset']['ec']['url']. "/default/rest/service";
		$this->_client = new Zend_Rest_Client ($this->_domain );
		$this->_client->getHttpClient()->setConfig(array('keepalive'=>true,'timeout'=>6000));//设定超时
	}
	
	/**
	 * 获得EC Client
	 * @return NULL
	 */
	public function getEcClient(){
		if(empty($this->_client)){
			$this->setEcClient();
		}
		return $this->_client;
	}
	
	/**
	 * 更新EC用户--暂时只做更新密码操作
	 * @param unknown_type $params
	 * @throws Exception
	 * @return multitype:number string NULL Ambigous <string, unknown>
	 */
	public function updateUser($params = array()){
		$result = array('state' => 0, 'message' => '');
		try {
// 			print_r($params);
			$params['check_number'] = 'EC_20140313';
			$params = serialize($params);
			$params = Common_Common::authcode($params, 'CODE');

			try{
				$this->setEcClient();
				$ecClient = $this->getEcClient();
				$return = $ecClient->updateUser($params)->post();
			}catch(Exception $eee){
				throw new Exception($eee->getMessage(),'50000');
			}
		
			if ($return->status == 'success') {
				$return = $return->getIterator()->updateUser;
				$return = Common_Common::objectToArray($return);
				if (isset($return['ask']) && $return['ask'] == '1') {
					$result = $return;
				} else {
					$result['message'] = isset($return['message']) ? $return['message'] : 'API Internal error.1';
				}
		
			} else {
				throw new Exception('API Internal error.');
			}
		} catch (Exception $e) {
			$result['message'] = $e->getMessage();
			$result['err_code'] = $e->getCode();
			Ec::showError(print_r($params, true), 'ERP_TO_EC_Sync_');
			Ec::showError($e->getMessage(), 'ERP_TO_EC_Sync_');
		}
		return $result;
	}
	
	
}

?>