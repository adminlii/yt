<?php
/**
 * User: Max
 * Date: 2013-6-28 10:39:16
 */
// set_error_handler('error_function',E_WARNING);
class Ec_AutoRun {
	
	public function init($run_app,$account='',$company_code='') {
		$now = date ( "Y-m-d H:i:s",strtotime('-15 minutes') );
		$con = array (
// 				'last_run_time_more' => $now,
				'run_app'=>$run_app,
				'user_account'=>$account,
				'company_code'=>$company_code,
		        'status'=>'1'
		);
		$rows = Service_RunControl::getByCondition ( $con );
		foreach ( $rows as $key => $row ) {
		    $acc = $row['user_account'];
		    $comp = $row['company_code'];
			$platform = $row ['platform'];
			try{
				$date = date("H:i:s");
				$startTime = $row['start_time'];
				$endTime = $row['end_time'];
				if($date<$startTime||$date>$endTime){//不在时间段之内，抛出异常,程序终止
					throw new Exception('程序不在运行时间段');
				}				
				$condition = array (
						'user_account' => $row ['user_account'],
						'company_code'=>$row['company_code'],
						'app_type' => $row ['run_app'],
						'platform' => $platform,
						'status' => '1'
				);
			    $last_run_time =  strtotime($row['last_run_time'])+$row['run_interval_minute']*60;
				$exist = Service_LoadDataControl::getByCondition ( $condition );
				$return = false;
				// 如果没有同店铺API在运行，则插入记录开始运行
				if ($exist ) { // 如果存在，则返回错误		
				    $exist = array_pop($exist);
				    if(strtotime($exist['run_start_time'])+3600<strtotime(date('Y-m-d H:i:s'))){//运行超过1个小时，重新运行
			            $last_run_time =  strtotime($exist['load_end_time']);
					    $return = $this->$row['run_app']($exist['ldc_id']);//执行方法				        
				    }else{
				        throw new Exception ( '已经有一个任务在运行-->ldc_id:'.$exist['ldc_id'].print_r($condition,true) );
				    }							
				}else{
				    $now = date('Y-m-d H:i:s');
				    $now_time =  strtotime($now);
				    
				    $minites = 15;//当前时间的前15分钟之内的数据不下载
				    if($last_run_time>=$now_time-$minites*60){
				        throw new Exception('运行时间小于当前时间 -'.$minites.' minites');
				    }
				    $load_start_time =date('Y-m-d\TH:i:s',strtotime($row['last_run_time'])-15*60-8*3600);//开始时间
				    $load_end_time = date('Y-m-d\TH:i:s',$last_run_time-8*3600);//结束时间
				    
				    $load_data_control = array (
				            'company_code' => $row ['company_code'],
				            'app_type' => $row ['run_app'],
				            'load_start_time' => $load_start_time,
				            'load_end_time' => $load_end_time,
				            'run_start_time' => date ( "Y-m-d H:i:s" ),
				            'user_account' => $row ['user_account'],
				            'platform' => $platform,
				            'status' => '1',
				            'currt_run_count' => '1'
				    );
				    if (! $loadId=Service_LoadDataControl::add ( $load_data_control )) {
				        throw new Exception ( '插入运行控制失败' );
				    }				    
				    	
				    if(! method_exists ($this,  $row['run_app'] ) ){//判断方法是否实现
				        throw new Exception($row['run_app'].'方法未实现');
				    }
				    $return = $this->$row['run_app']($loadId);//执行方法,方法名称存储于run_control表中的run_app字段
				    Common_ApiProcess::log("Platform:{$platform},CompanyCode:{$comp},UserAccount:{$acc},RunApp:{$row['run_app']}");	
				    Common_ApiProcess::log($return['message']);	
				}
				if ($return&&$return['ask']=='1') {//提示返回成功，更新最后运行时间
				    $last_run_time_now = date ( 'Y-m-d H:i:s', $last_run_time );
				    $updateRow = array (
				            'last_run_time' => $last_run_time_now
				    );
				    if (! Service_RunControl::update ( $updateRow, $row ['run_id'], 'run_id' )) {
				        throw new Exception ( '更新运行控制失败' );
				    }
				}
			}catch (Exception $e){
			    Common_ApiProcess::log("Platform:{$platform},CompanyCode:{$comp},UserAccount:{$acc},Message:".$e->getMessage());
			}
		}
	}

	public function countLoad($loadId,$finsh,$allCount){//公共方法
		$row=Service_LoadDataControl::getByField($loadId, 'ldc_id');
		$load_data_control=array(
				'run_end_time'=>date("Y-m-d H:i:s"),
				'ldc_id'=>$loadId,
				'status'=>$finsh,
				'all_order_count'=>$allCount,
		);
		if($finsh=='1'){
			$load_data_control['currt_run_count']=$row["currt_run_count"]+1;
		}
		Service_LoadDataControl::update($load_data_control,$loadId,'ldc_id');
	}
	
	public function getLoadParam($loadId){//公共方法
		$row=Service_LoadDataControl::getByField($loadId, 'ldc_id');
		return $row;
	}
	/**
	 * @param string $app_type 调用方法
	 * @param string $account 运行账号 为空表示所有账号
	 */
	public  function run($app_type,$account='',$company_code=''){//运行主方法
		return $this->init($app_type,$account,$company_code);
	}
	/**
	 * ebay时间转本地时间
	 * @param unknown_type $ebayTime
	 * @return string
	 */
	public static function getLocalTime($ebayTime){
	    return date("Y-m-d H:i:s",strtotime($ebayTime));
	}
	/**
	 * 本地时间转ebay时间
	 * @param unknown_type $localTime
	 * @return string
	 */
	public static function getEbayTime($localTime){
	    return date("Y-m-d H:i:s",strtotime($localTime)-8*3600);
	}
	
	/**
	 * 日志输出
	 * @param unknown_type $str
	 */
	public static function sapiDebug($str){
	    if(PHP_SAPI=='cli'){
	        echo "[".date('Y-m-d H:i:s')."]".$str."\n";
	    }
	}

	/**
	 * 将null替换为空字符串
	 *
	 * @param unknown_type $arr
	 * @throws Exception
	 * @return string
	 */
	public static function arrayNullToEmptyString($arr)
	{
	    if(! is_array($arr)){
	        throw new Exception('参数非数组');
	    }
	    foreach($arr as $k => $v){
	        if(! isset($v)){
	            $arr[$k] = '';
	        }
	    }
	    return $arr;
	}
}