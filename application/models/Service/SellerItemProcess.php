<?php
class Service_SellerItemProcess extends Common_Service
{

    /**
     * 更新产品的补货数量
     * 
     * @param unknown_type $params            
     * @throws Exception
     * @return multitype:number string multitype:unknown unknown
     */
    public function updateSupplyQtyTransaction($params)
    {
        $return = array(
            "ask" => 0,
            "message" => "Update Supply Qty"
        );
        
        $success = $fail = array();
        try{
            if(! is_array($params)){
                throw new Exception('参数错误。。。');
            }
            $db = Common_Common::getAdapter();
            foreach($params as $param){
                $itemId = $param['item_id'];
                $db->beginTransaction();
                $item = null;
                $content = '设置补货数量';
                try{
                    $item = Service_SellerItem::getByField($itemId, 'item_id');
                    $message = '';
                    
                    switch($item['sell_type']){
                        case '2':
                            $supQty = $param['qty'];
                            $this->_updateItemSupplyQty($item, $supQty);
                            $message = $itemId . '[' . $item['sku'] . ']补货数量从' . $item['supply_qty'] . '设置为' . $supQty;
                            $content .= $message . ';';
                            $success[] = array(
                                'item_id' => $itemId,
                                'sku' => $item['sku'],
                                'qty' => $supQty,
                                'message' => $message,
                                'clazz' => $itemId
                            );
                            
                            $variations = $param['var'];
                            if(empty($variations)){
                                throw new Exception('一口价多品，参数错误。。。。');
                            }
                            $content .= '';
                            foreach($variations as $variationId => $qty){
                                $variation = Service_SellerItemVariations::getByField($variationId, 'variation_id');
                                $this->_updateItemVariationSupplyQty($variationId, $qty);
                                $message = $itemId . '[多品][' . $variation['sku'] . ']补货数量从' . $variation['supply_qty'] . '设置为' . $qty;
                                $content .= $message . ';';
                                $success[] = array(
                                    'item_id' => $itemId,
                                    'sku' => $variation['sku'],
                                    'qty' => $qty,
                                    'message' => $message,
                                    'clazz' => $itemId . '_' . $variationId
                                );
                            }
                            break;
                        case '1':
                            $supQty = $param['qty'];
                            $this->_updateItemSupplyQty($item, $supQty);
                            $message = $itemId . '[' . $item['sku'] . ']补货数量从' . $item['supply_qty'] . '设置为' . $supQty;
                            $content .= $message . ';';
                            $success[] = array(
                                'item_id' => $itemId,
                                'sku' => $item['sku'],
                                'qty' => $supQty,
                                'message' => $message,
                                'clazz' => $itemId
                            );
                            break;
                        default:
                    }
                    /**
                     * 日志
                     */
                    $this->log($itemId, $content);
                    $db->commit();
                }catch(Exception $e){
                    $db->rollback();
                    $fail[] = array(
                        'item_id' => $itemId,
                        'sku' => $item ? $item['sku'] : '',
                        'message' => $e->getMessage()
                    );
                    /**
                     * 日志
                     */
                    $content = '设置补货数量失败，原因：' . $e->getMessage();
                    $this->log($itemId, $content);
                }
            }
            $return['ask'] = 1;
            $return['message'] = '数据操作完毕。。';
        }catch(Exception $ee){
            $return['message'] = $ee->getMessage();
        }
        $return['success'] = $success;
        $return['fail'] = $fail;
        return $return;
    }

    /**
     * 更新补货数量
     * 
     * @param unknown_type $item            
     * @param unknown_type $supQty            
     * @throws Exception
     */
    private function _updateItemSupplyQty($item, $supQty)
    {
        if($item['item_status'] != 'Active'){
            throw new Exception('SKU:' . $item['sku'] . '不是在售产品，不可补货');
        }
        if(! in_array($item['sell_type'], array(
            '1',
            '2'
        ))){
            throw new Exception('SKU:' . $item['sku'] . '销售类型错误，不可补货');
        }
        
        $endTime = strtotime($item['end_time']) + 8 * 3600;
        $now = strtotime(date("Y-m-d H:i:s"));
        if($endTime < $now){ // 产品已经过了有效期
//             throw new Exception('SKU:' . $item['sku'] . '已经过了有效期,不可补货');
        }
        $updateRow = array(
            'need_supply' => '1',
            'sync_status' => '0',
            'supply_qty' => $supQty,
            'sync_status' => '0'
        );
        Service_SellerItem::update($updateRow, $item['item_id'], 'item_id');
    }

