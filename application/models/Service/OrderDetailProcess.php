<?php
class Service_OrderDetailProcess {

    /**
     * 相关订单
     * @param unknown_type $refId
     * @throws Exception
     * @return Ambigous <mixed, multitype:, string>
     */
    public static function getCustomerOrder($refId){
        try{
            $order = Service_Orders::getByField($refId,'refrence_no_platform');
            if(!$order){
                throw new Exception('数据错误,订单不存在-->'.$refId);
            }
            $buyerId = $order['buyer_id'];
            $buyerName = $order['buyer_name'];
            $buyerMail = $order['buyer_mail'];
            if($order['platform']=='ebay'){
                $con = array('buyer_id'=>$buyerId,'platform'=>$order['platform']); 
                if(empty($buyerId)){
                    throw new Exception('ebay订单没有买家账号');
                }               
            }else{
                $con = array('buyer_id'=>$buyerId,'buyer_name'=>$buyerName,'buyer_mail'=>$buyerMail,'platform'=>$order['platform']);
            }
            $orders = Service_Orders::getByCondition($con,'*',50 , 1);
            $orderStatus = Service_OrderProcess::$statusArr;
            foreach($orders as $k=>$v){
                $v['order_status_title'] = isset($orderStatus[$v['order_status']])?$orderStatus[$v['order_status']]['name']:'其他';
                $orders[$k] = $v;
                if($v['refrence_no_platform']==$refId){
                    unset($orders[$k]);
                }
            }
            if(empty($orders)){
                throw new Exception('No Data');
            }
            return $orders;
            
        }catch(Exception $e){
            return $e->getMessage();
        }
        
    }
    /**
     * message
     * @param unknown_type $refId
     * @throws Exception
     * @return Ambigous <mixed, multitype:, string>
     */
    public static function getCustomerMessage($refId){
        try{
            $order = Service_Orders::getByField($refId,'refrence_no_platform');
            if(!$order){
                throw new Exception('数据错误,订单不存在-->'.$refId);
            }
            $buyerId = $order['buyer_id'];
            if(empty($buyerId)){
                throw new Exception('No Data');
            }
            $con = array('sender_id'=>$buyerId);
            
            $messages = Service_EbayMessage::getByCondition($con);
            if(empty($messages)){
                throw new Exception('No Data');
            }
            return $messages;
        
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * 纠纷
     */
    public static function getCustomerCase($refId){
        try{
            $order = Service_Orders::getByField($refId,'refrence_no_platform');
            if(!$order){
                throw new Exception('数据错误,订单不存在-->'.$refId);
            }
            $buyerId = $order['buyer_id'];
            if(empty($buyerId)){
                throw new Exception('No Data');
            }
            $con = array('buyer_id'=>$buyerId);
            $cases = Service_EbayUserCases::getByCondition($con);
            if(empty($cases)){
                throw new Exception('No Data');
            }
            return $cases;
        
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * 评价
     * @param unknown_type $refId
     * @throws Exception
     * @return Ambigous <mixed, multitype:, string>
     */
    public static function getCustomerFeedBack($refId){
        try{
            $order = Service_Orders::getByField($refId,'refrence_no_platform');
            if(!$order){
                throw new Exception('数据错误,订单不存在-->'.$refId);
            }
            $buyerId = $order['buyer_id'];
            if(empty($buyerId)){
                throw new Exception('No Data');
            }
            $con = array('ecf_commenting_user'=>$buyerId);
            $feedbacks = Service_EbayCustomerFeedback::getByCondition($con);
            if(empty($feedbacks)){
                throw new Exception('No Data');
            }
            return $feedbacks;
        
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
}