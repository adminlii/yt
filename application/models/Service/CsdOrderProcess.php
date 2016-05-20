<?php
class Service_CsdOrderProcess extends Common_Service
{

    /**
     * 订单明细
     * @param unknown_type $order_id
     * @throws Exception
     * @return multitype:number string NULL multitype:mixed Ambigous <number, mixed, multitype:, string> Ambigous <mixed, multitype:, string>
     */
    public static function getOrderInfo($order_id)
    {
        $return = array(
            'ask' => 0,
            'message' => 'Error'
        );
        try{
            $dataArr = array();
            $statusArr = Service_OrderProcess::getOrderStatus();
            $countrys = Common_DataCache::getCountry();
            if(empty($order_id)){
                throw new Exception(Ec::Lang('参数错误'));
            }
            $order = Service_CsdOrder::getByField($order_id, 'order_id');
            if(! $order){
                throw new Exception(Ec::Lang('订单不存在'));
            }
            $db = Common_Common::getAdapter();
            $sql = "select * from csi_customer where customer_id='{$order['customer_id']}';";
            $csi_customer = $db->fetchRow($sql);
            
            $con = array(
                'order_id' => $order_id
            );
            $invoice = Service_CsdInvoice::getByCondition($con, '*', 0, 0, 'invoice_id asc');
            if(empty($invoice)){
                throw new Exception(Ec::Lang('申报信息不存在'));
            }
            $order_declared_value = 0;
            foreach($invoice as $k => $v){
                $v['invoice_unitcharge'] = $v['invoice_quantity'] ? ($v['invoice_totalcharge'] / $v['invoice_quantity']) : 0;
                $invoice[$k] = $v;
                $order_declared_value+=$v['invoice_totalcharge'];
            }
            $order['declared_value'] = $order_declared_value;
            $extservice = Service_CsdExtraservice::getByCondition($con);
            $shipperConsignee = Service_CsdShipperconsignee::getByField($order_id, 'order_id');
            
            $shipperConsignee['consignee_countryname_cn'] = isset($countrys[$shipperConsignee['consignee_countrycode']])?$countrys[$shipperConsignee['consignee_countrycode']]['country_cnname']:$shipperConsignee['consignee_countrycode'];
            $shipperConsignee['consignee_countryname_en'] = isset($countrys[$shipperConsignee['consignee_countrycode']])?$countrys[$shipperConsignee['consignee_countrycode']]['country_enname']:$shipperConsignee['consignee_countrycode'];

            $shipperConsignee['shipper_countryname_cn'] = isset($countrys[$shipperConsignee['shipper_countrycode']])?$countrys[$shipperConsignee['shipper_countrycode']]['country_cnname']:$shipperConsignee['shipper_countrycode'];
            $shipperConsignee['shipper_countryname_en'] = isset($countrys[$shipperConsignee['shipper_countrycode']])?$countrys[$shipperConsignee['shipper_countrycode']]['country_enname']:$shipperConsignee['shipper_countrycode'];
             
            if(! $shipperConsignee){
                throw new Exception(Ec::Lang('收发件人信息不存在'));
            }
            $dataArr['csi_customer'] = $csi_customer;
            $dataArr['order'] = $order;
            $dataArr['invoice'] = $invoice;
            $dataArr['extservice'] = $extservice;
            $dataArr['shipper_consignee'] = $shipperConsignee;
            
            $return['ask'] = 1;
            $return['message'] = 'Success';
            $return['data'] = $dataArr;
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
}
