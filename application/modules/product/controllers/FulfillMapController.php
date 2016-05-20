<?php
class Product_FulfillMapController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "product/views/fulfill-map/";
    }

    public function listAction()
    {
        if($this->_request->isPost()){

            $db = Common_Common::getAdapter();
            $page = $this->_request->getParam('page', 1);
            $pageSize = $this->_request->getParam('pageSize', 20);
            
            $page = $page ? $page : 1;
            $page = max(0,$page);
            $pageSize = $pageSize ? $pageSize : 20;
            
            $return = array(
                    "state" => 0,
                    "message" => "No Data"
            );
            $con = array();

            $shipping_method = $this->_request->getParam('shipping_method', '');
            $con['shipping_method'] = $shipping_method;
            $shipping_method_mark = $this->_request->getParam('shipping_method_mark', '');
            $con['shipping_method_mark'] = $shipping_method_mark;
            $country_code = $this->_request->getParam('country_code', '');
            $con['country_code'] = $country_code;
            
            $count = Service_OrderFulfillmentMap::getByCondition($con,'count(*)');
            
            $return['total'] = $count; 
            if ($count) {
                $data = Service_OrderFulfillmentMap::getByCondition($con,'*',$pageSize,$page); 
                foreach($data as $k=>$v){
                    $v['country_name'] = '';
                    $country = Service_Country::getByField($v['country_code'],'country_code');
                    if($country){
                        $v['country_name'] = $country['country_name'];
                    }
                    $data[$k] = $v;
                }
                $return['data'] = $data;
                $return['state'] = 1;
            }
            die(Zend_Json::encode($return));
            
        }
        $country = Service_Country::getByCondition();
        $this->view->country = $country;
        echo Ec::renderTpl($this->tplDirectory . "list.tpl", 'layout');
    }

    public function uploadNew($file){
    
        $return = array(
                'ask' => 0,
                'message' => 'Request Err'
        );
    
        $err = array();
        $fileName = $file['name'];
        $filePath = $file['tmp_name'];
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
            if(empty($fileName)||empty($filePath)){
                throw new Exception('请选择文件');
            }
            $pathinfo = pathinfo($fileName);
            if ( !isset($pathinfo["extension"]) || $pathinfo["extension"] != "xls") {
                throw new Exception('请上传excel文件');
            }
            $data_tmp = Service_ProductTemplate::readUploadFile($fileName, $filePath);
            if(! is_array($data_tmp)){
                throw new Exception($data_tmp);
            }        
            $data = array();
            foreach($data_tmp as $v){
                foreach($v as $kk=>$vv){//全部转为大写
                    $v[$kk] = strtoupper($vv);
                }
                $v['国际简称'] = trim($v['国际简称']);
                $v['国际简称'] = preg_replace('/^\(([A-Z]{2})\).*/','$1',$v['国际简称']);
                
                $v['是否通知买家'] = trim($v['是否通知买家']);
                $v['是否通知买家'] = $v['是否通知买家']=='Y'?'Y':'N';
                $row = array(
                    'shipping_method' => $v['派送方式'],
                    'country_code' => $v['国际简称'],
                    'shipping_method_mark' => $v['标记派送方式'],
                    'notify_customer'=>$v['是否通知买家']
                );
                $data[] = $row;
            }
//             print_r($data);exit;
            
            foreach($data as $k=>$v){
                if(empty($v['shipping_method'])){
                    unset($data[$k]);
                    continue;
                }
                if(empty($v['country_code'])){
                    $err[] = "派送方式[{$v['shipping_method']}]国际简称不能为空";
                }else{
                    $country = Service_Country::getByField($v['country_code'], 'country_code');
                    if(! $country){
                        $err[] = "派送方式[{$v['shipping_method']}]国际简称不存在";
                    }
                }                
                if(empty($v['shipping_method_mark'])){
                    $err[] = "派送方式[{$v['shipping_method']}]标记派送方式不可为空";
                }
            }
            if(!empty($err)){//判断是否数据异常
                throw new Exception('Excel 数据不合法');
            }
//             print_r($data);exit;
            foreach($data as $k=>$v){
                $con = array(
                    'shipping_method' => $v['shipping_method'],
                    'country_code' => $v['country_code']
                );
                $exist = Service_OrderFulfillmentMap::getByCondition($con);

                $v['create_time'] = date('Y-m-d H:i:s');
                $v['user_id'] = Service_User::getUserId();
                if($exist){
                    $exist = $exist[0];                    
                    Service_OrderFulfillmentMap::update($v, $exist['id'], 'id');
                }else{
                    Service_OrderFulfillmentMap::add($v);
                }
            }
            
            $db->commit();
            $return['ask'] = 1;
            $return['message'] = '所有数据上传成功';
            $return['data'] = Zend_Json::encode($data);
        }catch(Exception $e){
            $db->rollback();    
            $return['ask'] = 0;
            $return['message'] = $e->getMessage();
        }
        $return['err'] = $err;
        return $return;
    }
    public function uploadAction(){

        if($this->_request->isPost()){
            set_time_limit(0);
            $file = $_FILES['fileToUpload'];
            $return = $this->uploadNew($file);
            die(Zend_Json::encode($return));
        }
        echo Ec::renderTpl($this->tplDirectory . "upload.tpl", 'layout');
    }
    
    public function editAction(){
        
        $id = $this->_request->getParam('id', '');
        $shipping_method = $this->_request->getParam('shipping_method', '');
        $shipping_method_mark = $this->_request->getParam('shipping_method_mark', '');
        $country_code = $this->_request->getParam('country_code', '');        
        $notify_customer = $this->_request->getParam('notify_customer', '');
        
        $result = array('ask'=>0,'message'=>'Fail');
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
            $row = array(
                    'shipping_method' => $shipping_method,
                    'shipping_method_mark' => $shipping_method_mark,
                    'country_code' => $country_code,
                    'notify_customer'=>$notify_customer
            );
            foreach($row as $k=>$v){
                if(empty($v)){
                    throw new Exception("{$k} can not be empty");
                }
            }
            $exist = Service_OrderFulfillmentMap::getByField($id, 'id');

            $row['create_time'] = date('Y-m-d H:i:s');
            $row['user_id'] = Service_User::getUserId();
            if($exist){          
                Service_OrderFulfillmentMap::update($row, $id, 'id');
            }else{
                Service_OrderFulfillmentMap::add($row);
                
            }
            $result['ask'] = 1;
            $result['state'] = 2;

            $result['message'] = 'Success';
            
            $db->commit();
        }catch(Exception $e){
            $db->rollback();
            $result['message'] = $e->getMessage();
        }
        
        die(Zend_Json::encode($result));
        
    }

    public function getByJsonAction()
    {
        $result = array('state' => 0, 'message' => 'Fail', 'data' => array());
        $paramId = $this->_request->getParam('paramId', '');
        if (!empty($paramId) && $rows = Service_OrderFulfillmentMap::getByField($paramId, 'id')) {
            //             $rows = $this->serviceClass->getVirtualFields($rows);
            $result = array('state' => 1, 'message' => '', 'data' => $rows);
        }
        die(Zend_Json::encode($result));
    }
    /**
     * 删除
     */
    public function deleteAction(){
        $result = array(
            'ask' => 0,
            'message' => 'No Data'
        );
        $id = $this->_request->getParam('paramId', '');
        
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
            Service_OrderFulfillmentMap::delete($id, 'id');
            $result['ask'] = 1;
            $result['state'] = 2;
            $result['message'] = 'Operation Success';
            $db->commit();
        }catch(Exception $e){
            $db->rollback();
            $result['message'] = $e->getMessage();
        }
        die(Zend_Json::encode($result));
    }
    
}