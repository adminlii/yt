<?php
class Process_BorrowInventory
{
    //产品库存操作类
    protected $_date = '';
    protected $_productArr = array();
    protected $_errorMessage = array();

    //操作类型
    public static $_operationType = array(
        1 => 'borrowed', 		// 借用
        2 => 'sold', 			// 销售(订单审核)
        3 => 'cancel', 			// 取消销售(订单截单)
    	4 => 'return', 			// 退回
    );
    
    /**
     * 操作类型
     * @param string $lang
     * @return array
     */
    public static function operationType($lang = 'zh_CN')
    {
    	$tmp = array(
    			'zh_CN' => array(
				        1 => '借用', 				// 借用
				        2 => '销售(订单审核)', 		// 销售(订单审核)
				        3 => '取消销售(订单截单)', 	// 取消销售(订单截单)
				    	4 => '退回', 				// 退回
    			),
    			'en_US' => array(
				        1 => 'borrowed', 		// 借用
				        2 => 'sold', 			// 销售(订单审核)
				        3 => 'cancel', 			// 取消销售(订单截单)
				    	4 => 'return', 			// 退回
    			)
    	);
    	if($lang == 'auto'){
    		$lang = Ec::getLang();
    	}
    	return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

    public function __construct()
    {
        $this->_date = date('Y-m-d H:i:s');
    }

    /**
     * @param array $params
     * @return array
     */
    private function validator($params = array())
    {
        $row = array(
            'product_id' => 0,
            'quantity' => 0,
            'operationType' => 0,
        	'company_code' => '',
            'warehouse_id' => 0,
            'reference_no' => '', //操作单号
            'application_code' => '', //操作类型
            'note' => '',
        );
        $valid = array('product_id', 'quantity', 'operationType', 'warehouse_id');
        if (!is_array($params) || empty($params)) {
            $this->_errorMessage[] = array('errorCode' => 10001, 'errorMsg' => Ec::Lang('paramsErr'));
            return $row;
        }

        foreach ($row as $key => $val) {
            $row[$key] = isset($params[$key]) ? $params[$key] : '';
        }

        $require = Ec::Lang('require');
        $type = self::$_operationType;
        if (!isset($type[$row['operationType']])) {
            $this->_errorMessage[] = array('errorCode' => 10002, 'errorMsg' => 'operationType ' . $require);
            return $row;
        }
        $warehouse = Common_DataCache::getWarehouse();
        if (!isset($warehouse[$row['warehouse_id']])) {
            $this->_errorMessage[] = array('errorCode' => 10002, 'errorMsg' => 'Warehouse does not exist ' . $require);
            return $row;
        }
        
        $productRow = Service_Product::getByField($row['product_id'], 'product_id');
        if (empty($productRow)) {
            $this->_errorMessage[] = array('errorCode' => 10002, 'errorMsg' => 'Product does not exist ' . $require);
            return $row;
        }
        $this->_productArr = array(
            'product_sku' => $productRow['product_sku'],
            'customer_id' => $productRow['customer_id'],
            'customer_code' => $productRow['customer_code'],
            'product_barcode' => $productRow['product_barcode'],
        	'company_code' => $productRow['company_code'],
        );

        foreach ($valid as $key) {
            if (!isset($row[$key]) || empty($row[$key])) {
                $this->_errorMessage[] = array('errorCode' => 10002, 'errorMsg' => $key . ' ' . $require);
            }
        }
        return $row;
    }


    /**
     * @更新借用库存
     * @param array $params
     * @return array('state','message'=>array())
     * @return array
     * @throws Exception
     */
    public function update($params = array())
    {
        $result = array('state' => 0, 'message' => '', 'error' => array());
        $row = $this->validator($params);
        if (!empty($this->_errorMessage)) {
            $result['error'] = $this->_errorMessage;
            return $result;
        }

        try {
            $isInventory = false;
            $updateRow = array(
                'pbi_borrowed' => 0,
                'pbi_sold' => 0,
                'pbi_unused' => 0,
            	'pbi_return' => 0,
                'product_id' => $row['product_id'],
                'product_sku' => $this->_productArr['product_sku'],
                'warehouse_id' => $row['warehouse_id'],
            	'company_code' => $row['company_code'],
                'from_company_code' => $this->_productArr['company_code'],
            );
            
            $inventoryRow = Service_ProductBorrowInventory::getByWarehouseProduct($row['warehouse_id'], $row['product_id'], $row['company_code']);
            if (!empty($inventoryRow)) {
                $isInventory = true;
                foreach ($updateRow as $key => $val) {
                    if (isset($inventoryRow[$key])) {
                        $updateRow[$key] = $inventoryRow[$key];
                    }
                }
            }
//             print_r($updateRow);die;
            //统一日志
            $addLog = array();
            switch ((int)$row['operationType']) {
                case 1:
                    $updateRow['pbi_borrowed'] = $updateRow['pbi_borrowed'] + $row['quantity'];
                    $updateRow['pbi_unused'] = $updateRow['pbi_unused'] + $row['quantity'];
                    $addLog[] = array(
                        'from_type' => '',
                        'to_type' => 'pbi_unused',
                        'quantity' => $row['quantity']
                    );
                    break;
                case 2:
                    $updateRow['pbi_unused'] = $updateRow['pbi_unused'] - $row['quantity'];
                    $updateRow['pbi_sold'] = $updateRow['pbi_sold'] + $row['quantity'];
                    $addLog[] = array(
                        'from_type' => 'pbi_unused',
                        'to_type' => 'pbi_sold',
                        'quantity' => $row['quantity']
                    );
                    break;
                case 3:
                    $updateRow['pbi_unused'] = $updateRow['pbi_unused'] + $row['quantity'];
                    $updateRow['pbi_sold'] = $updateRow['pbi_sold'] - $row['quantity'];
                    $addLog[] = array(
                        'from_type' => 'pbi_sold',
                        'to_type' => 'pbi_unused',
                        'quantity' => $row['quantity']
                    );
                    break;
                case 4:
                    $updateRow['pbi_return'] = $updateRow['pbi_return'] + $row['quantity'];
                    $updateRow['pbi_unused'] = $updateRow['pbi_unused'] - $row['quantity'];
                    $addLog[] = array(
                        'from_type' => 'pbi_unused',
                        'to_type' => 'pbi_return',
                        'quantity' => $row['quantity']
                    );
                    break;
                default:
                    throw new Exception('Internal error! Type Wrong.', 50000);
                    break;
            }
            
            foreach ($updateRow as $key => $val) {
                if (is_numeric($val) && $val < 0) {
                    throw new Exception('Insufficient Stock!', 50000);
                }
            }
            
            $pbi_id = '';
            if ($isInventory) {
            	$pbi_id = $inventoryRow['pbi_id'];
                if (!Service_ProductBorrowInventory::update($updateRow, $inventoryRow['pbi_id'], 'pbi_id')) {
                    throw new Exception('Internal error! Update Fail.', 50000);
                }
            } else {
            	$updateRow['add_time'] =  $this->_date;
                $pbi_id = Service_ProductBorrowInventory::add($updateRow);
                
                $condition = array("company_code" => $row['company_code'], "product_id" => $row['product_id']);
                $productMap = Service_ProductBorrowMap::getByCondition($condition);
                
                // 为空时增加借用关系
                if(empty($productMap)) {
	                $productMap = array(
	                		'company_code' => $updateRow['company_code'],
	                		'from_company_code' => $updateRow['from_company_code'],
	                		'product_id' => $updateRow['product_id'],
	                		'product_sku' => $updateRow['product_sku'],
	                		'target_product_sku' => 'FX_' . $updateRow['product_sku'],
	                		'sequence' => '',
	                );
	                
	                Service_ProductBorrowMap::add($productMap);
                }
            }
//             print_r($row);die;
            //日志
            $updateRow['reference_no'] = $row['reference_no'];
            $updateRow['application_code'] = $row['application_code'];
            foreach ($addLog as $key => $val) {
            	$updateRow['pbi_id'] = $pbi_id;
                $updateRow['pbil_quantity'] = $val['quantity'];
                $updateRow['pbil_type'] = $row['operationType'];
                $updateRow['from_type'] = $val['from_type'];
                $updateRow['to_type'] = $val['to_type'];
                $updateRow['note'] = $row['note'];
                $updateRow['add_time'] = $this->_date;
                
                if (!Service_ProductBorrowInventoryLog::add($updateRow)) {
                	throw new Exception('Internal error! Update Inventory  Fail.', 50000);
                	return false;
                }
            }

            $result['state'] = 1;
            $result['message'] = 'Success';
        } catch (Exception $e) {
            $result['error'][] = array(
                'errorCode' => $e->getCode(),
                'errorMsg' => $e->getMessage()
            );
        }
        
        return $result;
    }
}