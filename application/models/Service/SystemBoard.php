<?php
/**
 * 销售系统面板统计类
 * @author Frank
 * @date 2013-9-23 17:12:09
 *
 */
class Service_SystemBoard  extends Common_Service{
	
	/**
	 * 获得店铺账户
	 */
	public static function getUserAccount($platform){
		
		//查询条件
		$con = array(
				//'company_code' => Common_Company::getCompanyCode(),
				'platform' => $platform,
				'status' => '1',
		);
		//调用查询
		$result = Service_PlatformUser::getByCondition($con, array(
				'user_account','company_code'
		));
		//转换数组
		$userAccountArr = array();
		foreach($result as $rr){
// 			$userAccountArr[] = $rr['user_account'];
			$userAccountArr[] = array(
								'user_account'=>$rr['user_account'],
								'company_code'=>$rr['company_code']
									);
		}
		return $userAccountArr;
	}
	
	/**
	 * ebay消息面板
	 */
	public static function boardToEbayMessage(){
		// 未回复，已回复未到ebay，已回复到ebay，标记回复，时间
		/*
		 * 1. 定义消息状态于默认值，节点与消息状态映射关系，更新时间，面板ID，应用类型，该面板最大记录条数等
		*/
		$defaultStatus = array(
				'0'=>'0',			//未回复
				'1'=>'0',			//未同步到eBay
				'2'=>'0',			//已同步到eBay
				'3'=>'0'			//标记回复
		);
		$messagStateMap = array(
				'MSN1'=>'0',			//未回复
				'MSN2'=>'1',			//未同步到eBay
				'MSN3'=>'2',			//已同步到eBay
				'MSN4'=>'3',			//标记回复
				'MSN5'=>'updateTime',	//更新时间
		);
		$date = date("Y-m-d H:i:s");
		$panelId = '6';
		$appCode = 'eb_message';
		$maxInsertNum = 3;
		
		/*
		 * 2. 拿到数据库连接，查询客户所有账户 Customer Service
		 */
		$table = new DbTable_Orders();
		$db = $table->getAdapter();
		$userAccountArr = self::getUserAccount('ebay');
		
		/*
		 * 3.1. 循环查询，各个账户当前订单的数量
		*/
		$messageNumRow = array();
		foreach ($userAccountArr as $key1 => $value1) {
			$sql = "select count(*) num,t2.response_status from ebay_message t2 ";
			$sql .="where t2.user_account = '" . $value1['user_account'] . "' ";
			$sql .="and t2.company_code = '" . $value1['company_code'] . "' ";
			$sql .="GROUP BY t2.response_status";
				
			$data = $db->fetchAll($sql);
				
			/*
			 * 3.2. 封装各状态
			*/
			$tmpStatusNum = $defaultStatus;
			$tmpStatusNum['user_account'] = $value1['user_account'];
			$tmpStatusNum['company_code'] = $value1['company_code'];
			$tmpStatusNum['updateTime'] = $date;
				
			foreach ($data as $key2 => $value2) {
				$tmpStatusNum[$value2['response_status']] = $value2['num'];
			}
			$messageNumRow[] = $tmpStatusNum;
		}
// 		print_r($messageNumRow);
// 		exit;

		/*
		 * 4. 封装操作统计数据
		*/
		//多个面板的所以数据集
		$panelRows = array();
		foreach ($messageNumRow as $key3 => $value3) {
			//一个面板的所以节点数据集
			$panelRow = array();
			foreach ($messagStateMap as $key4 => $value4) {
				$row = array(
						'os_application_code'=>$appCode,
						'os_node'=>$key4,
						'os_node'=>$key4,
						'os_node_amount'=>$value3[$value4],
						'os_warehouse_id'=>'',
						'company_code'=>$value3['company_code'],
						'os_user_account'=>$value3['user_account'],
						'os_panel_id'=>$panelId,
						'os_date_refresh'=>$date
				);
				$panelRow[] = $row;
			}
			$panelRows[] = $panelRow;
		}
// 		print_r($panelRows);

		/*
		 * 5. 循环插入面板数据
		*/
		foreach ($panelRows as $k => $v) {
			Service_SystemBorderCommonProcess::excuteBoardInfo($v,$maxInsertNum);
		}
	}
	