    /**
     * 更新多品补货数量
     * 
     * @param unknown_type $variationId            
     * @param unknown_type $supQty            
     * @throws Exception
     */
    private function _updateItemVariationSupplyQty($variationId, $supQty)
    {
        $updateRow = array(
            'sync_status' => '0',
            'supply_qty' => $supQty
        );
        $variation = Service_SellerItemVariations::getByField($variationId, 'variation_id');
        if(! $variation){
            throw new Exception('variation not exists');
        }
        Service_SellerItemVariations::update($updateRow, $variationId, 'variation_id');
    }

    /**
     * 获取item详情
     * 
     * @param unknown_type $itemId            
     */
    public static function getSellerItemDetail($itemId)
    {
        $item = Service_SellerItem::getByField($itemId, 'item_id');
        if(!$item){
            return false;
        }
        $v = $item;
        $statusArr = array(
            'Active' => '销售中',
            'Completed' => '已下架',
            'Ended' => '已下架'
        );
        $typeArr = array(
            '0' => '拍卖',
            '1' => '一口价(单品)',
            '2' => '一口价(多品)'
        );
        
        $endTime = strtotime($v['end_time']) + 8 * 3600;
        $now = strtotime(date("Y-m-d H:i:s"));
        if($endTime < $now){ // 产品已经过了有效期
//             $v['item_status'] = 'Ended';
        }
        $v['start_time'] = date('Y-m-d H:i:s', strtotime($v['start_time']) + 8 * 3600);
        $v['end_time'] = date('Y-m-d H:i:s', strtotime($v['end_time']) + 8 * 3600);
        
        $v['item_status_title'] = $statusArr[$v['item_status']];
        $v['sell_type_title'] = $typeArr[$v['sell_type']];
        
        if($v['sell_type'] == '0'){}
        switch($v['sell_type']){
            case '2':
                $con = array(
                    'item_id' => $v['item_id']
                );
                // print_r($con);exit;
                $variations = Service_SellerItemVariations::getByCondition($con);
                // print_r($variations);exit;
                $variationsT = array();
                foreach($variations as $kk => $variation){
                    $con = array(
                        'variation_id' => $variation['variation_id']
                    );
                    $variationAttr = Service_SellerItemVariationsAttr::getByCondition($con);
                    $variation['attr'] = $variationAttr;
                    $variations[$kk] = $variation;
                    $variationsT[$variation['variation_id']] = $variation;
                }
                $v['variation'] = $variationsT;
                break;
            default:
        }
        
        $picArr = $v['pic_path'] ? explode('#:|:#', $v['pic_path']) : '';
        if(! empty($picArr)){
            $v['src'] = $picArr[0];
        }else{
            $v['src'] = '/images/base/noimg.jpg';
        }
        return $v;
    }

