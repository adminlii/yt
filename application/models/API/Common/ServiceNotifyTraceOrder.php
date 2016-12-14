<?php

class API_Common_ServiceNotifyTraceOrder
{    
    /**
     * @desc FBA通知EMS
     * @param array $conditionArr
     * @param int $loop
     */
    public function notifyOrderToServiceFba()
    {
    	Common_Common::myEcho('开始通知订单！');
    	Common_Common::myEcho('通知中。。。。。。');
    	$date = date('Y-m-d H:i:s');
    	$lastdate = date('Y-m-d H:i:s',strtotime("-30 days"));
    
    	/*
    	 * 1、获取所有需要通知到服务商的订单
    	*/
    	$pageSize = 20;
    	$page = 1;
    	$condition = "ems_status=1  and  nextnotify_date<='{$date}' and create_date>='{$lastdate}' ";
    	$sql = 'select count(*) as count from csd_orderfba where '.$condition;
    	$count  = Common_Common::fetchOne($sql);
    	$totalPage = ceil($count / $pageSize);
    	//减少执行时间
    	if ($count == 0) {
    		Ec::showError("此次请求，无需要同步订单！" . $date, 'express_fba_trace_ems_excute');
    		Common_Common::myEcho('本次没有需要同步的订单.....over....');
    		return;
    	}
    
    
    	//调整超时
    	$theTime = $runTime = time();
    	//按页同步订单
    	for ($i = 1; $i <= $totalPage; $i++) {
    		$limit = ($i-1)*$pageSize;
    		$sql = 'select * from csd_orderfba where '.$condition.' limit '.$limit.','.$pageSize;
    		$synchronousOrder = Common_Common::fetchAll($sql);
    		foreach ($synchronousOrder as $key => $val) {
    			Common_Common::myEcho(print_r($val,true));
    			try {
    				$obj = new API_Common_TraceServiceCommonClass();
    				//调用TMS查询物流
    				$openobj = new API_YunExpress_ForApiService();
    				$param = array('server_code'=>$val['shipper_hawbcode'],'channel'=>'YunTu');
    				$trackDeatail = $openobj->gettrackDetail(1, $param);
    				Common_Common::myEcho(print_r($trackDeatail,true));
    				if($trackDeatail['ack']==1){
    					$trackDeatail = json_decode($trackDeatail['data'],1);
    					
    					if(!empty($trackDeatail['Data'])||$trackDeatail['Data']=='null'){
    						$_trackDeatail = $trackDeatail['Data']['TEvent'];
    						//更新主表
    						//1-15天下次通知时间为1天
    						if(time()-strtotime($val['create_date'])<=15*24*3600){
    							$update_csd_orderfba['nextnotify_date'] =  date("Y-m-d H:i:s",strtotime("+4 hours"));
    						}else{
    							$update_csd_orderfba['nextnotify_date'] =  date("Y-m-d H:i:s",strtotime("+1 day"));
    						}
    						if($trackDeatail['Data']['Status']==3){
    							$update_csd_orderfba['nextnotify_date'] = '2999-10-01 00:00:00';
    						} 
    						//如果当前并没有事件
    						if(empty($_trackDeatail)){
    							continue;
    							//break;
    						}
    					}else{
    						continue;
    						//break;
    					} 
    				}else{
    					continue;
    					//break;
    				}
    				//更新主表
    				if(!empty($update_csd_orderfba))
    					$obj->updateFba($val['order_id'],$update_csd_orderfba);
    				//查询物流
    				$lastQquery = $obj->getLastQueryTrace($val['shipper_hawbcode']);
    				//需要推送的消息默认为此次查询的全部
    				$needSendTrace = $_trackDeatail;
    				if(!empty($lastQquery)){
    					//对比物流
    					$lastQqueryTrace = json_decode($lastQquery['msg'],1);
    					Common_Common::myEcho(print_r($lastQqueryTrace,true));
    					$num = count($_trackDeatail)-count($lastQqueryTrace);
    					if($num<=0){
    						//不需要推送
    						continue;
    							//break;;
    					}
    					//更新物流日志操作
    					$obj->updateTrace($val['shipper_hawbcode'], json_encode($_trackDeatail));
    					//对比后赛选出最新的消息
    					$needSendTrace = array_slice($_trackDeatail,0-$num);
    				}else{
    					//插入物流日志
    					$obj->addTrace($val['shipper_hawbcode'], json_encode($_trackDeatail));
    				}
    				
    				//赛选出要推送的event
    				$_errorlist  = array();
    				Common_Common::myEcho(print_r($needSendTrace,true));
    				foreach ($needSendTrace as $traceV){
    					$trace_param='<request>';
    					$trace_param.='<type>I</type>';
    					$trace_param.='<mail_num>'.xml_filter_c($val['shipper_hawbcode']).'</mail_num>';
    					$trace_param.='<order_num>'.xml_filter_c($val['shipper_hawbcode']).'</order_num>';
    					$ckdate = date('Y-m-d H:i:s',strtotime($traceV['Datetime']));
    					$trace_param.='<event_time>'.$ckdate.'</event_time>';
    					$trace_param.='<event_time_zone>+8</event_time_zone>';
    					$trace_param.='<event_status>'.$obj->getEventCode($traceV['Status']).'</event_status>';
    					$localtion = preg_split("/[-,]+/", $traceV['Location']);
    					$trace_param.='<event_country>'.trim($localtion[0]).'</event_country>';
    					$trace_param.='<event_city>'.trim($localtion[1]).'</event_city></request>';
    					$sendEmsRs = $obj->sendEmsApi($val['shipper_hawbcode'],$trace_param);
    					if($sendEmsRs['ack']!=1||$sendEmsRs['data']['success']=='false'){
    						$_errorlist[]=$trace_param;
    					}
    				}
    				//失败插入失败event列表
    				foreach ($_errorlist as $errV){
    					$obj->addErrorlog($val['shipper_hawbcode'], $errV);
    				}
    				//测试
    				//break;
    				Common_Common::myEcho($val['shipper_hawbcode'] . '处理完成...');
    			} catch (Exception $e) {
    				Common_Common::myEcho($val['shipper_hawbcode'] . '处理异常...'.$e->getMessage());
    				Ec::showError("同步未知异常，订单号：" . $val["shipper_hawbcode"] . "异常信息：" . $e->getMessage(), 'express_notify_tnt');
    			}
    			$runTime=time();
    		}
    	}
    
    	Common_Common::myEcho('所有订单同步操作完成.....');
    }
    
/**
     * @desc 错误记录
     * @param array $conditionArr
     * @param int $loop
     */
    public function notifyErrorOrder()
    {
    	Common_Common::myEcho('开始通知订单！');
    	Common_Common::myEcho('通知中。。。。。。');
    	$date = date('Y-m-d H:i:s');
    	//时间增量
    	$time_ext  = array(
    		 5*60,10*60,20*60,30*60,40*60
    	);
    	/* 
    	$time_ext  = array(
    			1*60,1*60,1*60,1*60,1*60
    	); */
    	/*
    	 * 1、获取所有需要通知到服务商的订单
    	*/
    	$pageSize = 20;
    	$page = 1;
    	$nowtime = date('Y-m-d H:i:s');
    	$condition = "notifydate<'{$nowtime}' and num<5";
    	$sql = 'select count(*) as count from notifytrace_errlog where '.$condition;
    	$count  = Common_Common::fetchOne($sql);
    	$totalPage = ceil($count / $pageSize);
    	//减少执行时间
    	if ($count == 0) {
    		//Ec::showError("此次请求，无需要同步订单！" . $date, 'express_fba_trace_ems_excute');
    		Common_Common::myEcho('本次没有需要同步的订单.....over....');
    		return;
    	}
    	//按页同步订单
    	for ($i = 1; $i <= $totalPage; $i++) {
    		$limit = ($i-1)*$pageSize;
    		$sql = 'select * from notifytrace_errlog where '.$condition.' limit '.$limit.','.$pageSize;
    		$synchronousOrder = Common_Common::fetchAll($sql);
    		foreach ($synchronousOrder as $key => $val) {
    			Common_Common::myEcho(print_r($val,true));
    			try {
    				$obj = new API_Common_TraceServiceCommonClass();
    				$msg = $val['msg_content'];
    				$sendEmsRs = $obj->sendEmsApi($val['shipper_hawbcode'],$msg);
    				if($sendEmsRs['ack']!=1||$sendEmsRs['data']['success']=='false'){
    					//修改
    					$num = $val['num'];
    					$nextNotifyTime = date('Y-m-d H:i:s',time()+$time_ext[$num]);
    					Common_Common::myEcho(print_r($nextNotifyTime,true));
    					$obj->updateErrorLog($val['id'],$nextNotifyTime, $num);
    				}else{
    					//成功后就给num赋值为999
    					$obj->updateErrorLog($val['id'],date('Y-m-d H:i:s'),126); //tinyint 最大值127
    				}
    				//测试
    				//break;
    				Common_Common::myEcho($val['shipper_hawbcode'] . '处理完成...');
    			} catch (Exception $e) {
    				Common_Common::myEcho($val['shipper_hawbcode'] . '处理异常...'.$e->getMessage());
    				Ec::showError("同步未知异常，订单号：" . $val["shipper_hawbcode"] . "异常信息：" . $e->getMessage(), 'express_notify_tnt');
    			}
    			$runTime=time();
    		}
    	}
    
    	Common_Common::myEcho('所有订单同步操作完成.....');
    }
    
