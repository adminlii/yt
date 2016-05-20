<?php
/**
 * 计费任务服务类
 * @author Frank
 * @date 2014-02-18 13:21:19
 */
class Common_BillingTasksProcess{
	/**
	 * 日志文件名
	 * @var unknown_type
	 */
	private static $log_name = 'runBillingTasks_';
		
	/**
	 * 构造器
	 */
	public function __construct()
	{
		set_time_limit(0);
	}
	
	/**
	 * 将某个时间向前/后推移 XX 天/小时/分钟
	 * @param unknown_type $date	时间
	 * @param unknown_type $date_val 向前推移的时间(+ 先后推移，- 先前推移)
	 * @param unknown_type $date_unit 时间单位(默认：day ,day 天 / hour 小时 / minutes 分钟)
	 * @param unknown_type $format  时间格式(默认：Y-m-d H:i:s)
	 *
	 */
	public static function getChangeTime($date , $date_val , $date_unit = 'day' , $format = 'Y-m-d H:i:s'){
		return date($format,strtotime("$date_val $date_unit",strtotime($date)));
	}
	
	/**
	 * 获取两个时间之间的间隔
	 * @param unknown_type $start 	开始时间
	 * @param unknown_type $end		结束时间
	 * @param unknown_type $type	返回时间类型(M：分钟、S：秒、H：小时、D：天)
	 */
	public static function getIntervalTime($start , $end , $type = 'M'){
		if(strtotime($end) > strtotime($start)){
			$tmpDate = strtotime($end) - strtotime($start);
		}else{
			$tmpDate = strtotime($start) - strtotime($end);
		}
		switch($type){
			case 'S':
				$tmpDate = $tmpDate;
				break;
			case 'M':
				$tmpDate = $tmpDate/60;
				break;
			case 'H':
				$tmpDate = $tmpDate/360;
				break;
			case 'D':
				$tmpDate = $tmpDate/8640;
				break;
			default :
				$tmpDate = 0;
		}
		return $tmpDate;
	}
	
	/**
	 * 传入时间，获得下个账单日（每月出账单）
	 * @param unknown_type $currentBillingDate
	 * @return boolean|string
	 */
	function getNextBillingDate($currentBillingDate,$monCount=1){
		if(!preg_match('/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/',$currentBillingDate,$match)){
			return false;
		}
		$y = $match[1];
		$m = $match[2];
		$d = $match[3];
		$t = date('t', strtotime("{$y}-{$m}-01"));
		$d = $d<$t?$d:$t;
	
		$nextMonthFirstDay = date('Y-m-d', strtotime('+'.$monCount.' month', strtotime($y . '-' . $m . '-01'))); // 下月第一天
		$nextMonth = date('Y-m', strtotime($nextMonthFirstDay)); // 下月第一天
		$nextMonthLastDay = date('t', strtotime($nextMonthFirstDay)); // 下月最后一天
	
		$d = $d < $nextMonthLastDay ? $d : $nextMonthLastDay;
		return $nextMonth . '-' . $d;
	}
	
	/**
	 * 扫描免费时限内的客户计费情况
	 * 若超出了免费时限，修改数据，进入正常计费状态
	 */
	public function checkFreeTimeBillingTasks(){
		/*
		 * 1. 查询条件
		 * 		计费状态：正常
		 * 		是否检查计费：不检查
		 */
		$con = array(
				'btf_status'=>1,
				'btf_is_billing'=>'0'
				);
		$result = Service_BillingTariffPackages::getByCondition($con);
		if(!empty($result)){
			$nowDate = date('Y-m-d H:i:s');
			foreach ($result as $key => $value) {
				
				/*
				 * 2. 获得免费期限日期
				 */
				$addDate = $value['btp_add_date'];
				$freeTime = $value['btp_free_time'];
				$overTime = self::getChangeTime($addDate, $freeTime, 'day');
				
				/*
				 * 3. 对比日期，更改检查时间等
				 */
				$row = $value;
				$row['btp_check_date'] = $nowDate;	//检查时间
				if($nowDate > $overTime){
					//当前时间，已经超过免费时限，修改计费表数据，开始正常检查计费
					$row['btp_is_billing'] = 1;														//正常检查计费
					$recent_billing_date = date('Y-m-d',strtotime(self::getChangeTime($overTime, 1, 'day')));
					$row['btp_recent_billing_date'] = $recent_billing_date;							//最近账单日，初次计费，使用当前的检查时间
					$row['btp_next_billing_date'] = self::getNextBillingDate($recent_billing_date);	//下次账单日
					$row['btp_update_date'] = $nowDate;
				}else{
					//当前时间，还在免费时限内，不做正常检查计费
					//只更新检查时间
				}
				
				/*
				 * 4. 更新动作
				 */
				Service_BillingTariffPackages::update($row, $row['btp_id']);	//更新数据
			}
		}
	}
	
