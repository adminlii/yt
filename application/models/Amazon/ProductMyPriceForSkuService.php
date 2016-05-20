<?php
// SELECT * from amazon_merchant_listing GROUP BY product_id,seller_sku HAVING COUNT(*)>1;
// SELECT * FROM `amazon_merchant_listing` group by user_account,seller_sku HAVING COUNT(*)>1;
require_once 'XmlHandle.php';
class Amazon_ProductMyPriceForSkuService extends Amazon_Service
{

    private $_err = array();

    private $_success = array();
    /**
     * 构造器
     */
    public function __construct($token_id, $token, $saller_id, $site)
    {
        // 访问秘钥ID
        $this->_tokenConfig['AWS_ACCESS_KEY_ID'] = $token_id;
        // 访问秘钥
        $this->_tokenConfig['AWS_SECRET_ACCESS_KEY'] = $token;
        // 销售ID
        $this->_tokenConfig['MERCHANT_ID'] = $saller_id;
        // 站点
        $this->_tokenConfig['SITE'] = $site;
        // 应用名称
        $this->_tokenConfig['APPLICATION_NAME'] = Amazon_AmazonLib::APPLICATION_NAME;
        // 应用版本
        $this->_tokenConfig['APPLICATION_VERSION'] = Amazon_AmazonLib::APPLICATION_VERSION;
    
        /*
         * 秘钥
        */
        $countryCode = $this->_tokenConfig['SITE'];
    
        /*
         * 2. 取得亚马逊站点、地址
        */
        $amazonConfig = Amazon_AmazonLib::getAmazonConfig();
        if(empty($amazonConfig[$countryCode])){
            throw new Exception("amzon站点： $countryCode ，未能找到对应的亚马逊服务地址及商城编号.");
        }
        $this->_MarketplaceId = $amazonConfig[$countryCode]['marketplace_id'];
        /*
         * 3. 初始化配置信息，创建request对象
        */
        $serviceUrl = $amazonConfig[$countryCode]['service_url'];
        $config = array(
                'ServiceURL' => $serviceUrl,
                'ProxyHost' => null,
                'ProxyPort' => - 1,
                'MaxErrorRetry' => 3
        );
        $this->_config = $config;
        $this->_config['ServiceURL'] .= '/Products/2011-10-01';
        $service = new MarketplaceWebServiceProducts_Client($this->_tokenConfig['AWS_ACCESS_KEY_ID'], $this->_tokenConfig['AWS_SECRET_ACCESS_KEY'], $this->_tokenConfig['APPLICATION_NAME'], $this->_tokenConfig['APPLICATION_VERSION'], $this->_config);
        
        $this->_service = $service;
    }
    /**
     * $SellerSKUList 最大长度为20============================
     * 
     * @param unknown_type $IdArr            
     */
    public function GetMyPriceForSKU($SellerSKUList)
    {
        $service = $this->_service;
        
        $request = new MarketplaceWebServiceProducts_Model_GetMyPriceForSKURequest();
        $request->setSellerId($this->_tokenConfig['MERCHANT_ID']);
        $request->setMarketplaceId($this->_MarketplaceId);
        
        $IdList = new MarketplaceWebServiceProducts_Model_SellerSKUListType();
        sort($SellerSKUList);
        $IdList->setSellerSKU($SellerSKUList);
        
        $request->setSellerSKUList($IdList);
//         print_r($SellerSKUList);exit;
        return $this->invokeGetMyPriceForSKU($service, $request);
    }

    function invokeGetMyPriceForSKU(MarketplaceWebServiceProducts_Client $service, $request)
    {
        $rs = array(
            'ask' => 0,
            'message' => 'Fail.'
        );
        $xml = '';
        try{
            $response = $service->GetMyPriceForSKU($request);
            $this->_responseMetadata($response);
            
            Amazon_Service::log("Service Response=============================================================================");
            
            $dom = new DOMDocument();
            $dom->loadXML($response->toXML());
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $xml = $dom->saveXML();
            file_put_contents(APPLICATION_PATH . '/../data/log/invokeGetMyPriceForSKU.xml', $xml);
            $data = XML_unserialize($xml);
            $results = $response->getGetMyPriceForSKUResult();
            foreach($results as $result){
                $this->_saveMyPriceForSKU($result);
                // file_put_contents(APPLICATION_PATH.'/../data/log/invokeGetMyPriceForSKU0.txt', print_r($result,true));exit;
            }
            
//             file_put_contents(APPLICATION_PATH . '/../data/log/invokeGetMyPriceForSKU.xml', $xml); // exit;
                                                                                                 // file_put_contents(APPLICATION_PATH.'/../data/log/invokeGetMyPriceForSKU.txt', print_r($data,true));exit;
            
            $rs['ask'] = 1;
            $rs['message'] = 'Success';
        }catch(MarketplaceWebServiceProducts_Exception $ex){
            $exception = $this->logException($ex);
            $rs['exception'] = $exception;
        }
        $rs['err'] = $this->getErr();
        $rs['success'] = $this->getSuccess();

        Amazon_Service::log(print_r($rs,true));

        //             $xml = preg_replace('/>\s+</','><',($xml));
        $xml = trim(htmlspecialchars($xml));
        $rs['xml'] = $xml;
        return $rs;
    }

