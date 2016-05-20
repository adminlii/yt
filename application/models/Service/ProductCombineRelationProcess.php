<?php
class Service_ProductCombineRelationProcess
{
    // 如果有绑定账号，取得绑定账号的关系，否则取默认关系
    public static function getRelation($productSku, $account = '',$company_code='')
    {
        if($productSku == ''){
            return array();
        }
        if(empty($company_code)){
            $company_code = Common_Company::getCompanyCode();
        }
        if(empty($company_code)){
            return array();            
        }
        $con = array(
            'product_sku' => $productSku,
            'company_code' => $company_code,
            'user_account' => $account
        );
        if($account == ''){
            $con['user_account_null'] = 'null';
        }
        
        $result = Service_ProductCombineRelation::getByCondition($con, '*');
        if(empty($result)){
            unset($con['user_account']);
            $con['user_account_null'] = 'null';
            $result = Service_ProductCombineRelation::getByCondition($con, '*');
        }
        return $result;
    }
}