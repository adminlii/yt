<?php
class Service_PutawayProcess
{
    protected $_putawayCode = null;
    protected $_putaway = array();
    protected $_putawayDetail = array();
    protected $_userWarehouseIds = array(0);
    protected $_userId = 0;
    protected $_errorArr = array();
    protected $_params = array();
    protected $_date = '';

    //状态
    public static function getItemStatus()
    {
        return Common_Status::putawayDetailStatus('auto');
    }

    //上架搜索
    public static function getSearchType()
    {
        return array(
            0 => '质检单号',
            1 => '入库单号',
            2 => '上架单号',
            3 => '分箱单号',
        );
    }

    //类型
    public static function getItemType()
    {
        return Common_Type::putawayDetailType('auto');
    }

    public function __construct()
    {
        $this->_date = date('Y-m-d H:i:s');
    }

    /**
     * @desc 数据初始化以及数据验证
     * @param $params
     * @return bool
     */
    private function setDataValid($params)
    {
        $this->_userWarehouseIds = Service_User::getUserWarehouseIds();
        $this->_userId = Service_User::getUserId();
        $pdIds = array();
        $locationObj = new Service_Location();
        $warehouseId = '';
        foreach ($params as $key => $val) {
            if (!in_array($val['pd_id'], $pdIds)) {
                $pdIds[] = $val['pd_id'];
            } else {
                $this->_errorArr[] = array('errorCode' => '10011', 'errorMsg' => '第' . $key . '行,数据重复.');
                continue;
            }
            if ($warehouseId == '') {
                $pdRow = Service_PutawayDetail::getByField($val['pd_id'], 'pd_id', array('putaway_id'));
                $putawayRow = Service_Putaway::getByField($pdRow, 'putaway_id', array('warehouse_id'));
                $warehouseId = $putawayRow['warehouse_id'];
                if (!isset($this->_userWarehouseIds[$warehouseId])) {
                    $this->_errorArr[] = array('errorCode' => '10022', 'errorMsg' => '您没有权限操作');
                    break;
                }
            }
            if (empty($val['lc_code']) || !isset($val['lc_code'])) {
                $this->_errorArr[] = array('errorCode' => '10009', 'errorMsg' => Ec::Lang('notEmptyShelf'));
            } else {
                $locationRow = $locationObj->getByField($val['lc_code'], 'lc_code', array('lc_code', 'lc_status', 'warehouse_id'));
                if (empty($locationRow)) {
                    $this->_errorArr[] = array('errorCode' => '10011', 'errorMsg' => Ec::Lang('ShelfNotExist'));
                } elseif ($locationRow['lc_status'] == '0' || $locationRow['warehouse_id'] != $warehouseId) {
                    $this->_errorArr[] = array('errorCode' => '10012', 'errorMsg' => Ec::Lang('ShelfNotBeUsed'));
                }
            }
            $val['lc_code'] = strtoupper($val['lc_code']);
            $this->_params['putawayDetail'][$val['pd_id']] = $val;
        }
        if (!empty($this->_errorArr)) {
            return false;
        }

        $putawayDetailRows = Service_PutawayDetail::getByCondition(array('pd_id_arr' => $pdIds), '*');
        if (empty($putawayDetailRows)) {
            $this->_errorArr[] = array('errorCode' => '10002', 'errorMsg' => Ec::Lang('paramsErrormsg'));
            return false;
        }

        foreach ($putawayDetailRows as $key => $val) {
            $this->_putawayDetail[$val['pd_id']] = $val;
        }

        foreach ($this->_params['putawayDetail'] as $key => $val) {
            if (!isset($this->_putawayDetail[$val['pd_id']])) {
                $this->_errorArr[] = array('errorCode' => '10002', 'errorMsg' => Ec::Lang('paramsErrormsg'));
                return false;
            }
        }
    }

    /**
     * @desc 更新上架数据状态
     */
    private function updatePutawayDetail()
    {
        $pdObj = new Service_PutawayDetail();
        foreach ($this->_params['putawayDetail'] as $key => $val) {
            $pdObj->update(array('pd_status' => 1, 'lc_code' => $val['lc_code'], 'putaway_user_id' => $this->_userId, 'pd_putaway_time' => $this->_date, 'pd_update_time' => $this->_date), $val['pd_id']);
        }
    }