    /**
     * 加入黑名单
     */
    public static function addToBlackList($userAccount, $sku, $itemId = '')
    {
        $return = array(
            "ask" => 0,
            "message" => "Fail.."
        );
        $content = 'SKU加入黑名单';
        try{
            if(empty($userAccount)){
                throw new Exception('参数错误，缺少账号参数');
            }
            if(empty($sku)){
                throw new Exception('参数错误，缺少产品参数');
            }
            $row = array(
                'company_code' => Common_Company::getCompanyCode(),
                'user_account' => $userAccount,
                'sku' => $sku
            );
            $exists = Service_SellerItemBlackList::getByCondition($row);
            if(! $exists){
                Service_SellerItemBlackList::add($row);
                $return = array(
                    "ask" => 1,
                    "message" => "该SKU成功加入黑名单"
                );
                // log
            }else{
                $return = array(
                    "ask" => 0,
                    "message" => "该SKU已经存在于黑名单"
                );
            }
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        /**
         * 日志
         */
        $content .= 'SKU:' . $sku . ',' . $return['message'];
        self::log($itemId, $content);
        
        return $return;
    }

    /**
     * 解除黑名单
     */
    public static function releaseBlackList($userAccount, $sku, $itemId = '')
    {
        $return = array(
            "ask" => 0,
            "message" => "Fail.."
        );
        $content = 'SKU解除黑名单';
        try{
            if(empty($userAccount)){
                throw new Exception('参数错误，缺少账号参数');
            }
            if(empty($sku)){
                throw new Exception('参数错误，缺少产品参数');
            }
            
            $row = array(
                'user_account' => $userAccount,
                'sku' => $sku
            );
            $exists = Service_SellerItemBlackList::getByCondition($row);
            if($exists){
                foreach($exists as $v){
                    Service_SellerItemBlackList::delete($v['sibl_id'], 'sibl_id');
                }
            }
            $return = array(
                "ask" => 1,
                "message" => "该SKU成功从黑名单中移除"
            );
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        /**
         * 日志
         */
        $content = 'SKU:' . $sku . ',' . $return['message'];
        self::log($itemId, $content);
        return $return;
    }

    /**
     * 补货同步
     * 
     * @param array $itemIds            
     */
    public function syncSupplyQty($itemIds,$force=false)
    {
        return $this->syncSupplyQtyNew($itemIds,$force);
    }

    /**
     * 补货同步
     *
     * @param array $itemIds
     */
    public function syncSupplyQtyNew($itemIds,$force=false)
    {
        $return = array(
                "ask" => 0,
                "message" => "Request Err"
        );
        $success = $fail = array();
        try{
            if(! is_array($itemIds)){
                throw new Exception('参数错误');
            }
            $itemIds = array_unique($itemIds);
    
            $itemArr = array();
            $inventoryArr = array();
            foreach($itemIds as $itemId){
                $item = $this->getSellerItemDetail($itemId);
                try{

                    if(!$item){
                        throw new Exception('ItemID:' . $itemId .'不存在或已删除');
                    }
                    
                    if(! $item['need_supply']){
                        throw new Exception('ItemID:' . $itemId . '未设置补货');
                    }

                    if(empty($item['supply_qty']) && intval($item['supply_qty']) !== 0){
                        throw new Exception('ItemID:' . $itemId . '未设置补货数量');
                    }


                    if(!$force){//强制同步
                        if($item['sync_status'] == '1'){
                            continue;
                            //                             throw new Exception('ItemID:' . $itemId . '已经同步过，无需重复同步');
                        }
                    }
                    
                    // 获取账号对应黑名单sku 开始。。。
                    $con = array(
                            'user_account' => $item['user_account'],
                            'company_code' => $item['company_code'],
                    );
                    $blackList = Service_SellerItemBlackList::getByCondition($con);
                    $blackListSku = array();
                    foreach($blackList as $v){
                        $blackListSku[] = $v['sku'];
                    }
                    // 获取账号对应黑名单sku 结束。。。
                    
                    if(in_array($item['sku'], $blackListSku)){
                        throw new Exception('ItemID:' . $itemId . '对应SKU:' . $item['sku'] . '存在于黑名单中，不可更新补货数量');
                    }
                    $kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk = $item['company_code'].'*#*#*'.$item['user_account'];
                    switch($item['sell_type']){
                        case '2':
                            if(empty($item['variation'])){
                                throw new Exception('ItemID[多品]:' . $itemId.'缺少多品参数variation');
                            }
                            $content .= '多品补货设置(';
                            foreach($item['variation'] as $v){
                                $attr = array();
                                foreach($v['attr'] as $vv){
                                    $attr[$vv['key']] = $vv['val'];
                                }
                                if(empty($v['supply_qty']) && intval($v['supply_qty']) !== 0){
                                    throw new Exception('ItemID:' . $itemId .'对应SKU:' . $v['sku'] . '未设置补货数量');
                                }
                                //
                                if(in_array($v['sku'], $blackListSku)){
                                    throw new Exception('ItemID[多品]:' . $itemId . '对应SKU:' . $v['sku'] . '存在于黑名单中，不可更新补货数量');
                                }  
                                $inventoryArr[$kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk][] = array(
                                    'item_id' => $itemId,
                                    'user_account' => $item['user_account'],
                                    'company_code' => $item['company_code'],
                                    'sku' => $v['sku'],
                                    'qty' => $v['supply_qty'],
                                    'sell_qty' => $v['qty'],
                                    'sold_qty' => $v['qty_sold'],
                                    'supply_qty' => $v['supply_qty']
                                );
                            }
                            $content .= ')';
                            break;
                        case '1':
                            $inventoryArr[$kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk][] = array(
                                'item_id' => $itemId,
                                'user_account' => $item['user_account'],
                                'company_code' => $item['company_code'],
                                'sku' => $item['sku'],
                                'qty' => $item['supply_qty'],
                                'sell_qty' => $item['sell_qty'],
                                'sold_qty' => $item['sold_qty'],
                                'supply_qty' => $item['supply_qty']
                            );
                            break;
                        default:
                    }
                    
                }catch (Exception $e){
                    $fail[] = array(
                            'item_id' => $itemId,
                            'sku' => $item ? $item['sku'] : '',
                            'message' => $e->getMessage()
                    );
                    /**
                     * 日志
                     */
                    $content = '补货失败，原因:' . $e->getMessage();
                    $this->log($itemId, $content, 2);
                }                
            }
//             print_r($inventoryArr);exit;
            $mailData = array();
            $failSupply = array();
            if($inventoryArr){//需要同步的产品
                foreach($inventoryArr as $kkkk=>$params){   
                    $kkkk = explode('*#*#*', $kkkk);
                    $companyCode = $kkkk[0];
                    $userAccount = $kkkk[1];
                    $token=Ebay_EbayLib::getUserToken($userAccount,$companyCode);
                    
                    $chunks = array_chunk($params, 4);
                    foreach($chunks as $chunk){

                        $chunkClone = array();
                        foreach($chunk as $v){
                            $v['sku'] = trim($v['sku']);
                            $chunkClone[$v['item_id'].'_'.$v['sku']] = $v;
                            $this->log($v['item_id'], $v['sku'].'开始补货', 2);
                            $updateRow = array(
                                    'sync_status' => '4',//补货中
                                    'sync_time' => date('Y-m-d H:i:s')
                            );
                            Service_SellerItem::update($updateRow, $v['item_id'], 'item_id');
                        }
                        $data = Ebay_EbayLib::ReviseInventoryStatus($token, $chunk);
                        $data['ReviseInventoryStatusResponse']['user_account'] = $userAccount;
                        $data['ReviseInventoryStatusResponse']['company_code'] = $companyCode;
                        if($data['ReviseInventoryStatusResponse']['Ack'] != 'Failure'){
                            $InventoryStatus = $data['ReviseInventoryStatusResponse']['InventoryStatus'];
                            if(!isset($InventoryStatus[0])){
                                $InventoryStatusT = array();
                                $InventoryStatusT[] = $InventoryStatus;
                                $InventoryStatus = $InventoryStatusT;
                            }
                            foreach($InventoryStatus as $v){
                                $itemId = $v['ItemID'];
                                $i = $chunkClone[$itemId.'_'.$v['SKU']];
                                unset($chunkClone[$itemId.'_'.$v['SKU']]);
                                
                                $updateRow = array(
                                        'sync_status' => '1',
                                        'sync_time' => date('Y-m-d H:i:s')
                                );
                                Service_SellerItem::update($updateRow, $itemId, 'item_id');
                                $success[] = array(
                                        'item_id' => $itemId,
                                        'sku' => $v['SKU'],
                                        'supply_qty'=>$i?$i['supply_qty']:''
                                );
                                /**
                                 * 日志
                                */
                                $content = '补货成功,SKU:'.$v['SKU'].($i?',补货数量为:'.$i['supply_qty']:'').',返回参数'.print_r($v,true);
                                $this->log($itemId, $content, 2);
                                /**
                                 * 补货成功， 写入补货日志
                                 */
                                if($i){
                                    $supLogRow = array(
                                        'item_id' => $itemId,
                                        'sku' => $v['SKU'],
                                        'sell_qty' => $i['sell_qty'],
                                        'sold_qty' => $i['sold_qty'],
                                        'supply_qty' => $i['supply_qty']
                                    );
                                    Service_SellerItemSupLog::add($supLogRow);
                                }
                                
                            }
                        }

                        $Errors = $data['ReviseInventoryStatusResponse']['Errors'];
                        if(isset($Errors)){
                            if(!isset($Errors[0])){
                                $ErrorsT = array();
                                $ErrorsT[] = $Errors;
                                $Errors = $ErrorsT;
                            }
                            foreach($Errors as $kkk=>$err){
                                $itemId = $err['ErrorParameters'][0]['Value'];
                                $sku = $err['ErrorParameters'][1]['Value']?$err['ErrorParameters'][1]['Value']:'';
                                if($itemId&&$err['ErrorCode'] !='21917092'){//ebay返回的信息提示,数量未变化
                                    $this->log($itemId, '['.$sku.']eBay返回信息：['.$err['ErrorCode'].']'.$err['LongMessage'], 2);
                                }
                                if($itemId&&$err['ErrorCode'] =='21917092'){//Lvis validation blocked.销售限制
                                    $this->log($itemId, '['.$sku.']eBay返回信息：['.$err['ErrorCode'].']'.$err['LongMessage'], 2);
                                    $updateRow = array(
                                            'sync_status' => '4',
                                            'sync_time' => date('Y-m-d H:i:s')
                                    );
                                    Service_SellerItem::update($updateRow, $itemId, 'item_id');
                                }
                                if($itemId&&$err['ErrorCode'] =='21916293'){//ebay返回的信息提示, You are not allowed to revise an ended item "190961417191" or sku "DE6CXL214".
                                    unset($chunkClone[$itemId.'_'.$sku]);
                                    $updateRow = array(
                                            'item_status'=>'Ended',
                                            'sync_status' => '3',
                                            'sync_time' => date('Y-m-d H:i:s')
                                    );
                                    Service_SellerItem::update($updateRow, $itemId, 'item_id');
                                }
                                if($itemId&&$err['SeverityCode']=='Warning'){
                                    unset($Errors[$kkk]);
                                }
                            }
                            if(!empty($Errors)){
                                $mailData[] = $Errors;
                            }   
//                             Ec::showError(print_r($data, true), 'item_supply_qty_sync_errors_');                     
                        }
                        
                        if(!empty($chunkClone)){
                            $failSupply[] = $chunkClone;  
                            
                            foreach($chunkClone as $c){//异常
                                $itemId = $c['item_id'];
                                $updateRow = array(
                                        'sync_status' => '3',
                                        'sync_time' => date('Y-m-d H:i:s')
                                );
                                Service_SellerItem::update($updateRow, $itemId, 'item_id');
                                $this->log($itemId, $c['sku'].'补货失败', 2);
                                $fail[] = array(
                                    'item_id' => $itemId,
                                    'sku' => $c['sku'],
                                    'message' => '补货失败,请查看eBay上SKU是否包含有空格或sku已删除'
                                );
                            }                          
                        }
                    }
                }
            }
            if(!empty($failSupply)||!empty($mailData)){
                $body = '';
                if($failSupply){
                    $body = print_r($failSupply,true);
                    $body = str_replace("\n", '<br/>', $body);
                    $body = str_replace(" ", '&nbsp;', $body);
                    $body .= '<hr/><br/>'.serialize($failSupply);
                }
                $mailDataStr = '';
                if($mailData){
                    $mailDataStr = print_r($mailData,true);
                    $mailDataStr = str_replace("\n", '<br/>', $mailDataStr);
                    $mailDataStr = str_replace(" ", '&nbsp;', $mailDataStr);
                    $mailDataStr .= '<hr/><br/>'.serialize($mailData);
                }                
                $mailParam = array(
                        'bodyType' => 'html',
                        'email' => array('eb-error@eccang.com'),
                        'subject' => '补货不成功Item ['. date('Y-m-d H:i:s').']',
                        'body' => $body."<hr/>".$mailDataStr,
                );
                Common_Email::send($mailParam);
            }
            
            $return['ask'] = 1;
            $return['message'] = '补货同步结果....';
        }catch(Exception $ee){
            $return['message'] = $ee->getMessage();
        }
    
        $return['success'] = $success;
        $return['fail'] = $fail;
        // print_r($return);exit;
        return $return;
    }
    
    /**
     * 操作日志，如修改补货数量，手动同步到ebay，加入黑名单，取消黑名单等。。。
     * $type=1 操作日志 $type=2 补货日志
     * 
     * @param unknown_type $logRow            
     */
    public static function log($itemId, $content = '', $type = '1')
    {
        $row = array();
        $row['content'] = $content;
        $row['item_id'] = $itemId;
        $row['type'] = $type;
        Service_SellerItemOpLog::add($row);
    }

    /**
     * 获取当前正在出售的产品
     * @return unknown
     */
    public static function getActiveSellerItem(){
        $sql = "SELECT * FROM `seller_item` WHERE (1 =1) AND (item_status = 'Active')";
        
        $db = Common_Common::getAdapter();
        $items = $db->fetchAll($sql);
        return $items;
    }
    /**
     * 自动将补货数量同步到ebay
     */
    public function syncSupplyQtyAuto($account='',$company_code='')
    {
        while(true){
            $sql = "SELECT item_id FROM `seller_item` WHERE (1 =1) AND (item_status = 'Active')  AND (need_supply = '1') and sync_status in (0,2) ";
            if($account){
                $sql.=" and user_account='{$account}'";
            }
            if($company_code){
                $sql.=" and company_code='{$company_code}'";
            }
            $sql.=' limit 1';
            $db = Common_Common::getAdapter();
            $item = $db->fetchRow($sql);
            if($item){
                $return = $this->syncSupplyQtyNewSingle($item['item_id']);
                print_r($return);                 
            }else{
                break;
            }
        }
    }

    /**
     * 补货
     * @param unknown_type $itemId
     * @return false or array
     */
    public static function syncSupplyQtyNewSingle($itemId)
    {
        $return = array();
        $inventoryArr = array();
        $item = Service_SellerItemProcess::getSellerItemDetail($itemId);
        $token = Ebay_EbayLib::getUserToken($item['user_account'],$item['company_code']);
        if(!$token){
            return false;
        }
        // 获取账号对应黑名单sku 开始。。。
        $con = array(
                'user_account' => $item['user_account']
        );
        $blackList = Service_SellerItemBlackList::getByCondition($con);
        $blackListSku = array();
        foreach($blackList as $v){
            $blackListSku[] = $v['sku'];
        }
        // 获取账号对应黑名单sku 结束。。。
        if(in_array($item['sku'], $blackListSku)){//黑名单，跳过补货
            $content = '补货失败,SKU:' . $item['sku'] . '在补货黑名单中，无法进行补货';
            Service_SellerItemProcess::log($itemId, $content, 4);
            return false;
        }
    
        if($item['sell_type'] == '1'){
            if(intval($item['supply_qty'])>=0){
                $inventoryArr[] = array(
                        'item_id' => $itemId,
                        'sell_type'=>$item['sell_type'],
                        'user_account' => $item['user_account'],
                        'sku' => $item['sku'],
                        'qty' => $item['supply_qty'],
                        'sell_qty' => $item['sell_qty'],
                        'sold_qty' => $item['sold_qty'],
                        'supply_qty' => $item['supply_qty'],
                        'sync_status'=>$item['sync_status']
                );
            }
        }else{
            foreach($item['variation'] as $v){
                if(intval($v['supply_qty'])<0){
                    continue;
                }
                if($v['sync_status']=='1'){
                    continue;
                }
                if(in_array($v['sku'], $blackListSku)){//黑名单，跳过补货
                    $content = '补货失败,SKU:' . $v['sku'] . '在补货黑名单中，无法进行补货';
                    Service_SellerItemProcess::log($itemId, $content, 4);
                    continue;
                }
                 
                $inventoryArr[] = array(
                        'item_id' => $itemId,
                        'sell_type'=>$item['sell_type'],
                        'variation_id'=>$v['variation_id'],
                        'user_account' => $item['user_account'],
                        'sku' => $v['sku'],
                        'qty' => $v['supply_qty'],
                        'sell_qty' => $v['qty'],
                        'sold_qty' => $v['qty_sold'],
                        'supply_qty' => $v['supply_qty'],
                        'sync_status'=>$v['sync_status']
                );
            }
        }
        if(empty($inventoryArr)){//没有需要补货的产品，表示补货成功
            $updateRow = array(
                    'sync_status' => '1',
                    'sync_time' => date('Y-m-d H:i:s')
            );
            Service_SellerItem::update($updateRow, $itemId, 'item_id');
        }
    
    
        foreach($inventoryArr as $inventory){
            $data = Ebay_EbayLib::ReviseInventoryStatusSingle($token, $inventory);
            $inventory['Ack'] = $data['ReviseInventoryStatusResponse']['Ack'];
            if($data['ReviseInventoryStatusResponse']['Ack'] != 'Failure'){
                $InventoryStatus = $data['ReviseInventoryStatusResponse']['InventoryStatus'];
    
                if(! isset($InventoryStatus[0])){
                    $InventoryStatusT = array();
                    $InventoryStatusT[] = $InventoryStatus;
                    $InventoryStatus = $InventoryStatusT;
                }
                $inventory['InventoryStatus'] = $InventoryStatus;
                foreach($InventoryStatus as $v){
                    /*
                     $updateRow = array(
                             'sync_status' => '1',
                             'sync_time' => date('Y-m-d H:i:s')
                     );
                    Service_SellerItem::update($updateRow, $itemId, 'item_id');
                    */
    
                    if($inventory['sell_type']=='1'){
                        $i = Service_SellerItem::getByField($itemId, 'item_id');
                        if($i['sync_status']!='1'){//避免垃圾日志
                            $tt = array();
                            foreach($v as $kkk => $vvv){
                                $tt[] = $kkk . ':' . $vvv;
                            }
                            $content = '补货成功,SKU:' . $v['SKU'] . ',补货数量为:' . $inventory['supply_qty'] . ',返回参数:' . implode('; ', $tt);
                            Service_SellerItemProcess::log($itemId, $content, 2);
                        }
                        $updateRow = array(
                                'sync_status' => '1',
                                'sync_time' => date('Y-m-d H:i:s')
                        );
                        Service_SellerItem::update($updateRow, $itemId, 'item_id');
                    }else{
                        $i = Service_SellerItemVariations::getByField($inventory['variation_id'], 'variation_id');
                        if($i['sync_status']!='1'){//避免垃圾日志
                            $tt = array();
                            foreach($v as $kkk => $vvv){
                                $tt[] = $kkk . ':' . $vvv;
                            }
                            $content = '补货成功,SKU:' . $v['SKU'] . ',补货数量为:' . $inventory['supply_qty'] . ',返回参数:' . implode('; ', $tt);
                            Service_SellerItemProcess::log($itemId, $content, 2);
                        }
                        $updateRow = array(
                                'sync_status' => '1',
                                'sync_time' => date('Y-m-d H:i:s')
                        );
                        Service_SellerItemVariations::update($updateRow, $inventory['variation_id'], 'variation_id');
                    }
    
    
                }
            }
    
            $inventory['Error'] = array();
            $Errors = $data['ReviseInventoryStatusResponse']['Errors'];
            if(isset($Errors)){
                $eee = array();
                if(! isset($Errors[0])){
                    $ErrorsT = array();
                    $ErrorsT[] = $Errors;
                    $Errors = $ErrorsT;
                }
                foreach($Errors as $kkk => $err){
                    $itemId = $err['ErrorParameters'][0]['Value'];
                    $sku = $err['ErrorParameters'][1]['Value'] ? $err['ErrorParameters'][1]['Value'] : '';
                    if($itemId && $err['ErrorCode'] != '21917092'){ // ebay返回的信息提示,数量未变化
                        Service_SellerItemProcess::log($itemId, '[' . $sku . ']eBay返回信息：[' . $err['ErrorCode'] . ']' . $err['LongMessage'], 3);
                    }elseif($itemId && $err['ErrorCode'] == '21916293'){ // Lvis
                        // validation
                        // blocked.销售限制
                        Service_SellerItemProcess::log($itemId, '[' . $sku . ']eBay返回信息：[' . $err['ErrorCode'] . ']' . $err['LongMessage'], 4);
                        $updateRow = array(
                                'sync_status' => '4',
                                'sync_time' => date('Y-m-d H:i:s')
                        );
                        Service_SellerItem::update($updateRow, $itemId, 'item_id');
                    }elseif($itemId && $err['ErrorCode'] == '21916333'){ // ebay返回的信息提示,
                        // You
                        // are
                        // not
                        // allowed
                        // to
                        // revise
                        // an
                        // ended
                        // item
                        // "190961417191"
                        // or
                        // sku
                        // "DE6CXL214".
                        $updateRow = array(
                                'item_status' => 'Ended',
                                'sync_status' => '3',
                                'sync_time' => date('Y-m-d H:i:s')
                        );
                        Service_SellerItem::update($updateRow, $itemId, 'item_id');
                        Service_SellerItemProcess::log($itemId, '[' . $sku . ']eBay返回信息：[' . $err['ErrorCode'] . ']' . $err['LongMessage'], 3);
                    }
                    unset($err['ErrorParameters']);
                    $inventory['Error'][] = $err;
                }
            }
            $return[] = $inventory;
        }
    
        return $return;
    }
    
}