    /**
     * 数据保存
     * 
     * @param MarketplaceWebServiceProducts_Model_GetMyPriceForSKUResult $result            
     */
    private function _saveMyPriceForSKU(MarketplaceWebServiceProducts_Model_GetMyPriceForSKUResult $result)
    {
        $row = array();

        $SellerSKU = '';
        if($result->isSetSellerSKU()){
            $SellerSKU = $result->getSellerSKU();
            $row['seller_sku'] = $SellerSKU;
        }
        Amazon_Service::log("[{$this->_user_account}][{$SellerSKU}]数据保存");
        if($result->isSetError()){
            $err = $result->getError();
            Amazon_Service::log("[{$this->_user_account}][{$SellerSKU}]".$err->getMessage());
            $errArr = array();
            $errArr['type'] = $err->getType();
            $errArr['code'] = $err->getCode();
            $errArr['message'] = $err->getMessage();
            $errArr['seller_sku'] = $result->getSellerSKU();
            $errArr['status'] = $result->getstatus();
            $this->_err[] = $errArr;
            return;
        }
        if($result->isSetstatus()){
            Amazon_Service::log("[{$this->_user_account}][{$SellerSKU}]Status:".$result->getstatus());
            if($result->getstatus() != 'Success'){
                return;
            }
        }
        if($result->isSetProduct()){
            $product = $result->getProduct();
            
            $MarketplaceId = $product->getIdentifiers()
                ->getMarketplaceASIN()
                ->getMarketplaceId();
            
            $ASIN = $product->getIdentifiers()
                ->getMarketplaceASIN()
                ->getASIN();
            
            $SellerId = $product->getIdentifiers()
                ->getSKUIdentifier()
                ->getSellerId();
            
            $SellerSKU = $product->getIdentifiers()
                ->getSKUIdentifier()
                ->getSellerSKU();
            
            $SellerSKU = empty($SellerSKU) ? '-NoSku-' : $SellerSKU;
            $con = array(
                'seller_sku' => $SellerSKU,
                'company_code' => $this->_company_code,
                'user_account' => $this->_user_account
            );
            $exists = Service_AmazonMyPriceForSku::getByCondition($con);
            foreach($exists as $v){
                Service_AmazonMyPriceForSku::delete($v['id'], 'id');
            }
            $OffersList = $product->getOffers();
            $offers = $OffersList->getOffer();
            if(empty($offers)){
                Amazon_Service::log("[{$this->_user_account}][{$SellerSKU}]没有报价offers");
                return;
            }
            foreach($offers as $offer){
                $buyingPrice = $offer->getBuyingPrice();
                
                $LandedPrice_currency = $buyingPrice->getLandedPrice()->getCurrencyCode();
                $LandedPrice_amount = $buyingPrice->getLandedPrice()->getAmount();
                
                $ListingPrice_currency = $buyingPrice->getListingPrice()->getCurrencyCode();
                $ListingPrice_amount = $buyingPrice->getListingPrice()->getAmount();
                
                $Shipping_currency = $buyingPrice->getShipping()->getCurrencyCode();
                $Shipping_amount = $buyingPrice->getShipping()->getAmount();
                
                $RegularPrice_currency = $offer->getRegularPrice()->getCurrencyCode();
                $RegularPrice_amount = $offer->getRegularPrice()->getAmount();
                
                $FulfillmentChannel = $offer->getFulfillmentChannel();
                $ItemCondition = $offer->getItemCondition();
                $ItemSubCondition = $offer->getItemSubCondition();
                $SellerId = $offer->getSellerId();
                $offerSellerSKU = $offer->getSellerSKU();
                
                $row['market_place_id'] = $MarketplaceId;
                $row['asin'] = $ASIN;
                $row['seller_id'] = $SellerId;
                $row['seller_sku'] = $SellerSKU;
                $row['landed_price_currency'] = $LandedPrice_currency;
                $row['landed_price_amount'] = $LandedPrice_amount;
                $row['listing_price_currency'] = $ListingPrice_currency;
                $row['listing_price_amount'] = $ListingPrice_amount;
                $row['shipping_currency'] = $Shipping_currency;
                $row['shipping_amount'] = $Shipping_amount;
                $row['regular_price_currency'] = $RegularPrice_currency;
                $row['regular_price_amount'] = $RegularPrice_amount;
                $row['fulfillment_channel'] = $FulfillmentChannel;
                $row['item_condition'] = $ItemCondition;
                $row['item_sub_condition'] = $ItemSubCondition;
                $row['seller_id'] = $SellerId;
                $row['offer_seller_sku'] = $offerSellerSKU;
                
                $row['company_code'] = $this->_company_code;
                $row['user_account'] = $this->_user_account;
                $row['create_time'] = date('Y-m-d H:i:s');
                $row = Common_ApiProcess::nullToEmptyString($row);
                Service_AmazonMyPriceForSku::add($row);
                $this->_success[] = $row;                
            }
        }
    }

