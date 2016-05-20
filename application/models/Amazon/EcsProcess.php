<?php
require 'AmazonECS.class.php';
class Amazon_EcsProcess
{

    private $_aws_api_key = '';

    private $_aws_secret_key = '';

    private $_country = '';

    private $_aws_associate_tag = '';

    private $_account = '';
    private $_company_code = '';
    
    private $_site = 'US';
    
    // de, com, co.uk, ca, fr, co.jp, it, cn, es
    private $_arr = array(
        'DE' => 'de',
        'US' => 'com',
        'GB' => 'co.uk',
        'UK' => 'co.uk',
        'CA' => 'ca',
        'JP' => 'co.jp',
        'IT' => 'it',
        'CN' => 'cn',
        'ES' => 'es'
    );

    public function __construct($api_key, $secret_key, $country, $associate_tag)
    {
        $this->_aws_api_key = $api_key;
        $this->_aws_secret_key = $secret_key;
        $this->_country = $country;
        $this->_aws_associate_tag = $associate_tag;
    }

    public function setAccount($acc)
    {
        $this->_account = $acc;
    }

    public function setCompanyCode($comp)
    {
    	$this->_company_code = $comp;
    }
    
    public function setCountryBySite($site)
    {
        $site = strtoupper($site);
        $this->_site = $site;
        $arr = $this->_arr;
        if(! isset($arr[$site])){
            throw new Exception('Site不合法-->' . $site);
        }
        $this->_country = $arr[$site];
    }

    /**
     * responseGroup==>'Tags','Help','ListMinimum','VariationSummary','VariationMatrix',
     * responseGroup==>'TransactionDetails','VariationMinimum','VariationImages','PartBrandBinsSummary',
     * responseGroup==>'CustomerFull','CartNewReleases','ItemIds','SalesRank','TagsSummary','Fitments','Subjects',
     * responseGroup==>'Medium','ListmaniaLists','PartBrowseNodeBinsSummary','TopSellers','Request','HasPartCompatibility',
     * responseGroup==>'PromotionDetails','ListFull','Small','Seller','OfferFull','Accessories','VehicleMakes',
     * responseGroup==>'MerchantItemAttributes','TaggedItems','VehicleParts','BrowseNodeInfo','ItemAttributes','PromotionalTag',
     * responseGroup==>'VehicleOptions','ListItems','Offers','TaggedGuides','NewReleases','VehiclePartFit','OfferSummary',
     * responseGroup==>'VariationOffers','CartSimilarities','Reviews','ShippingCharges','ShippingOptions','EditorialReview',
     * responseGroup==>'CustomerInfo','PromotionSummary','BrowseNodes','PartnerTransactionDetails','VehicleYears','SearchBins',
     * responseGroup==>'VehicleTrims','Similarities','AlternateVersions','SearchInside','CustomerReviews','SellerListing',
     * responseGroup==>'OfferListings','Cart','TaggedListmaniaLists','VehicleModels','ListInfo','Large','CustomerLists',
     * responseGroup==>'Tracks','CartTopSellers','Images','Variations','RelatedItems','Collections'
     *
     * lookup============>
     * 'Request'
     * 'ItemIds'
     * 'Small'
     * 'Medium'
     * 'Large'
     * 'Offers'
     * 'OfferFull'
     * 'OfferSummary'
     * 'OfferListings'
     * 'PromotionSummary'
     * 'PromotionDetails'
     * 'Variations'
     * 'VariationImages'
     * 'VariationMinimum'
     * 'VariationSummary'
     * 'TagsSummary'
     * 'Tags'
     * 'VariationMatrix'
     * 'VariationOffers'
     * 'ItemAttributes'
     * 'MerchantItemAttributes'
     * 'Tracks'
     * 'Accessories'
     * 'EditorialReview'
     * 'SalesRank'
     * 'BrowseNodes'
     * 'Images'
     * 'Similarities'
     * 'Subjects'
     * 'Reviews'
     * 'ListmaniaLists'
     * 'SearchInside'
     * 'PromotionalTag'
     * 'AlternateVersions'
     * 'Collections'
     * 'ShippingCharges'
     * 'RelatedItems'
     * 'ShippingOptions'
     */
    public function lookup($asinArr = array('B00KJBL5TK'))
    {
        $return = array('ask'=>0,'message'=>'Fail');
        try{
        	if(empty($this->_account)){
        		throw new Exception('账号不可为空');
        	}
        	if(empty($this->_company_code)){
        		throw new Exception('公司代码不可为空');
        	}
        	$con = array(
        			'platform' => 'amazon',
        			'company_code' => $this->_company_code,
        			'user_account' => $this->_account,
        			'status'=>1
        	);
        	$pUser = Service_PlatformUser::getByCondition($con);
        	if(empty($pUser)){
        		throw new Exception('账号不存在或未激活');
        	}
        	$pUser = array_pop($pUser);
        	$this->setCountryBySite($pUser['site']);
        	
            Common_ApiProcess::log("获取信息开始[{$this->_account}]-->" . implode(' ', $asinArr));
            $amazonEcs = new AmazonECS($this->_aws_api_key, $this->_aws_secret_key, $this->_country, $this->_aws_associate_tag);
            
            $amazonEcs->associateTag($this->_aws_associate_tag);
            
            $response = $amazonEcs->responseGroup('Large')->lookup($asinArr);
            $response = Common_Common::objectToArray($response);
                // Common_ApiProcess::log(print_r($response, true));
            $config = array(
                $this->_aws_api_key,
                $this->_aws_secret_key,
                $this->_country,
                $this->_aws_associate_tag,
                $this->_site,
                $this->_account,
                $this->_company_code,
            );
            Common_ApiProcess::log("获取信息结束-->" . implode(' ', $asinArr));
            Ec::showError(print_r($config,true)."\n".print_r($response, true), "amazon_ecs_");
            
            $this->_saveLookup($response);
            $return['ask'] = 1;
            $return['message'] = 'Success';
            $return['data'] = $response;
        }catch(Exception $e){
            Ec::showError($e->getMessage(), 'amazon_ecs_err_');
            Common_ApiProcess::log($e->getMessage());
        }
        
        return $return;
    }

