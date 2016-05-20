<?php
class Common_UploadData
{

    public static function readUploadFile($fileName, $filePath,$sheet=0)
    {
        $pathinfo = pathinfo($fileName);
        $fileData = array();

        if (isset($pathinfo["extension"]) && $pathinfo["extension"] == "xls") {
            $fileData = Common_Upload::readEXCEL($filePath,$sheet);
        }
        $result = array();
        $columnMap = array();
        if ($fileData) {
            foreach ($fileData[0] as $key => $value) {
                if (isset($columnMap[$value])) {
                    $fileData[0][$key] = $columnMap[$value];
                }
            }
            foreach ($fileData as $key => $value) {
                if ($key == 0) {
                    continue;
                }
                foreach ($value as $vKey => $vValue) {
                    if ($fileData[0][$vKey] == "") continue;
                    /*     $vValue = htmlspecialchars($vValue);
                     $vValue = str_replace(chr(10), "<br>", $vValue);
                       $vValue = str_replace(chr(32), "&nbsp;", $vValue);*/
                    $result[$key][$fileData[0][$vKey]] = $vValue;
                }
            }
        }
        return $result;
    }

    public static function orderKey()
    {
        $orderKeys = array(
            'TrackingNumber' => '',
            'ReferenceNo' => '',
            'Shipping Method' => '',
            'Country' => '',
            'First Name' => '',
            'Last Name' => '',
            'WarehouseCode' => '',
            'Address1' => '',
            'Address2' => '',
            'City' => '',
            'State/Provice' => '',
            'Postalcode' => '',
            'Email' => '',
            'Company' => '',
            'PhoneNo' => '',
        );
        return $orderKeys;
    }
    
    public static function productRelationKey()
    {
    	$relationKeys = array(
    			'productSKU' => '',
    			'childSKU' => '',
    			'childQuantity' => '',
    	);
    	return $relationKeys;
    }
    
    /**
     * @批量导入产品关联
     * 批量导入产品关联须知，列名见模板头：
     * 1、productSKU必须是现有的产品SKU
     * 2、prProductSKU组合子产品SKU(虚拟SKU)，如果信息存在于产品表（product）,则必须是组合产品类型，如果不存在，则直接录入
     * @param $fileName
     * @param $filePath
     * @return array
     */
    public static function uploadProductRelation($fileName,$filePath,$customerCode = 'EC001',$customerId = '1'){
    	$result = array('state' => 0, 'errorMsg' => array(), 'message' => '');
    	$fileData = self::readUploadFile($fileName, $filePath);
    	if (!isset($fileData[1]) || !is_array($fileData[1])) {
    		$result['errorMsg'] = array('上传失败，无法解析文件内容;');
    		return $result;
    	}
    	
    	$orderData = $orderArr = $smArr = $warehouseArr = $productQtyArr = $errorArr = array();
    	$relationKeys = self :: productRelationKey();
    	
    	$tableKeys = array();
    	$date = date('Y-m-d H:i:s');
    	//获取excl文件数据进行处理
    	foreach ($fileData as $key => $val) {
    		//计算每条记录的列数必须足够3个
    		if (count($val) < 3) {
                $errorArr[] = '第 ' . $key . ' 行,' . '数据异常.';
                continue;
            }
            
            //清除空值，过滤客户上传空数据
            $filerArr = array_filter($val);
            foreach ($relationKeys as $ok => $ov) {
            	$orderData[$key][$ok] = isset($filerArr[$ok]) ? $filerArr[$ok] : '';
            }
            
             //产品sku
             //验证数据，产品sku必须存在
    		$orderProductRow = Service_Product::getByCondition(array('product_sku' => $val['productSKU'], 'customer_code' => $customerCode), '*',1);
            if (empty($orderProductRow)) {
            	$errorArr[] = '第 ' . $key . ' 行,' . '产品SKU(productSKU)：' . $val['productSKU'] . ' 不存在.';
            	continue;
            }
            
            //产品sku必须是普通产品
            if($orderProductRow[0]['product_type'] == '1'){
            	$errorArr[] = '第 ' . $key . ' 行,' . '产品SKU(productSKU)：' . $val['productSKU'] . ' 必须是普通产品.';
            	continue;
            }
            
            //对应数据库表字段赋值
            $orderData[$key]['product_id'] = $orderProductRow[0]['product_id'];
            $orderData[$key]['customer_id'] = $customerId;
            $orderData[$key]['customer_code'] = $customerCode;
            $orderData[$key]['product_sku'] = $val['productSKU'];
            
            $orderData[$key]['product_barcode'] = $orderProductRow[0]['product_barcode'];
            
            //组合产品SKU(虚拟SKU)
            $orderRow = Service_Product::getByCondition(array('product_sku' => $val['prProductSKU'], 'customer_code' => $customerCode), '*',1);
            if (!empty($orderRow)) {
            	//如果该组合产品SKU(虚拟SKU)存在，则必须是组合产品类型
            	if($orderRow[0]['product_type'] == '0'){
            		$errorArr[] = '第 ' . $key . ' 行,' . '产品SKU(prProductSKU)：' . $val['prProductSKU'] . ' 不是组合产品';
            		continue;
            	}
            }
            
            //对应数据库表字段赋值
           $orderData[$key]['pr_product_sku'] = $val['prProductSKU'];
            
            //组合产品数量
            if (!isset($val['prProductQuantity']) || trim($val['prProductQuantity'])=='') {
            	$errorArr[] = '第 ' . $key . ' 行,' . '子产品数量(prProductQuantity) 不能为空.';
            	continue;
            }
            //对应数据库表字段赋值
            $orderData[$key]['pr_quantity'] = $val['prProductQuantity'];
            $orderData[$key]['pr_add_time'] = $date;
    	}
    	
    	if (!empty($errorArr)) {
    		$result['errorMsg'] = $errorArr;
    		return $result;
    	}
    	
    	/*******************数据校验完毕，进行数据录入***********************/
    	$result = self::addProductRelation($orderData);
    	
    	return $result;
    }
    
