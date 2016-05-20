<?php
class Common_CountryController extends Ec_Controller_Action
{

    public function getByKeywordAction()
    {
        $sku = $this->_request->getParam('term', '');
        $limit = $this->_request->getParam('limit', '20');
        
        $company_code = Common_Company::getCompanyCode();
        $db = Common_Common::getAdapter();
        $sql = "select * from idd_country where (country_code like '%{$sku}%' or country_enname like '%{$sku}%' or country_cnname like '%{$sku}%' ) order by country_code asc limit {$limit}";
        $result = $db->fetchAll($sql);
        $lang = Ec::getLang(1);
        foreach($result as $k => $v){
            $v['label'] = $v['country_code'] . '[' . $v['country_enname'] . ']' . '[' . $v['country_cnname'] . ']';
            $v['value'] = $v['country_code'];
            $result[$k] = $v;
        }
        die(Zend_Json::encode($result));
    }
}