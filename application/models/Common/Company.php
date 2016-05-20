<?php
/**
 * 
 * @author Administrator
 *
 */
class Common_Company
{

    public static function getUserToken($userAccount, $companyCode)
    {
        $con = array(
            'user_account' => $userAccount,
            'company_code' => $companyCode
        );
        if(empty($userAccount) || empty($companyCode)){
            throw new Exception('参数错误');
        }
        $row = Service_PlatformUser::getByCondition($con);
        if($row){
            if(count($row) > 1){
                throw new Exception('账号异常');
            }
            $row = array_pop($row);
            return $row["user_token"];
        }else{
            throw new Exception('账号不存在');
        }
    }

    public static function getCompanyCode($userId = 0)
    {
        if(! $userId){
            $companyCode = Service_User::getCustomerCode();
            if($companyCode){
                return $companyCode;
            }
        }else{
            $db = Common_Common::getAdapter();
            $sql = "select b.* from user a inner join csi_customer b on a.customer_id=b.customer_id where a.user_id='{$userId}'";
            $user = $db->fetchRow($sql);
            if($user && $user['customer_code']){
                return $user['customer_code'];
            }
        }
        return '';
    }
    
    //===============================================================ebay start
    
    public static function getEbayConfig(){
        if(!Zend_Registry::isRegistered('company')){
            $db = Common_Common::getAdapter();
            $sql = "select * from config where config_attribute='EBAY_CONFIG_EVN';";
            $config = $db->fetchRow($sql);
            if(!$config){
                throw new Exception('基础数据config未设置EBAY_CONFIG_EVN');
            }
            $types = array('sandbox','product');
            $config['config_value'] = strtolower($config['config_value']);
            if(!in_array($config['config_value'],$types)){
                throw new Exception('基础数据config设置EBAY_CONFIG_EVN不合法');                
            }
            
            $sql = "select * from ebay_config where status=1 and type='{$config['config_value']}' order by rand() limit 1;";
            $ebayConfig = $db->fetchRow($sql);
            if(! $ebayConfig){
                throw new Exception('基础数据ebay_config未设置/未激活');
            }
            Zend_Registry::set('company', $ebayConfig);
        }
        return Zend_Registry::get('company');
    }
    
    public static function getEbayDevid()
    {
        $config = self::getEbayConfig();
        return $config['devid'];
    }

    public static function getEbayAppid()
    {
        $config = self::getEbayConfig();
        return $config['appid'];
    }

    public static function getEbayCertid()
    {
        $config = self::getEbayConfig();
        return $config['certid'];
    }

    public static function getEbayServerurl()
    {
        $config = self::getEbayConfig();
        return $config['serverurl'];
    }

    public static function getEbayVersion()
    {
        $config = self::getEbayConfig();
        return $config['version'];
    }

    public static function getEbayRuname()
    {
        $config = self::getEbayConfig();
        return $config['runame'];
    }

    public static function getEbayLoginUrl()
    {
        $config = self::getEbayConfig();
        return $config['loginurl'];
    }

    public static function getEbayEndpoint()
    {
        $config = self::getEbayConfig();
        return $config['endpoint'];
    }
    //===============================================================ebay end

//     //===============================================================paypal
//     public static function getPaypalEndpoint()
//     {
//         $config = self::getEbayConfig();
//         return $config['paypal']['endpoint'];
//     }

//     public static function getPaypalPay()
//     {
//         $config = self::getEbayConfig();
//         return $config['paypal']['pay'];
//     }

//     public static function getPaypalReceivableUsername()
//     {
//         $config = self::getEbayConfig();
//         return $config['paypal']['receivable']['username'];
//     }

//     public static function getPaypalReceivablePassword()
//     {
//         $config = self::getEbayConfig();
//         return $config['paypal']['receivable']['password'];
//     }

//     public static function getPaypalReceivableSignature()
//     {
//         $config = self::getEbayConfig();
//         return $config['paypal']['receivable']['signature'];
//     }

//     public static function getAliPayReceivableUsername()
//     {
//         $config = self::getEbayConfig();
//         return $config['alipay']['receivable']['username'];
//     }

//     //===============================================================paypal

//     //===============================================================alipay
//     public static function getAliPayReceivablePartner()
//     {
//         $config = self::getEbayConfig();
//         return $config['alipay']['receivable']['partner'];
//     }

//     public static function getAliPayReceivableKey()
//     {
//         $config = self::getEbayConfig();
//         return $config['alipay']['receivable']['key'];
//     }
//     //===============================================================alipay

//     public static function getAppFroEbayDevKey()
//     {
//         $config = self::getEbayConfig();
//         return $config['app']['for']['ebay']['devkey'];
//     }

//     public static function getAppFroEbayAppKey()
//     {
//         $config = self::getEbayConfig();
//         return $config['app']['for']['ebay']['appkey'];
//     }

//     public static function getAppFroEbayErpAppUuid()
//     {
//         $config = self::getEbayConfig();
//         return $config['app']['for']['ebay']['erp']['appuuid'];
//     }

    public static function initAcc(){
        
    }
    
    /**
     * 获取数据库配置
     */
    public static function getDBConfig(){
    	if(!Zend_Registry::isRegistered('db_config')) {
    		$sql = "select * from config;";
    		$config = Common_Common::fetchAll($sql);
    		
    		$config_arr = array();
    		foreach($config as $k => $row) {
    			$config_arr[$row['config_attribute']] = $row;
    		}
    		
    		Zend_Registry::set('db_config', $config_arr);
    	}
    	return Zend_Registry::get('db_config');
    }
}