    /**
     * @批量导入订单入口
     * @param $fileName
     * @param $filePath
     * @param string $customerCode
     * @return array
     */
    public static function uploadOrders($fileName, $filePath, $customerCode = 'EC001')
    {
        $result = array('state' => 0, 'errorMsg' => array(), 'message' => '');
        $fileData = self::readUploadFile($fileName, $filePath);
        if (!isset($fileData[1]) || !is_array($fileData[1])) {
            $result['errorMsg'] = array('上传失败，无法解析文件内容;');
            return $result;
        }
        $orderData = $orderArr = $smArr = $warehouseArr = $productQtyArr = $errorArr = array();
        $orderKeys = self::orderKey();
        foreach ($fileData as $key => $val) {
            if (count($val) < 10) {
                $errorArr[] = '第 ' . $key . ' 行,' . '数据异常.';
                continue;
            }
            //清除空值
            $filerArr = array_filter($val);
            //  $orderData[$key] = $filerArr;
            foreach ($orderKeys as $ok => $ov) {
                $orderData[$key][$ok] = isset($filerArr[$ok]) ? $filerArr[$ok] : '';
            }

            if (!empty($val['ReferenceNo'])) {
                if (!in_array($val['ReferenceNo'], $orderArr)) {
                    $orderArr[] = $val['ReferenceNo'];
                } else {
                    $errorArr[] = '第 ' . $key . ' 行,' . '参考号：' . $val['ReferenceNo'] . ' 不允许重复.';
                    continue;
                }
                //验证数据
                $orderRow = Service_Orders::getByCondition(array('reference_no' => $val['ReferenceNo'], 'customer_code' => $customerCode), '*',1);
                if (!empty($orderRow)) {
                    $errorArr[] = '第 ' . $key . ' 行,' . '参考号：' . $val['ReferenceNo'] . ' 已存在.';
                    continue;
                }
            }

            //国家
            if (!isset($val['Country']) || $val['Country'] == '') {
                $errorArr[] = '第 ' . $key . ' 行,' . 'Country 不能为空.';
                continue;
            }
            $countryCode = substr($val['Country'], 0, 2);
            $countryRow = Service_Country::getByField($countryCode, 'country_code');
            if (empty($countryRow)) {
                $errorArr[] = '第 ' . $key . ' 行,' . 'Country：' . $val['Country'] . ' 不存在.';
                continue;
            }
            $orderData[$key]['country_id'] = $countryRow['country_id'];

            //收件人
            $name = $orderData[$key]['First Name'].$orderData[$key]['Last Name'];
            if (empty($name)) {
                $errorArr[] = '第 ' . $key . ' 行,' . '收件人姓名,不能为空.';
                continue;
            }

            //运输方式
            if (!isset($val['Shipping Method']) || trim($val['Shipping Method'])=='') {
                $errorArr[] = '第 ' . $key . ' 行,' . 'Shipping Method 不能为空.';
                continue;
            }

            if (!isset($smArr[$val['Shipping Method']])) {
                $smRow = Service_ShippingMethod::getByField($val['Shipping Method'], 'sm_code');
                if (empty($smRow)) {
                    $errorArr[] = '第 ' . $key . ' 行,' . 'Shipping Method：' . $val['Shipping Method'] . ' 不存在.';
                    continue;
                }
                $smArr[$val['Shipping Method']] = $smRow['sm_name_cn'];
            }
            //仓库
            if (!isset($val['WarehouseCode']) || $val['WarehouseCode'] == '') {
                $errorArr[] = '第 ' . $key . ' 行,' . 'WarehouseCode 不能为空.';
                continue;
            }
            //地址1
            if (!isset($val['Address1']) || $val['Address1'] == '') {
                $errorArr[] = '第 ' . $key . ' 行,' . 'Address1 不能为空.';
                continue;
            }
            //判断创建
            if (!isset($warehouseArr[$val['WarehouseCode']])) {
                $wRow = Service_Warehouse::getByField($val['WarehouseCode'], 'warehouse_code');
                if (empty($wRow)) {
                    $errorArr[] = '第 ' . $key . ' 行,' . 'WarehouseCode：' . $val['WarehouseCode'] . ' 不存在.';
                    continue;
                }
                $warehouseArr[$val['WarehouseCode']] = $wRow['warehouse_id'];
            }
            $orderData[$key]['warehouse_id'] = $warehouseArr[$val['WarehouseCode']];

            //判断有几个产品
            $keys = array_pop(array_keys($filerArr));
            $keyArr = explode('#', $keys);
            if (!preg_match("/qty/i", $keys) || !isset($keyArr[1]) || !is_numeric($keyArr[1])) {
                $errorArr[] = '第 ' . $key . ' 行,' . '产品数量异常.';
                continue;
            }

            //客户
            $customerRow = Service_Customer::getByField($customerCode, 'customer_code');
            if (empty($customerRow)) {
                $errorArr[] = 'customerCode:' . $customerCode . ' 不存在.';
                continue;
            }
            $orderData[$key]['customer_id'] = $customerRow['customer_id'];
            //内件数
            $orderData[$key]['parcel_quantity'] = $keyArr[1];

/*            $orderData[$key]['order_pick_type'] = $keyArr[1] != '1' || $val['Qty #1'] != '1' ? 1 : 0;
            $orderData[$key]['order_pick_type'] = isset($val['SKU #2']) ? 2 : $orderData[$key]['order_pick_type'];*/
            $skuArr = array(); //订单行不允许重复SKU
            for ($i = 1; $i <= $keyArr[1]; $i++) {
                $sku = isset($val['SKU #' . $i]) ? $val['SKU #' . $i] : '';
                $qty = isset($val['Qty #' . $i]) ? $val['Qty #' . $i] : 0;
                if (empty($sku) || $qty == '0' || !is_numeric($qty) || $qty < 1) {
                    $errorArr[] = '第 ' . $key . ' 行,' . ' SKU 或 Qty 异常.SKU:' . $sku . ' Qty:' . $qty;
                    continue;
                }
                if (!in_array($sku, $skuArr)) {
                    $skuArr[$sku] = $sku;
                } else {
                    $errorArr[] = '第 ' . $key . ' 行,' . ' SKU:' . $sku . ' 重复,请合并数量.';
                    continue;
                }
                $productRows = Service_Product::getByCondition(array('product_sku' => $sku, 'customer_code' => $customerCode), '*', 1);
                if (empty($productRows)) {
                    $errorArr[] = '第 ' . $key . ' 行,' . ' SKU:' . $sku . ' 不存在.';
                    continue;
                }

                //统计各产品数量
                if (!isset($productQtyArr[$sku])) {
                    $productQtyArr[$sku] = array(
                        'product_id' => $productRows[0]['product_id'],
                        'product_sku' => $sku,
                        'qty' => $qty,
                        'warehouse_id' => $orderData[$key]['warehouse_id']
                    );
                } else {
                    $productQtyArr[$sku]['qty'] += $qty;
                }

                $orderData[$key]['order_product'][] = array(
                    'product_sku' => $sku,
                    'op_quantity' => $qty,
                    'product_id' => $productRows[0]['product_id'],
                    'product_barcode' => strtoupper($productRows[0]['product_barcode']),
                    'op_category' => $productRows[0]['pc_id'],
                );
            }

        }

        //  print_r($orderData);
        //print_r($productQtyArr);
        if (!empty($errorArr)) {
            $result['errorMsg'] = $errorArr;
            return $result;
        }

        /*        //验证库存
                $piObj = new Service_ProductInventory();
                foreach ($productQtyArr as $k => $v) {
                    $productInventoryRows = $piObj->getByCondition(array('warehouse_id' => $v['warehouse_id'], 'product_id' => $v['product_id']), '*');
                    if (empty($productInventoryRows) || !isset($productInventoryRows[0]['pi_sellable']) || $productInventoryRows[0]['pi_sellable'] < $v['qty']) {
                        $errorArr[] = 'SKU：' . $v['product_sku'] . ' 库存不足,' . '可用库存数量：' . (isset($productInventoryRows[0]['pi_sellable']) ? $productInventoryRows[0]['pi_sellable'] : 0) . '  全部订单所需库存数量：' . $v['qty'];
                    }
                }
                if (!empty($errorArr)) {
                    $result['errorMsg'] = $errorArr;
                    return $result;
                }*/
        //导入订单数据
        $result = self::addOrders($orderData, $customerCode);
        return $result;
    }