    protected function _saveLookup($response)
    {
    	Common_Common::checkTableColumnExist('amazon_lookup_image_set', 'site');
    	Common_Common::checkTableColumnExist('amazon_lookup_image_set', 'country');
    	Common_Common::checkTableColumnExist('amazon_lookup_image_set', 'user_account');
    	Common_Common::checkTableColumnExist('amazon_lookup', 'company_code');
    	
        $Items = $response['Items']['Item'];
        if(empty($Items)){
            return;
        }
        if(! isset($Items[0])){
            $ItemsT = array();
            $ItemsT[] = $Items;
            $Items = $ItemsT;
        }
        $arr = array_flip($this->_arr);
        foreach($Items as $Item){
            $row = array(
                'asin' => $Item['ASIN'],
                'parent_asin' => $Item['ParentASIN'],
                'detail_page_url' => $Item['DetailPageURL'],
                'large_image' => $Item['LargeImage']['URL'],
                'site' => $this->_site,
                'country' => $this->_country,
                'user_account'=>$this->_account,
                'company_code'=>$this->_company_code,
            );
            $row = Ec_AutoRun::arrayNullToEmptyString($row);
            $exist = Service_AmazonLookup::getByField($row['asin'], 'asin');
            if($exist){
                $al_id = $exist['id'];
                Service_AmazonLookup::update($row, $exist['id'], 'id');
            }else{
                $al_id = Service_AmazonLookup::add($row);
            }
			$con = array (
					'asin' => $row ['asin'],
					'site' => $this->_site,
					'country' => $this->_country,
					'user_account' => $this->_account 
			);
			$images = Service_AmazonLookupImageSet::getByCondition($con);
			foreach($images as $img){
				Service_AmazonLookupImageSet::delete($img['id'],'id');
			}
            
            $imageSet = $Item['ImageSets']['ImageSet'];

            if(! isset($imageSet[0])){
                $imageSetT = array();
                $imageSetT[] = $imageSet;
                $imageSet = $imageSetT;
            }
            foreach($imageSet as $image){
				$arr = array (
						'asin' => $row ['asin'],
						'al_id' => $al_id,
						'large_image' => $image ['LargeImage'] ['URL'],
						'site' => $this->_site,
						'country' => $this->_country,
						'user_account' => $this->_account 
				);
				Service_AmazonLookupImageSet::add ( $arr );
			}
        }
    }

    public static function cron_load_amazon_asin_lookup()
    {
        $table = 'cron_load_amazon_asin_lookup';
        //replace into `cron_load_amazon_asin_lookup`(asin,sku,user_account,company_code) SELECT a.asin1,seller_sku,a.user_account,a.company_code from amazon_merchant_listing a LEFT JOIN amazon_lookup b on a.asin1=b.asin and a.user_account=b.user_account  and a.company_code=b.company_code where 1=1 and a.company_code='qk' and a.user_account='xxx' and b.asin is null;

        $sql = "show tables like '{$table}';";
        $exist = Common_Common::fetchRow($sql);
        if(!$exist){
	        $sql = "
	            CREATE TABLE if not exists `{$table}` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `asin` varchar(64) NOT NULL COMMENT 'asin',
				  `sku` varchar(64) NOT NULL COMMENT 'sku',
				  `user_account` varchar(64) NOT NULL comment 'replace into `cron_load_amazon_asin_lookup`(asin,sku,user_account,company_code) SELECT a.asin1,a.seller_sku,a.user_account,a.company_code from amazon_merchant_listing a LEFT JOIN amazon_lookup b on a.asin1=b.asin and a.user_account=b.user_account  and a.company_code=b.company_code where 1=1 and b.asin is null;',
				  `company_code` varchar(200) DEFAULT 'company_code',
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `unique` (`asin`,`user_account`,`company_code`),
				  KEY `asin` (`asin`) USING BTREE
				) ;            
	        	";
	        Common_Common::query($sql);
        }
        
