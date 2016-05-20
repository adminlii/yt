<?php
/**
 * 公司账户服务类
 * @author Frank
 * @date 2014-2-24 12:07:18
 */
class Common_CompanyAccountProcess{
	/**
	 * 日志文件名
	 * @var unknown_type
	 */
	private static $log_name = 'runCompanyAccountProcess_';
		
	/**
	 * 构造器
	 */
	public function __construct()
	{
		set_time_limit(0);
	}
	
	/**
	 * 调用充值
	 * @param unknown_type $type			调用类型
	 * @param unknown_type $params			参数
	 * @param unknown_type $company_code	公司代码
	 * @param unknown_type $user_id			操作人
	 */
	public function callRechargeApi($type, $params, $company_code, $user_id){
		/*
		 * 1. 检查公司账户状态是否正常
		 */
		$companyAccount = array(
				'company_code'=>$company_code,
				);
		$result_companyAccount = Service_CompanyAccount::getByCondition($companyAccount);
		
		if(!empty($result_companyAccount)){
			//是否启用
			if($result_companyAccount[0]['ca_status'] != 1){
				throw new Exception("公司账户已经停用，请仔细检查登陆账户!");
			}	
		}else{
			throw new Exception("未能找个公司账户信息，公司代码：'$company_code'");
		}
		
		/*
		 * 2. 检查参数是否完整
		 */
		switch(!empty($type)){
			case 'Alipay':
				
			case 'Other':
				
			default:
		}
	}
	
	/**
	 * 检查支付宝参数
	 * @param unknown_type $params
	 */
	private function checkAlipayParams($params){
		/*
		 * 1. 检查金额
		 */
		$amount = $params['amount'];
		
	}
}