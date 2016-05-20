<?php
class Order_CountryMapController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "order/views/country-map/";
        $this->serviceClass = new Service_ProductAttribute();
    }

    public function listAction()
    {
    	$country = Common_DataCache::getCountry();
        if($this->_request->isPost()){

            $db = Common_Common::getAdapter();
            $page = $this->_request->getParam('page', 1);
            $pageSize = $this->_request->getParam('pageSize', 20);
            
            $page = $page ? $page : 1;
            $page = max(0,$page);
            $pageSize = $pageSize ? $pageSize : 20000;
            
            $return = array(
                    "state" => 0,
                    "message" => "No Data"
            );
            $con = array();

            $original_countryname = $this->_request->getParam('original_countryname', '');
            $country_code = $this->_request->getParam('country_code', ''); 
            $original_countryname = trim($original_countryname);            
            $country_code = trim($country_code);            
            
            $customer_id = Service_User::getCustomerId();

            $sql = "select TYPE from csd_country_mapping a where customer_id='{$customer_id}'";
            if($original_countryname){
            	$sql .= " and a.original_countryname='{$original_countryname}'";
            }
            if($country_code){
            	$sql .= " and a.country_code='{$country_code}'";
            }
//             echo $sql;exit;
            $count = Common_Common::fetchOne(preg_replace('/TYPE/', 'count(*)', $sql));
            
            $return['total'] = $count; 
            if ($count) {
           		$data = Common_Common::fetchAll(preg_replace('/TYPE/', '*', $sql));
                foreach($data as $k=>$v){
                	$v = array_merge($v, $country[$v['country_code']]);
                    $data[$k] = $v;
                }
//                 print_r($data);exit;
                $return['data'] = $data;
                $return['state'] = 1;
            }
            die(Zend_Json::encode($return));
            
        }
        
        $this->view->country = $country;
        echo Ec::renderTpl($this->tplDirectory . "list.tpl", 'layout');
    }
 
    /**
     * 删除
     */
    public function deleteAction(){
        $result = array(
            'ask' => 0,
            'message' => 'Success'
        );
        $cmp_id = $this->_request->getParam('cmp_id', '');
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
    		$customer_id = Service_User::getCustomerId();
        	$sql = "select * from csd_country_mapping where cmp_id='{$cmp_id}' and customer_id='{$customer_id}'";
        	$exist = Common_Common::fetchRow($sql);
        	if(!$exist){
        		throw new Exception('没有权限操作或者已经删除');
        	}
        	$sql = "delete from csd_country_mapping where cmp_id='{$cmp_id}' and customer_id='{$customer_id}'";
        	$db->query($sql);
            $db->commit();
        }catch(Exception $e){
            $db->rollback();
            $result['message'] = $e->getMessage();
        }
        die(Zend_Json::encode($result));
    }
    

    public function editAction() {
    	$return = array (
    			'state' => 0,
    			'message' => '',
    			'errorMessage' => array ()
    	);
    
    	if ($this->_request->isPost ()) {
    		$ct_code = $this->_request->getParam ( "ct_code" );
    		$ct_name = $this->_request->getParam ( "ct_name" );
    			
    		try {
    			// 大小写转换、去掉空格
    			$ct_name = strtoupper ( trim ( $ct_name ) );
    			
    			if (empty($ct_name)){
    				throw new Exception('自定义国家不能为空');
    			}
    			$ct = Service_IddCountry::getByField($ct_code,'country_code');
    			if (!$ct){
    				throw new Exception('国家不存在');
    			}
    			// 查询记录是否存在
    			$db = Common_Common::getAdapter();
    			
    			$customer_id = Service_User::getCustomerId();
    			
    			$sql = "SELECT * FROM csd_country_mapping WHERE original_countryname = '{$ct_name}' AND customer_id = '{$customer_id}';";
    			$exist = $db->fetchRow($sql);
    			if (empty ( $exist )) {
    				$table = 'csd_country_mapping';
    				$bind = array(
    						'customer_id' => $customer_id,
    						'original_countryname' => $ct_name,
    						'country_code' => $ct_code,
    						'cmp_createdate' => date("Y-m-d H:i:s")
    				);
    				$re = $db->insert($table, $bind);
    				if ($re){
    					$return ['message'] = '添加成功';
    					$return ['state'] = 1;
    				}
    			} else {
    				$return ['errorMessage'] = "该国家已存在";
    			}
    
    		} catch ( Exception $e ) {
    			$return ['errorMessage'] = $e->getMessage ();
    		}
    		die ( Zend_Json::encode ( $return ) );
    	}
    	$cts = Service_IddCountry::getAll ();
    	$this->view->country = $cts;
    	echo Ec::renderTpl ( $this->tplDirectory . "add_country.tpl", 'layout' );
    }
}