    public function getErr()
    {
        return $this->_err;
    }

    public function getSuccess()
    {
        return $this->_success;
    }
    

    /**
     * $IdArr 最大长度为5============================
     * @param unknown_type $IdArr
     */
    public function GetMatchingProductForId($IdArr){
        $this->_config['ServiceURL'].='/Products/2011-10-01';
        $service = new MarketplaceWebServiceProducts_Client(
                $this->_tokenConfig['AWS_ACCESS_KEY_ID'],
                $this->_tokenConfig['AWS_SECRET_ACCESS_KEY'],
                $this->_tokenConfig['APPLICATION_NAME'],
                $this->_tokenConfig['APPLICATION_VERSION'],
                $this->_config);
    
        //         print_r($this->_config);exit;
        $request = new MarketplaceWebServiceProducts_Model_GetMatchingProductForIdRequest();
        $request->setSellerId($this->_tokenConfig['MERCHANT_ID']);
        $request->setMarketplaceId($this->_MarketplaceId);
        $request->setIdType('SellerSKU');
        $IdList =  new MarketplaceWebServiceProducts_Model_IdListType();
        //         $IdList->setId(array(
        //                 'A00009'
        //         ));
        sort($IdArr);
        $IdList->setId($IdArr);
    
        $request->setIdList($IdList);
        $this->invokeGetMatchingProductForId($service, $request);
    }
    
    public function invokeGetMatchingProductForId(MarketplaceWebServiceProducts_Client $service, $request)
    {
        try {
            $response = $service->GetMatchingProductForId($request);
    
            $xml = $response->toXML();
            file_put_contents(APPLICATION_PATH.'/../data/log/invokeGetMatchingProductForId.xml', $xml);exit;
            $data = XML_unserialize($xml);
            Ec::showError(print_r($data,true),'__a');
            echo __LINE__;
            exit;
            print_r($data);exit;
            echo $response->toXML();exit;
            echo("Service Response\n");
            echo("=============================================================================\n");
    
            $dom = new DOMDocument();
            $dom->loadXML($response->toXML());
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            echo $dom->saveXML();
            echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
    
        } catch (MarketplaceWebServiceProducts_Exception $ex) {
            $this->logException($ex);
        }
    }
    
    
    public function GetMatchingProduct(){
        $this->_config['ServiceURL'].='/Products/2011-10-01';
        $service = new MarketplaceWebServiceProducts_Client(
                $this->_tokenConfig['AWS_ACCESS_KEY_ID'],
                $this->_tokenConfig['AWS_SECRET_ACCESS_KEY'],
                $this->_tokenConfig['APPLICATION_NAME'],
                $this->_tokenConfig['APPLICATION_VERSION'],
                $this->_config);
    
        //         print_r($this->_config);exit;
        $request = new MarketplaceWebServiceProducts_Model_GetMatchingProductRequest();
        $request->setSellerId($this->_tokenConfig['MERCHANT_ID']);
        $request->setMarketplaceId($this->_MarketplaceId);
        //         $request->setASINList($value)
    
        $IdList =  new MarketplaceWebServiceProducts_Model_ASINListType();
        $IdList->setASIN(array(
                //             'A00009',
                'A00009'
        ));
        $request->setASINList($IdList);
        $this->invokeGetMatchingProduct($service, $request);
    }
    public function invokeGetMatchingProduct(MarketplaceWebServiceProducts_Client $service, $request)
    {
        try {
            $response = $service->GetMatchingProduct($request);
    
            echo ("Service Response\n");
            echo ("=============================================================================\n");
    
            $dom = new DOMDocument();
            $dom->loadXML($response->toXML());
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            echo $dom->saveXML();
            echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
    
        } catch (MarketplaceWebServiceProducts_Exception $ex) {
            $this->logException($ex);
        }
    }
    
}