    /**
     * @添加组合产品关联
     * @param array $ordersArr
     * @param string $customerCode
     * @return array
     * @throws Exception
     */
    public static function  addProductRelation($ordersArr = array()){
    	$result = array(
    			'state' => 0,
    			'errorMsg' => array(),
    			'message' => ''
    	);
    	
    	$db = Service_ProductCombineRelation::getModelInstance()->getAdapter();
    	
    	try {
    		$db->beginTransaction();
    		$relationObj = new Service_ProductRelation();
    		foreach ($ordersArr as $key => $val){
    			
    			$productId = $val['productSKU'];
    			$row = array(
                        'product_id' =>$val['product_id'],
                        'customer_id' => $val['customer_id'],
                        'customer_code' => $val['customer_code'],
                        'product_sku' => $val['product_sku'],
                        'product_barcode' => $val['product_barcode'],
                        'pr_product_sku' => $val['pr_product_sku'],
                        'pr_quantity' => $val['pr_quantity'],
                        'pr_add_time' => $val['pr_add_time'],
                    );
    			
    			if (!$relationId = $relationObj->add($row)) {
    				throw new Exception('第 ' . $key . ' 行,组合产品关联失败.');
    			}
    			
    		}
    		$db->commit();
    		$result['state'] = 1;
    		$result['message'] = '成功创建组合产品关联数量：' . count($ordersArr);
    		return $result;
    	} catch (Exception $e) {
    		$db->rollBack();
    		$result['errorMsg'] = $e->getMessage();
    		return $result;
    	}
    	
    }
    
