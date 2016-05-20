<?php
class Service_SystemBorderCommonProcess extends Common_Service
{
	
	/**
	 * 统计信息
	 * @param unknown_type $statistics		一个面板的数据
	 * @param unknown_type $maxInsertNum	该面板最大记录条数
	 */
	public static function excuteBoardInfo($statistics = array(), $maxInsertNum = 1){
		$date = date('Y-m-d H:i:s');
		foreach($statistics as $key=>$val){
			/*
			 * 1.检查该节点是否有设置，如果有设置则进行update，反之insert
			 */
			$countdion = array("os_node"=>$val["os_node"],
					"os_panel_id"=>$val["os_panel_id"]);
			
			/*
			 * 2. 因该方法通用，但是电商需要绑定到用户，仓储只需要绑定到仓库，所以需要判断传入的账户信息和仓库信息是否有值
			 */
			if(isset($val["company_code"]) && $val["company_code"] != ""){
				$countdion["company_code"]  = $val["company_code"];
			}
			if(isset($val["os_warehouse_id"]) && $val["os_warehouse_id"] != ""){
				$countdion["os_warehouse_id"]  = $val["os_warehouse_id"];
			}
			if(isset($val["os_user_account"]) && $val["os_user_account"] != ""){
				$countdion["os_user_account"]  = $val["os_user_account"];
			}
			
			$deveing = Service_OsOperatingStatistics::getByCondition($countdion,"*",0,1,array('os_date_refresh asc'));
// 			print_r($deveing);
// 			print("</p>");
// 			exit;
			//结果条数
			$countArray = 0;
			if(!empty($deveing)){
				$countArray = count($deveing);
			}
			
			/*
			 * 3. 如果结果条数小于需要插入的条数则，则进行insert
			 */
			if($countArray < $maxInsertNum){
				//insert
				$addOperation = array(
						"os_application_code"=>$val["os_application_code"],
						"os_node"=>$val["os_node"],
						"os_node_amount"=>$val["os_node_amount"],
						"os_panel_id"=>$val["os_panel_id"],
						"os_date_refresh"=>$val["os_date_refresh"],
						"os_warehouse_id"=>$val["os_warehouse_id"],
						"os_user_account"=>$val["os_user_account"],
						"company_code"=>$val["company_code"],
				);
				Service_OsOperatingStatistics::add($addOperation);
			}else{
				//update
				Service_OsOperatingStatistics::update(array("os_node_amount"=>$val["os_node_amount"],"os_date_refresh"=>$val["os_date_refresh"]),$deveing[0]["os_id"],"os_id");
			}
		}
	}
	
	/*
	 * 获取当日
	 */
	public static function getDate(){
		$date = date('Y-m-d');
		return $date;
	}
}