
<?php

/**
 * @desc WMS提供给API系统对接
 * @Tips 业务逻辑类
 */
class Common_ApiSvcService
{

    /**
     * @desc 获取需要预报物流系统的订单
     * @param array $params
     * @return array
     */
    public static function loadOrder($params = array())
    {
    	Ec::showError(print_r($params, true), 'loadOrder_condition' . date('Y-m-d'));
        $return = array(
            'ask' => 'Failure',
            'message' => '',
            'data' => array(),
            'count' => 0, //当前请求返回数据数
            'total' => 0, //总条数
        );
        
        $page = isset($params['page']) ? $params['page'] : 1;
        $pageSize = isset($params['pageSize']) ? $params['pageSize'] : 5;
        $smCodeArr = isset($params['sm_code_arr']) ? $params['sm_code_arr'] : array();
        $page = $page ? $page : 1;
        $pageSize = $pageSize ? $pageSize : 5;

        //获取未执行的订单数据
        $condition = array(
            "ops_status" => "0",
            "ops_syncing_status" => "0",
        );

        if (!empty($smCodeArr)) {
            $condition['sm_code_arr'] = $smCodeArr;
        }

//         Ec::showError(print_r($condition, true), 'loadOrder_condition' . date('Y-m-d'));

        /**
         * 1、获取订单数据
         */
        $count = Service_OrderProcessing::getByCondition($condition, "count(*)");
//         Ec::showError($count, 'dataSetByChannelForOrderStatusErr_' . date('Y-m-d'));
        //未换号总数
        $return['total'] = $count;
        $total = 0;
        if ($count) {
            $data = array();
            $processing = Service_OrderProcessing::getByCondition(
                $condition,
                array("order_processing.ops_id", "order_processing.shipper_hawbcode", "order_processing.formal_code","order_processing.ops_count"),
                $pageSize,
                $page,
                array('order_processing.ops_count'));
            
//             Ec::showError("1.--". print_r($processing, true), 'loadOrder_condition' . date('Y-m-d'));
            foreach ($processing as $key => $val) {
                // 判断订单状态
                $orderRow = Service_CsdOrder::getByField($val['shipper_hawbcode'], "shipper_hawbcode", array('order_status'));
//                 Ec::showError(print_r("2.--". $orderRow, true), 'loadOrder_condition' . date('Y-m-d'));
                if ($orderRow['order_status'] != 'S') {
                    Service_OrderProcessing::update(array('ops_status' => 2, 'ops_count' => $val['ops_count'] + 1, 'ops_note' => 'API-Service:订单状态异常[' . $orderRow['order_status'] . ']', 'ops_update_time' => date('Y-m-d H:i:s')), $val['ops_id'], 'ops_id');
                    Common_Common::myEcho("orderStatus:" . $orderRow['order_status']);
                    Ec::showError($val['shipper_hawbcode'] . ' orderStatus  ' . $orderRow['order_status'], 'dataSetByChannelForOrderStatusErr_' . date('Y-m-d'));
                    continue;
                }
                //初始化订单数据
                $rs = self::dataSetByChannel($val["shipper_hawbcode"], $val['formal_code']);
                if ($rs['ask'] != '1') {
                    Common_Common::myEcho(print_r($rs, true));
                    Ec::showError(print_r($rs, true), 'dataSetByChannel_' . date('Y-m-d'));
                    continue;
                } else {
                    //标记任务为处理中
                    Service_OrderProcessing::update(array('ops_syncing_status' => 1, 'ops_count' => $val['ops_count'] + 1, 'ops_update_time' => date('Y-m-d H:i:s')), $val['ops_id'], 'ops_id');
                    $data[] = $rs['data'];
                }
            }
            //当前数据总数
            $total = count($data);
            $return['count'] = $total;
            $return['data'] = $data;
            if ($total) {
                $return['ask'] = "Success";
            }
        }
        return $return;
    }

    /**
     * @desc 获取订单对应的API数据集
     * @param $orderCode
     * @return mixed
     */
    public static function dataSetByChannel($orderCode = '', $formalCode = '')
    {
        $return = array(
            'ask' => '0',
            'message' => '',
            'orderCode' => $orderCode,
            'smCode' => $formalCode,
            'data' => array(),
        );
        
        try {
            $objCommon = new API_Common_ServiceCommonClass();
            $serviceCode = $objCommon->getServiceCodeByFormalCode($formalCode);
            if (empty($serviceCode)) {
                throw new Exception("无法获取到[{$formalCode}]对应的API服务代码");
            }
            //服务对应的数据类
            $class = $objCommon->getForApiServiceClass($serviceCode);
            if (empty($class)) {
                throw new Exception("无法获取到[{$serviceCode}]对应的数据映射类");
            }
            if (class_exists($class)) {
                $obj = new $class();
            } else {
                throw new Exception("无法获取到[{$class}]对应的数据映射文件类");
            }
            //设置参数 API代码、订单号
            $obj->setParam($serviceCode, $orderCode);
            //获取订单数据
            $return['data'] = $obj->getData();
            $return['ask'] = 1;
        } catch (Exception $e) {
            $return['ask'] = 0;
            $return['message'] = $e->getMessage();
        }
        return $return;
    }