    /**
     * @创建订单
     * @param array $ordersArr
     * @param string $customerCode
     * @return array
     * @throws Exception
     */
    private static function addOrders($ordersArr = array(), $customerCode = 'EC001')
    {
        $result = array(
            'state' => 0,
            'errorMsg' => array(),
            'message' => ''
        );
        $userId = Service_User::getUserId();
        $db = Service_Orders::getModelInstance()->getAdapter();
        try {
            $db->beginTransaction();
            $orderObj = new Service_Orders();
            $orderProductObj = new Service_OrderProduct();
            $addressObj = new Service_OrderAddressBook();
            $date = date('Y-m-d H:i:s');
            $piObj = new Service_ProductInventoryProcess();
            $soId = 0;
            $soCode = '';
            $orderStatus = 4;
            $parcelQuantity = 0;
            foreach ($ordersArr as $key => $orderArr) {
                $orderStatus = 4;
                $parcelQuantity = 0;
                $orderCode = Common_GetNumbers::getCode('order', $data['company_code'], 'TT');
                foreach ($orderArr['order_product'] as $k => $product) {
                    //库存
                    $row = array(
                        'product_id' => $product['product_id'],
                        'quantity' => $product['op_quantity'],
                        'warehouse_id' => $orderArr['warehouse_id'],
                    );
                    $parcelQuantity += $product['op_quantity'];
                    $piRows = Service_ProductInventory::getByCondition($row,'*',1);
                    if (empty($piRows) || !isset($piRows[0]['pi_sellable']) || $piRows[0]['pi_sellable'] < $product['op_quantity']) {
                        $orderStatus = 3;
                    }
                }
                $orderPickType = Service_OrderDispatchProcess::getOrderPickType($orderArr['order_product']);
                $order = array(
                    'order_code' => $orderCode,
                    'customer_id' => $orderArr['customer_id'],
                    'customer_code' => $customerCode,
                    'parcel_quantity' => $parcelQuantity,
                    'warehouse_id' => $orderArr['warehouse_id'],
                    'sm_code' => strtoupper($orderArr['Shipping Method']),
                    'currency_code' => 'USD',
                    'order_status' => $orderStatus,
                    'underreview_status' => $orderStatus == '4' ? 0 : 2,
                    'reference_no' => $orderArr['ReferenceNo'],
                    'order_pick_type' => $orderPickType,
                    'add_time' => $date,
                );
                if (!$orderId = $orderObj->add($order)) {
                    throw new Exception('第 ' . $key . ' 行,创建订单失败.');
                }

                $orderAddress = array(
                    'order_id' => $orderId,
                    'order_code' => $orderCode,
                    'oab_firstname' => $orderArr['First Name'],
                    'oab_lastname' => $orderArr['Last Name'],
                    'oab_company' => $orderArr['Company'],
                    'oab_country_id' => $orderArr['country_id'],
                    'oab_postcode' => $orderArr['Postalcode'],
                    'oab_state' => $orderArr['State/Provice'],
                    'oab_city' => $orderArr['City'],
                    'oab_street_address1' => $orderArr['Address1'],
                    'oab_street_address2' => $orderArr['Address2'],
                    'oab_email' => $orderArr['Email'],
                    'oab_phone' => $orderArr['PhoneNo'],
                );
                if (!$addressObj->add($orderAddress)) {
                    throw new Exception('第 ' . $key . ' 行,创建订单地址失败.');
                }

                //订单节点
                $orderNode = array(
                    'order_id' => $orderId,
                    'order_code' => $orderCode,
                    'oot_code' => 'add',
                    'oon_note' => 'Upload Order',
                    'user_id' => $userId,
                );
                Service_OrderOperationNode::add($orderNode);

                //shipOrder
                if (isset($orderArr['TrackingNumber']) && $orderArr['TrackingNumber'] != '') {
                    $soCode = Common_GetNumbers::getCode('shiporder', $customerCode, 'SP');
                    $orderRow = array(
                        'so_code' => $soCode,
                        'order_id' => $orderId,
                        'order_code' => $orderCode,
                        'warehouse_id' => $orderArr['warehouse_id'],
                        'sm_code' => $orderArr['Shipping Method'],
                        'pp_barcode' => '',
                        'tracking_number' => $orderArr['TrackingNumber'],
                        'so_add_time' => $date,
                    );
                    if (!$soId = Service_ShipOrder::add($orderRow)) {
                        throw new Exception('添加物流信息失败.');
                    }
                }

                foreach ($orderArr['order_product'] as $k => $product) {
                    if ($orderStatus == '4') {
                        //库存
                        $row = array(
                            'product_id' => $product['product_id'],
                            'quantity' => $product['op_quantity'],
                            'operationType' => 5,
                            'warehouse_id' => $orderArr['warehouse_id'],
                            'reference_code' => $orderCode, //操作单号
                            'application_code' => 'submitOrder', //操作类型
                            'note' => ''
                        );
                        $piReturn = $piObj->update($row);
                        if (!isset($piReturn['state']) || $piReturn['state'] != '1') {
                            throw new Exception('SKU:' . $product['product_sku'] . ',可用库存不足.');
                        }
                    }

                    unset($product['product_sku']);
                    $product['order_id'] = $orderId;
                    $product['order_code'] = $orderCode;
                    $product['op_add_time'] = $date;
                    if (!$opId = $orderProductObj->add($product)) {
                        throw new Exception('第 ' . $key . ' 行,创建订单产品:' . $product['product_barcode'] . '失败.');
                    }

                    //shipOrder
                    if ($soId != '' && $soId != '0') {
                        $detailRow = array(
                            'so_id' => $soId,
                            'so_code' => $soCode,
                            'order_id' => $orderId,
                            'order_code' => $orderCode,
                            'op_id' => $opId,
                            'sod_quantity' => $product['op_quantity'],
                            /*                        'op_description' => $val['product_title_en'],
                                                    'cn_description' => $val['product_title'],
                                                    'op_title' => $val['product_title'],
                                                    'hs_code' => isset($val['hs_code']) ? $val['hs_code'] : '',
                                                    'op_unit_price' => $val['product_sales_value'],
                                                    'op_subtotal' => round($val['op_quantity'] * $val['product_sales_value'], 4),
                                                    'op_origin' => '',
                                                    'op_declared_value' => $val['product_declared_value'],*/
                        );
                        if (!Service_ShipOrderDetail::add($detailRow)) {
                            throw new Exception ('添加物流详细失败. ' . $opId);
                        }
                    }
                }

                //订单节点
                $orderNode = array(
                    'order_id' => $orderId,
                    'order_code' => $orderCode,
                    'oot_code' => $orderStatus == '4' ? 'submit' : 'abnormal',
                    'oon_note' => 'Upload Order',
                    'user_id' => $userId,
                );
                Service_OrderOperationNode::add($orderNode);

            }
            $db->commit();
            $result['state'] = 1;
            $result['message'] = '成功导入订单数量：' . count($ordersArr);
            return $result;
        } catch (Exception $e) {
            $db->rollBack();
            $result['errorMsg'] = $e->getMessage();
            return $result;
        }
    }

