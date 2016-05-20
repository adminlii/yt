<?php
/**
 * 标记发货
 * @author Administrator
 *
 */
class Platform_OrderShipMark
{

    private $_ref_id = '';

    private $_order = null;

    private $_carrier_name = '';

    private $_shipping_method_no = '';

    private $_err = array();

    public function __construct()
    {}

    public function setRefId($ref_id)
    {
        $this->_ref_id = $ref_id;
    }

    public function setCarrierName($carrier_name)
    {
        $this->_carrier_name = $carrier_name;
    }

    public function setShippingMethod($shipping_method_no)
    {
        $this->_shipping_method_no = $shipping_method_no;
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
                'sync_status' => '6',
                'shipping_method_no' => $this->_shipping_method_no,
                'carrier_name' => $this->_carrier_name
            );
            Service_Orders::update($updateRow, $this->_ref_id, 'refrence_no_platform');
            
            // 日志
            $logRow = array(
                'ref_id' => $this->_ref_id,
                'log_content' => '订单手工标记发货,操作人' . Service_User::getUserName()
            );
            Service_OrderLog::add($logRow);
            
            $return['ask'] = 1;
            $return['message'] = Ec::Lang('订单成功添加到标记发货任务');
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        $return['err'] = $this->_err;
        // print_r($return);exit;
        return $return;
    }
}