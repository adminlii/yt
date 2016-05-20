<?php
class Invoice_InvoiceController extends Ec_Controller_Action
{

    public function preDispatch()
    {
        $this->tplDirectory = "invoice/views/invoice/";
    }

    /**
     * 列表
     *
     * @see Ec_Controller_Action::listAction()
     */
    public function listAction()
    {
        if($this->_request->isPost()){
            set_time_limit(0);
            $page = $this->_request->getParam('page', 1);
            $pageSize = $this->_request->getParam('pageSize', 20);
            
            $page = $page ? $page : 1;
            $pageSize = $pageSize ? $pageSize : 20;
            
            $return = array(
                "state" => 0,
                "message" => "No Data"
            );
            $condition = $this->getRequest()->getParams();
            $condition['company_code'] = Service_User::getCustomerCode();
            $orderBy = array();
            $keyword = $this->_request->getParam('keyword', 20);
            $condition['keyword'] = $keyword;
            
            $count = Service_CsdInvoiceInfo::getByCondition($condition, 'count(*)');
            $return['total'] = $count;
            if($count){
                $db = Common_Common::getAdapter();
                $rows = Service_CsdInvoiceInfo::getByCondition($condition, "*", $pageSize, $page, $orderBy);
                foreach($rows as $k => $v){
                    $rows[$k] = $v;
                }
                $return['data'] = $rows;
                $return['state'] = 1;
                $return['message'] = "Success";
            }
            // 是否重新统计
            $reTongji = new Zend_Session_Namespace('reTongji');
            
            $return['reTongji'] = $reTongji->reTongji;
            die(Zend_Json::encode($return));
        }
        
        // print_r($productKind);
        $con = array(
            'unit_status' => 'ON'
        );
        $units = Service_AddDeclareunit::getByCondition($con);
        $this->view->units = $units;
        echo Ec::renderTpl($this->tplDirectory . "invoice_index.tpl", 'layout');
    }

    /**
     * 手工创建订单
     */
    public function createAction()
    {
        if($this->getRequest()->isPost()){
            $orderR = array();
            $params = $this->getRequest()->getParams();
            
            // 申报信息
            $invoice = $this->getParam('invoice', array());
            
            $invoiceArr = array();
            foreach($invoice as $column => $v){
                foreach($v as $kk => $vv){
                    $invoiceArr[$kk][$column] = $vv;
                }
            }
            // php hack
            if(! empty($invoiceArr)){
                array_unshift($invoiceArr, array());
                unset($invoiceArr[0]);
            }
//             print_r($invoiceArr);
//              exit();
            $process = new Process_InvoiceInfoProcess();
            $return = $process->addInvoiceInfoBatch($invoiceArr);
            // print_r($params);exit;
            die(Zend_Json::encode($return));
        }
    }

    public function getByKeywordAction()
    {
        $sku = $this->_request->getParam('term', '');
        $limit = $this->_request->getParam('limit', '20');
        
        $company_code = Common_Company::getCompanyCode();
        $db = Common_Common::getAdapter();
        $sql = "select * from csd_invoice_info where (invoice_code like '%{$sku}%' or invoice_enname like '%{$sku}%' or invoice_cnname like '%{$sku}%') and company_code = '{$company_code}' limit {$limit}";
        $result = $db->fetchAll($sql);
        $lang = Ec::getLang(1);
        foreach($result as $k => $v){
            $v['label'] = $v['invoice_code'] . '[' . $v['invoice_cnname'] . ']';
            $v['value'] = $v['invoice_code'];
            $v['product_title'] = $v['product_title' . $lang];
            $result[$k] = $v;
        }
        die(Zend_Json::encode($result));
    }

    public function deleteAction()
    {
        $return = array(
                'ask' => 0,
                'message' => 'No Data'
        );
        try{
            $id = $this->_getParam('id', '');
            $db = Common_Common::getAdapter();
            $sql = "select * from csd_invoice_info where id='{$id}';";
            $data = $db->fetchRow($sql);
            if(! $data){
                throw new Exception('No Data');
            }
            if($data['company_code']!=Service_User::getCustomerCode()){
                throw new Exception(Ec::Lang('没有权限操作'));                
            }
            Service_CsdInvoiceInfo::delete($id,'id');
            
            $return['ask'] = 1;
            $return['message'] = 'Success';
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
    
        echo Zend_Json::encode($return);
    }
    
    public function getByJsonAction()
    {
        $return = array(
            'ask' => 0,
            'message' => 'No Data'
        );
        try{
            $id = $this->_getParam('id', '');
            $db = Common_Common::getAdapter();
            $sql = "select * from csd_invoice_info where id='{$id}';";
            $data = $db->fetchRow($sql);
            if(! $data){
                throw new Exception('No Data');
            }
            if($data['company_code']!=Service_User::getCustomerCode()){
                throw new Exception(Ec::Lang('没有权限操作'));                
            }            
            $return['data'] = $data;
            $return['ask'] = 1;
            $return['message'] = 'Success';
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        
        echo Zend_Json::encode($return);
    }

    public function updateAction()
    {
        $return = array(
                'ask' => 0,
                'message' => 'No Data'
        );
        try{
            $invoice = $this->getParam('invoice');
            $id = $invoice['id'];
            
            $db = Common_Common::getAdapter();
            $sql = "select * from csd_invoice_info where id='{$id}';";
            $data = $db->fetchRow($sql);
            if(! $data){
                throw new Exception('No Data');
            }
            if($data['company_code']!=Service_User::getCustomerCode()){
                throw new Exception(Ec::Lang('没有权限操作'));                
            }
            $row = array(
                'invoice_enname' => $invoice['invoice_enname'],
                'invoice_cnname' => $invoice['invoice_enname'],
                'unit_code' => $invoice['unit_code'],
                'invoice_unitcharge' => $invoice['invoice_unitcharge'],
            	'invoice_weight'=>$invoice['invoice_weight'],
                'hs_code' => $invoice['hs_code'],
                'invoice_note' => $invoice['invoice_note'],
                'invoice_url' => $invoice['invoice_url'],
            );

            if(!preg_match('/^[0-9]+(\.[0-9]+)?$/',$invoice['invoice_unitcharge'])){
                throw new Exception(Ec::Lang('申报单价不可为空且需为数字'));                 
            }
            if(!preg_match('/^[0-9]+(\.[0-9]+)?$/',$invoice['invoice_weight'])){
            	throw new Exception(Ec::Lang('申报重量不可为空且需为数字'));
            }
            Service_CsdInvoiceInfo::update($row, $id,'id');
            $return['ask'] = 1;
            $return['message'] = 'Success';
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
    
        echo Zend_Json::encode($return);
    }
    
}