<?php
class Service_BuyerProcess
{


    /**
     * 相关bid
     * @param unknown_type $bid
     * @throws Exception
     * @return Ambigous <mixed, multitype:, string>
     */
    public static function getCustomerOrder($bid){
        try{
            $buyer = Service_Buyer::getByField($bid,'bid');
            if(!$buyer){
                throw new Exception('数据错误,bid不存在-->'.$bid);
            }
            $platform = $buyer['platform'];
            $buyerId = $buyer['buyer_account'];
            $buyerName = $buyer['buyer_name'];
            $buyerMail = $buyer['buyer_mail'];
            $con = array(
                'platform' => $platform,
                'buyer_id' => $buyerId,
//                 'buyer_name' => $buyerName,
//                 'buyer_mail' => $buyerMail,
            
            );
            $buyers = Service_Orders::getByCondition($con,'*',0,0,'order_id desc');
            $buyerStatus = Service_OrderProcess::$statusArr;
            $userAccountArr = Common_Common::getPlatformUser();
            foreach($buyers as $k=>$v){
                $v['order_status'] = $v['order_status'].'';
                
                
                $v['order_status_title'] = isset($buyerStatus[$v['order_status']])?$buyerStatus[$v['order_status']]['name']:'其他';
                $v['platform_user_name'] = empty($v)||!isset($userAccountArr[$v['user_account']])?'':$userAccountArr[$v['user_account']]['platform_user_name'];
                
                $v['subtotal'] = !isset($v['subtotal'])?0:$v['subtotal'];
                $v['ship_fee'] = !isset($v['ship_fee'])?0:$v['ship_fee'];
                $v['platform_fee'] = !isset($v['platform_fee'])?0:$v['platform_fee'];
                $v['finalvaluefee'] = !isset($v['finalvaluefee'])?0:$v['finalvaluefee'];
                
                $buyers[$k] = $v;

                if($v['order_status']=='0'||$v['order_status']=='1'){
                    unset($buyers[$k]);
                }
                 
            }
            if(empty($buyers)){
                throw new Exception('No Data');
            }
            return $buyers;
    
        }catch(Exception $e){
            return $e->getMessage();
        }
    
    }
    /**
     * message
     * @param unknown_type $bid
     * @throws Exception
     * @return Ambigous <mixed, multitype:, string>
     */
    public static function getCustomerMessage($bid){
        try{
            $buyer = Service_Buyer::getByField($bid,'bid');
            if(!$buyer){
                throw new Exception('数据错误,bid不存在-->'.$bid);
            }
            $buyerId = $buyer['buyer_account'];
            if(empty($buyerId)){
                throw new Exception('No Data');
            }
            if($buyer['platform']!='ebay'){
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
     * 推送邮件message
     * @param unknown_type $bid
     * @throws Exception
     * @return Ambigous <mixed, multitype:, string>
     */
    public static function getCustomerFeedbackMessage($bid){
        try{
            $buyer = Service_Buyer::getByField($bid,'bid');
            if(!$buyer){
                throw new Exception('数据错误,bid不存在-->'.$bid);
            }
            if($buyer['platform']!='ebay'){
                throw new Exception('No Data');
            }

            $buyerId = $buyer['buyer_account'];
            if(empty($buyerId)){
                throw new Exception('No Data');
            }
            $con = array('buyer_id'=>$buyerId);
            $field = array('title','user_account','create_time','sync_status','content','bid');
            
            $messages = Service_EbayFeedbackMessage::getByCondition($con,'*');
            
            if(empty($messages)){
                throw new Exception('No Data');
            }
            $syncStatusArr = array('0'=>'未同步到eBay','1'=>'已同步到eBay','2'=>'同步到eBay异常','3'=>'拦截成功');
            foreach($messages as $k=>$v){
                $v['sync_status_title'] = $syncStatusArr[$v['sync_status']]?$syncStatusArr[$v['sync_status']]:'状态异常';
                $messages[$k] = $v;
            }
            return $messages;
    
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * 推送邮件message
     * @param unknown_type $bid
     * @throws Exception
     * @return Ambigous <mixed, multitype:, string>
     */
    public static function addCustomerFeedbackMessage($bids,$title,$content){
        $return = array('ask'=>0,'message'=>'Fail');
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        $err = array();
        try{
            if(empty($title)){
                throw new Exception('title empty!');
            }
            if(empty($content)){
                throw new Exception('content empty!');                
            }
            foreach($bids as $bid){
                $buyer = Service_Buyer::getByField($bid,'bid');
                if(!$buyer){
                    throw new Exception('数据错误,bid不存在-->'.$bid);
                }                
                if($buyer['platform']!='ebay'){
                    $err[] = $buyer['buyer_name'].'不是eBay客户,无法添加Message';
                    continue;
                }
                $buyerId = $buyer['buyer_account'];
                if(empty($buyerId)){
                    $err[] = $buyer['buyer_name'].'没有对应的客户ID,无法添加Message';
                    continue;
                }
                $row = array(
                    'user_account' => $buyer['user_account'],
                    'buyer_id' => $buyer['buyer_account'],
                    'title' => $title,
                    'content' => $content,
                    'create_time'=>date('Y-m-d H:i:s')
                );
//                 print_r($row);exit;
                Service_EbayFeedbackMessage::add($row);
            }    
            $return['ask'] = 1;  
            $return['message'] = '操作完成';        
            $db->commit();    
        }catch(Exception $e){
            $db->rollback();
            $return['message'] = $e->getMessage();
        }

        $return['has_err'] = empty($err)?false:true;
        $return['err'] = $err;  
        return $return;
    }
    /**
     * 取消推送邮件message
     * @param unknown_type $bid
     * @throws Exception
     * @return Ambigous <mixed, multitype:, string>
     */
    public static function cancelCustomerFeedbackMessage($efm_id){
        $return = array('ask'=>0,'message'=>'Fail');
        try{
            $message = Service_EbayFeedbackMessage::getByField($efm_id,'efm_id');
            if($message['sync_status']!=0){
                throw new Exception('message已经同步到eBay，不可取消');
            }
            $updateRow = array('sync_status'=>'3');
            Service_EbayFeedbackMessage::update($updateRow, $efm_id,'efm_id');
            
//             Service_EbayFeedbackMessage::delete($efm_id,'efm_id');
            
            $return['ask'] = 1;
            $return['message'] = '拦截成功';
    
        }catch(Exception $e){
            $return['message'] =  $e->getMessage();
        }
        return $return;
    }
    /**
     * 纠纷
     */
    public static function getCustomerCase($bid){
        try{
            $buyer = Service_Buyer::getByField($bid,'bid');
            if(!$buyer){
                throw new Exception('数据错误,bid不存在-->'.$bid);
            }
            $buyerId = $buyer['buyer_account'];
            if(empty($buyerId)){
                throw new Exception('No Data');
            }
            if($buyer['platform']!='ebay'){
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
     * @param unknown_type $bid
     * @throws Exception
     * @return Ambigous <mixed, multitype:, string>
     */
    public static function getCustomerFeedBack($bid){
        try{
            $buyer = Service_Buyer::getByField($bid,'bid');
            if(!$buyer){
                throw new Exception('数据错误,bid不存在-->'.$bid);
            }
            $buyerId = $buyer['buyer_account'];
            if(empty($buyerId)){
                throw new Exception('No Data');
            }
            if($buyer['platform']!='ebay'){
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