	/**
	 * 扫描正常计费状态下，需要检查计费的客户计费情况
	 */
	public function checkNormalBilling(){
		/*
		 * 1. 查询条件
		 * 		计费状态：正常
		 * 		是否检查计费：检查
		 */
		$con = array(
				'btp_status'=>1,
				'btp_is_billing'=>1
				);
		$result = Service_BillingTariffPackages::getByCondition($con);
		$nowDate = date('Y-m-d H:i:s');
		
		if(!empty($result)){
			foreach ($result as $key => $value) {
				/*
				 * 2. 更新检查时间
				 */
				$btp_id = $value['btp_id'];
				Service_BillingTariffPackages::update(array('btp_check_date'=>$nowDate), $btp_id);
				
				//当前时间超过账单日后，进行计费，出账单
				if($nowDate > $value['btp_next_billing_date']){
					
					$db = Common_Common::getAdapter();
					$db->beginTransaction();
					try{
						/*
						 * 3. 调用出账单动作
						*/
						$return_btpd = self::executeTheBillActions($btp_id);
						if($return_btpd['state'] == 1){
							/*
							 * 4. 执行扣费动作
							 */
							$btpd_id = $return_btpd['paramId'];
							$return_ca = self::executeAccountDeductions($btpd_id);
							if($return_ca['state'] != 1){
								//计费动作不影响出账单的动作
								//throw new Exception("扣费动作失败");
								Ec::showError("[计费->扣费动作失败" . $return_ca['message'], self::$log_name);
							}
							$db->commit();
						}else{
							throw new Exception("出账单动作失败");
						}						
					}catch (Exception $e){
						print_r($e->getMessage());
						$db->rollBack();
						$return['message'] = $e->getMessage();
						Ec::showError("[计费->响应时间：'$nowDate' 计费程序异常，计费ID：$btp_id]==>" . $e->getMessage(), self::$log_name);
					}
				}
			}
		}
	}
	
