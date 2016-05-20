<?php
class Service_ProductInventoryProcess
{
    //产品库存操作类
    protected $_date = '';
    protected $_productArr = array();
    protected $_errorMessage = array();

    //操作类型
    public static $_operationType = array(
        1 => 'onWay', // 创建ASN
        2 => 'pending', // 收货
        3 => 'sellable', // 上架
        4 => 'unsellable', // 问题数量
        5 => 'reserved', // 冻结增加 可用减少
        6 => 'shipped', // 已出货增加 冻结减少
        7 => 'stopOrder', // 可用增加 冻结减少
        8 => 'stopAsn', // 在途减少
        9 => 'deleteDagOrder', // 将已装袋的订单删除、发货数量减少 可用数量增加
        10 => 'onWayForSellable', //快捷收货并上架
        11 => 'unsellableForDestruction', //销毁不良品
        12 => 'unsellableForReserved', //不良品For冻结 创建退货订单
        13 => 'unsellableForPending', //不良品For收货 创建特采重新上架
        14 => 'plannedForDel', //删除计划库存
        15 => 'plannedForonWay', //计划库存 在途同步处理
        16 => 'planned', //添加计划库存
    );

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
            'customQty' => 0, //用于其它
            'unsellable' => 0, //问题数量
            'operationType' => 0,
            'warehouse_id' => 0,
            'reference_code' => '', //操作单号
            'application_code' => '', //操作类型
            'note' => '',
        	'company_code'=>$params['company_code']
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
        );

        foreach ($valid as $key) {
            if (!isset($row[$key]) || empty($row[$key])) {
                $this->_errorMessage[] = array('errorCode' => 10002, 'errorMsg' => $key . ' ' . $require);
            }
        }
        return $row;
    }


    /**
     * @更新库存
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
                'pi_planned' => 0,
                'pi_onway' => 0,
                'pi_pending' => 0,
                'pi_sellable' => 0,
                'pi_unsellable' => 0,
                'pi_reserved' => 0,
                'pi_shipped' => 0,
                'product_id' => $row['product_id'],
                'product_barcode' => $this->_productArr['product_barcode'],
                'warehouse_id' => $row['warehouse_id'],
                'customer_id' => $this->_productArr['customer_id'],
            	'company_code'=>$row['company_code']
            );
            $inventoryRow = Table_ProductInventory::getInstance()->getByWhProduct($row['warehouse_id'], $row['product_id']);
            if (!empty($inventoryRow)) {
                $isInventory = true;
                foreach ($updateRow as $key => $val) {
                    if (isset($inventoryRow[$key])) {
                        $updateRow[$key] = $inventoryRow[$key];
                    }
                }
            }

            //统一日志
            $addLog = array();
            switch ((int)$row['operationType']) {
                case 1:
                    $updateRow['pi_onway'] = $updateRow['pi_onway'] + $row['quantity'];
                    $addLog[] = array(
                        'from_it_code' => '',
                        'to_it_code' => 'onway',
                        'quantity' => $row['quantity']
                    );
                    break;
                case 2:
                    $updateRow['pi_onway'] = $updateRow['pi_onway'] - $row['customQty'];
                    $updateRow['pi_pending'] = $updateRow['pi_pending'] + $row['quantity'];
                    if ($row['customQty'] != $row['quantity']) {
                        $addLog[] = array(
                            'from_it_code' => 'onway',
                            'to_it_code' => 'onway',
                            'quantity' => $row['customQty']
                        );
                    }
                    $addLog[] = array(
                        'from_it_code' => 'onway',
                        'to_it_code' => 'pending',
                        'quantity' => $row['quantity']
                    );
                    if ($row['unsellable'] != 0) {
                        $updateRow['pi_unsellable'] = $updateRow['pi_unsellable'] + $row['unsellable'];
                        $addLog[] = array(
                            'from_it_code' => 'pending',
                            'to_it_code' => 'unsellable',
                            'quantity' => $row['unsellable']
                        );
                    }

                    break;
                case 3:
                    $updateRow['pi_sellable'] = $updateRow['pi_sellable'] + $row['quantity'];
                    $updateRow['pi_pending'] = $updateRow['pi_pending'] - $row['quantity'];
                    $addLog[] = array(
                        'from_it_code' => 'pending',
                        'to_it_code' => 'sellable',
                        'quantity' => $row['quantity']
                    );
                    //插入补货点
                    $this->addProductInventoryChangesNodes($updateRow['warehouse_id'],$updateRow['product_id'],$updateRow['product_barcode']);
                    break;
                case 4:
                    $updateRow['pi_pending'] = $updateRow['pi_pending'] - $row['quantity'];
                    $updateRow['pi_unsellable'] = $updateRow['pi_unsellable'] + $row['quantity'];
                    $addLog[] = array(
                        'from_it_code' => 'pending',
                        'to_it_code' => 'unsellable',
                        'quantity' => $row['quantity']
                    );
                    break;
                case 5:
                    $updateRow['pi_reserved'] = $updateRow['pi_reserved'] + $row['quantity'];
                    $updateRow['pi_sellable'] = $updateRow['pi_sellable'] - $row['quantity'];
                    $addLog[] = array(
                        'from_it_code' => 'sellable',
                        'to_it_code' => 'reserved',
                        'quantity' => $row['quantity']
                    );
                    break;
                case 6:
                    $updateRow['pi_shipped'] = $updateRow['pi_shipped'] + $row['quantity'];
                    $updateRow['pi_reserved'] = $updateRow['pi_reserved'] - $row['quantity'];
                    $addLog[] = array(
                        'from_it_code' => 'reserved',
                        'to_it_code' => 'shipped',
                        'quantity' => $row['quantity']
                    );
                    break;
                case 7:
                    $updateRow['pi_sellable'] = $updateRow['pi_sellable'] + $row['quantity'];
                    $updateRow['pi_reserved'] = $updateRow['pi_reserved'] - $row['quantity'];
                    $addLog[] = array(
                        'from_it_code' => 'reserved',
                        'to_it_code' => 'sellable',
                        'quantity' => $row['quantity']
                    );
                    break;
                case 8:
                    $updateRow['pi_onway'] = $updateRow['pi_onway'] - $row['quantity'];
                    $addLog[] = array(
                        'from_it_code' => 'onway',
                        'to_it_code' => '',
                        'quantity' => $row['quantity']
                    );
                    break;
                case 9:
                    $updateRow['pi_sellable'] = $updateRow['pi_sellable'] + $row['quantity'];
                    $updateRow['pi_shipped'] = $updateRow['pi_shipped'] - $row['quantity'];
                    $addLog[] = array(
                        'from_it_code' => 'shipped',
                        'to_it_code' => 'sellable',
                        'quantity' => $row['quantity']
                    );
                    break;
                case 10:
                    $updateRow['pi_onway'] = $updateRow['pi_onway'] - $row['customQty'];
                    if ($row['customQty'] != $row['quantity']) {
                        $addLog[] = array(
                            'from_it_code' => 'onway',
                            'to_it_code' => 'onway',
                            'quantity' => $row['customQty']
                        );
                    }
                    $updateRow['pi_sellable'] = $updateRow['pi_sellable'] + $row['quantity'];
                    $addLog[] = array(
                        'from_it_code' => 'onway',
                        'to_it_code' => 'pending',
                        'quantity' => $row['quantity']
                    );
                    $addLog[] = array(
                        'from_it_code' => 'pending',
                        'to_it_code' => 'sellable',
                        'quantity' => $row['quantity']
                    );
                    if ($row['unsellable'] != 0) {
                        $updateRow['pi_unsellable'] = $updateRow['pi_unsellable'] + $row['unsellable'];
                        $addLog[] = array(
                            'from_it_code' => 'pending',
                            'to_it_code' => 'unsellable',
                            'quantity' => $row['unsellable']
                        );
                    }
                    break;
                case 11:
                    $updateRow['pi_unsellable'] = $updateRow['pi_unsellable'] - $row['quantity'];
                    $addLog[] = array(
                        'from_it_code' => 'unsellable',
                        'to_it_code' => '',
                        'quantity' => $row['quantity']
                    );
                    break;
                case 12:
                    $updateRow['pi_reserved'] = $updateRow['pi_reserved'] + $row['quantity'];
                    $updateRow['pi_unsellable'] = $updateRow['pi_unsellable'] - $row['quantity'];
                    $addLog[] = array(
                        'from_it_code' => 'unsellable',
                        'to_it_code' => 'reserved',
                        'quantity' => $row['quantity']
                    );
                    break;
                case 13:
                    $updateRow['pi_pending'] = $updateRow['pi_pending'] + $row['quantity'];
                    $updateRow['pi_unsellable'] = $updateRow['pi_unsellable'] - $row['quantity'];
                    $addLog[] = array(
                        'from_it_code' => 'unsellable',
                        'to_it_code' => 'pending',
                        'quantity' => $row['quantity']
                    );
                    break;
                case 14:
                    //$updateRow['pi_add_time']=date('Y-m-d H:i:s');
                    $updateRow['pi_planned'] = $updateRow['pi_planned'] - $row['quantity'] < 0 ? 0 : $updateRow['pi_planned'] - $row['quantity'];
                    $addLog[] = array(
                        'from_it_code' => 'planned',
                        'to_it_code' => '',
                        'quantity' => $row['quantity']
                    );
                    break;
                case 15:
                    //在途库存
                    $updateRow['pi_onway'] = $updateRow['pi_onway'] + $row['quantity'];
                    $addLog[] = array(
                        'from_it_code' => '',
                        'to_it_code' => 'onway',
                        'quantity' => $row['quantity']
                    );
                    //计划库存
                    if ($row['customQty']>0) {
                        $updateRow['pi_planned'] = ($updateRow['pi_planned'] - $row['customQty'] < 0) ? 0 : $updateRow['pi_planned'] - $row['customQty'];
                        $addLog[] = array(
                            'from_it_code' => 'planned',
                            'to_it_code' => 'onway',
                            'quantity' => $row['customQty']
                        );
                    }
                    break;
                case 16:
                    $updateRow['pi_planned'] = $updateRow['pi_planned'] + $row['quantity'];
                    $addLog[] = array(
                        'from_it_code' => '',
                        'to_it_code' => 'planned',
                        'quantity' => $row['quantity']
                    );
                    break;
                default:
                    throw new Exception('Internal error! Type Wrong.', 50000);
                    break;
            }

            foreach ($updateRow as $key => $val) {
                if (is_numeric($val) && $val < 0) {
                    throw new Exception('Internal error!', 50000);
                }
            }
            if ($isInventory) {
                if (!Service_ProductInventory::update($updateRow, $inventoryRow['pi_id'], 'pi_id')) {
                    throw new Exception('Internal error! Update Fail.', 50000);
                }
            } else {
                Service_ProductInventory::add($updateRow);
            }
            //日志
            $updateRow['reference_code'] = $row['reference_code'];
            $updateRow['application_code'] = $row['application_code'];
            $updateRow['product_barcode'] = $this->_productArr['product_barcode'];
            $updateRow['note'] = $row['note'];
            foreach ($addLog as $key => $val) {
                $updateRow['pil_quantity'] = $val['quantity'];
                $updateRow['from_it_code'] = $val['from_it_code'];
                $updateRow['to_it_code'] = $val['to_it_code'];
                $this->addLog($updateRow);
            }

            $result['state'] = 1;
            $result['message'] = 'Success';
        } catch (Exception $e) {
            $result['error'][] = array(
                'errorCode' => $e->getCode(),
                'errorMse' => $e->getMessage()
            );
        }
        return $result;
    }

    /**
     * @param $row
     * @return bool
     * @throws Exception
     */
    private function addLog($row)
    {
        $user = new Zend_Session_Namespace('userAuthorization');
        $userId = isset($user->userId) ? $user->userId : 0;
        $addLog = array(
            'product_id' => $row['product_id'],
            'warehouse_id' => $row['warehouse_id'],
            'product_barcode' => $row['product_barcode'],
            'user_id' => $userId,
            'pil_planned' => $row['pi_planned'],
            'pil_onway' => $row['pi_onway'],
            'pil_pending' => $row['pi_pending'],
            'pil_sellable' => $row['pi_sellable'],
            'pil_unsellable' => $row['pi_unsellable'],
            'pil_reserved' => $row['pi_reserved'],
            'pil_shipped' => $row['pi_shipped'],
            'pil_quantity' => $row['pil_quantity'],
            'from_it_code' => $row['from_it_code'],
            'to_it_code' => $row['to_it_code'],
            'reference_code' => $row['reference_code'],
            'application_code' => $row['application_code'],
            'pil_note' => $row['note'],
            'pil_add_time' => $this->_date
        );
        if (!Service_ProductInventoryLog::add($addLog)) {
            throw new Exception('Internal error! Update Inventory  Fail.', 50000);
            return false;
        }
        return true;
    }


    /**
     * 修改库存
     * @author solar
     * @param int $warehouse_id
     * @param string $lc_code
     * @param int $product_id
     * @param int $quantity
     * @param string $note
     * @param string $reference_code
     * @return true|string
     */
    public function changeSellable($warehouse_id, $lc_code, $product_id, $quantity, $note, $reference_code='') {
        $row = Service_ProductInventory::getByWhProduct($warehouse_id, $product_id);
        if(empty($row)) return '找不到库存记录';
        $aInventory = Service_ProductInventory::getForUpdate($row['pi_id']);
        $where = array('warehouse_id'=>$warehouse_id, 'lc_code'=>$lc_code, 'product_barcode'=>$aInventory['product_barcode']);
        $batchList =  Service_InventoryBatch::listByWhere($where);
        $total_quantity = 0;
        foreach($batchList as &$row) $total_quantity += $row['ib_quantity'];
        $balance = $logBalance = $total_quantity - $quantity;
        if($balance == 0) return true;
        if($balance < 0) {
            $batchRow = array_pop($batchList);
            $new_quantity = $batchRow['ib_quantity'] - $balance;
            $batchUpdate['ib_quantity'] = $new_quantity;
            $batchUpdate['ib_update_time'] = date('Y-m-d H:i:s');
            Service_InventoryBatch::update($batchUpdate, $batchRow['ib_id']);
            //日志
            Service_InventoryBatchLog::log($batchRow, $batchRow['ib_quantity'], $new_quantity, $note);
        } else {
            while(count($batchList)>0) {
                $batchRow = array_shift($batchList);
                if($balance < $batchRow['ib_quantity']) {
                    $new_quantity = $batchRow['ib_quantity'] - $balance;
                    $batchUpdate['ib_quantity'] = $new_quantity;
                    $batchUpdate['ib_update_time'] = date('Y-m-d H:i:s');
                    Service_InventoryBatch::update($batchUpdate, $batchRow['ib_id']);
                    //日志
                    Service_InventoryBatchLog::log($batchRow, $batchRow['ib_quantity'], $new_quantity, $note);
                    break;
                } else if($balance == $batchRow['ib_quantity']) {
                    Service_InventoryBatch::delete($batchRow['ib_id']);
                    break;
                } else {
                    $balance -= $batchRow['ib_quantity'];
                    Service_InventoryBatch::delete($batchRow['ib_id']);
                }
            }
        }
        $piUpdate['pi_sellable'] = $aInventory['pi_sellable'] - $logBalance;
        if($piUpdate['pi_sellable'] < 0) throw new Exception('库存异常');
        $piUpdate['pi_update_time'] = date('Y-m-d H:i:s');
        Service_ProductInventory::update($piUpdate, $aInventory['pi_id']);
        //产品库存日志
        $piLog['product_id'] = $product_id;
        $piLog['product_barcode'] = $aInventory['product_barcode'];
        $piLog['warehouse_id'] = $warehouse_id;
        $piLog['reference_code'] = $reference_code;
        $piLog['user_id'] = Service_User::getUserId();
        $piLog['pil_onway'] = $aInventory['pi_onway'];
        $piLog['pil_pending'] = $aInventory['pi_pending'];
        $piLog['pil_sellable'] = $aInventory['pi_sellable'];
        $piLog['pil_unsellable'] = $aInventory['pi_unsellable'];
        $piLog['pil_reserved'] = $aInventory['pi_reserved'];
        $piLog['pil_shipped'] = $aInventory['pi_shipped'];
        $piLog['pil_quantity'] = abs($logBalance);
        $piLog['pil_add_time'] = date('Y-m-d H:i:s');
        $piLog['pil_ip'] = Common_Common::getIP();
        $piLog['pil_note'] = $note.'，可用库存从'.$aInventory['pi_sellable'].'变为'.$piUpdate['pi_sellable'];
        Service_ProductInventoryLog::add($piLog);
        return true;
    }



    /**
     * @desc 插入补货点
     * @param int $warehouseId
     * @param int $productId
     * @param string $productBarcode
     * @return bool
     */
    private function addProductInventoryChangesNodes($warehouseId = 0, $productId = 0, $productBarcode = '')
    {
        $date = date('Y-m-d');
        $count = Service_ProductInventoryChangesNodes::getByCondition(array(
            'warehouse_id' => $warehouseId,
            'product_id' => $productId,
            'picn_type' => '1',//补货点
            'picn_date' => $date,
        ), 'count(*)');
        if (!$count) {
            $nodeArr = array(
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'product_barcode' => $productBarcode,
                'picn_status' => 0,
                'picn_type' => '1',//补货点
                'picn_date' => $date,
                'picn_update_time' => $this->_date,
            );
            Service_ProductInventoryChangesNodes::add($nodeArr);
        }
    }

}