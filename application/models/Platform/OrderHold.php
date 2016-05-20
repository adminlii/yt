<?php
/**
 * 订单暂存
 * @author Administrator
 *
 */
class Platform_OrderHold
{

    private $_ref_id = '';

    private $_order = null;

    private $_err = array();

    public function setRefId($ref_id)
    {
        $this->_ref_id = $ref_id;
    }

    public function _validate()
    {
        if(empty($this->_ref_id)){
            throw new Exception('订单号不可为空');
        }else{
            $order = Service_Orders::getByField($this->_ref_id, 'refrence_no_platform');
            $this->_order = $order;
            if(empty($order)){
                throw new Exception('订单不存在');
            }
            $allowStatus = array('2','7');
            if(!in_array($order['order_status'], $allowStatus)){
                throw new Exception('订单不允许冻结');                
            }
        }
    }

    /**
     * 冻结
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
            if($this->_err){}
            $updateRow = array(
                'order_status' => '5'
            );
            Service_Orders::update($updateRow, $this->_ref_id, 'refrence_no_platform');
            // 日志           
            $logRow = array(
                'ref_id' => $this->_ref_id,
                'log_content' => '订单暂存,操作人'.Service_User::getUserName()
            );
            Service_OrderLog::add($logRow);
            $return['ask'] = 1;
            $return['message'] = Ec::Lang('暂存成功');
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        $return['err'] = $this->_err;
        return $return;
    }

}