	/**
	 * 订单面板
	 */
	public static function boardToOrder(){
	
		/*
		 * 1. 定义订单状态于默认值，节点与订单状态映射关系，更新时间，面板ID，应用类型，该面板最大记录条数等
		 */
		$defaultStatus = array(
				'0'=>'0',			//已作废
				'1'=>'0',			//未付款
				'2'=>'0',			//待发货审核
				'3'=>'0',			//待发货
				'4'=>'0',			//已发货
				'5'=>'0',			//冻结中
				'6'=>'0',			//缺货中
				'7'=>'0',			//问题件
				'total'=>'0'		//总数
		);
		$orderStateMap = array(
				'DON1'=>'updateTime',	//更新时间	
				'DON2'=>'total',		//订单总数	
				'DON3'=>'2',			//待发货审核		
				'DON4'=>'3',			//待发货		
				'DON5'=>'4',			//已发货		
				'DON6'=>'6',			//缺货中		
				'DON7'=>'5',			//冻结中		
				'DON8'=>'7',			//问题件		
				'DON9'=>'0',			//已作废		
		);
		$date = date("Y-m-d H:i:s");
		$panelId = '5';
		$appCode = 'order';
		$maxInsertNum = 5;
		
		/*
		 * 2.1 拿到数据库连接，查询客户所有的账户
		 */
		$table = new DbTable_Orders();
		$db = $table->getAdapter();
		$userAccountArr = self::getUserAccount('');
		//print_r($userAccountArr);
		
		
		/*
		 * 3.1. 循环查询，各个账户当前订单的数量
		 */
		$orderNumRow = array();
		foreach ($userAccountArr as $key1 => $value1) {
			/*
			 * 3.2.  判断是否当天是否统计过
			*/
			$sqlCheck = "select * from  os_operating_statistics t6 where t6.company_code = '" . $value1['company_code'] . "' and t6.os_user_account = '" . $value1['user_account'] . "' and t6.os_panel_id = '$panelId' and t6.os_node = 'DON1'";
			$sqlCheck .= "order by t6.os_node_amount desc";
			$dataCheck = $db->fetchAll($sqlCheck);
// 			print_r($sqlCheck);
			
			$nowDate = date('Y-m-d');
			if(!empty($dataCheck)){
				$updateDate = $dataCheck[0]['os_node_amount'];
				if(date('Y-m-d',strtotime($updateDate)) == $nowDate){
					//当天已有数据，不更新
					break;
				}
			}
// 			if(date('Y-m-d H:i:s') < (date('Y-m-d') . ' 22:00:00')){
// 				//时间小于当天22点之前，也不更新
// 				break;
// 			}
// 			$nowDate = '2014-02-13';
			$sql = "select count(*) as num, t.order_status from orders t "; 
			$sql .= "where t.user_account = '" . $value1['user_account'] . "' ";
			$sql .="and t.company_code = '" . $value1['company_code'] . "' ";
			$sql .= "and t.date_create > '$nowDate 00:00:00' ";
			$sql .= "GROUP BY t.order_status";
			
			
			$data = $db->fetchAll($sql);
// 			print_r($data);exit;
			
			/*
			 * 3.3. 封装各状态
			 */
			$tmpStatusNum = $defaultStatus;
			$tmpStatusNum['user_account'] = $value1['user_account'];
			$tmpStatusNum['company_code'] = $value1['company_code'];
			$tmpStatusNum['updateTime'] = date('Y-m-d',strtotime($date));
			$total = 0;
			foreach ($data as $key2 => $value2) {
				$tmpStatusNum[$value2['order_status']] = $value2['num'];
				$total = $total + $value2['num'];
			}
			$tmpStatusNum['total'] = $total;
			$orderNumRow[] = $tmpStatusNum;
		}
// 		print_r($orderNumRow);
// 		exit;
		
		/*
		 * 4. 封装操作统计数据
		 */
		//多个面板的所以数据集
		$panelRows = array();
		foreach ($orderNumRow as $key3 => $value3) {
			//一个面板的所以节点数据集
			$panelRow = array();
			foreach ($orderStateMap as $key4 => $value4) {
				$row = array(
						'os_application_code'=>$appCode,
						'os_node'=>$key4,
						'os_node_amount'=>$value3[$value4],
						'os_warehouse_id'=>'',
						'company_code'=>$value3['company_code'],
						'os_user_account'=>$value3['user_account'],
						'os_panel_id'=>$panelId,
						'os_date_refresh'=>$date
				);
				$panelRow[] = $row;
			}
			$panelRows[] = $panelRow;
		}
// 		print_r($panelRows);
// 		exit;
		/*
		 * 5. 循环插入面板数据
		 */
		foreach ($panelRows as $k => $v) {
			Service_SystemBorderCommonProcess::excuteBoardInfo($v,$maxInsertNum);
		}
	}
	