    /**
     * @desc 导入ASN数据
     * @param $fileName
     * @param $filePath
     * @param string $customerCode
     */
    public static function uploadAsn($fileName, $filePath, $asnRow = array())
    {
        $result = array('state' => 0, 'errorMsg' => array(), 'message' => '');
        $fileData = self::readUploadFile($fileName, $filePath);
        if (!isset($fileData[1]) || !is_array($fileData[1])) {
            $result['errorMsg'] = array('<span style="color:red;">上传失败<span>：无法解析到文件内容;');
            return $result;
        }

        $customerCode = isset($asnRow['customer_code']) ? $asnRow['customer_code'] : '';
        $warehouseId = isset($asnRow['warehouse_id']) ? $asnRow['warehouse_id'] : '';
        if (empty($customerCode)) {
            $result['errorMsg'] = array('customerCode 不能为空.');
            return $result;
        }

        if (empty($warehouseId)) {
            $result['errorMsg'] = array('warehouseCode 不能为空.');
            return $result;
        }

        $customerRow = Service_User::getByField($customerCode, 'company_code');
        if (empty($customerRow)) {
            $result['errorMsg'] = array('customerCode:' . $customerCode . ' 不存在.');
            return $result;
        }

        $asnRow['customer_id'] = $customerRow['user_id'];

        $productArr = $skuArr = array();
        foreach ($fileData as $key => $val) {
            if ((count($val) != 3 && count($val) != 2) || $val['SKU'] == '' || $val['Qty'] == '') {
                $errorArr[] = '第 ' . $key . ' 行,' . '数据异常.';
                continue;
            }

            if (!in_array($val['SKU'], $skuArr)) {
                $skuArr[] = $val['SKU'];
            } else {
                $errorArr[] = 'SKU:' . $val['SKU'] . ' 不允许重复.';
                continue;
            }

            if (!is_numeric($val['Qty']) || $val['Qty'] < 1) {
                $errorArr[] = '第 ' . $key . ' 行,' . 'SKU:' . $val['SKU'] . ',数量异常.';
                continue;
            }

            $productRows = Service_Product::getByCondition(array('product_sku' => $val['SKU'], 'company_code' => $customerCode), array('product_id', 'product_barcode', 'product_is_qc','product_purchase_value','currency_code','product_status'),0);
            if (empty($productRows)) {
                $errorArr[] = '第 ' . $key . ' 行,' . 'SKU:' . $val['SKU'] . ' 不存在.';
                continue;
            }
            //判断开发状态
/*            if($productRows[0]['product_status']!='1'){
                $errorArr[] = 'SKU:' . $val['SKU'] . ' 未完成开发';
                continue;
            }*/

            $smCode = '';
            if (isset($val['ShippingMethod']) && $val['ShippingMethod'] != '' && $asnRow['receiving_type'] == '3') {
                if (!isset($smArr[$val['ShippingMethod']])) {
                    $smRow = Service_ShippingMethod::getByField($val['ShippingMethod'], 'sm_code', array('sm_code'));
                    $smArr[$val['ShippingMethod']] = isset($smRow['sm_code']) ? $smRow['sm_code'] : '';
                }
                if ($smArr[$val['ShippingMethod']] == '') {
                    $errorArr[] = '第 ' . $key . ' 行,' . '运输方式:' . $val['ShippingMethod'] . ' 不存在';
                    continue;
                }
                $smCode = $smArr[$val['ShippingMethod']];
            }
            //获取默认供应商及采购单价
            /**
            $row = Table_ProductDevelop::getInstance()->getDevelopDefaultSupplierId($productRows[0]['product_barcode']);
            if(!empty($row)){
                $spRows=Service_SupplierProduct::getByCondition(array('supplier_id'=>$row['supplier_id'],'pd_id'=>$row['pd_id']),array('sp_unit_price','currency_code'),0,0);
            }
            $unitPrice = isset($spRows[0]['sp_unit_price']) ? $spRows[0]['sp_unit_price'] : $productRows[0]['product_purchase_value'];
            $currencyCode = isset($spRows[0]['currency_code']) ? $spRows[0]['currency_code'] : $productRows[0]['currency_code'];
            $supplierId = isset($row['supplier_id']) ? $row['supplier_id'] : 0;
            **/
//             print_r($productRows);
            $product =  $productRows[0];
            $unitPrice = (!empty($product['product_purchase_value']) ?$product['product_purchase_value']:0);
            $currencyCode = $product['currency_code'];
            $supplierId = 0;
            
            
            $productArr[$key] = array(
                'product_sku' => strtoupper($val['SKU']),
                'product_id' => $product['product_id'],
                'product_barcode' => strtoupper($product['product_barcode']),
                'rd_receiving_qty' => $val['Qty'],
                'is_qc' => $product['product_is_qc'],
                'sm_code' => strtoupper($smCode),
                //成本记录
                'unit_price' => $unitPrice,
                'currency_code' => $currencyCode,
                'supplier_id' => $supplierId,
            );
        }

        if (!empty($errorArr)) {
            $result['errorMsg'] = $errorArr;
            return $result;
        }

        return self::addAsn($asnRow, $productArr);
    }

