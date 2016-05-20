<?php
/**
 * API换号工厂类
 * @author Administrator
 *
 */
class API_Common_ChangeNOFactory
{

    /**
     * @desc 预报换号
     * @param $orderCode
     * @return string
     */
    public function changeNOByForecast($orderId, $orderCode, $formalCode,$stringId=array(""),$shipperCode)
    {
        // 返回结果
    	$return = array('ack' => '0', 
    					"type"=> 0,
		    			'message' => '', 
		    			'trackingNumber' => '', 
    			);
    	
    	try {
    		
    		// 获取服务处理类
			//所有渠道都走API_YunExpress_ForApiService
			$channelCode = "YUNEXPRESS";
    		$objCommon = new API_Common_ServiceCommonClass();
    		$channel = $objCommon->getServiceChannelByFormalCode($formalCode);
    		if (empty($channel)) {
    			throw new Exception("无法获取到 [{$formalCode}] 对应的API服务");
    		}
    		
    		// 当换号模式为延时时，插入换号记录由延时服务去换号
    		if($channel['as_docking_mode'] == '1' || $channel['as_docking_mode'] == '2') {
    			 $order_process = array(
    			 		'order_id' => $orderId,
    			 		'server_channelid' => $channel['server_channelid'],
    			    	'shipper_hawbcode' => $shipperCode,
    			    	'formal_code' => $formalCode,
    			    	'ops_create_date' => date('Y-m-d H:i:s'),
    			 );
    			 Service_OrderProcessing::add($order_process);
    			    	
    			 // 返回
    			 $return['trackingNumber'] = $orderCode;
    			 $return['type'] = $channel['as_docking_mode'];
    			 $return['ack'] = 1;
    			 return $return;
    		}
    		
//     		print_r($channel);die;
    		
    		//服务对应的数据类
    		$class = $objCommon->getForApiServiceClass($channel['as_code']);
    		if (empty($class)) {
    			throw new Exception("无法获取到[{$formalCode}]对应的数据映射类");
    		}
    		
    		if (class_exists($class)) {
    			$obj = new $class();
    		} else {
    			throw new Exception("无法获取到[{$class}]对应的数据映射文件类");
    		}
    		
    		//设置参数 API代码、订单号
    		$obj->setParam($channel['as_code'], $orderCode, $channel['server_channelid'], $channel['server_product_code']);
    		$result = $obj->createAndPreAlertOrderServiceByCode();
    		$return['ack'] = $result['ack'];
    		$return['trackingNumber'] = $result['trackingNumber'];
    		$return['message'] = $result['error'];
    		
    	} catch (Exception $e) {
    		$return['ack'] = 0;
    		$return['message'] = $e->getMessage();
    	}
    	
    	return $return;
        
    }
    
    /**
     * 处理异常标签，重新获取标签
     */
    public function processLabelException($orderCode = '', $serviceCode = '') {
    	
    	$condition = array('process_count_less' => 5);
    	if(!empty($orderCode)) {
    		$condition['order_code'] = $orderCode;
    	}
    	if(!empty($serviceCode)) {
    		$condition['service_code'] = $serviceCode;
    	}
    	
    	// 查询需重新下载标签的数据
    	$rows = Service_OrderLabelErr::getByCondition($condition);
    	if(empty($rows)) {
    		echo Common_Common::myEcho('本次未找到需要下载的标签.....over..... \n');
    		return;
    	}
    	
    	// 调用获取标签方法
    	foreach($rows as $k => $row) {
    		
    		$objCommon = new API_Common_ServiceCommonClass();
	    	$class = $objCommon->getForApiServiceClass($row['service_code']);
	    	$obj = new $class();
//     		print_r($row);
//     		print_r($class);
//     		die;
    		
	    	$result = $obj->getLabel($row['tracking_code'], $row['order_code']);
	    	if($result['state'] == '1') {
    			// 成功删除数据
	    		Service_OrderLabelErr::delete($row['ole_id']);
	    		Common_Common::myEcho($row['order_code'] . ' 标签下载成功 \n');
	    	} else {
	    		$update_row = array(
	    				'modify_date' => date('Y-M-D h:i:s'), 
	    				'process_count' => $row['process_count'] + 1, 
	    				'note' => $result['message'], 
	    				);
	    		// 失败更新处理次数
	    		Service_OrderLabelErr::update($update_row, $row['ole_id']);
	    		Common_Common::myEcho($row['order_code'] . ' 标签下载失败 \n');
	    	}
    	}
    }
    
    /**
     * @desc @desc 自动将pdf转png (使用外部服务器)
     * @param string $smCode
     */
    public static function autoPdf2png($content, $path)
    {
	    	// 获取图片转换路径
	    	$configRow = Service_Config::getByField('SVC-FOR-PDF2PNG-URL', 'config_attribute');
	    	if (empty($configRow)) {
	    		throw new Exception('pdf2png-URL未配置');
	    	}
	    	
	    	$url = $configRow['config_value'];
	    	//可配置多个URL
	    	$urlArr = array_filter(explode(';', $url));
	    	
	    	// 取最后一个URL
	    	$url = array_pop($urlArr);
	    	
	    	//创建文件夹
	    	Common_Common::mkdirs($path);
	    	$base64_content = base64_encode($content);
	    	//echo $_abs_png_fold_path."\r\n";
	    	$rs = Common_Common::curlRequest($url, $base64_content);
	    	
// 	    	print_r($rs);die;
	    	if (strtoupper($rs['Ack']) == 'SUCCESS') {
	    		$png_arr = $rs ['png_base64_arr'];
	    		foreach ($png_arr as $key => $png) {
	    			file_put_contents($path . "/" . $key . ".png",  base64_decode($png['base64']));
	    		}
	    	} else {
	    		throw new Exception($rs['message']);
	    	}
    }
    
}