	/**
	 * 我的销售任务
	 */
	public static function taskToSales(){
		/*
		 * 1. 定义订单状态于默认值，节点与订单状态映射关系，更新时间，面板ID，应用类型，该面板最大记录条数等
		*/
		$defaultStatus = array(
				'2'=>'0',			//待发货审核
				'6'=>'0',			//缺货中
				'7'=>'0',			//问题件
				'messageOrder'=>'0' //留言待处理订单
		);
		$orderStateMap = array(
				'TASKDO1'=>'2',				//待发货审核
				'TASKDO2'=>'6',				//缺货中
				'TASKDO3'=>'7',				//问题件
				'TASKDO4'=>'messageOrder',	//留言待处理订单
		);
		$date = date("Y-m-d H:i:s");
		$panelId = '7';
		$appCode = 'order';
		$maxInsertNum = 1;
		
		/*
		 * 2. 拿到数据库连接，查询客户所有的账户
		*/
		$table = new DbTable_Orders();
		$db = $table->getAdapter();
		$userAccountArr = self::getUserAccount('');
		
		/*
		 * 3.1. 循环查询，各个账户当前订单的数量
		*/
		$orderNumRow = array();
		foreach ($userAccountArr as $key1 => $value1) {
				
			$sql = "select count(*) as num, t.order_status from orders t ";
			$sql .="where t.company_code = '" . $value1['company_code'] . "' and t.user_account = '" . $value1['user_account'] . "' ";
			$sql .="GROUP BY t.order_status";
				
			$data = $db->fetchAll($sql);
			
			//有留言待处理的订单数
			$sqlHasBuyerNote = "select count(*) as num from orders t5 where t5.company_code = '" . $value1['company_code'] . "' and t5.user_account = '" . $value1['user_account'] . "'  and t5.has_buyer_note = '1' and t5.order_status = '2'";
			$dataHasBuyerNote = $db->fetchAll($sqlHasBuyerNote);
			/*
			 * 3.2. 封装各状态
			*/
			$tmpStatusNum = $defaultStatus;
			$tmpStatusNum['user_account'] = $value1['user_account'];
			$tmpStatusNum['company_code'] = $value1['company_code'];
			foreach ($data as $key2 => $value2) {
				if(isset($tmpStatusNum[$value2['order_status']])){
					$tmpStatusNum[$value2['order_status']] = $value2['num'];
				}
			}
			$tmpStatusNum['messageOrder'] = $dataHasBuyerNote[0]['num'];
			$orderNumRow[] = $tmpStatusNum;
		}
// 				print_r($orderNumRow);
		
		/*
		 * 4. 封装操作统计数据
		*/
		//多个面板的所以数据集
		$panelRows = array();
		foreach ($orderNumRow as $key3 => $value3) {
			//一个面板的所以节点数据集
			$panelRow = array();
			foreach ($orderStateMap as $key4 => $value4) {
				$row = array(
						'os_application_code'=>$appCode,
						'os_node'=>$key4,
						'os_node_amount'=>$value3[$value4],
						'os_warehouse_id'=>'',
						'company_code'=>$value3['company_code'],
						'os_user_account'=>$value3['user_account'],
						'os_panel_id'=>$panelId,
						'os_date_refresh'=>$date
				);
				$panelRow[] = $row;
			}
			$panelRows[] = $panelRow;
		}
// 				print_r($panelRows);exit;
		/*
		 * 5. 循环插入面板数据
		*/
		foreach ($panelRows as $k => $v) {
			Service_SystemBorderCommonProcess::excuteBoardInfo($v,$maxInsertNum);
		}
	}
	
