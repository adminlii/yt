<?php
class Ebay_ItemEbayService extends Ec_AutoRun
{

    private $_user_account = '';

    private $_company_code = '';

    public function setUserAccount($user_account)
    {
        $this->_user_account = $user_account;
    }

    public function setCompanyCode($company_code)
    {
        $this->_company_code = $company_code;
    }

    /**
     * 在售产品
     *
     * @param unknown_type $loadId            
     * @return multitype:string
     */
    public function loadEbayItem($loadId)
    {
        return $this->getSellerList($loadId, 'Start');
    }

    /**
     * 停售产品
     *
     * @param unknown_type $loadId            
     * @return multitype:string
     */
    public function loadEbayItemEnd($loadId)
    {
        return $this->getSellerList($loadId, 'End');
    }

    public function getSellerList($loadId, $type)
    {
        try{
            // 得到当前同步订单的关键参数
            $param = $this->getLoadParam($loadId);
            
            $userAccount = $param["user_account"];
            $companyCode = $param["company_code"];
            
            $this->_user_account = $userAccount;
            $this->_company_code = $companyCode;
            
            $start = $param["load_start_time"];
            $end = $param["load_end_time"];
            
            $rowCount = $this->callEbay($start, $end, $type);
            $this->countLoad($loadId, 2, $rowCount); // 运行结束
            
            return array(
                'ask' => '1',
                'message' => "eBay Time : " . $start . " ~ " . $end . ',' . $userAccount . ' item count ' . $rowCount
            );
        }catch(Exception $e){
            $this->countLoad($loadId, 3, 0); // 运行异常
            Ec::showError("账号：" . $userAccount . '发生错误，eBay时间：' . $start . ' To ' . $end . ',错误原因：' . $e->getMessage(), 'runItem_');
            return array(
                'ask' => '0',
                'message' => "账号：" . $userAccount . '发生错误，eBay时间：' . $start . ' To ' . $end . ',错误原因：' . $e->getMessage()
            );
        }
    }

    /**
     * 定期更新可售的item
     *
     * @param unknown_type $loadId            
     * @return multitype:number string
     */
    public function loadEbayItemActive($loadId)
    {
        try{
            // 得到当前同步订单的关键参数
            $param = $this->getLoadParam($loadId);
            
            $userAccount = $param["user_account"];
            $companyCode = $param["company_code"];
            
            $this->_user_account = $userAccount;
            $this->_company_code = $companyCode;
            
            $start = $param["load_start_time"];
            $end = $param["load_end_time"];
            $table = Ebay_EbayServiceCommon::table_cron_load_ebay_item();
            $sql = "select count(*) from seller_item where item_status='Active' and user_account='{$userAccount}';";
            $rowCount = Common_Common::fetchOne($sql);
            
            $sql = "replace into {$table}(item_id,user_account,company_code) select item_id,user_account,company_code from seller_item where item_status='Active' and user_account='{$userAccount}'and company_code='{$companyCode}';";
            Common_Common::query($sql);
            $this->countLoad($loadId, 2, $rowCount); // 运行结束
            return array(
                'ask' => 1,
                'message' => $companyCode . "," . $userAccount . ' item update count ' . $rowCount
            );
        }catch(Exception $e){
            $this->countLoad($loadId, 3, 0); // 运行异常
            return array(
                'ask' => 0,
                'message' => $companyCode . "," . $userAccount . '发生错误,错误原因：' . $e->getMessage()
            );
        }
    }

