<?php
class Amazon_InventoryService extends Amazon_FeedService
{

    protected $_feedType = '_POST_INVENTORY_AVAILABILITY_DATA_';
    
    protected $_inventoryListing = array();
    
    protected $_MessageArr = array();

    /**
     * 验证补货状态,账号状态
     */
    private function _checkAccSupplyStatus(){
        $sql = "SELECT * from platform_user where user_account='{$this->_user_account}';";
    
        $row = Common_Common::fetchRow($sql);
        if(! $row){
            throw new Exception('账号不存在');
        }
    
        $sql = "SELECT * from platform_user_supply_set where pu_id='{$row['pu_id']}';";
        $platform_user_supply_set = Common_Common::fetchRow($sql);
        // 未设置补货，不补货
        // 验证账号是否开启补货，未开启补货任务，不补货
        // 验证账号是否有效，无效，不补货
        if(! $platform_user_supply_set || $platform_user_supply_set['status'] != 1 || $row['status'] != 1){
            throw new Exception("账号补货禁用/账号禁用/未设置补货,无需补货");
        }
    }
    
    public function getData()
    {
        //验证补货状态,账号状态
        $this->_checkAccSupplyStatus();
        //补货数据初始化
        $supProcess = new Common_SupplyQtyProcess();
        $supProcess->setCompanyCode($this->_company_code);
        $supProcess->setUserAccount($this->_user_account);
        $supProcess->init();//初始化补货数
        
        // 数组，请严格按照该格式拼装
        $data = array();
        $data['Header'] = array(
            'DocumentVersion' => '1.01',
            'MerchantIdentifier' => $this->_MarketplaceId
        );
        $data['MessageType'] = 'Inventory';
        $con = array(
            'company_code' => $this->_company_code,
            'user_account' => $this->_user_account,
//             'sync_status' => '0',//同步状态
            'status' => '1',//生效状态
            'platform' => 'amazon'
        )
        ;
        $listing = Service_SellerItemSupplyQty::getByCondition($con, '*', 0, 1);
        foreach($listing as $k=>$v){
            if($v['sync_status']==1){
                unset($listing[$k]);
            }
        }
        if($listing){//日志
            Ec::showError(print_r($con,true)."\n".print_r($listing,true),"_AmazonInventoryService_".date('Y-m-d'));
        }
        $this->_inventoryListing = $listing;
        $MessageArr = array();
        foreach($listing as $k => $v){
            //FBA是不是能够补货？？==============================
            $conn = array(
                'company_code' => $this->_company_code,
                'user_account' => $this->_user_account,
//                 'fulfillment_channel' => 'DEFAULT',
                'seller_sku'=>$v['sku'],
//                 'item_status'=>'on_sale',
//                 'product_id'=>$v['item_id']
            );
            $exist = Service_AmazonMerchantListing::getByCondition($conn);  
//             print_r($exist);exit; 
            if(empty($exist)){//客户发货的产品，才可以补货
                Common_ApiProcess::log($this->_user_account.'==>'.$v['sku'].'不存在');                
                Service_SellerItemSupplyQty::delete($v['id'],'id');
                continue;
            }   
            $qty = $v['qty'];     
            if($v['supply_type']=='2'){
                //补货数为空，跳过
                if($qty===''){
                    continue;
                }
            }else{
                //按仓库补货产品，计算补货库存
                $process = new Common_SupplyQtyProcess();
                try {
                    $qty = $process->getWarehouseInventory($v['user_account'], $v['sku'], $v['supply_warehouse'], array()); 
                    $updateArr = array('qty'=>$qty);
                    Service_SellerItemSupplyQty::update($updateArr,$v['id'],'id');
                } catch (Exception $e) {
                    $qty = '';
                }                
                //补货数为空，跳过
                if($qty===''){
                    continue;
                }                
            } 
            $MessageArr[] = array(
                'Message' => array(
                    'MessageID' => $k + 1,
                    'OperationType' => 'Update',
                    'Inventory' => array(
                        // 'SwitchFulfillmentTo' => 'MFN',
                        'SKU' => $v['sku'],
                        'Quantity' => $qty
                    )
                )
            );
        }
        $this->_MessageArr = $MessageArr;
        if(empty($MessageArr)){
            throw new Exception('没有需要更新的数据',999);
        }

        foreach($MessageArr as $k=>$v){
            $v['Message']['MessageID'] = $k+1;
            $MessageArr[$k] = $v;
        }
        Ec::showError(print_r($con,true)."\n".print_r($MessageArr,true),"_AmazonInventoryService_".date('Y-m-d'));
       
        foreach($MessageArr as $k=>$v){
            $data[$k+1] = $v;
        }
//         print_r($data);exit;
        return $data;
    }
    
    /**
     * 补货的产品
     * @return multitype:
     */
    public function getInventoryListing(){
        return $this->_inventoryListing;
    }

    /**
     * 补货的产品
     * @return multitype:
     */
    public function getMessageArr(){
        return $this->_MessageArr;
    }
    /**
     * 继承父类，重写该方法
     *
     * @return string
     */
    public function getXml()
    {
        $data = $this->getData();
        $feed = $this->getXmlContent($data);
        return $feed;
    }
}