    /**
     * @desc 其他物流渠道通知EMS
     * @param array $conditionArr
     * @param int $loop
     */
    public function notifyOrderToService($producttype)
    {
    	Common_Common::myEcho('开始通知订单！');
    	Common_Common::myEcho('通知中。。。。。。');
    	$date = date('Y-m-d H:i:s');
    	$lastdate = date('Y-m-d H:i:s',strtotime("-30 days"));
    
    	/*
    	 * 1、获取所有需要通知到服务商的订单
    	*/
    	$pageSize = 20;
    	$page = 1;
    	
    	$condition = "ems_status=1 and formal_code = '{$producttype}'  and  nextnotify_date<='{$date}' and ops_create_date>='{$lastdate}' ";
    	
    	$sql = 'select count(*) as count FROM `order_processing` a left join `csd_order` b on a.order_id = b.order_id where '.$condition;
    	$count  = Common_Common::fetchOne($sql);
    	$totalPage = ceil($count / $pageSize);
    	//减少执行时间
    	if ($count == 0) {
    		Ec::showError("此次请求，无需要同步订单！" . $date, 'express_'.$producttype.'_trace_ems_excute');
    		Common_Common::myEcho('本次没有需要同步的订单.....over....');
    		return;
    	}
    
    
    	//调整超时
    	$theTime = $runTime = time();
    	//按页同步订单
    	for ($i = 1; $i <= $totalPage; $i++) {
    		$limit = ($i-1)*$pageSize;
    		$sql = 'SELECT * FROM `order_processing` a left join `csd_order` b on a.order_id = b.order_id where '.$condition.' limit '.$limit.','.$pageSize;
    		$synchronousOrder = Common_Common::fetchAll($sql);
    		foreach ($synchronousOrder as $key => $val) {
    			Common_Common::myEcho(print_r($val,true));
    			try {
    				if($val['order_status']!='P'){
    					continue;
    				}
    				$obj = new API_Common_TraceServiceCommonClass();
    				//调用TMS查询物流
    				$openobj = new API_YunExpress_ForApiService();
    				$param = array('server_code'=>$val['server_hawbcode'],'channel'=>'YunTu');
    				$productInfo = Common_Common::getProductAllByCode($val['product_code']);
    				$param['channel'] = $productInfo['ccode'];
    				if(!empty($val['small_hawbcode'])){
    					$param['server_code'] = $val['small_hawbcode'];
    				}
    				$trackDeatail = $openobj->gettrackDetail(1, $param);
    				Common_Common::myEcho(print_r($trackDeatail,true));
    				if($trackDeatail['ack']==1){
    					$trackDeatail = json_decode($trackDeatail['data'],1);
    						
    					if(!empty($trackDeatail['Data'])||$trackDeatail['Data']=='null'){
    						$_trackDeatail = $trackDeatail['Data']['TEvent'];
    						//更新主表
    						//1-15天下次通知时间为1天
    						if(time()-strtotime($val['create_date'])<=15*24*3600){
    							$update_csd_order['nextnotify_date'] =  date("Y-m-d H:i:s",strtotime("+4 hours"));
    						}else{
    							$update_csd_order['nextnotify_date'] =  date("Y-m-d H:i:s",strtotime("+1 day"));
    						}
    						if($trackDeatail['Data']['Status']==3){
    							$update_csd_order['nextnotify_date'] = '2999-10-01 00:00:00';
    						}
    						//如果当前并没有事件
    						if(empty($_trackDeatail)){
    							continue;
    							//break;
    						}
    					}else{
    						continue;
    						//break;
    					}
    				}else{
    					continue;
    					//break;
    				}
    				//更新主表
    				if(!empty($update_csd_order)){
    					Service_OrderProcessing::update($update_csd_order, $val['order_id'],'order_id');
    				}
    				//查询物流
    				$lastQquery = $obj->getLastQueryTrace($val['shipper_hawbcode']);
    				//需要推送的消息默认为此次查询的全部
    				$needSendTrace = $_trackDeatail;
    				if(!empty($lastQquery)){
    					//对比物流
    					$lastQqueryTrace = json_decode($lastQquery['msg'],1);
    					Common_Common::myEcho(print_r($lastQqueryTrace,true));
    					$num = count($_trackDeatail)-count($lastQqueryTrace);
    					if($num<=0){
    						//不需要推送
    						continue;
    						//break;;
    					}
    					//更新物流日志操作
    					$obj->updateTrace($val['shipper_hawbcode'], json_encode($_trackDeatail));
    					//对比后赛选出最新的消息
    					$needSendTrace = array_slice($_trackDeatail,0-$num);
    				}else{
    					//插入物流日志
    					$obj->addTrace($val['shipper_hawbcode'], json_encode($_trackDeatail));
    				}
    
    				//赛选出要推送的event
    				$_errorlist  = array();
    				Common_Common::myEcho(print_r($needSendTrace,true));
    				foreach ($needSendTrace as $traceV){
    					$trace_param='<request>';
    					$trace_param.='<type>I</type>';
    					$trace_param.='<mail_num>'.xml_filter_c($val['server_hawbcode']).'</mail_num>';
    					if(!empty($val['small_hawbcode'])){
    						$trace_param.='<order_num>'.xml_filter_c($val['small_hawbcode']).'</order_num>';
    					}else
    						$trace_param.='<order_num>'.xml_filter_c($val['shipper_hawbcode']).'</order_num>';
    					$ckdate = date('Y-m-d H:i:s',strtotime($traceV['Datetime']));
    					$trace_param.='<event_time>'.$ckdate.'</event_time>';
    					$trace_param.='<event_time_zone>+8</event_time_zone>';
    					$trace_param.='<event_status>'.$obj->getEventCode($traceV['Status']).'</event_status>';
    					$localtion = preg_split("/[-,]+/", $traceV['Location']);
    					$trace_param.='<event_country>'.trim($localtion[0]).'</event_country>';
    					$trace_param.='<event_city>'.trim($localtion[1]).'</event_city></request>';
    					$sendEmsRs = $obj->sendEmsApi($val['shipper_hawbcode'],$trace_param);
    					if($sendEmsRs['ack']!=1||$sendEmsRs['data']['success']=='false'){
    						$_errorlist[]=$trace_param;
    					}
    				}
    				//失败插入失败event列表
    				foreach ($_errorlist as $errV){
    					$obj->addErrorlog($val['shipper_hawbcode'], $errV);
    				}
    				//测试
    				//break;
    				Common_Common::myEcho($val['shipper_hawbcode'] . '处理完成...');
    			} catch (Exception $e) {
    				Common_Common::myEcho($val['shipper_hawbcode'] . '处理异常...'.$e->getMessage());
    				Ec::showError("同步未知异常，订单号：" . $val["shipper_hawbcode"] . "异常信息：" . $e->getMessage(), 'express_notify_tnt');
    			}
    			$runTime=time();
    		}
    	}
    
    	Common_Common::myEcho('所有订单同步操作完成.....');
    }
}