    /**
     * @desc 创建ASN
     * @param array $ordersArr
     * @param string $customerCode
     */
    private static function addAsn($asnArr = array(), $productArr = array())
    {
        $result = array(
            'state' => 0,
            'errorMsg' => array(),
            'message' => '',
            'asnCode' => ''
        );
        $db = Service_Orders::getModelInstance()->getAdapter();
        $piObj = new Service_ProductInventoryProcess();
        try {
            $db->beginTransaction();
            $code = Common_GetNumbers::getCode('ASN', $asnArr['warehouse_id'], 'R');
            $date = date('Y-m-d H:i:s');
            $asnRow = array(
                'receiving_code' => $code,
                'reference_no' => $asnArr['reference_no'],
                'tracking_number' => $asnArr['tracking_number'],
                'warehouse_id' => $asnArr['warehouse_id'],
                'to_warehouse_id' => $asnArr['to_warehouse_id'],
                'receiving_type' => $asnArr['receiving_type'],
                'customer_id' => $asnArr['customer_id'],
                'customer_code' => $asnArr['customer_code'],
                'receiving_description' => $asnArr['receiving_description'],
                'receiving_add_user' =>Service_User::getUserId(),
                'receiving_status' => '5',
                'receiving_add_time' => $date
            );
            if (!$rId = Service_Receiving::add($asnRow)) {
                throw new Exception('创建ASN失败.');
            }
            $rdObj = new Service_ReceivingDetail();
            $cost=0;
            foreach ($productArr as $key => $item) {
                //获取产品成本
            	$rdc_id = '';
                if ($item['unit_price'] > 0) {
                    $cost++;
                    $currencyRow = Service_Currency::getByField($item['currency_code'], 'currency_code', array('currency_rate'));
                    $itemCost = array(
                        'unit_price' => $item['unit_price'],
                        'supplier_id' => $item['supplier_id'],
                        'currency_code' => $item['currency_code'],
                        'currency_rate' => (isset($currencyRow['currency_rate']) ? $currencyRow['currency_rate'] : 0),
                        'receiving_id' => $rId,
                        'receiving_code' => $code,
                        'reference_no' => $code,
                        'product_id' => $item['product_id'],
                        'product_barcode' => $item['product_barcode'],
                        'quantity' => $item['rd_receiving_qty'],
                        'rdc_add_time' => $date,
                    );
                    $rdc_id = Service_ReceivingDetailCost::add($itemCost);
                }

                $sku = $item['product_sku'];
                unset($item['product_sku'],$item['unit_price'],$item['currency_code'],$item['supplier_id']);
                $item['receiving_id'] = $rId;
                $item['rdc_id'] = $rdc_id;
                $item['receiving_code'] = $code;
                $item['rd_add_time'] = $date;
                
                if (!$rdObj->add($item)) {
                    throw new Exception('创建ASN Item失败,SKU:' . $sku);
                }
                //库存
                $row = array(
                    'product_id' => $item['product_id'],
                    'quantity' => $item['rd_receiving_qty'],
                    'operationType' => 1,
                    'warehouse_id' => $asnRow['warehouse_id'],
                    'reference_code' => $code, //操作单号
                    'application_code' => 'ASN', //操作类型
                    'note' => '',
                	'company_code'=>Common_Company::getCompanyCode()
                );            
                
                $piReturn = $piObj->update($row);
                if (!isset($piReturn['state']) || $piReturn['state'] != '1') {
                    throw new Exception('SKU:' . $sku . ',操作库存失败.');
                }

            }
            //成本表
            if($cost){
                $row=array(
                    'receiving_id' => $rId,
                    'receiving_code' => $code,
                    'rc_add_time' => $date,
                );
                Service_ReceivingCost::add($row);
            }
            $db->commit();
            $result['state'] = 1;
            $result['asnCode'] = $code;
            $result['message'] = '导入成功，<b>ASN单号</b>：<span style="color:#1B9301;">' . $code . '</span>&nbsp;&nbsp;&nbsp;&nbsp;<b>SKU种类</b>：<span style="color:#1B9301;">' . count($productArr) . '</span>';
        } catch (Exception $e) {
            $db->rollBack();
            $result['errorMsg'] = $e->getMessage();
        }
        return $result;
    }


