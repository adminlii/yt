<?php
class Amazon_PriceService extends Amazon_FeedService
{
    protected $_feedType = '_POST_PRODUCT_PRICING_DATA_';
    protected $_priceListing = array();
    // 格式化数组,重写父类方法
    protected function array2xml($info, &$xml)
    {
        foreach($info as $key => $value){
            if(is_array($value)){
                if(is_numeric($key)){
                    $key = array_pop(array_keys($value));
                    $value = array_pop($value);
                }
                $subnode = $xml->addChild("{$key}");
                $this->array2xml($value, $subnode);
            }else{
                if(preg_match('/\s+/', $key)){ // 针对这种格式做特殊处理==========='SalePrice currency=USD' => 19.99
                    $split = preg_split('/\s+/', $key);
                    $key = array_shift($split);
                    
                    $subnode = $xml->addChild("{$key}", htmlspecialchars("$value"));
                    foreach($split as $v){
                        $arr = explode('=', $v);
                        $subnode->addAttribute($arr[0], $arr[1]);
                    }
                }else{
                    $subnode = $xml->addChild("{$key}", htmlspecialchars("$value"));
                }
            }
        }
    }
    

    public function getData()
    {
        // 数组，请严格按照该格式拼装
        $data = array();
        $data['Header'] = array(
            'DocumentVersion' => '1.01',
            'MerchantIdentifier' => $this->_MarketplaceId
        );
        $data['MessageType'] = 'Price';
        
        $con = array(
            'company_code' => $this->_company_code,
            'user_account' => $this->_user_account,
            'sync_status' => '0'
        );
        
        $listing = Service_AmazonMerchantListingPriceSet::getByCondition($con, '*', 0, 1);
        $this->_priceListing = $listing;
        if(empty($listing)){
            throw new Exception('没有需要更新的数据',999);
        }
        // print_r($listing);exit;
        foreach($listing as $k => $v){
            $arr = array(
                'Message' => array(
                    'MessageID' => $k + 1,
                    'OperationType' => 'Update',
                    'Price' => array(
                        'SKU' => $v['seller_sku'],
                        'StandardPrice currency=' . $v['regular_price_currency'] . '' => $v['regular_price']
                    )
                )
                
            );
            if($v['regular_price'] != $v['listing_price']){
                $arr['Message']['Price']['Sale'] = array(
                    'SalePrice currency=' . $v['listing_price_currency'] . '' => $v['listing_price'],
                    'StartDate' => date('Y-m-d\TH:i:s.000\Z', strtotime($v['start_date'])),
                    'EndDate' => date('Y-m-d\TH:i:s.000\Z', strtotime($v['end_date'])),
                );
            }
            $data[$k + 1] = $arr;
        }

        // $data = array();
        
        return $data;
    }
    
    public function getPriceListing(){
        return $this->_priceListing;
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