    /**
     * @desc 更新ASN上架数量
     * @item for update 1.QC状态 2.AsnItem 3.box(创建时已完成,查找未完成box可以查找putawayDetail)
     */
    private function updateAsnDetailAndQc()
    {
        $qcArr = array();
        foreach ($this->_putawayDetail as $key => $val) {
            if (isset($this->_params['putawayDetail'][$key])) {
                //统计各QC上架数量
                if (!isset($qcArr[$val['qc_code']])) {
                    $qcArr[$val['qc_code']] = $val['pd_quantity'];
                } else {
                    $qcArr[$val['qc_code']] += $val['pd_quantity'];
                }
            }
        }
        if (empty($qcArr)) {
            $this->_errorArr[] = array('errorCode' => '10013', 'errorMsg' => '上架数据异常');
            return false;
        }
        $qcObj = new Service_QualityControl();
        $rdbObj = new Service_ReceivingDetailBatch();
        $rdObj = new  Service_ReceivingDetail();
        foreach ($qcArr as $qcCode => $quantity) {
            $qcRow = $qcObj->getByField($qcCode, 'qc_code');
            if (empty($qcRow)) {
                $this->_errorArr[] = array('errorCode' => '10013', 'errorMsg' => Ec::Lang('QcNotExist'));
            } else {
                //分箱上架,多条上架记录,QC是否已上架，没有“上架中”状态，暂时先判断数量，数量相等才“已上架”
                $qcStatus = $qcRow['qc_quantity_sellable'] == $quantity ? 2 : $qcRow['qc_status'];
                $qcObj->update(array('qc_status' => $qcStatus, 'qc_update_time' => $this->_date), $qcRow['qc_id']);
                //存在不良品,上架数量不一定等于收货数量
                $rdbObj->update(array('rdb_putaway_qty' => $quantity, 'rdb_update_time' => $this->_date), $qcCode, 'qc_code');
                $rdRow = $rdObj->getByField($qcRow['rd_id'], 'rd_id', array('rd_putaway_qty', 'rd_received_qty'));
                $rdObj->update(array('rd_putaway_qty' => $quantity + $rdRow['rd_putaway_qty'], 'rd_update_time' => $this->_date), $qcRow['rd_id'], 'rd_id');
            }
        }
    }

    /**
     * @desc 添加货架批次库存
     */
    private function inventoryBatch()
    {
        $inventoryBatchObj = new  Service_InventoryBatch();
        $receivingObj = new Service_Receiving();
        $batchLogObj = new Service_InventoryBatchLog();
        $receiving = array();
        $ip = Common_Common::getIP();
        foreach ($this->_putawayDetail as $key => $val) {
            if (isset($this->_params['putawayDetail'][$key])) {
                if (!isset($receiving[$val['receiving_code']])) {
                    $receiving[$val['receiving_code']] = $receivingObj->getByField($val['receiving_code'], 'receiving_code');
                }
                $rdcRows = Table_ReceivingDetailCost::getInstance()->getByCondition(array('receiving_id' => $receiving[$val['receiving_code']]['receiving_id'], 'product_id' => $val['product_id']), array('supplier_id', 'po_code'), 0, 0);
                $supplierId = isset($rdcRows[0]['supplier_id']) ? $rdcRows[0]['supplier_id'] : $receiving[$val['receiving_code']]['supplier_id'];
                $poCode = isset($rdcRows[0]['po_code']) ? $rdcRows[0]['po_code'] : $receiving[$val['receiving_code']]['po_code'];
                $row = array(
                    'lc_code' => $this->_params['putawayDetail'][$key]['lc_code'],
                    'product_id' => $val['product_id'],
                    'product_barcode' => $val['product_barcode'],
                    'reference_no' => $val['qc_code'],
                    'box_code' => $val['box_code'],
                    'application_code' => 'Putaway',
                    'warehouse_id' => $val['warehouse_id'],
                    'receiving_code' => $val['receiving_code'],
                    'receiving_id' => $receiving[$val['receiving_code']]['receiving_id'],
                    'supplier_id' => $supplierId,
                    'po_code' => $poCode,
                    'lot_number' => 1,
                    'ib_status' => 1,
                    'ib_quantity' => $val['pd_quantity'],
                    'ib_fifo_time' => $this->_date,
                    'ib_add_time' => $this->_date,
                );
                $ibId = $inventoryBatchObj->add($row);
                $log = array(
                    'lc_code' => $this->_params['putawayDetail'][$key]['lc_code'],
                    'product_id' => $val['product_id'],
                    'product_barcode' => $val['product_barcode'],
                    'reference_no' => $val['qc_code'],
                    'box_code' => $val['box_code'],
                    'application_code' => 'Putaway',
                    'warehouse_id' => $val['warehouse_id'],
                    'receiving_code' => $val['receiving_code'],
                    'supplier_id' => $supplierId,
                    'po_code' => $poCode,
                    'ib_id' => $ibId,
                    'ibl_quantity_before' => 0,
                    'ibl_quantity_after' => $val['pd_quantity'],
                    'user_id' => $this->_userId,
                    'ibl_ip' => $ip,
                    'ibl_add_time' => $this->_date,
                );
                $batchLogObj->add($log);
            }
        }
    }