	/**
	 * 我的客服任务
	 */
	public static function taskToCustomerService(){
		/*
		 * 1. 定义节点默认数据，更新时间，面板ID，应用类型，该面板最大记录条数等
		*/
		$customerServiceStateMap = array(
				'TASKCS1'=>'0',		//待审核退款
				'TASKCS2'=>'0',		//待回复邮件
		);
		
		$date = date("Y-m-d H:i:s");
		$panelId = '8';
		$appCode = 'eb_message';
		$maxInsertNum = 1;
		
		/*
		 * 2. 拿到数据库连接，查询客户所有的账户
		*/
		$table = new DbTable_RmaOrders();
		$db = $table->getAdapter();
		$userAccountArr = self::getUserAccount('');
		
		/*
		 * 3.1. 循环查询，各个账户客服任务数据
		*/
		$customerServiceNumRow = array();
		foreach ($userAccountArr as $key1 => $value1) {
			
			//待审核退款
			$sqlRma = "select count(*) as num from rma_orders t2 ";
			$sqlRma .= "where t2.rma_ebay_account = '" . $value1['user_account'] . "' and t2.company_code = '" . $value1['company_code'] . "' ";
			$sqlRma .= "and t2.rma_refund_type in (1,0) and t2.rma_status = '1'";
			
			$dataRma = $db->fetchAll($sqlRma);
			$tmpStatusNum['TASKCS1'] = $dataRma[0]['num'];
			
			//待回复消息
			$sqlMessage = "select count(*) as num from ebay_message t3 ";
			$sqlMessage .= "where t3.user_account = '" . $value1['user_account'] . "' and t3.company_code = '" . $value1['company_code'] . "' ";
			$sqlMessage .= "and t3.response_status = '0'";
				
			$dataMessage = $db->fetchAll($sqlMessage);
			$tmpStatusNum['TASKCS2'] = $dataMessage[0]['num'];

			/*
			 * 3.2. 封装各状态
			 */
			$tmpStatusNum['user_account'] = $value1['user_account'];
			$tmpStatusNum['company_code'] = $value1['company_code'];
			$customerServiceNumRow[] = $tmpStatusNum;
		}
// 		print_r($customerServiceNumRow);exit;
		/*
		 * 4. 封装操作统计数据
		*/
		//多个面板的所以数据集
		$panelRows = array();
		foreach ($customerServiceNumRow as $key3 => $value3) {
			//一个面板的所以节点数据集
			$panelRow = array();
			foreach ($customerServiceStateMap as $key4 => $value4) {
				$row = array(
						'os_application_code'=>$appCode,
						'os_node'=>$key4,
						'os_node_amount'=>$value3[$key4],
						'os_warehouse_id'=>'',
						'company_code'=>$value3['company_code'],
						'os_user_account'=>$value3['user_account'],
						'os_panel_id'=>$panelId,
						'os_date_refresh'=>$date
				);
				$panelRow[] = $row;
			}
			$panelRows[] = $panelRow;
		}
// 		print_r($panelRows);
		/*
		 * 5. 循环插入面板数据
		*/
		foreach ($panelRows as $k => $v) {
			Service_SystemBorderCommonProcess::excuteBoardInfo($v,$maxInsertNum);
		}
	}
	
