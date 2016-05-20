<?php
/**
 * Class Common_WomProcess
 * @desc 获取当前仓库的操作模式
 */
class Common_WomProcess
{
    protected $_params = array();
    protected $_warehouse = array();
    public static $_modelClass = null;

    public function __construct($params = null)
    {
        if (is_array($params)) {
            $this->setParams($params);
        }
    }

    public function setParams($params)
    {
        $this->_params['warehouse_id'] = isset($params['warehouse_id']) ? $params['warehouse_id'] : 0;
        $this->_params['application_code'] = isset($params['application_code']) ? $params['application_code'] : '';
        $this->_params['type'] = isset($params['wom_type']) ? $params['type'] : '1';
        $this->_params['detailLevel'] = isset($params['detailLevel']) ? $params['detailLevel'] : '0';
    }

    private function setOperationMode()
    {
        $this->_warehouse = Service_Warehouse::getByField($this->_params['warehouse_id'], 'warehouse_id', array('warehouse_id','warehouse_code', 'warehouse_status', 'warehouse_type', 'warehouse_virtual'));
        if (empty($this->_warehouse)) {
            throw new Exception('无法获取仓库信息');
        }
        $condition = array(
            'warehouse_id' => $this->_params['warehouse_id'],
            'type' => $this->_params['type'],
        );
        $womRows = Table_WarehouseOperationMode::getInstance()->getJoinInnerWarehouseWomMapByCondition($condition, '*', 0, 0, array('wom_default desc'));
        if (empty($womRows)) {
            throw new Exception('仓库未配置操作模式');
        }
        $mode = $womRows[0];
        unset($womRows, $mode['wom_add_time'], $mode['wom_update_time']);
        //返回详细
        if ($this->_params['detailLevel'] == '1') {
            $map = Table_WarehouseOperationModeMap::getInstance()->getJoinInnerByWomIdAppCode($mode['wom_id'], $this->_params['application_code']);
            if (empty($map)) {
                throw new Exception('无法仓库操作模式信息');
            }
            $nodeObj = Service_WarehouseOperationNodeMap::getModelInstance();
            foreach ($map as $row) {
                $mode[$row['application_code']] = $row;
                $mode[$row['application_code']]['rule'] = array();
                $ruleArr=$nodeObj->getByCondition(array('won_id' => $row['won_id']), array('won_id', 'ref_id', 'wonm_type'), 0);
                if(!empty($ruleArr)){
                    foreach($ruleArr as $val){
                        $mode[$row['application_code']]['rule'][$val['ref_id']]=$val;
                    }
                }
            }
        }
        $this->_warehouse['mode'] = $mode;
    }

    private function init()
    {
        $result = array('state' => 0, 'message' => '', 'data' => array());
        try {
            $this->setOperationMode();
            $result['data'] = $this->_warehouse;
            $result['state'] = 1;
        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
        }
        return $result;
    }

    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Common_WomProcess();
        }
        return self::$_modelClass;
    }

    /**
     * @desc 获取操作模式
     * @return array
     */
    public function getOperationMode($params = null)
    {
        if (is_array($params)) {
            $this->setParams($params);
        }
        return $this->init();
    }


    /**
     * @desc 设置收货操作模式应用代码
     */
    private function setReceivingOperationMode($params = null)
    {

    }

    /**
     * @desc 根据仓库ID及应该代码检查操作模式
     * @param int $warehouseId
     * @param string $appCode
     * @return bool
     */
    public static function checkOperationMode($warehouseId = 0, $appCode = '')
    {
        $condition = array(
            'warehouse_id' => $warehouseId,
            'application_code' => $appCode,
        );
        if (Table_WarehouseOperationMode::getInstance()->getJoinInnerWarehouseWomMapByCondition($condition, 'count(*)')) {
            return true;
        }
        return false;
    }

}