    /**
     * 获取数据
     *
     * @param unknown_type $userAccount            
     * @param unknown_type $start            
     * @param unknown_type $end            
     * @param unknown_type $type            
     * @throws Exception
     * @return number
     */
    public function callEbay($start, $end, $type = 'Start')
    {
        $typeArr = array(
            'Start',
            'End'
        );
        if(! in_array($type, $typeArr)){
            $type = 'Start';
        }
        $userAccount = $this->_user_account;
        $companyCode = $this->_company_code;
        
        if(empty($userAccount)){
            throw new Exception('UserAccount未赋值');
        }
        if(empty($companyCode)){
            throw new Exception('CompanyCode未赋值');
        }
        $token = Ebay_EbayLib::getUserToken($userAccount, $companyCode);
        if(! $token){
            throw new Exception('账号异常');
        }
        Common_ApiProcess::log("[{$type}]账号开始拉取:{$companyCode},{$userAccount},{$start}~{$end}");
        
        $TotalNumberOfEntries = 0;
        $rowCount = 0;
        $page = 0;
        while(true){
            $page ++;
            Common_ApiProcess::log("账号[{$companyCode},{$userAccount}]开始拉取第{$page}页");
            $data = Ebay_EbayLib::GetEbayItem($start, $end, $token, $page, $type);
            if($data['GetSellerListResponse']['Ack'] == 'Failure'){
                throw new Exception(print_r($data['GetSellerListResponse'], true));
            }
            $response = $data['GetSellerListResponse'];
            $total = $response['PaginationResult']['TotalNumberOfEntries'];
            
            if($TotalNumberOfEntries == 0){
                $TotalNumberOfEntries = $total;
            }elseif($TotalNumberOfEntries != $total){
                $TotalNumberOfEntries = $total;
                $page = 0;
                continue;
            }
            
            $items = array();
            $ItemArray = $data['GetSellerListResponse']['ItemArray'];
            if(isset($ItemArray['Item'])){
                $ItemArray = $ItemArray['Item'];
                if(isset($ItemArray[0])){
                    $items = $ItemArray;
                }else{
                    $items[] = $ItemArray;
                }
            }
            $rowCount += count($items); // 数量
            Common_ApiProcess::log("数据共{$total}条,当前页" . (count($items)) . "条");
            foreach($items as $k => $item){
                Common_ApiProcess::log(($k + 1) . "/" . (count($items)) . "[{$item['ItemID']}]数据保存/更新");
                $item['user_account'] = $userAccount;
                $this->saveSellerItem($item);
            }
            $db = Common_Common::getAdapter();
            foreach($items as $k => $item){
                Common_ApiProcess::log(($k + 1) . "/" . (count($items)) . "[{$item['ItemID']}]插入更新任务表");
                try{
                    // 插入更新任务
                    $table = Ebay_EbayServiceCommon::table_cron_load_ebay_item();
                    $arr = array(
                        'item_id' => $item['ItemID'],
                        'user_account' => $userAccount,
                        'company_code' => $companyCode
                    );
                    $db->insert($table, $arr);
                }catch(Exception $e){
                    //
                }
            }
            if($data['GetSellerListResponse']['HasMoreItems'] != 'true'){ // 不成功或者没有下一页
                                                                          // 程序终止,注意：返回的HasMoreItems
                                                                          // 是字符串类型
                break;
            }
        }
        return $rowCount;
    }

    /**
     * 数据保存
     *
     * @param unknown_type $item            
     * @param unknown_type $userAccount            
     */
    public function saveSellerItem($item)
    {
        $userAccount = $this->_user_account;
        $companyCode = $this->_company_code;
        if(empty($userAccount)){
            throw new Exception('UserAccount未赋值');
        }
        if(empty($companyCode)){
            throw new Exception('CompanyCode未赋值');
        }
        // 保存到数据库seller_item表------
        // .....
        $itemId = $item["ItemID"];
        
        $PictureURL = $item['PictureDetails']['PictureURL'];
        if(! is_string($PictureURL)){
            // 数组拼接
            $PictureURL = implode('#:|:#', $PictureURL);
        }
        $itemType = 1; // 无属性一口价产品，如颜色，尺寸
        if($item['SellingStatus']['BidIncrement'] > 0){
            $itemType = 0; // 拍卖
        }
        // 保存组合产品
        if($item['Variations']){}
        $itemTemp = array(
            "item_id" => $itemId,
            "item_url" => $item['ListingDetails']['ViewItemURL'],
            "start_time" => $item['ListingDetails']['StartTime'],
            "end_time" => $item['ListingDetails']['EndTime'],
            "sell_type" => $item["ListingType"],
            "sell_qty" => $item["Quantity"],
            "sold_qty" => $item['SellingStatus']['QuantitySold'],
            "item_status" => $item['SellingStatus']['ListingStatus'],
            "site" => $item['Site'],
            "price_sell" => $item['SellingStatus']['CurrentPrice'],
            "currency" => $item['SellingStatus']['CurrentPrice attr']['currencyID'],
            "pic_path" => $PictureURL,
            "item_title" => $item['Title'],
            "out_of_stock_control" => $item['OutOfStockControl'],
            "sku" => $item['SKU'],
            'platform' => 'ebay',
            'category_id' => $item['PrimaryCategory']['CategoryID'],
            'category_name' => $item['PrimaryCategory']['CategoryName'],
            'item_location' => $item['Location'],
            'sell_type' => $itemType, // 0拍卖,1无属性,2多属性
            'paypal_email_address' => $item['PayPalEmailAddress'],
            'list_type' => $item['ListingType'],
            'user_account' => $userAccount,
            'company_code' => $companyCode
        );
        $itemTemp = Ec_AutoRun::arrayNullToEmptyString($itemTemp);
        
        $exist = Service_SellerItem::getByField($itemId, 'item_id');
        if($exist){
            Service_SellerItem::update($itemTemp, $itemId, "item_id");
            $diff = array_diff_assoc($itemTemp, $exist);
            if(! empty($diff)){
                // 有差异，记录日志，并更新
                $log = array();
                foreach($diff as $k => $v){
                    $log[] = $k . ':from [' . $exist[$k] . ']to[' . $itemTemp[$k] . ']';
                }
                $logRow = array(
                    'item_id' => $itemId,
                    'content' => implode("\n", $log),
                    'update_time' => date('Y-m-d H:i:s')
                );
                Service_SellerItemLog::add($logRow);
            }
        }else{
            Service_SellerItem::add($itemTemp);
        }
    }