	/**
	 *  执行出账单动作
	 * @param unknown_type $btp_id
	 */
	private function executeTheBillActions($btp_id){
		$return = array(
				'state'=>0,
				'paramId'=>'',
				'message'=>''
				);
		
		/*
		 * 1. 查询该公司的计费信息
		 */
		$result = Service_BillingTariffPackages::getByField($btp_id);
		$nowDate = date('Y-m-d H:i:s');
		
		if(!empty($result)){
			$tp_id = $result['tp_id'];													//套餐ID
			$received_installation_fee = $result['btp_received_installation_fee'];		//是否已收取初装费
			
			/*
			 * 2. 查询套餐数据
			 */
			$tpRow = Service_TariffPackages::getByField($tp_id);
			if(!empty($tpRow)){
				/*
				 * 3. 组织参数
				 */
				$company_code = $result['company_code'];
				$installation_fee = ($received_installation_fee == 0) ? $tpRow['tp_installation_fee'] : 0;		//初装费
				$maintenance_costs = $tpRow['tp_maintenance_costs'];											//维护费
				$single_ticket_fees = $tpRow['tp_single_ticket_fees'];											//单票费用
				$currency_code = $tpRow['tp_currency_code'];													//币种
				
				$orders_max = $tpRow['tp_orders_end'];															//订单范围结束值(峰值)
				$limit_orders = $tpRow['tp_limit_orders'];														//订单上限比例
				$limit_orders_val = $orders_max + ($orders_max * $limit_orders);								//订单数量上限值
				
				$charge_time_start = $result['btp_recent_billing_date'];										//计费时段--开始
				$charge_time_end = $result['btp_next_billing_date'];											//计费时段--结束
				
				/*
				 * 4. 计算订单是否需要收取费用
				 */
				$count_sql = "SELECT count(*) as num FROM orders t WHERE t.company_code = '$company_code' AND t.order_status IN (3, 4) AND t.date_release > '$charge_time_start' AND t.date_release < '$charge_time_end';";
				$model = Service_BillingTariffPackages::getModelInstance();
				$db = $model->getAdapter();
				$orders_result = $db->fetchAll($count_sql);
				$orders_num = $orders_result[0]['num'];															//计费时段订单数量
				$orders_exceeded = 0;																			//需要收费的订单数量
				$exceed_orders_expenses = 0.00;																	//超出的订单费用
				if($orders_num > $limit_orders_val){
					$orders_exceeded = $orders_num - $limit_orders_val;
					$exceed_orders_expenses = bcmul($orders_exceeded, $single_ticket_fees, 2);
				}
				
				$total = bcadd($installation_fee,$maintenance_costs,2);
				$total = bcadd($total,$exceed_orders_expenses,2);
				
				/*
				 * 5. 插入 / 更新数据
				 */
// 				$db->beginTransaction();
// 				try{
					
					$btpd_code = Common_GetNumbers::getCode("BILL",$company_code,"BILL");
					//计费明细
					$btpdRow = array(
							'tp_id'=>$tp_id,
							'btp_id'=>$btp_id,
							'btpd_code'=>$btpd_code,
							'btpd_status'=>0,
							'btpd_charge_time_start'=>$charge_time_start,
							'btpd_charge_time_end'=>$charge_time_end,
							'btpd_installation_fee'=>$installation_fee,
							'btpd_maintenance_costs'=>$maintenance_costs,
							'btpd_orders_max'=>$orders_max,
							'btpd_limit_orders'=>$limit_orders,
							'btpd_limit_orders_val'=>$limit_orders_val,
							'btpd_orders'=>$orders_num,
							'btpd_single_ticket_fees'=>$single_ticket_fees,
							'btpd_orders_exceeded'=>$orders_exceeded,
							'btpd_exceed_orders_expenses'=>$exceed_orders_expenses,
							'btpd_total_amount'=>$total,
							'tp_currency_code'=>$currency_code,
							'btpd_add_date'=>$nowDate,
					);
					
					$btpd_id = Service_BillingTariffPackagesDetail::add($btpdRow);
					
					//更新计费表
					$btpRow = array(
							'btp_last_billable_date'=>$nowDate,
							'btp_recent_billing_date'=>$result['btp_next_billing_date'],
							'btp_next_billing_date'=>self::getNextBillingDate($result['btp_next_billing_date']),
							'btp_recent_btpd_code'=>$btpd_code,
							'btp_update_date'=>$nowDate
							);
					if($received_installation_fee == 0){
						//是否收取初装费，改为1---不再收取
						$btpRow['btp_received_installation_fee'] = 1;
					}
					Service_BillingTariffPackages::update($btpRow, $btp_id);
					
// 					$db->commit();
					$return['state'] = 1;
					$return['paramId'] = $btpd_id;
// 				}catch (Exception $e){
// 					$db->rollBack();
// 					$return['message'] = $e->getMessage();
// 					Ec::showError("出账单->响应时间：'$nowDate' 计算费用异常，插入明细： " . print_r($btpdRow) . "----更新计费：" . print_r($btpRow), self::$log_name);
// 				}
				
			}else{
				$return['message'] = "未能找到对应的套餐，套餐ID：$tp_id";
				Ec::showError("出账单->响应时间：'$nowDate' 计费ID=>'$btp_id' 未能找到套餐ID=>'$tp_id'的套餐数据", self::$log_name);
			}
		}else{
			$return['message'] = "未能找到对应的计费数据，计费ID：$btp_id";
			Ec::showError("出账单->响应时间：'$nowDate' 未能找到计费ID=>'$btp_id'的计费数据", self::$log_name);
		}
		
		return $return;
	}
	
