<?php

class API_Common_ServiceExpressBatchCreateOrders
{
	
    /**
     * @desc 同步物流订单
     * @param array $conditionArr
     * @param int $loop
     */
    public function createOrderToService($formalCode, $server_channelid,$loop=0)
    {
        Common_Common::myEcho('开始同步订单！');
        Common_Common::myEcho('同步中。。。。。。');
        $date = date('Y-m-d H:i:s');

        
        
        //指定渠道
        if (empty($formalCode)) {
        	Common_Common::myEcho('未指定运输方式直接返回');
        	return;
        }
        
        
        /*
         * 1、获取所有需要同步到服务商的订单
        */
         $db2=Common_Common::getAdapterForDb2();
         
         
         $sql="SELECT departbatch_labelcode FROM bsn_departurebatch
         WHERE server_channelid = '{$server_channelid}'
         AND batchstatus_code = 'C'
         AND departbatch_status = 'D';";
         $zongdan=$db2->fetchAll($sql);
    
        //减少执行时间
        if (!$zongdan) {
            Ec::showError("此次请求，无需要同步订单！" . $date, 'express_create_orders_excute');
            Common_Common::myEcho('本次没有需要同步的订单.....over....');
            return;
        }
        
        /*
         * 2、获取订单对应的物流服务商
        */
        $objCommon = new API_Common_ServiceCommonClass();
        $channel = $objCommon->getServiceChannelByFormalCode($formalCode);
        if (empty($channel)) {
        	throw new Exception("无法获取到 [{$formalCode}] 对应的API服务");
        }
        
        $class = $objCommon->getForApiServiceClass($channel['as_code']);
        if (empty($class)) {
        	throw new Exception("无法获取到[{$formalCode}]对应的数据映射类");
        }
        
        if (class_exists($class)) {
        	$obj = new $class();
        } else {
        	throw new Exception("无法获取到[{$class}]对应的数据映射文件类");
        }

       
           // foreach ($synchronousOrder as $key => $val) {	          	
//                 if ($runTime - $theTime > 1700) {
//                     Common_Common::myEcho('执行时间超出限制,强制中断');
//                     return;
//                 }

       foreach ($zongdan as $k=>$v){
                Common_Common::myEcho(print_r($v['departbatch_labelcode'],true));
                try {
                    //设置参数 API代码、订单号
                    $obj->setParam($channel['as_code'], $v['departbatch_labelcode'], $channel['server_channelid'], $channel['server_product_code']);
                    
                    // 当为已同步时间，先判断是否存在删除预报的接口，如果有，先做删除原预报，再重新预报
                   /*  if(true == method_exists($obj, 'deleteForecast') && $val['ops_syncing_status'] == 1) {
                    	$obj->deleteForecast();
                    } */
                    
                    
                    $result = $obj->createAndPreAlertOrderServiceByAllCode();
					print_r($result);
                    // 更新同步结果
                    $status = 'P';
                    $note = "已预报";
					if($result['ack'] == '0') {
						$status = "F";
						$note = $result['error'];
					}                    

                    /*
                     * 4、处理同步结果
                    */
       
				   $table='bsn_departurebatch';
				   $where="departbatch_labelcode='{$result['departbatch_labelcode']}'";
				  
				   $bind=array(
				   		'departbatch_status'=>$status,
				   		'departbatch_note'=>$note
				   );
				   $re=$db2->update($table,$bind,$where);
                   
                   Common_Common::myEcho($val['departbatch_labelcode'] . '处理完成...');
                } catch (Exception $e) {
                	Common_Common::myEcho($val['departbatch_labelcode'] . '处理异常...'.$e->getMessage());
                    Ec::showError("同步未知异常，总单号：" . $v["departbatch_labelcode"] . "异常信息：" . $e->getMessage(), 'express_create_orders_excute');
                }
                $runTime=time();
           // }
        }
        
        Common_Common::myEcho('所有订单同步操作完成.....');
    }
 
    
    
}