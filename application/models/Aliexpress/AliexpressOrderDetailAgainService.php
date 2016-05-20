<?php
/**
 * 速卖通-再次检查【未付款，风控等】订单信息并更新原始数据服务
 * @author Frank
 * @date 2014-9-24 15:03:10
 */
class Aliexpress_AliexpressOrderDetailAgainService extends Aliexpress_AliexpressService{
	/**
	 * 日志文件名
	 * @var unknown_type
	 */
	private static $log_name = 'aliexpress_OrderDetail_Again_';
	
	/**
	 * 每次查询订单明细的上限
	 * @var unknown_type
	 */
	private static $PAGE_SIZE_MAX = 150;
	
	/**
	 * 速卖通订单
	 */
	private static $aliexpressOrderRow = array();
	
	/**
	 * 构造器
	 */
	public function __construct()
	{
		set_time_limit(0);
	}
	
	/**
	 * 在AutoRun调用中被调用的方法，自动同步程序的入口
	 * @param unknown_type $loadId
	 */
	public function callOrderDetailAgainQuery($loadId){
		return $this->runOrderDetailAgainQuery($loadId);
	}
	
	/**
	 * Aliexpress 订单明细查询
	 * @see Ec_AutoRun::run()
	 */
	public function runOrderDetailAgainQuery($loadId){
		$i = 1;
		echo $i++ . '、进入服务<br/><br/>';
		
		/*
		 * 1.加载当前同步程序的控制参数
		*/
		$param 		 = $this->getLoadParam($loadId);
		$user_account = $param["user_account"];					//绑定的Aliexpress账户
		$start 		 = $param["load_start_time"];				//开始时间
		$end    	 = $param["load_end_time"];					//结束时间
		$count 		 = $param["currt_run_count"];				//当前运行第几页
		echo $i++ . "、加载任务参数,UserAccount：$user_account ,start: $start ,end：$end <br/><br/>";
		
		/*
		 * 2.查询Aliexpress授权信息
		*/
		echo $i++ . '、查询Aliexpress签名<br/><br/>';
		$result_PlatformUser = Service_PlatformUser::getByField($user_account,'user_account');
		
		if(empty($result_PlatformUser)){
			echo $i++ . "、Aliexpress账户：‘$user_account’ 未查询到签名信息<br/><br/>";
			$errorMessage = "Aliexpress账户：$user_account 未维护签名信息，请维护！";
			Ec::showError($errorMessage, self::$log_name);
			return array (
					'ask' => '0',
					'message' => $errorMessage
			);
		}else if($result_PlatformUser['status'] != 1){
			echo $i++ . "、Aliexpress账户：‘$user_account’ 未生效<br/><br/>";
			$errorMessage = "Aliexpress账户：$user_account 未生效";
			Ec::showError($errorMessage, self::$log_name);
			return array (
					'ask' => '0',
					'message' => $errorMessage
			);
		}
		
		/*
		 * 3.检查Token是否过期
		 * 是：更新，并返回最新授权信息
		 * 否：直接返回
		 */
		echo $i++ . "、检查Token是否过期 <br/><br/>";
		try {
			$result_PlatformUser = self::checkAliexpressToken($result_PlatformUser['pu_id']);
		} catch (Exception $e) {
			//记录Token检查是否异常
			Ec::showError($e->getMessage(), self::$log_name);
			return array (
					'ask' => '0',
					'message' => $e->getMessage()
			);
		}
		
		/*
		 * 4.查询原始订单表，需要查询明显的订单
		 */
		echo $i++ . "、查询需要继续下载明细的订单 <br/><br/>";
		$con_load = array(
				'user_account'=>$user_account,
				'is_loaded'=>'3'
				);
		$result_AliexpressOrders = Service_AliexpressOrderOriginal::getByCondition($con_load, '*', self::$PAGE_SIZE_MAX, 1, 'aoo_id asc');
		
		if(!empty($result_AliexpressOrders) && count($result_AliexpressOrders) > 0){
			/*
			 * 5.组织参数,并调用订单明细查询接口
			*/
			$app_key = $result_PlatformUser['app_key'];
			$app_secret = $result_PlatformUser['app_signature'];
			$access_token = $result_PlatformUser['user_token'];
			
			foreach ($result_AliexpressOrders as $key_o => $value_o) {
				
				$aoo_id = $value_o['aoo_id'];
				$orderId = $value_o['order_id'];
				
				//检查风控和代付款订单，系统创建时间超过12小时才会继续检查订单最新状态
				$curr_order_status = $value_o['order_status'];
				$curr_gmt_create = $value_o['gmt_create'];
				$control_status = array(
						'RISK_CONTROL',
						'PLACE_ORDER_SUCCESS'
				);
				if(in_array($curr_order_status, $control_status)){
					$format = 'Y-m-d H:i:s';
					$date_now = date($format);
					//推算成北京时间
					$curr_gmt_create_beijin = date($format,strtotime("+".Aliexpress_AliexpressLib::$Time_Difference." hour",strtotime($curr_gmt_create)));
					//往后推移14小时
					$curr_gmt_create_beijin_postponed = date($format,strtotime("+".Aliexpress_AliexpressLib::$Protection_Time." hour",strtotime($curr_gmt_create_beijin)));
					if($date_now < $curr_gmt_create_beijin_postponed){
						$tmp_print = array(
								'订单号'=>$orderId,
								'当前状态'=>$curr_order_status,
								'创建时间'=>$curr_gmt_create,
								'创建时间[北京]'=>$curr_gmt_create_beijin,
								'保护时间'=>$curr_gmt_create_beijin_postponed,
								'运行时间'=>$date_now
						);
						echo $i++ . "、订单在保护时间[".Aliexpress_AliexpressLib::$Protection_Time."]内，不予查询订单最新状态 ".print_r($tmp_print,true)."<br/><br/>";
						continue;
					}
				}
				
				$params = array(
						'orderId'=>$orderId,
						'access_token'=>$access_token,
				);
				
				$response = null;
				try {
					$response = Aliexpress_AliexpressLib::getOrderDetailById($app_key, $app_secret, $params);
				} catch (Exception $e) {
					echo $i++ . "、订单明细接口调用异常 <br/><br/>";
					Ec::showError('参数：' . print_r($params) . ' 异常：' . $e->getMessage(), self::$log_name);
				}
				
				//查看接口调用是否成功
				if(isset($response['Status']) && $response['Status']['Code'] == '200' && $response['Responses']['0']['Status']['Code'] == '200'){
					/*
					 * 成功
					 * 	封装参数	
					 */
					$aliexpress_result = $response['Responses']['0']['Result'];
					//封装参数
					echo $i++ . "、封装数据<br/><br/>";
// 					print_r($aliexpress_result);exit;
					$row_detail = Aliexpress_AliexpressOrderDetailService::convertOrderInfo($aoo_id, $aliexpress_result, 'detail_load_again');
					self::$aliexpressOrderRow[$orderId]['order'] = $row_detail;
					self::$aliexpressOrderRow[$orderId]['log'] = $aliexpress_result;
				}else{
					/*
					 * 失败
					 * 	记录日志，并返回
					 */
					echo $i++ . "、调用接口，返回异常信息，详情：". print_r($response,true) ."<br/><br/>";
					$log_message = print_r($response,true);
					Ec::showError($log_message,self::$log_name);
				}
			}
		}else{
			echo $i++ . "、没有订单需要下载明细 <br/><br/>";
		}
		
		/*
		 * 5、检查下载订单数据-->校验重复-->保存-->返回
		*/
		echo $i++ . "、检查数据<br/><br/>";
		$addRowNum = 0;
		if(count(self::$aliexpressOrderRow) > 0){
// 			print_r(self::$aliexpressOrderRow);
			foreach (self::$aliexpressOrderRow as $key_u => $value_u) {
				$order_row = $value_u['order'];
				$aoo_id = $order_row['aoo_id'];
				$order_id = $order_row['order_id'];
				
				$order_log = $value_u['log'];
				unset($order_row['aoo_id']);
				try {
					Service_AliexpressOrderOriginal::update($order_row, $aoo_id);
					$update_log = array(
							'order_code'=>$order_id,
							'content'=>print_r($order_log,true),
							'add_time'=>date('Y-m-d H:i:s')
							);
					Service_AliexpressOrderLog::add($update_log);
					$addRowNum++;
				} catch (Exception $e) {
					echo $i++ . "、订单数据更新异常 ：".$e->getMessage()."<br/><br/>";
					Ec::showError('订单ID：' . $aoo_id . ' 再更新异常：' . $e->getMessage(), self::$log_name);
				}
			}
		}else{
			echo $i++ . '、无数据需要校验<br/><br/>';
		}
		
		//再拉取后的，订单检查，是否更新标准订单
		$obj = new Aliexpress_GenerateSystemOrders();
		$obj->callAliexpressOrdersToSysOrderUpdate($user_account);
		
		/*
		 * 6.  处理完成，更新数据控制表
		*/
		echo $i++ . "、再次更新Aliexpress订单明细服务执行完毕,总计更新数据 $addRowNum 条<br/><br/>";
		$this->countLoad($loadId, 2, $addRowNum);
		return array(
				'ask' => '1',
				'message' => "Aliexpress账户：$user_account,已处理: '$start' ~ '$end' 的订单再次更新任务完成."
		);
		
	}
	
	
}