	/**
	 * 执行账户扣费动作
	 * @param unknown_type $btpd_id		账单ID
	 */
	public function executeAccountDeductions($btpd_id){
		$return = array(
				'state'=>0,
				'paramId'=>'',
				'message'=>''
		);
		
		/*
		 * 1. 查询账单信息
		 */
		$result_btpd = Service_BillingTariffPackagesDetail::getByField($btpd_id);
		$nowDate = date('Y-m-d H:i:s');
		if(!empty($result_btpd)){
			if($result_btpd['btpd_status'] != 0){
				$return['message'] = '账单状态不是：代付款，不能进行扣款';
				return $return;
			}
			/*
			 * 2. 查询账单所属公司
			 */
			$btp_id = $result_btpd['btp_id'];
			$result_btp = Service_BillingTariffPackages::getByField($btp_id);
			
			if(!empty($result_btp)){
				
				/*
				 * 3. 查询公司账户信息（余额）
				 */
				$company_code = $result_btp['company_code'];
				$con_companyAccount = array(
						'company_code'=>$company_code,
						'ca_status'=>1
						);
				 $result_ca = Service_CompanyAccount::getByCondition($con_companyAccount);
				 if(!empty($result_ca)){
				 	/*
				 	 * 4. 判断数据余额，进行扣费，增加账户流水
				 	 */
				 	$total_amount = $result_btpd['btpd_total_amount'];						//总金额
				 	$actually_paid_amount = $result_btpd['btpd_actually_paid_amount'];		//实付金额
				 	$amount_payable = bcsub($total_amount, $actually_paid_amount,2);		//应付金额
				 	$now_balance = $result_ca[0]['ca_balance'];								//账户余额												
				 	
				 	/*
				 	 * 5. 扣费
				 	 * 		a、消减账户余额
				 	 * 		b、增加账户流水
				 	 * 		c、更改账单状态，实付金额，和到账时间 
				 	 */
				 	if($now_balance > 0){
				 		$serial_amount = 0;				//扣款金额
				 		$new_balance = 0;				//账户余额
				 		$is_full_payment = false;		//是否全额付款
				 		if($now_balance > $amount_payable || $now_balance == $amount_payable){
				 			$serial_amount = $amount_payable;
				 			$new_balance = bcsub($now_balance, $amount_payable,2);
				 			$is_full_payment = true;
				 		}else{
				 			$serial_amount = $now_balance;
				 			$new_balance = 0;
				 		}
				 		
				 		$cad_ref_no = $result_btpd['btpd_code'];					//参考号(账单号)
				 		$cad_currency_code = $result_btpd['tp_currency_code'];		//币种
				 		$note = "账单($cad_ref_no)扣款，应扣：$amount_payable" . " $cad_currency_code" . "，实扣金额：$serial_amount" . " $cad_currency_code";
				 		
				 		//新增账户流水
				 		$row_cad = array(
				 			  'ca_id'=>$result_ca[0]['ca_id'],
				              'cad_type'=>2,
				              'cad_amount'=>$serial_amount,
				              'cad_currency_code'=>$cad_currency_code,
				              'cad_ref_no'=>$cad_ref_no,
				              'cad_note'=>$note,
				              'cad_create_id'=>0,
				              'cad_add_date'=>$nowDate,
				 				);
				 		$cad_id = Service_CompanyAccountDetail::add($row_cad);
				 		
				 		//修改账户余额
				 		$row_ca = array(
				 				'ca_balance'=>$new_balance,
				 				'ca_last_update_date'=>$nowDate,
				 				'ca_update_id'=>0
				 				);
				 		Service_CompanyAccount::update($row_ca, $row_cad['ca_id']);
				 		
				 		//修改账单信息
				 		$btpd_status = ($is_full_payment)?1:0;		//账单状态，是否全额付款
				 		$row_btpd = array(
				 				'btpd_actually_paid_amount'=>bcadd($actually_paid_amount,$serial_amount,2),
				 				'btpd_toaccount_date'=>$nowDate,
				 				'btpd_status'=>$btpd_status
				 				);
				 		Service_BillingTariffPackagesDetail::update($row_btpd, $btpd_id);
				 	}
				 	$return['state'] = 1;
				 }else{
				 	$return['message'] = '';
				 	Ec::showError("账户扣费--响应时间：'$nowDate' 未能找到公司账户信息，公司代码=>'$company_code'", self::$log_name);
				 }
			}else{
				$return['messgae'] = "未能找到对应的计费数据，计费ID：'$btp_id'";
				Ec::showError("账户扣费--响应时间：'$nowDate' 未能找到计费ID=>'$btp_id'计费信息", self::$log_name);
			}
		}else{
			$return['message'] = "未能找到对应的账单数据，账单ID：$btpd_id";
			Ec::showError("账户扣费--响应时间：'$nowDate' 未能找到账单ID=>'$btpd_id'的费用明细", self::$log_name);
		}
		
		return $return;
	}
	
}