    /**
     * @desc 导入产品数据
     * @param $fileName
     * @param $filePath
     * @param string $customerCode
     */
    public static function uploadProduct($fileName, $filePath, $customerCode = 'EC001')
    {
        $result = array('state' => 0, 'errorMsg' => array(), 'message' => '');
        $fileData = self::readUploadFile($fileName, $filePath);
        if (!isset($fileData[1]) || !is_array($fileData[1])) {
            $result['errorMsg'] = array('上传失败，无法解析文件内容;');
            return $result;
        }

        if (empty($customerCode)) {
            $result['errorMsg'] = array('customerCode 不能为空.');
            return $result;
        }
        $customerCode = strtoupper($customerCode);
        $customerRow = Service_Customer::getByField($customerCode, 'customer_code');
        if (empty($customerRow)) {
            $result['errorMsg'] = array('customerCode:' . $customerCode . ' 不存在.');
            return $result;
        }

        $customerId = $customerRow['customer_id'];
        $pcRows = Service_ProductCategory::getAll();
        $pcArr = array();
        foreach ($pcRows as $val) {
            $pcArr[$val['pc_shortname']] = $val['pc_id'];
        }

        $keys = array(
            'SKU' => '',
            'productTitle' => '',
            'productTitleCn' => '',
            'productCategory' => '',
            'productLength(CM)' => '',
            'productWidth(CM)' => '',
            'productHeight(CM)' => '',
            'productWeight(KG)' => '',
            'productDeclaredValue' => '',
            'hsCode' => '',
        );

        $productArr = $skuArr = array();
        foreach ($fileData as $key => $val) {
            //匹配表头
            foreach ($keys as $k => $v) {
                $val[$k] = isset($val[$k]) ? $val[$k] : '';
            }
            if ($val['SKU'] == '') {
                $errorArr[] = '第 ' . $key . ' 行,' . 'SKU不能为空.';
                continue;
            }

            if (!in_array($val['SKU'], $skuArr)) {
                $skuArr[] = $val['SKU'];
            } else {
                $errorArr[] = 'SKU:' . $val['SKU'] . ' 不允许重复.';
                continue;
            }

            if ($val['productCategory'] == '') {
                $errorArr[] = '第 ' . $key . ' 行,' . '品类不能为空.';
                continue;
            }
            if (!isset($pcArr[$val['productCategory']])) {
                $errorArr[] = '第 ' . $key . ' 行,' . '品类不存在.';
                continue;
            }

            if (!is_numeric($val['productWeight(KG)'])) {
                $errorArr[] = '第 ' . $key . ' 行,' . 'SKU:' . $val['SKU'] . ',重量异常.';
                continue;
            }

            $productRows = Service_Product::getByCondition(array('product_sku' => $val['SKU'], 'customer_code' => $customerCode), array('product_id', 'product_barcode', 'product_is_qc'),2);
            if (!empty($productRows)) {
                $errorArr[] = '第 ' . $key . ' 行,' . 'SKU:' . $val['SKU'] . ' 系统中已存在.';
                continue;
            }
            $productArr[$key] = array(
                'product_sku' => $val['SKU'],
                'product_barcode' => $val['SKU'],
                'customer_id' => $customerId,
                'customer_code' => $customerCode,
                'product_title_en' => $val['productTitle'],
                'product_title' => $val['productTitleCn'],
                'product_receive_status' => 1,
                'currency_code' => 'USD',
                'product_length' => $val['productLength(CM)'],
                'product_width' => $val['productWidth(CM)'],
                'product_height' => $val['productHeight(CM)'],
                'product_weight' => $val['productWeight(KG)'],
                'product_declared_value' => $val['productDeclaredValue'],
                'pc_id' => $pcArr[$val['productCategory']]
            );
            if (isset($val['hsCode']) && $val['hsCode'] != '') {
                $productArr[$key]['hs_code'] = $val['hsCode'];
            }
        }

        if (!empty($errorArr)) {
            $result['errorMsg'] = $errorArr;
            return $result;
        }
        return self::addProduct($productArr);
    }

