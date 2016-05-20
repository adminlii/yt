<?php
class Order_OrderTemplateController extends Ec_Controller_Action
{

    public function preDispatch()
    {
        $this->tplDirectory = "order/views/order-template/";
    }

    public function listAction()
    {
        
        $this->forward('upload');
    }

    /**
     * 生成标准模板
     */
    public function genStandardColumnAction(){
        $process = new Process_OrderTemplate();
        $process->genStandardColumn();
    }
    /**
     * 订单导入
     */
    public function uploadAction()
    {
        $process = new Process_OrderTemplate();
        // 标准模板
        $standardColumn = $process->getStandardColumn();
        $this->view->standardColumn = $standardColumn;
        if($this->getRequest()->isPost()){
            try{
                set_time_limit(0);
                ini_set('memory_limit', '1024M');
                $return = array(
                    'ask' => 0,
                    'message' => 'Request Method Err'
                );
                
                $file = $_FILES['fileToUpload'];
                // print_r($file);exit;
                
                // 标准模板
                // $standardColumn = $process->getStandardColumn();
                // print_r($standardColumn);
                // 自定义模板
                $rs = $process->getUserTemplate($file);
                if($rs['ask']){
                    $userTemplate = $rs['file_data'];
//                     print_r($userTemplate);
//                     exit();
                    $this->view->userTemplate = $userTemplate;
                    $this->view->file_name = $rs['file_name'];
                }else{
                    throw new Exception($rs['message']);
                }
                // $session = new Zend_Session_Namespace('userTemplate');
                // $session->unsetAll();
                // $session->userTemplate = $userTemplate;
            }catch(Exception $e){
                $this->view->errMsg = $e->getMessage();
            }
        }
        // print_r($standardColumn);
        // Process_OrderTemplate::genStandardColumn();
        echo Ec::renderTpl($this->tplDirectory . "template_upload.tpl", 'layout-upload');
    }

    /**
     * 订单保存
     */
    public function saveAction()
    {
        $reportId = $this->getParam('report_id', '');
        $fileName = $this->getParam('file_name', 'empty');
        $customer_column = $this->getParam('customer_column', array());
        $standard_column = $this->getParam('standard_column', array());
//         print_r($standard_column);exit;
        $process = new Process_OrderTemplate();
        $return = $process->saveUserTemplate($customer_column,$standard_column, $fileName, $reportId);
        echo Zend_Json::encode($return);
    }
    
    /**
     * 编辑
     */
    public function editAction(){
        $customer_id = Service_User::getCustomerId();
        $db = Common_Common::getAdapter();
        $sql = "select * from csd_customer_report where customer_id='{$customer_id}'";
        $reportArr = $db->fetchAll($sql);
//         print_r($reportArr);
        if($reportArr){
            $this->view->reportArr = $reportArr;
            echo Ec::renderTpl($this->tplDirectory . "template_edit.tpl", 'layout-upload');
        }else{            
            echo Ec::renderTpl($this->tplDirectory . "template_upload.tpl", 'layout-upload');
        }
    }

    /**
     * 编辑
     */
    public function getTemplateFormAction(){
        $report_id = $this->getParam('report_id', '');

        $db = Common_Common::getAdapter();
        $process = new Process_OrderTemplate();
        // 标准模板
        $standardColumn = $process->getStandardColumn();
        foreach($standardColumn as $k => $v){
            $sc_id =$v['sc_id'];
            $sql = "select distinct sc_id,sc_columncode,mt_code,mt_value from csd_customer_reportmapping where report_id='{$report_id}' and sc_id='{$sc_id}';";
//             echo $sql;
            $map = $db->fetchRow($sql);
            if($map){
                $map['mt_code_title'] = $map['mt_code'] == 'C'?'直接拷贝':'字段连接';
                $v['map'] = $map;
            }
            $standardColumn[$k] = $v;
        }
//         exit;
        $this->view->standardColumn = $standardColumn;
        $userTemplate = $process->getReportTemplate($report_id);
        foreach($userTemplate as $k => $v){            
            $userTemplate[$k] = $v;
        }
//         print_r($userTemplate);exit;
        $this->view->userTemplate = $userTemplate;
        $this->view->report_id = $report_id;
        echo $this->view->render($this->tplDirectory . "template_edit_form.tpl");
    }
    
    public function deleteAction(){
        $reportId = $this->getParam('report_id', '');

        $process = new Process_OrderTemplate();
        $return = $process->deleteTemplate($reportId);
        echo Zend_Json::encode($return);
    }
    /**
     * 订单导入
     */
    public function testAction()
    {
        $process = new Process_OrderTemplate();
        // 标准模板
        $standardColumn = $process->getStandardColumn();
        $this->view->standardColumn = $standardColumn;
        
        $session = new Zend_Session_Namespace('userTemplate');
        if($session->userTemplate){
            $userTemplate = $session->userTemplate;
            $session->userTemplate = $userTemplate;
            $this->view->userTemplate = $userTemplate;
        }
        echo Ec::renderTpl($this->tplDirectory . "template_upload.tpl", 'layout-upload');
    }
}