<?php
class Process_SkuRelationProcess
{

    private $_company_code = '';

    private $_user_account = null;

    private $_product_sku = '';

    private $_pcr_product_sku_qty_arr = array();
    
    private $_total_qty = 0;

    private $_err = array();

    private $_db = null;

    public function setCompanyCode($company_code)
    {
        $this->_company_code = $company_code;
    }

    public function setUserAccount($user_account)
    {
        $this->_user_account = $user_account;
    }

    public function setProductSku($product_sku)
    {
        $this->_product_sku = $product_sku;
    }

    public function setPcrProductSkuArr($pcr_product_sku_qty_arr)
    {
        $this->_pcr_product_sku_qty_arr = $pcr_product_sku_qty_arr;
    }

    public function addPcrProductSku($pcr_product_sku_qty)
    {
        $this->_pcr_product_sku_qty_arr[] = $pcr_product_sku_qty;
    }

    public function setDb($db)
    {
        $this->_db = $db;
    }

    private function _validate()
    {
        if(empty($this->_company_code)){
            $this->_err[] = Ec::Lang('公司代码不可为空');
        }
        if(!isset($this->_user_account)){
            $this->_err[] = Ec::Lang('账号未设置');            
        }
        if(empty($this->_user_account)){
            $this->_err[] = Ec::Lang('平台SKU不可为空');
        }
        if(empty($this->_pcr_product_sku_qty_arr)){
            $this->_err[] = Ec::Lang('平台SKU不可为空');
        }else{
            foreach($this->_pcr_product_sku_qty_arr as $k => $sku_qty){
                $sku = $sku_qty['pcr_product_sku'];
                $qty = $sku_qty['pcr_quantity'];
                if(empty($sku) || empty($qty)){
                    unset($this->_pcr_product_sku_qty_arr[$k]);
                }                
            }
            if(empty($this->_pcr_product_sku_qty_arr)){
                $this->_err[] = Ec::Lang('平台SKU不可为空');
            }
        }
        foreach($this->_pcr_product_sku_qty_arr as $k=> $v){
            $sku = $sku_qty['pcr_product_sku'];
            $qty = $sku_qty['pcr_quantity'];
            $con = array('company_code'=>$this->_company_code,'invoice_code'=>$sku);
            $invoiceInfo = Service_CsdInvoiceInfo::getByCondition($con);
            if(empty($invoiceInfo)){
                $this->_err[] = Ec::Lang('申报代码不存在').'-->'.$sku;
            }
            if(!preg_match('/^[0-9]+$/', $qty)||intval($qty)<=0){
                $this->_err[] = Ec::Lang('申报数量需大于0的整数').'-->'.$qty;                
            }
        }
    }

    public function getErrs(){
        return $this->_err;
    }
    public function process()
    {
        $return = array(
            'ask' => 0,
            'message' => 'Fail.'
        );
        try{
            $this->_validate();
//             print_r($this->_err);exit;
            if(! empty($this->_err)){
                throw new Exception('生成关系异常');
            }
            if(! $this->_db){
                $this->_db = Common_Common::getAdapter();
            }
            $sql = "delete from product_combine_relation where company_code='{$this->_company_code}' and user_account='{$this->_user_account}' and product_sku='{$this->_product_sku}';";
            
            $this->_db->query($sql);

//                         print_r($this->_pcr_product_sku_qty_arr);exit;

            foreach($this->_pcr_product_sku_qty_arr as $k=> $v){
                $this->_total_qty+= $v['pcr_quantity'];
            }
            foreach($this->_pcr_product_sku_qty_arr as $v){
                $row = array(
                    'product_sku' => $this->_product_sku,
                    'pcr_product_sku' => $v['pcr_product_sku'],
                    'pcr_quantity' => $v['pcr_quantity'],
                    'pcr_percent' => round(1/$this->_total_qty,3),
                    'user_account' => $this->_user_account,
                    'company_code' => $this->_company_code,
                    'pcr_add_time' => date('Y-m-d H:i:s')
                );
                Service_ProductCombineRelation::add($row);
            }
            // 日志
            $logRow = array(
                'company_code' => $this->_company_code,
                'product_sku' => $this->_product_sku,
                'log_content' => print_r($this->_pcr_product_sku_qty_arr),
                'user_account' => $this->_user_account,
                'pcrl_add_time' => date('Y-m-d H:i:s'),
                'user_id' => Service_User::getUserId()
            );
            Service_ProductCombineRelationLog::add($logRow);
            $return['ask'] = 1;
            $return['message'] = 'Success';
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        $return['err'] = $this->_err;
        return $return;
    }
}