    /**
     * @desc 更新产品库存
     * @throws Exception
     */
    private function updateInventory()
    {
        $inventoryObj = new Service_ProductInventoryProcess();
        foreach ($this->_putawayDetail as $key => $val) {
            if (isset($this->_params['putawayDetail'][$key])) {
                $row = array(
                    'product_id' => $val['product_id'],
                    'quantity' => $val['pd_quantity'],
                    'customQty' => 0, //用于其它
                    'operationType' => 3,
                    'unsellable' => 0,
                    'warehouse_id' => $val['warehouse_id'],
                    'reference_code' => $val['qc_code'], //操作单号
                    'application_code' => 'Putaway', //操作类型
                    'note' => $val['box_code']
                );

                $result = $inventoryObj->update($row);
                // print_r($result);
                if (!isset($result['state']) || $result['state'] != '1') {
                    throw new Exception('Inventory Internal error');
                }
            }
        }
    }

    /**
     * @desc 更新产品历史货架
     * @throws Exception
     */
    private function updateProductLocationMap()
    {
        $obj = new Service_ProductLocationMap();
        $result = true;
        $productArr = array();
        foreach ($this->_putawayDetail as $key => $val) {
            if (isset($this->_params['putawayDetail'][$key])) {
                if (!in_array($val['product_id'], $productArr)) {
                    $productArr[] = $val['product_id'];
                } else {
                    continue;
                }
                $condition = array(
                    'product_id' => $val['product_id'],
                    'warehouse_id' => $val['warehouse_id'],
                );
                $rows = $obj->getByCondition($condition);
                if (!empty($rows)) {
                    if ($rows[0]['lc_code'] == $this->_params['putawayDetail'][$key]['lc_code']) {
                        continue;
                    }
                    $result = $obj->update(array('lc_code' => $this->_params['putawayDetail'][$key]['lc_code']), $rows[0]['plm_id'], 'plm_id');
                } else {
                    $condition['lc_code'] = $this->_params['putawayDetail'][$key]['lc_code'];
                    $result = $obj->add($condition);
                }
            }
        }
        if (!$result) {
            throw new Exception('Update Product Location Internal error');
        }
    }

    /**
     * @Condition 中转类型 如果为特采类型则需要判断是否存在异常通知单
     * @desc 更新中转状态 更新异常处理通知单状态
     */
    private function updateReceiving()
    {
        //更新中转状态
        $obj = new Service_Receiving();
        $asnCodes = array();
        $raCodes = array();
        foreach ($this->_putawayDetail as $key => $val) {
            if (isset($this->_params['putawayDetail'][$key]) && !in_array($val['receiving_code'], $asnCodes)) {
                $asnCodes[] = $val['receiving_code'];
                if ($row = $obj->getByField($val['receiving_code'], 'receiving_code')) {
                    //更新中转状态
                    if ($row['receiving_type'] == '3' && $row['receiving_transfer_status'] == '0') {
                        Service_Receiving::update(array('receiving_transfer_status' => 1, 'receiving_update_time' => $this->_date), $val['receiving_code'], 'receiving_code');
                        //更新特采通知单状态
                    } elseif ($row['receiving_type'] == '5' && $val['qc_code'] != '' && $raRow = Service_ReceivingAbnormal::getByField($val['receiving_code'], 'receiving_code')) {
                        if ($raRow['ra_status'] != '2') {
                            $raCodes[$raRow['ra_id']] = $raRow['ra_id'];
                            Service_ReceivingAbnormalDetail::update(array('rad_status' => 2, 'ra_update_time' => $this->_date), $val['qc_code'], 'qc_code');
                        }
                    }
                }
            }
        }
        //更新单头状态
        if (!empty($raCodes)) {
            foreach ($raCodes as $raid) {
                if (!Service_ReceivingAbnormalDetail::getByCondition(array('ra_id' => $raid, 'rad_status_in' => array(0, 1)), 'count(*)')) {
                    Service_ReceivingAbnormal::update(array('ra_status' => 2, 'ra_update_time' => $this->_date), $raid, 'ra_id');
                }
            }
        }
    }

    /**
     * @desc 产品上架 事务处理 主入口
     * @param array $params
     * @return array
     */
    public function submit($params = array())
    {
        $result = array(
            'state' => 0,
            'error' => array(),
            'message' => ''
        );
        $db = Service_QualityControl::getModelInstance()->getAdapter();
        $this->setDataValid($params);
        if (!empty($this->_errorArr)) {
            $result['error'] = $this->_errorArr;
            return $result;
        }
        $db->beginTransaction();
        try {
            $this->updatePutawayDetail();
            $this->updateAsnDetailAndQc();
            $this->inventoryBatch();
            $this->updateInventory();
            $this->updateProductLocationMap();
            $this->updateReceiving();
            $db->commit();
            $result = array('state' => 1, 'message' => Ec::Lang('operationSuccess'));
            return $result;
        } catch (Exception $e) {
            $db->rollBack();
            $result['error'][] = array(
                'errorCode' => '50000',
                'errorMsg' => $e->getMessage()
            );
            return $result;
        }
    }

}