    /**
     * 更新Item信息
     *
     * @param unknown_type $item            
     * @return boolean
     */
    public static function updateItem($itemId, $userAccount = '', $companyCode = '')
    {
        $sellerItem = Service_SellerItem::getByField($itemId, 'item_id');
        if($sellerItem){
            $userAccount = $sellerItem['user_account'];
            $companyCode = $sellerItem['company_code'];
        }
        if(empty($userAccount)){
            throw new Exception(' userAccount Empty');
        }
        if(empty($companyCode)){
            throw new Exception(' companyCode Empty');
        }
        $token = Ebay_EbayLib::getUserToken($userAccount, $companyCode);
        
        Common_ApiProcess::log("[{$companyCode},{$userAccount}][{$itemId}]数据拉取");
        $data = Ebay_EbayLib::GetItem($token, $itemId);
        
        $data['companyCode'] = $companyCode;
        $data['userAccount'] = $userAccount;
        $data['item_id'] = $itemId;
        if($data['GetItemResponse']['Ack'] != 'Failure'){
            $item = $data['GetItemResponse']['Item'];
            $item['company_code'] = $companyCode;
            $item['user_account'] = $userAccount;
            // 保存数据
            Ebay_ItemEbayService::saveEbayItem($item);
            // 保存数据
            $service = new Ebay_ItemEbayService();
            $service->setCompanyCode($companyCode);
            $service->setUserAccount($userAccount);
            $service->saveSellerItem($item);
        }else{
            $itemOpLogRow = array(
                'item_id' => $itemId,
                'content' => 'ITEM获取失败，原因：' . print_r($data['GetItemResponse']['Errors'], true),
                'create_time' => date('Y-m-d H:i:s'),
                'op_user_id' => Service_User::getUserId(),
                'type' => '0'
            );
            Service_SellerItemOpLog::add($itemOpLogRow);
        }
        
        return $data;
    }