    /**
     * @desc 创建 产品 事务处理
     * @param array $product
     */
    private static function addProduct($productArr = array())
    {
        $result = array(
            'state' => 0,
            'errorMsg' => array(),
            'message' => ''
        );

        $db = Service_Orders::getModelInstance()->getAdapter();
        $db->beginTransaction();
        try {
            $obj = new Service_ProductProcess();
            //默认添加QC项
            $qcArr[] = array(
                'pqo_id' => 4,
                'pq_detail' => 'Other'
            );
            foreach ($productArr as $key => $val) {
                $return = $obj->createProduct($val, $qcArr);
                if (!isset($return['state']) || $return['state'] != '1') {
                    throw new Exception('SKU:' . $val['product_sku'] . '导入失败.');
                }
            }
            $db->commit();
            $result['state'] = 1;
            $result['message'] = '导入成功,SKU数量：' . count($productArr);
        } catch (Exception $e) {
            $db->rollBack();
            $result['errorMsg'] = $e->getMessage();
        }
        return $result;
    }
    

    /**
     * @desc 导入产品数据
     * @param $fileName
     * @param $filePath
     * @param string $customerCode
     */
    public static function uploadInventoryShare($fileName, $filePath)
    {
    	$result = array('state' => 0, 'errorMsg' => array(), 'message' => '');
    	$fileData = self::readUploadFile($fileName, $filePath);
    	if (!isset($fileData[1]) || !is_array($fileData[1])) {
    		$result['errorMsg'] = array('上传失败，无法解析文件内容;');
    		return $result;
    	}
    
//     	print_r($fileData); die;
    	$companyCode = Service_User::getUserCompanyCode();
    	$keys = array(
    			'SKU' => '',
    			'Warehouse' => '',
    			'Quantity' => '',
    	);
    	
    	$warehouse = array();
    	$wh = Common_DataCache::getWarehouse();
    	foreach($wh as $row) {
    		$warehouse[$row['warehouse_code']] = $row;
    	}
//     	print_r($warehouse); 
    
    	$productArr = $skuArr = $errorArr = array();
    	foreach ($fileData as $key => $val) {
    		//匹配表头
    		foreach ($keys as $k => $v) {
    			$val[$k] = isset($val[$k]) ? $val[$k] : '';
    		}
//     		print_r($val);die;
    		
    		if ($val['Warehouse'] == '') {
    			$errorArr[] = '第 ' . $key . ' 行,' . '仓库不能为空.';
    		}

    		if (!isset($warehouse[$val['Warehouse']])) {
    			$errorArr[] = '第 ' . $key . ' 行,' . '仓库不存在.';
    		}

    		if ($val['Quantity'] == '') {
    			$errorArr[] = '第 ' . $key . ' 行,' . '数量不能为空.';
    		}
    		
    		if (!preg_match('/^[0-9]+$/',$val['Quantity']) || $val['Quantity'] < 1) {
    			$errorArr[] = '第 ' . $key . ' 行,' . '数量必须是大于0的整数.';
    		}
    		
    		if ($val['SKU'] == '') {
    			$errorArr[] = '第 ' . $key . ' 行,' . 'SKU不能为空.';
    			continue;
    		}
    
    		$productRows = Service_Product::getByCondition(array('product_sku' => $val['SKU'], 'company_code' => $companyCode), array('product_id', 'product_barcode', 'product_is_qc'),2);
    		if (empty($productRows)) {
    			$errorArr[] = '第 ' . $key . ' 行,' . 'SKU:' . $val['SKU'] . ' 不存在.';
    			continue;
    		}
    		
    		$productArr[$key] = array(
    				'product_id' => $productRows[0]['product_id'],
    				'warehouse_id' => $warehouse[$val['Warehouse']]['warehouse_id'],
    				'quantity' => $val['Quantity'],
    		);
    	}
    
    	if (!empty($errorArr)) {
    		$result['errorMsg'] = $errorArr;
    		return $result;
    	}
    	
    	$obj = new Service_ProductShare();
    	return $obj->createTransaction($productArr);
    }

}