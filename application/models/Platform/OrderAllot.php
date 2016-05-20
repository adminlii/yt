<?php
/**
 * 分配运输方式
 * @author Administrator
 *
 */
class Platform_OrderAllot
{

    private $_ref_id = '';

    private $_order = null;

    private $_shipping_method = '';
    
    private $_warehouse_code = '';

    private $_err = array();

    public function __construct()
    {}

    public function setRefId($ref_id)
    {
        $this->_ref_id = $ref_id;
    }

    public function setWarehouseCode($warehouse_code)
    {
        $this->_warehouse_code = $warehouse_code;
    }

    public function setShippingMethod($shipping_method_no)
    {
        $this->_shipping_method = $shipping_method_no;
    }

    public function _validate()
    {
        if(empty($this->_ref_id)){
            throw new Exception(Ec::Lang('订单号不可为空'));
        }else{
            $order = Service_Orders::getByField($this->_ref_id, 'refrence_no_platform');
            $this->_order = $order;
            if(empty($order)){
                throw new Exception(Ec::Lang('订单不存在'));
            }
            $allowStatus = array(
                '2',
                '5',
                '7'
            );
            if(! in_array($order['order_status'], $allowStatus)){
                throw new Exception(Ec::Lang('订单不允许操作'));
            }
        }
        if(empty($this->_shipping_method)){
            throw new Exception(Ec::Lang('运输方式不可为空'));            
        }
    }

    /**
     * 标记发货
     */
    public function process()
    {
        $return = array(
            'ask' => 0,
            'message' => 'Fail'
        );
        $return['ref_id'] = $this->_ref_id;
        try{
            $this->_validate();
            if($this->_err){
                throw new Exception(Ec::Lang('数据不合法'));
            }
            
            $updateRow = array(
                'shipping_method' => $this->_shipping_method,
//                 'warehouse_code' => $this->_warehouse_code
            );
            Service_Orders::update($updateRow, $this->_ref_id, 'refrence_no_platform');
            
            // 日志
            $logRow = array(
                'ref_id' => $this->_ref_id,
                'log_content' => '订单手工分配运输方式,操作人' . Service_User::getUserName()
            );
            Service_OrderLog::add($logRow);
            
            $return['ask'] = 1;
            $return['message'] = Ec::Lang('订单成功分配运输方式'.$this->_shipping_method);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        $return['err'] = $this->_err;
        // print_r($return);exit;
        return $return;
    }
}