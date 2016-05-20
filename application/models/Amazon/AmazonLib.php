<?php
/**
 * amazon接口服务类
 * @author Frank
 * @date 2013-8-17
 */
class Amazon_AmazonLib{
	/**
	 * amazon api版本号
	 * @var unknown_type
	 */
	const SERVICE_VERSION = '2011-01-01';
	
	/**
	 * 应用名称/版本
	 * @var unknown_type
	 */
	const APPLICATION_NAME = 'ECPhpScratchpad';
	const APPLICATION_VERSION = '1.0';
	
	/**
	 * 构造器
	 */
	public function __construct()
	{
		set_time_limit(60);
	}
		
	/**
	 * 获得亚马逊站点的服务地址及商城代码
	 */
	public static function getAmazonConfig(){
		$configArr = array(
				//北美
				'CA'=>array('marketplace_id'=>'A2EUQ1WTGCTBG2'
						,'service_url'=>'https://mws.amazonservices.ca'),
				'US'=>array('marketplace_id'=>'ATVPDKIKX0DER'
						,'service_url'=>'https://mws.amazonservices.com'),
				//欧洲
				'DE'=>array('marketplace_id'=>'A1PA6795UKMFR9'
						,'service_url'=>'https://mws-eu.amazonservices.com'),
				'ES'=>array('marketplace_id'=>'A1RKKUPIHCS9HS'
						,'service_url'=>'https://mws-eu.amazonservices.com'),
				'FR'=>array('marketplace_id'=>'A13V1IB3VIYZZH'
						,'service_url'=>'https://mws-eu.amazonservices.com'),
				'IN'=>array('marketplace_id'=>'A21TJRUUN4KGV'
						,'service_url'=>'https://mws.amazonservices.in'),
				'IT'=>array('marketplace_id'=>'APJ6JRA9NG5V4'
						,'service_url'=>'https://mws-eu.amazonservices.com'),
				'UK'=>array('marketplace_id'=>'A1F83G8C2ARO7P'
						,'service_url'=>'https://mws-eu.amazonservices.com'),
				//远东(叼毛)
				'JP'=>array('marketplace_id'=>'A1VC38T7YXB528'
						,'service_url'=>'https://mws.amazonservices.jp'),
				//中国
				'CN'=>array('marketplace_id'=>'AAHKV2X7AFYLW'
						,'service_url'=>'https://mws.amazonservices.com.cn'),
			);
		return $configArr;
	}
	
	/**
	 * 根据Amazon商城代码获得站点
	 * @param unknown_type $marketplace_id
	 */
	public static function getSiteByMarketplaceId($marketplace_id){
		$configArr = array(
				//北美
				'A2EUQ1WTGCTBG2'=>'CA',
				'ATVPDKIKX0DER'=>'US',
				//欧洲
				'A1PA6795UKMFR9'=>'DE',
				'A1RKKUPIHCS9HS'=>'ES',
				'A13V1IB3VIYZZH'=>'FR',
				'A21TJRUUN4KGV'=>'IN',
				'APJ6JRA9NG5V4'=>'IT',
				'A1F83G8C2ARO7P'=>'UK',
				//远东
				'A1VC38T7YXB528'=>'JP',
				//中国
				'AAHKV2X7AFYLW'=>'CN',
				);
		
		$site = $configArr[$marketplace_id];
		if(empty($site)){
			$site = $marketplace_id;
		}
		return $site;
	}
	
	/**
	 * 检查亚马逊服务运行表
	 * @param unknown_type $interface_name	接口名称
	 * @param unknown_type $seller_id		商户销售ID
	 * @param unknown_type $site			站点
	 * @param unknown_type $request_max		请求上限
	 */
	public static function checkAmazonRunControl($interface_name, $seller_id, $site, $request_max){
		$return = array(
				'ask'=>0,
				'paramId'=>null,
				'message'=>''
				);
		/*
		 * 1. 检查亚马逊控制表，客户是否有相同类型的接口正在运行
		 */
		$con = array(
				'interface_name'=>$interface_name,
				'seller_id'=>$seller_id,
				'site'=>$site,
				'status'=>array('1')
				);
		$result =  Service_AmazonRunControl::getByCondition($con);
		
		/*
		 * 2. 已有正在运行的记录 ,检查请求上限
		 */
		if(!empty($result)){
// 			$countNum = count($result);
// 			if(!($request_max > $countNum)){
// 				$return['message'] = '请求已到达上限';
// 				return $return;
// 			}
		}
		
		/*
		 * 3. 没有达到请求上限，插入记录
		 */
		$row = array(
				'interface_name'=>$interface_name,
				'site'=>$site,
				'seller_id'=>$seller_id,
				'status'=>1,
				'sys_last_mod_date'=>date('Y-m-d H:i:s'),
		);
		$arc_id = Service_AmazonRunControl::add($row);
		
		if($arc_id){
			$return['ask'] = 1;
			$return['paramId'] = $arc_id;
		}else{
			$return['message'] = '插入接口运行数据异常';
		}
		return $return;
	}
	
	/**
	 * 处理亚马逊服务运行表
	 * @param unknown_type $arc_id
	 * @param unknown_type $status
	 * @param unknown_type $message
	 */
	public static function closeAmazonRunControl($arc_id,$status,$message = 'Success'){
		Service_AmazonRunControl::update(array('status'=>$status,
										'sync_message'=>$message,
										'sys_last_mod_date'=>date('Y-m-d H:i:s')), 
										$arc_id,'arc_id');
	}
}