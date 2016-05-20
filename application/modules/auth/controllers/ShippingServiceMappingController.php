<?php
class Auth_ShippingServiceMappingController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "auth/views/shipping_method_platform/";
        $this->serviceClass = new Service_User();
//         Service_User::getPlatformUser('do');
    }

    public function shipBindAction(){
        if($this->getRequest()->isPost()){
            $return = array('ask'=>0,'message'=>'');
            try{

                $platform_shipping_service = $this->getRequest()->getParam('platform_shipping_service', '');
                $warehouse_shipping_service = $this->getRequest()->getParam('warehouse_shipping_service', ''); 
                $warehouse_id = $this->getRequest()->getParam('warehouse_id', '1'); 
                
                $row = array(
                        'platform' => $this->getRequest()->getParam('platform', 'ebay'),
                        'platform_shipping_service' => $platform_shipping_service,
                        'warehouse_shipping_service' => $warehouse_shipping_service,
                        'warehouse_id' => $warehouse_id
                );
                $con = array(
                        'platform_shipping_service' => $platform_shipping_service,
                        'warehouse_id' => $warehouse_id,
                        'warehouse_shipping_service' => $warehouse_shipping_service
                );
                $exist = Service_ShippingServiceMapping::getByCondition($con);
                if($exist){
                    $exist = $exist[0];
                    Service_ShippingServiceMapping::update($row, $exist['mapping_id'], 'mapping_id');
                }else{
                    Service_ShippingServiceMapping::add($row);
                }
                $return['ask'] = 1;
                $return['message'] = '绑定成功';
            }catch(Exception $e){
                $return['message'] = '绑定失败';
            }
            die(Zend_Json::encode($return));
        }
        $sms = Service_ShippingMethod::getByCondition(null,'*',0,0,'sm_code');
        $smps = Service_ShippingMethodPlatform::getByCondition(null,'*',0,0,'short_code');
        
        $this->view->sms = $sms;
        $this->view->smps = $smps;
		$wms_db = Zend_Registry::get('wms_db');//Wsm 数据库名 
		
        $sql = 'SELECT b.warehouse_id,c.warehouse_code,c.warehouse_desc,a.sm_code,a.sm_name_cn FROM `shipping_method` a INNER JOIN shipping_method_settings b on a.sm_id=b.sm_id  inner join warehouse c on c.warehouse_id=b.warehouse_id order by b.warehouse_id;';
//         echo $sql;exit;
        $db = Common_Common::getAdapter();
        $data = $db->fetchAll($sql);
        
        $warehouseShippingMethod = array();
        $warehouseArr = array();
        foreach($data as $v){
            $warehouseShippingMethod[$v['warehouse_id']][] = $v;
            $warehouse = $v;
            unset($warehouse['sm_code']);
            unset($warehouse['[sm_name_cn']);
            $warehouseArr[$v['warehouse_id']] = $warehouse;
        }
        $this->view->warehouseShippingMethodJson = Zend_Json::encode($warehouseShippingMethod);
        $this->view->warehouseArr = $warehouseArr;
        $this->view->warehouseArrJson = Zend_Json::encode($warehouseArr);
//         print_r($warehouseShippingMethod);exit;
        echo Ec::renderTpl($this->tplDirectory . "shipping_service_mapping.tpl", 'layout');
    }

    public function getShipBindListAction(){
        $platform_shipping_service = $this->getRequest()->getParam('platform_shipping_service', '');
        $warehouse_shipping_service = $this->getRequest()->getParam('warehouse_shipping_service', '');
        $warehouse_id = $this->getRequest()->getParam('warehouse_id', '');
        
        $sql = 'SELECT * FROM shipping_service_mapping a INNER JOIN shipping_method_platform b on a.platform_shipping_service=b.shipping_method_code  ';

        $sql.= ' where 1=1';
        
        if($platform_shipping_service){
            $sql .= " and a.platform_shipping_service='" . trim($platform_shipping_service) . "'";
        }
        if($warehouse_shipping_service){
            $sql .= " and a.warehouse_shipping_service='" . trim($warehouse_shipping_service) . "'";
        }
        if($warehouse_id){
            $sql .= " and a.warehouse_id='" . trim($warehouse_id) . "'";
        }
        // echo $sql;exit;
        $db = Common_Common::getAdapter();
        $data = $db->fetchAll($sql);
        
        if($data){
            $return = array(
                'state' => 1,
                'data' => $data
            );
        }else{
            $return = array(
                'state' => 0,
                'data' => $data
            );
        }
        die(Zend_Json::encode($return));
    }

    public function getShipBindAction(){
        $ssps = Service_ShippingServiceMapping::getAll();
        $return = array('state'=>1,'data'=>$ssps);
        die(Zend_Json::encode($return));
    }
    public function delShipBindAction(){
        $return = array(
            'ask' => 0,
            'message' => ''
        );
        try{            
            $mapping_id = $this->getRequest()->getParam('mapping_id', '0');
            Service_ShippingServiceMapping::delete($mapping_id);
            $return['ask'] = 1;
            $return['message'] = '删除成功';
        }catch(Exception $e){
            
            $return['message'] = '删除成功';
        }
        
        die(Zend_Json::encode($return));
    }
}