    /**
     * @desc 回写跟踪号&标签
     * @param array $params
     * @return array
     */
    public static function backTrackingNo($params = array())
    {
        $return = array(
            'ask' => 'Failure',
            'message' => '',
            'orderNo' => isset($params['orderNo']) ? $params['orderNo'] : '',
        );
        $date = date('Y-m-d H:i:s');
        
        //判断订单是否存在
        $oRow = Service_CsdOrder::getByField($params['orderNo'], 'shipper_hawbcode');
        if (empty($oRow)) {
        	$return['message'] = "订单号不存在";
        	return $return;
        }
        
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try {


            $apiCode = isset($params["apiCode"]) ? $params["apiCode"] : '';
            $smCode = isset($params["smCode"]) ? $params["smCode"] : '';
            $errorCode = isset($params["errorCode"]) ? $params["errorCode"] : '';
            $message = isset($params["message"]) ? $params["message"] : '';
            $error = isset($params["errorMessage"]) ? $params["errorMessage"] : '';
            $error = empty($error) ? $message : $error;
            $trackingNumber = isset($params['data']["trackingNumber"]) ? $params['data']["trackingNumber"] : '';
            $serviceNumber = isset($params['data']["serviceNumber"]) ? $params['data']["serviceNumber"] : '';
            $fileType = isset($params['data']["fileType"]) ? strtolower($params['data']["fileType"]) : 'gif';
            $syncExpressTime = isset($params['data']["syncExpressTime"]) ? strtolower($params['data']["syncExpressTime"]) : $date;
            $apiAsk = isset($params['ask']) ? $params['ask'] : 0;

            /**
             * 3、成功时,保存订单标签文件
             */
            if ($oRow['order_status'] == 'S' && $apiAsk == '1' && !empty($params['data']['label'])) {
            	//标签路径
            	$path = APPLICATION_PATH . "/../data/" . $fileType . '/' . $oRow['shipper_hawbcode'] . '/';
            	//删除文件夹内文件
            	Common_Common::delDirFile($path);
            	//建立标签文件夹
            	Common_Common::mkdirs($path);
            	foreach ($params['data']['label'] as $key => $label) {
            		//按类型保存标签文件
            		file_put_contents($path . $key . '.' . $fileType, base64_decode($label));
            		//如果为PDF,则需要添加任务转为png
            		if ($fileType == 'pdf') {
            			$labelRow = array(
            					"order_code" => $oRow["shipper_hawbcode"],
            					"org_path" => '/data/' . $fileType . '/' . $oRow['shipper_hawbcode'] . '/' . $key . '.pdf',
            					"sm_code" => $oRow["product_code"],
            					"ol_create_date" => date("Y-m-d H:i:s")
            			);
            			Service_OrderLabel::add($labelRow);
            		}
            	}
            }
            
            // 保存日志
            $opl_row = array(
            		'shipper_hawbcode' => $oRow['shipper_hawbcode'],
            		'server_hawbcode' => $serviceNumber,
            		'ops_create_date' => date('Y-m-d H:i:s')
        	);
        	
            // 订单状态异常
            if($oRow['order_status'] != 'S') {
            	$statusArr = Service_OrderProcess::getOrderStatus();
            	$opl_row['ops_note'] = "换号成功，订单状态异常。当前状态为：" . $statusArr[$oRow['order_status']];
            } else if($apiAsk == '1') {
            	$opl_row['ops_note'] = "换号成功";
	            	
            	// 更新订单状态为“已提交预报 ”
	            $order_row = array('modify_date' => date('Y-m-d H:i:s'), 'order_status' => 'P','server_hawbcode' => $serviceNumber);
	            Service_CsdOrder::update($order_row, $oRow['order_id']);
            } else {
            	$opl_row['ops_note'] = "换号失败：" . $error;
            	// 更新订单状态为“问题件”,以及问题保存问题原因
            	$order_row = array('modify_date' => date('Y-m-d H:i:s'), 'order_status' => 'U');
            	Service_CsdOrder::update($order_row, $oRow['order_id']);
            }
            
            $log = array(
            		'ref_id' => $oRow['shipper_hawbcode'],
            		'log_content' => $opl_row['ops_note'],
            		'create_time' => date('Y-m-d H:i:s'),
            		'system' => 'oms',
            		);
            Service_OrderLog::add($log);
            
            // 记录日志
            Service_OrderProcessingLog::add($opl_row);

            //删除任务
            Service_OrderProcessing::delete($oRow["shipper_hawbcode"], 'shipper_hawbcode');


            $db->commit();

            $return['ask'] = 'Success';
            $return['message'] = 'Success';
        } catch (Exception $e) {
            $db->rollBack();
            $return['ask'] = 'Failure';
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
}