	/**
	 * 我的订单销量图表
	 */
	public static function reportToOrderSaleS(){
		/*
		 * 1. 定义节点默认数据，更新时间，面板ID，应用类型，该面板最大记录条数等
		*/
		$orderSalesStateMap = array(
				'DOS1'=>'0',		//销售订单数量
				'DOS2'=>'0',		//时间
		);
		
		$date = date("Y-m-d H:i:s");
		$panelId = '9';
		$appCode = 'order';
		$maxInsertNum = 15;
		
		/*
		 * 2. 拿到数据库连接，查询客户所有的账户
		*/
		$table = new DbTable_RmaOrders();
		$db = $table->getAdapter();
		$userAccountArr = self::getUserAccount('');
		
		/*
		 * 3.1. 循环查询，各个账户订单销售数量
		*/
		$orderSalesNumRow = array();
		foreach ($userAccountArr as $key1 => $value1) {
			/*
			 * 3.2.  判断是否当天是否统计过
			*/
			$sqlCheck = "select * from  os_operating_statistics t6 where t6.company_code = '" . $value1['company_code'] . "' and t6.os_user_account = '" . $value1['user_account'] . "' and t6.os_panel_id = '$panelId' and t6.os_node = 'DOS2'";
			$sqlCheck .= "order by t6.os_node_amount desc";
			$dataCheck = $db->fetchAll($sqlCheck);
			if(!empty($dataCheck)){
				$updateDate = $dataCheck[0]['os_node_amount'];
				$nowDate = date('m-d');
				if($updateDate == $nowDate){
					//当天已有数据，不更新
					break;
				}
			}
			if(date('Y-m-d H:i:s') < (date('Y-m-d') . ' 22:00:00')){
				//时间小于当天18点之前，也不更新
				break;
			}
			
			//销售订单数量
			$nowDate = date('Y-m-d',strtotime($date));
			$sqlOrder = "select count(*) as num from orders t ";
			$sqlOrder .= "where t.company_code = '" . $value1['company_code'] . "' and t.user_account = '" . $value1['user_account'] . "' ";
			$sqlOrder .= "and t.order_status in (2,3,4,5,6,7) and t.date_create > '$nowDate'";
				
			$dataOrder = $db->fetchAll($sqlOrder);
			$tmpStatusNum['DOS1'] = $dataOrder[0]['num'];
			$tmpStatusNum['DOS2'] = date('m-d',strtotime($date));
			
			/*
			 * 3.2. 封装各状态
			*/
			$tmpStatusNum['company_code'] = $value1['company_code'];
			$tmpStatusNum['user_account'] = $value1['user_account'];
			$orderSalesNumRow[] = $tmpStatusNum;
		}
		// 		print_r($customerServiceNumRow);
		/*
		 * 4. 封装操作统计数据
		*/
		//多个面板的所以数据集
		$panelRows = array();
		foreach ($orderSalesNumRow as $key3 => $value3) {
			//一个面板的所以节点数据集
			$panelRow = array();
			foreach ($orderSalesStateMap as $key4 => $value4) {
				$row = array(
						'os_application_code'=>$appCode,
						'os_node'=>$key4,
						'os_node_amount'=>$value3[$key4],
						'os_warehouse_id'=>'',
						'company_code'=>$value3['company_code'],
						'os_user_account'=>$value3['user_account'],
						'os_panel_id'=>$panelId,
						'os_date_refresh'=>$date
				);
				$panelRow[] = $row;
			}
			$panelRows[] = $panelRow;
		}
// 		print_r($panelRows);
// 		exit;
		/*
		 * 5. 循环插入面板数据
		*/
		foreach ($panelRows as $k => $v) {
			Service_SystemBorderCommonProcess::excuteBoardInfo($v,$maxInsertNum);
		}
	}
}