    /**
     * 格式化数据
     *
     * @param unknown_type $item            
     * @return multitype:multitype:unknown Ambigous <NULL> Ambigous <NULL, unknown> multitype:multitype:string NULL unknown multitype:Ambigous <multitype:NULL multitype: unknown Ambigous <number, unknown> , multitype:unknown >
     */
    private static function formatEbayItem($item)
    {
        $data = array();
        $itemId = $item['ItemID'];
        $arr = array(
            'ItemID' => $item['ItemID'],
            'ListingType' => $item['ListingType'],
            'ListingDuration' => $item['ListingDuration'],
            
            "OutOfStockControl" => $item['OutOfStockControl'],
            'AutoPay' => $item['AutoPay'],
            'BuyerProtection' => $item['BuyerProtection'],
            'BuyItNowPrice' => $item['BuyItNowPrice'],
            'BuyItNowPrice_currencyID' => $item['BuyItNowPrice attr']['currencyID'],
            'Country' => $item['Country'],
            'Currency' => $item['Currency'],
            'GiftIcon' => $item['GiftIcon'],
            'HitCounter' => $item['HitCounter'],
            
            'Location' => $item['Location'],
            'PaymentMethods' => is_array($item['PaymentMethods']) ? implode(';', $item['PaymentMethods']) : $item['PaymentMethods'],
            'PayPalEmailAddress' => $item['PayPalEmailAddress'],
            
            'PrivateListing' => $item['PrivateListing'],
            'Quantity' => $item['Quantity'],
            
            'ReservePrice_currencyID' => $item['ReservePrice attr']['currencyID'],
            'ReviseStatus_ItemRevised' => $item['ReviseStatus']['ItemRevised'],
            'ReservePrice' => $item['ReservePrice'],
            
            'Site' => $item['Site'],
            
            'StartPrice_currencyID' => $item['StartPrice attr']['currencyID'],
            'StartPrice' => $item['StartPrice'],
            
            'TimeLeft' => $item['TimeLeft'],
            'Title' => $item['Title'],
            'UUID' => $item['UUID'],
            'HitCount' => $item['HitCount'],
            'SKU' => $item['SKU'],
            'PostalCode' => $item['PostalCode'],
            'DispatchTimeMax' => $item['DispatchTimeMax'],
            'ProxyItem' => $item['ProxyItem'],
            
            'BuyerGuaranteePrice_currencyID' => $item['BuyerGuaranteePrice attr']['currencyID'],
            'BuyerGuaranteePrice' => $item['BuyerGuaranteePrice'],
            'IntangibleItem' => $item['IntangibleItem'],
            'ConditionID' => $item['ConditionID'],
            'ConditionDisplayName' => $item['ConditionDisplayName'],
            'PostCheckoutExperienceEnabled' => $item['PostCheckoutExperienceEnabled'],
            'HideFromSearch' => $item['HideFromSearch'],
            'PrimaryCategory_CategoryID' => $item['PrimaryCategory']['CategoryID'],
            'PrimaryCategory_CategoryName' => $item['PrimaryCategory']['CategoryName'],
            
            'PictureDetails_GalleryType' => $item['PictureDetails']['GalleryType'],
            'PictureDetails_GalleryURL' => $item['PictureDetails']['GalleryURL'],
            'PictureDetails_PhotoDisplay' => $item['PictureDetails']['PhotoDisplay'],
            'PictureDetails_PictureURL' => ! empty($item['PictureDetails']['PictureURL']) && is_array($item['PictureDetails']['PictureURL']) ? implode('*#*#*', $item['PictureDetails']['PictureURL']) : $item['PictureDetails']['PictureURL'],
            'PictureDetails_PictureSource' => $item['PictureDetails']['PictureSource'],
            
            'SellingStatus_BidCount' => $item['SellingStatus']['BidCount'],
            
            'SellingStatus_BidIncrement' => $item['SellingStatus']['BidIncrement'],
            'SellingStatus_ConvertedCurrentPrice' => $item['SellingStatus']['ConvertedCurrentPrice'],
            'SellingStatus_CurrentPrice' => $item['SellingStatus']['CurrentPrice'],
            'SellingStatus_MinimumToBid' => $item['SellingStatus']['MinimumToBid'],
            
            'SellingStatus_BidIncrement_currencyID' => $item['SellingStatus']['BidIncrement attr']['currencyID'],
            'SellingStatus_ConvertedCurrentPrice_currencyID' => $item['SellingStatus']['ConvertedCurrentPrice attr']['currencyID'],
            'SellingStatus_CurrentPrice_currencyID' => $item['SellingStatus']['CurrentPrice attr']['currencyID'],
            'SellingStatus_MinimumToBid_currencyID' => $item['SellingStatus']['MinimumToBid attr']['currencyID'],
            
            'SellingStatus_LeadCount' => $item['SellingStatus']['LeadCount'],
            'SellingStatus_QuantitySold' => $item['SellingStatus']['QuantitySold'],
            'SellingStatus_ReserveMet' => $item['SellingStatus']['ReserveMet'],
            'SellingStatus_SecondChanceEligible' => $item['SellingStatus']['SecondChanceEligible'],
            'SellingStatus_ListingStatus' => $item['SellingStatus']['ListingStatus'],
            'SellingStatus_QuantitySoldByPickupInStore' => $item['SellingStatus']['QuantitySoldByPickupInStore'],
            
            'SellingStatus_PromotionalSaleDetails_OriginalPrice_currencyID' => $item['SellingStatus']['PromotionalSaleDetails']['OriginalPrice attr']['currencyID'],
            'SellingStatus_PromotionalSaleDetails_OriginalPrice' => $item['SellingStatus']['PromotionalSaleDetails']['OriginalPrice'],
            'SellingStatus_PromotionalSaleDetails_StartTime' => $item['SellingStatus']['PromotionalSaleDetails']['StartTime'],
            'SellingStatus_PromotionalSaleDetails_EndTime' => $item['SellingStatus']['PromotionalSaleDetails']['EndTime'],
            
            'BusinessSellerDetails_Address_Street1' => $item['BusinessSellerDetails']['Address']['Street1'],
            'BusinessSellerDetails_Address_CityName' => $item['BusinessSellerDetails']['Address']['CityName'],
            'BusinessSellerDetails_Address_StateOrProvince' => $item['BusinessSellerDetails']['Address']['StateOrProvince'],
            'BusinessSellerDetails_Address_CountryName' => $item['BusinessSellerDetails']['Address']['CountryName'],
            'BusinessSellerDetails_Address_Phone' => $item['BusinessSellerDetails']['Address']['Phone'],
            'BusinessSellerDetails_Address_PostalCode' => $item['BusinessSellerDetails']['Address']['PostalCode'],
            'BusinessSellerDetails_Address_CompanyName' => $item['BusinessSellerDetails']['Address']['CompanyName'],
            'BusinessSellerDetails_Address_FirstName' => $item['BusinessSellerDetails']['Address']['FirstName'],
            'BusinessSellerDetails_Address_LastName' => $item['BusinessSellerDetails']['Address']['LastName'],
            
            'BusinessSellerDetails_Email' => $item['BusinessSellerDetails']['Email'],
            'BusinessSellerDetails_LegalInvoice' => $item['BusinessSellerDetails']['LegalInvoice'],
            // =======================================
            'ListingDetails_Adult' => $item['ListingDetails']['Adult'],
            'ListingDetails_BindingAuction' => $item['ListingDetails']['BindingAuction'],
            'ListingDetails_CheckoutEnabled' => $item['ListingDetails']['CheckoutEnabled'],
            'ListingDetails_ConvertedBuyItNowPrice_currencyID' => $item['ListingDetails']['ConvertedBuyItNowPrice attr']['currencyID'],
            'ListingDetails_ConvertedBuyItNowPrice' => $item['ListingDetails']['ConvertedBuyItNowPrice attr'],
            'ListingDetails_ConvertedStartPrice_currencyID' => $item['ListingDetails']['ConvertedStartPrice attr']['currencyID'],
            'ListingDetails_ConvertedStartPrice' => $item['ListingDetails']['ConvertedStartPrice attr'],
            'ListingDetails_ConvertedReservePrice_currencyID' => $item['ListingDetails']['ConvertedReservePrice attr']['currencyID'],
            'ListingDetails_ConvertedReservePrice' => $item['ListingDetails']['ConvertedReservePrice attr'],
            'ListingDetails_HasReservePrice' => $item['ListingDetails']['HasReservePrice'],
            'ListingDetails_StartTime' => $item['ListingDetails']['StartTime'],
            'ListingDetails_EndTime' => $item['ListingDetails']['EndTime'],
            'ListingDetails_ViewItemURL' => $item['ListingDetails']['ViewItemURL'],
            'ListingDetails_HasUnansweredQuestions' => $item['ListingDetails']['HasUnansweredQuestions'],
            'ListingDetails_HasPublicMessages' => $item['ListingDetails']['HasPublicMessages'],
            'ListingDetails_ViewItemURLForNaturalSearch' => $item['ListingDetails']['ViewItemURLForNaturalSearch'],
            
            'ShipToLocations' => $item['ShipToLocations'],
            'Seller' => print_r($item['Seller'], true),
            'ReturnPolicy' => print_r($item['ReturnPolicy'], true)
        );
        
        $itemType = 1; // 无属性一口价产品，如颜色，尺寸
        if($item['SellingStatus']['BidIncrement'] > 0){
            $itemType = 0; // 拍卖
        }
        // 保存组合产品
        if($item['Variations']){
            $itemType = 2; // 多属性产品，如颜色，尺寸等
        }
        $itemArr = array(
            'company_code' => $item['company_code'],
            'user_account' => $item['user_account'],
            'item_id' => $arr['ItemID'],
            'item_type' => $itemType,
            'item_status' => $arr['SellingStatus_ListingStatus'],
            'listing_type' => $arr['ListingType'],
            'listing_duration' => $arr['ListingDuration'],
            
            "out_of_stock_control" => $arr['OutOfStockControl'],
            
            'auto_pay' => $arr['AutoPay'],
            'buyer_protection' => $arr['BuyerProtection'],
            'buy_it_now_price' => $arr['BuyItNowPrice'],
            'buy_it_now_price_currency' => $arr['BuyItNowPrice_currencyID'],
            'country' => $arr['Country'],
            'currency' => $arr['Currency'],
            'gift_icon' => $arr['GiftIcon'],
            'hit_counter' => $arr['HitCounter'],
            
            'location' => $arr['Location'],
            'payment_methods' => $arr['PaymentMethods'],
            'paypal_email' => $arr['PayPalEmailAddress'],
            
            'private_listing' => $arr['PrivateListing'],
            'quantity' => $arr['Quantity'],
            
            'reserve_price_currency' => $arr['ReservePrice_currencyID'],
            'reserve_price' => $arr['ReservePrice'],
            
            'revise_status_item_revised' => $arr['ReviseStatus_ItemRevised'],
            
            'site' => $arr['Site'],
            
            'start_price_currency' => $arr['StartPrice_currencyID'],
            'start_price' => $arr['StartPrice'],
            
            'time_left' => $arr['TimeLeft'],
            'title' => $arr['Title'],
            'uuid' => $arr['UUID'],
            'hit_count' => $arr['HitCount'],
            'sku' => $arr['SKU'],
            'postal_code' => $arr['PostalCode'],
            'dispatch_time_max' => $arr['DispatchTimeMax'],
            'proxy_item' => $arr['ProxyItem'],
            
            'buyer_guarantee_price_currency' => $arr['BuyerGuaranteePrice_currencyID'],
            'buyer_guarantee_price' => $arr['BuyerGuaranteePrice'],
            'intangible_item' => $arr['IntangibleItem'],
            'condition_id' => $arr['ConditionID'],
            'condition_display_name' => $arr['ConditionDisplayName'],
            'post_checkout_experience_enabled' => $arr['PostCheckoutExperienceEnabled'],
            'hide_from_search' => $arr['HideFromSearch'],
            'primary_category_category_id' => $arr['PrimaryCategory_CategoryID'],
            'primary_category_Category_name' => $arr['PrimaryCategory_CategoryName'],
            
            'picture_details_gallery_type' => $arr['PictureDetails_GalleryType'],
            'picture_details_gallery_url' => $arr['PictureDetails_GalleryURL'],
            'picture_details_photo_display' => $arr['PictureDetails_PhotoDisplay'],
            'picture_details_picture_url' => $arr['PictureDetails_PictureURL'],
            'picture_details_picture_source' => $arr['PictureDetails_PictureSource'],
            
            'selling_status_bid_count' => $arr['SellingStatus_BidCount'],
            
            'selling_status_bid_increment' => $arr['SellingStatus_BidIncrement'],
            'selling_status_converted_currentPrice' => $arr['SellingStatus_ConvertedCurrentPrice'],
            'selling_status_current_price' => $arr['SellingStatus_CurrentPrice'],
            'selling_status_minimum_to_bid' => $arr['SellingStatus_MinimumToBid'],
            
            'selling_status_bid_increment_currency' => $arr['SellingStatus_BidIncrement_currencyID'],
            'selling_status_converted_currentPrice_currency' => $arr['SellingStatus_ConvertedCurrentPrice_currencyID'],
            'selling_status_current_price_currency' => $arr['SellingStatus_CurrentPrice_currencyID'],
            'selling_status_minimum_to_bid_currency' => $arr['SellingStatus_MinimumToBid_currencyID'],
            
            'selling_status_lead_count' => $arr['SellingStatus_LeadCount'],
            'selling_status_quantity_sold' => $arr['SellingStatus_QuantitySold'],
            'selling_status_reserve_met' => $arr['SellingStatus_ReserveMet'],
            'selling_status_second_chance_eligible' => $arr['SellingStatus_SecondChanceEligible'],
            'selling_status_listing_status' => $arr['SellingStatus_ListingStatus'],
            'selling_status_quantity_sold_by_pickup_in_store' => $arr['SellingStatus_QuantitySoldByPickupInStore'],
            
            'selling_status_promotional_sale_details_original_price_currency' => $arr['SellingStatus_PromotionalSaleDetails_OriginalPrice_currencyID'],
            'selling_status_promotional_sale_details_original_price' => $arr['SellingStatus_PromotionalSaleDetails_OriginalPrice'],
            'selling_status_promotional_sale_details_start_time' => $arr['SellingStatus_PromotionalSaleDetails_StartTime'],
            'selling_status_promotional_sale_details_end_time' => $arr['SellingStatus_PromotionalSaleDetails_EndTime'],
            
            'business_seller_details_address_street1' => $arr['BusinessSellerDetails_Address_Street1'],
            'business_seller_details_address_city_name' => $arr['BusinessSellerDetails_Address_CityName'],
            'business_seller_details_address_state_or_province' => $arr['BusinessSellerDetails_Address_StateOrProvince'],
            'business_seller_details_address_country_name' => $arr['BusinessSellerDetails_Address_CountryName'],
            'business_seller_details_address_phone' => $arr['BusinessSellerDetails_Address_Phone'],
            'business_seller_details_address_postal_code' => $arr['BusinessSellerDetails_Address_PostalCode'],
            'business_seller_details_address_company_name' => $arr['BusinessSellerDetails_Address_CompanyName'],
            'business_seller_details_address_first_name' => $arr['BusinessSellerDetails_Address_FirstName'],
            'business_seller_details_address_last_name' => $arr['BusinessSellerDetails_Address_LastName'],
            
            'business_seller_details_email' => $arr['BusinessSellerDetails_Email'],
            'business_seller_details_legal_invoice' => $arr['BusinessSellerDetails_LegalInvoice'],
            
            'listing_details_adult' => $arr['ListingDetails_Adult'],
            'listing_details_binding_auction' => $arr['ListingDetails_BindingAuction'],
            'listing_details_checkout_enabled' => $arr['ListingDetails_CheckoutEnabled'],
            'listing_details_converted_buy_it_now_price_currency' => $arr['ListingDetails_ConvertedBuyItNowPrice_currencyID'],
            'listing_details_converted_buy_it_now_price' => $arr['ListingDetails_ConvertedBuyItNowPrice'],
            'listing_details_converted_start_price_currency' => $arr['ListingDetails_ConvertedStartPrice_currencyID'],
            'listing_details_converted_start_price' => $arr['ListingDetails_ConvertedStartPrice'],
            'listing_details_converted_reserve_price_currency' => $arr['ListingDetails_ConvertedReservePrice_currencyID'],
            'listing_details_converted_reserve_price' => $arr['ListingDetails_ConvertedReservePrice'],
            'listing_details_has_reserve_price' => $arr['ListingDetails_HasReservePrice'],
            'listing_details_start_time' => $arr['ListingDetails_StartTime'],
            'listing_details_end_time' => $arr['ListingDetails_EndTime'],
            'listing_details_view_item_url' => $arr['ListingDetails_ViewItemURL'],
            'listing_details_has_unanswered_questions' => $arr['ListingDetails_HasUnansweredQuestions'],
            'listing_details_has_public_messages' => $arr['ListingDetails_HasPublicMessages'],
            'listing_details_view_item_url_for_natural_search' => $arr['ListingDetails_ViewItemURLForNaturalSearch'],
            
            'ship_to_locations' => $arr['ShipToLocations'],
            'seller' => $arr['Seller'],
            'return_policy' => $arr['ReturnPolicy']
        );
        // 多品属性
        $itemVariationArr = array();
        $itemVariations = $item['Variations']['Variation'];
        if($itemVariations != null){
            if(! isset($itemVariations[0])){
                $itemVariationsT = array();
                $itemVariationsT[] = $itemVariations;
                $itemVariations = $itemVariationsT;
            }
            foreach($itemVariations as $v){
                $itemVariation = array(
                    "item_id" => $itemId,
                    "product_sku" => $item['SKU'],
                    "sku" => $v['SKU'] ? $v['SKU'] : '-NoSku-',
                    "qty" => $v['Quantity'] ? $v['Quantity'] : 0,
                    "qty_sold" => $v['SellingStatus']['QuantitySold'],
                    "start_pice" => @$v['StartPrice'],
                    "currency" => @$v['StartPrice attr']['currencyID']
                );
                $itemVariation['attr'] = array();
                $itemVariation['sku_desc'] = '';
                if($v['VariationSpecifics']){
                    $sku_desc = array();
                    $NameValueList = $v['VariationSpecifics']['NameValueList'];
                    if($NameValueList){
                        if(! $NameValueList[0]){
                            $NameValueListT = array();
                            $NameValueListT[] = $NameValueList;
                            $NameValueList = $NameValueListT;
                        }
                        foreach($NameValueList as $name_value){
                            $varAttr = array(
                                'item_id' => $itemId,
                                'name' => $name_value['Name'],
                                'val' => $name_value['Value']
                            );
                            $itemVariation['attr'][] = $varAttr;
                            
                            $sku_desc[] = $name_value['Name'] . ":" . $name_value['Value'];
                        }
                    }
                    $itemVariation['sku_desc'] = implode(';', $sku_desc);
                }
                $itemVariationArr[] = $itemVariation;
            }
        }else{
            $itemVariation = array(
                "item_id" => $itemId,
                "product_sku" => $item['SKU'],
                "sku" => $item['SKU'] ? $item['SKU'] : '-NoSku-',
                "sku_desc" => '',
                "qty" => $item["Quantity"],
                "qty_sold" => $item['SellingStatus']['QuantitySold'],
                "start_pice" => $item['SellingStatus']['CurrentPrice'],
                "currency" => $item['SellingStatus']['CurrentPrice attr']['currencyID']
            );
            $itemVariation['attr'] = array();
            $itemVariationArr[] = $itemVariation;
        }
        
        // 产品运费 start
        $ShippingServiceOptionsArr = array();
        $ShippingServiceOptions = $item['ShippingDetails']['ShippingServiceOptions'];
        if(! empty($ShippingServiceOptions)){
            if(isset($ShippingServiceOptions[0])){
                foreach($ShippingServiceOptions as $v){
                    $ShippingServiceOptionsArr[] = array(
                        'item_id' => $itemId,
                        'ship_type' => 'ShippingServiceOptions',
                        'shipping_service' => $v['ShippingService'],
                        'shipping_service_cost' => $v['ShippingServiceCost'],
                        'shipping_service_cost_currency' => $v['ShippingServiceCost attr']['currencyID'],
                        'shipping_service_addtion_cost' => $v['ShippingServiceAdditionalCost'],
                        'shipping_service_addtion_cost_currency' => $v['ShippingServiceAdditionalCost attr']['currencyID']
                    );
                }
            }else{
                $v = $ShippingServiceOptions;
                $ShippingServiceOptionsArr[] = array(
                    'item_id' => $itemId,
                    'ship_type' => 'ShippingServiceOptions',
                    'shipping_service' => $v['ShippingService'],
                    'shipping_service_cost' => $v['ShippingServiceCost'],
                    'shipping_service_cost_currency' => $v['ShippingServiceCost attr']['currencyID'],
                    'shipping_service_addtion_cost' => $v['ShippingServiceAdditionalCost'],
                    'shipping_service_addtion_cost_currency' => $v['ShippingServiceAdditionalCost attr']['currencyID']
                );
            }
        }
        $InternationalShippingServiceOptions = $item['ShippingDetails']['InternationalShippingServiceOption'];
        if(! empty($InternationalShippingServiceOptions)){
            if(isset($InternationalShippingServiceOptions[0])){
                foreach($InternationalShippingServiceOptions as $v){
                    $ShippingServiceOptionsArr[] = array(
                        'item_id' => $itemId,
                        'ship_type' => 'InternationalShippingServiceOption',
                        'shipping_service' => $v['ShippingService'],
                        'shipping_service_cost' => $v['ShippingServiceCost'],
                        'shipping_service_cost_currency' => $v['ShippingServiceCost attr']['currencyID'],
                        'shipping_service_addtion_cost' => $v['ShippingServiceAdditionalCost'],
                        'shipping_service_addtion_cost_currency' => $v['ShippingServiceAdditionalCost attr']['currencyID']
                    );
                }
            }else{
                $v = $InternationalShippingServiceOptions;
                $ShippingServiceOptionsArr[] = array(
                    'item_id' => $itemId,
                    'ship_type' => 'InternationalShippingServiceOption',
                    'shipping_service' => $v['ShippingService'],
                    'shipping_service_cost' => $v['ShippingServiceCost'],
                    'shipping_service_cost_currency' => $v['ShippingServiceCost attr']['currencyID'],
                    'shipping_service_addtion_cost' => $v['ShippingServiceAdditionalCost'],
                    'shipping_service_addtion_cost_currency' => $v['ShippingServiceAdditionalCost attr']['currencyID']
                );
            }
        }
        
        // 产品运费 end
        // print_r($item['PictureDetails']);exit;
        // 产品图片 satart
        $pictureArr = array();
        if(! empty($item['PictureDetails'])){
            if(is_array($item['PictureDetails']['PictureURL'])){
                foreach($item['PictureDetails']['PictureURL'] as $p){
                    $picture = array(
                        "item_id" => $itemId,
                        "name" => '',
                        "val" => '',
                        "picture_url" => $p,
                        "ext_picture_url" => $p
                    );
                    $pictureArr[] = $picture;
                }
            }else{
                $picture = array(
                    "item_id" => $itemId,
                    "name" => '',
                    "val" => '',
                    "picture_url" => $item['PictureDetails']['PictureURL'],
                    "ext_picture_url" => $item['PictureDetails']['PictureURL']
                );
                $pictureArr[] = $picture;
            }
        }
        // print_r($item['PictureDetails']);
        // print_r($pictureArr);exit;
        
        $VariationSpecificPictureSet = $item['Variations']['Pictures']['VariationSpecificPictureSet'];
        // print_r($item['Variations']['Pictures']);exit;
        if(! empty($VariationSpecificPictureSet)){
            if(! isset($VariationSpecificPictureSet[0])){
                $VariationSpecificPictureSetT = array();
                $VariationSpecificPictureSetT[] = $VariationSpecificPictureSet;
                $VariationSpecificPictureSet = $VariationSpecificPictureSetT;
            }
            $name = $item['Variations']['Pictures']['VariationSpecificName'];
            foreach($VariationSpecificPictureSet as $v){
                if(! empty($v['PictureURL'])){
                    if(is_array($v['PictureURL'])){
                        foreach($v['PictureURL'] as $p){
                            $picture = array(
                                "item_id" => $itemId,
                                "name" => $name,
                                "val" => $v['VariationSpecificValue'],
                                "picture_url" => $p,
                                "ext_picture_url" => $p
                            );
                            $pictureArr[] = $picture;
                        }
                    }else{
                        $picture = array(
                            "item_id" => $itemId,
                            "name" => $name,
                            "val" => $v['VariationSpecificValue'],
                            "picture_url" => $v['PictureURL'],
                            "ext_picture_url" => $v['ExternalPictureURL']
                        );
                        $pictureArr[] = $picture;
                    }
                }
            }
        }
        // print_r($pictureArr);exit;
        // 产品图片 end
        
        $data['item'] = $itemArr;
        $data['variation'] = $itemVariationArr;
        $data['shipping_service'] = $ShippingServiceOptionsArr;
        $data['pictures'] = $pictureArr;
        
        return $data;
    }