        Common_Common::checkTableColumnExist($table, 'company_code');
        Common_Common::checkTableColumnExist($table, 'sku');
        return $table;
    }

    public function lookupSingle($asin)
    {
        $row = Service_AmazonMerchantListing::getByField($asin, 'asin1');
        if(empty($row)){
            throw new Exception('ASIN不存在-->' . $asin);
        }
        
        $con = array(
            'platform' => 'amazon',
            'user_account' => $row['user_account'],
            'company_code' => $row['company_code'],
        );
        $pUser = Service_PlatformUser::getByCondition($con);
        if(empty($pUser)){
            throw new Exception('账号不存在-->' . $row['user_account']);
        }
        $pUser = array_pop($pUser);
        
        $asinArr = array();
        $asinArr[] = $row['asin1'];
        $asinArr[] = $row['asin2'];
        $asinArr[] = $row['asin3'];
        
        foreach($asinArr as $k => $asin){
            if(empty($asin)){
                unset($asinArr[$k]);
            }
        }
        array_unique($asinArr);
        sort($asinArr);
        
        $this->lookup($asinArr);
    }

    public static function lookupTest($asin = 'B00D8WIIR4')
    {
        $amazonEcs = new AmazonECS(AWS_API_KEY, AWS_API_SECRET_KEY, 'COM', AWS_ASSOCIATE_TAG);
        
        // for the new version of the wsdl its required to provide a associate Tag
        // @see https://affiliate-program.amazon.com/gp/advertising/api/detail/api-changes.html?ie=UTF8&pf_rd_t=501&ref_=amb_link_83957571_2&pf_rd_m=ATVPDKIKX0DER&pf_rd_p=&pf_rd_s=assoc-center-1&pf_rd_r=&pf_rd_i=assoc-api-detail-2-v2
        // you can set it with the setter function or as the fourth paramameter of ther constructor above
        $amazonEcs->associateTag(AWS_ASSOCIATE_TAG);
        
        // Looking up multiple items
        // $response = $amazonEcs->responseGroup('Large')->optionalParameters(array('Condition' => 'New'))->lookup(array('B00KJBL5TK', 'B00KAQ1YTK'));
        // $response = $amazonEcs->responseGroup('Large')->lookup($asin);
        // print_r($response);
        $groupArr = ' Tags Help ListMinimum VariationSummary VariationMatrix TransactionDetails VariationMinimum VariationImages PartBrandBinsSummary CustomerFull CartNewReleases ItemIds SalesRank TagsSummary Fitments Subjects Medium ListmaniaLists PartBrowseNodeBinsSummary TopSellers Request HasPartCompatibility PromotionDetails ListFull Small Seller OfferFull Accessories VehicleMakes MerchantItemAttributes TaggedItems VehicleParts BrowseNodeInfo ItemAttributes PromotionalTag VehicleOptions ListItems Offers TaggedGuides NewReleases VehiclePartFit OfferSummary VariationOffers CartSimilarities Reviews ShippingCharges ShippingOptions EditorialReview CustomerInfo PromotionSummary BrowseNodes PartnerTransactionDetails VehicleYears SearchBins VehicleTrims Similarities AlternateVersions SearchInside CustomerReviews SellerListing OfferListings Cart TaggedListmaniaLists VehicleModels ListInfo Large CustomerLists Tracks CartTopSellers Images Variations RelatedItems Collections ';
        $groupArr = trim($groupArr);
        $groupArr = preg_split('/\s+/', $groupArr);
        // print_r($groupArr);exit;
        foreach($groupArr as $key => $group){
            try{
                $response = $amazonEcs->responseGroup($group)->lookup($asin);
                Ec::showError(print_r($response, true), "amazon_ecs_" . $group);
                // print_r($response);
                // Common_ApiProcess::log(print_r($response, true));
                Common_ApiProcess::log($group . '==>Ok');
            }catch(Exception $e){
                Ec::showError($e->getMessage(), "amazon_ecs_err_" . $group);
                Common_ApiProcess::log($group . '==>' . $e->getMessage());
                unset($groupArr[$key]);
            }
        }
        print_r($groupArr);
    }
}