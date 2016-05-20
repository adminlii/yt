<?php
class Order_ShipTemplateController extends Ec_Controller_Action
{

    public function preDispatch()
    {
        $this->serviceClass = new Service_Orders();
    }

    /**
     * 订单打印
     */
    public function printAction()
    {
        $this->tplDirectory = "order/views/template/";
        try{
            $order_id_arr = $this->getParam('orderId', array());
            $type = $this->getParam('type', 'label');
            if(empty($order_id_arr) || is_array($order_id_arr)){
                throw new Exception(Ec::Lang('参数错误'));
            }
            $result = array();
            foreach($order_id_arr as $order_id){
                $rs = Service_CsdOrderProcess::getOrderInfo($order_id);
                $result[] = $rs;
            }
            $this->view->result = $rs;
            
            echo $this->view->render($this->tplDirectory . "Common.tpl");
        }catch(Exception $e){
            header("Content-type: text/html; charset=utf-8");
            echo $e->getMessage();
            exit();
        }
    }
}