    /**
     * 数据保存
     *
     * @param unknown_type $item            
     */
    public static function saveEbayItem($itemData)
    {
        $itemId = $itemData['ItemID'];
        try{
            $data = self::formatEbayItem($itemData);
            // print_r($data);
            $item = $data['item'];
            $variations = $data['variation'];
            $shipping_services = $data['shipping_service'];
            $pictures = $data['pictures'];
            
            $item = self::arrayNullToEmptyString($item);
            
            $itemExist = Service_EbayItem::getByField($item['item_id'], 'item_id');
            if($itemExist){
                Service_EbayItem::update($item, $item['item_id'], 'item_id');
            }else{
                Service_EbayItem::add($item);
            }
            
            Service_EbayItemVariations::delete($item['item_id'], 'item_id');
            Service_EbayItemVariationsAttr::delete($item['item_id'], 'item_id');
            foreach($variations as $variation){
                $attrs = $variation['attr'];
                $attrs = $attrs ? $attrs : array();
                unset($variation['attr']);
                $variation = self::arrayNullToEmptyString($variation);
                $varId = Service_EbayItemVariations::add($variation);
                // 多属性产品的属性
                foreach($attrs as $attr){
                    $attr['variation_id'] = $varId;
                    $attr = self::arrayNullToEmptyString($attr);
                    Service_EbayItemVariationsAttr::add($attr);
                }
            }
            
            // 运费
            Service_EbayItemShipFee::delete($item['item_id'], 'item_id');
            foreach($shipping_services as $shipping_service){
                $shipping_service = self::arrayNullToEmptyString($shipping_service);
                Service_EbayItemShipFee::add($shipping_service);
            }
            // 图片
            Service_EbayItemPictures::delete($item['item_id'], 'item_id');
            foreach($pictures as $picture){
                $picture = self::arrayNullToEmptyString($picture);
                Service_EbayItemPictures::add($picture);
            }
        }catch(Exception $e){
            Common_ApiProcess::log($e->getMessage());
            // echo $e->getMessage();exit;
            Ec::showError($e->getMessage(), 'saveEbayItem_');
        }
    }
}