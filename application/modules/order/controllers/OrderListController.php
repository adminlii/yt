<?php
class Order_OrderListController extends Ec_Controller_Action
{

    public function preDispatch()
    {
        $this->tplDirectory = "order/views/order/";
        $this->serviceClass = new Service_CsdOrder();
    }

    public function listAction()
    {
        $statusArr = Service_OrderProcess::getOrderStatus();
        
        $this->view->statusArrJson = Zend_Json::encode($statusArr);
        $this->view->statusArr = $statusArr;

        $countrys = Common_DataCache::getCountry();
//         print_r($countrys);exit;
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
            $condition['order_status'] = $this->getParam('status','');
            $condition['customer_id'] = Service_User::getCustomerId();
    		$condition['customer_channelid'] = Service_User::getChannelid();

            $shipper_hawbcode = $this->_request->getParam('shipper_hawbcode','');
            $shipper_hawbcode = preg_replace('/[^a-zA-Z0-9_\-]/', ' ', $shipper_hawbcode);
            $shipper_hawbcode = preg_replace('/\s+/', ' ', $shipper_hawbcode);
            $shipper_hawbcode = trim($shipper_hawbcode);
            
            $code_type = $this->_request->getParam('code_type','shipper');
            if($shipper_hawbcode){
                $shipper_hawbcode = explode(' ', $shipper_hawbcode);
                if(count($shipper_hawbcode)==1){
                	switch ($code_type) {
                		case 'shipper':
                			$condition['shipper_hawbcode_like'] = $shipper_hawbcode[0];
                			break;
                		case 'refer':
                			$condition['refer_hawbcode_like'] = $shipper_hawbcode[0];
                			break;
                		case 'server':
                			$condition['server_hawbcode_like'] = $shipper_hawbcode[0];
                			break;
                		default:
                			break;
                	}
                }else{
                	switch ($code_type) {
                		case 'shipper':
                			$condition['shipper_hawbcode_arr'] = $shipper_hawbcode;
                			break;
                		case 'refer':
                			$condition['refer_hawbcode_arr'] = $shipper_hawbcode;
                			break;
                		case 'server':
                			$condition['server_hawbcode_arr'] = $shipper_hawbcode;
                			break;
                		default:
                			break;
                	}
                }
            }else{
                $shipper_hawbcode = array();
            }
            unset($condition['shipper_hawbcode']);
            
            if($condition['print_sign'] == '1') {
            	$condition['print_date_unequals'] = '0000-00-00 00:00:00';
            } else if($condition['print_sign'] == '0') {
            	$condition['print_date'] = '0000-00-00 00:00:00';
            }
            
            unset($condition['print_sign']);
             
            $condition['order_status'] = strtoupper($condition['order_status']);
            if($condition['order_status']!='P'){
                unset($condition['hold_sign']);
            }
            
            $orderBy = $this->_request->getParam('orderBy','create_date desc');
            
            // 当查询全部订单时，不取废弃订单
            if(empty($condition['order_status'])) {
            	$condition['order_status_unequals'] = 'E';
            }
			//FBA列表
			if($condition['order_status']!="F"){
				$count = Service_CsdOrder::getByCondition($condition, 'count(*)');
				$return['total'] = $count;
				if($count){
					 
					$orderCreateCodeArr = Common_Type::orderCreateCode('auto');
				
					// 获取所有产品数据
					$productKind = Service_CsiProductkind::getAll();
					$productKindArr = array();
					foreach($productKind as $k => $row) {
						$productKindArr[$row['product_code']] = $row;
					}
				
					// 所有额外服务
					$extraserviceKind = Common_DataCache::getAtdExtraserviceKindAll();
				
					$rows = Service_CsdOrder::getByCondition($condition, "*", $pageSize, $page, $orderBy);
					foreach($rows as $k => $v){
						if(empty($v['checkin_date'])||strtotime($v['checkin_date'])<strtotime('2000-01-01')){
							$v['checkin_date'] = '0000-00-00 00:00:00';
						}
						if($v['checkin_date']=='0000-00-00 00:00:00'){
							$v['checkin_date'] = '';
						}
				
						if($v['print_date']=='0000-00-00 00:00:00'||strtotime($v['print_date'])<strtotime('2000-01-01')){
							$v['print_date'] = '';
						}
						//打印标记
						$v['print_sign'] = 'N';
						if($v['print_date']){
							$v['print_sign'] = 'Y';
						}
						$v['country_code'] = strtoupper($v['country_code']);
						$v['country_name'] = isset($countrys[$v['country_code']])?$countrys[$v['country_code']]['country_cnname']:$v['country_code'];
				
						//$v['order_create_code'] = isset($orderCreateCodeArr[$v['order_create_code']])?$orderCreateCodeArr[$v['order_create_code']]:'';
						$v['order_status_title'] = isset($statusArr[$v['order_status']])?$statusArr[$v['order_status']]['name']:$v['order_status'];
						//                     $shipper_consignee = Service_CsdShipperconsignee::getByField($v['order_id'],'order_id');
						//                     $v['shipper_consignee'] = $shipper_consignee;
						//来源标记
						$v['order_create_code'] = strtoupper($v['order_create_code']);
				
						// 产品
						$product = $productKindArr[$v['product_code']];
						//                     $product = Service_CsiProductkind::getByField($v['product_code'],'product_code');
						//                     $v['product_code'] = $product?$v['product_code'].'['.$product['product_cnname'].']':$v['product_code'];
						$v['product_code'] = $product?$product['product_cnname']:$v['product_code'];
				
						// 保险报关标志
						$InsuranceSign = 'N';
						$CustomerSign = 'N';
						$csd_extraservice = Service_CsdExtraservice::getByCondition(array('order_id' => $v['order_id']));
						if(!empty($csd_extraservice)) {
							foreach($csd_extraservice as $extraservice) {
								if(isset($extraserviceKind[$extraservice['extra_servicecode']])) {
									continue;
								}
				
								$extraserviceKindRow = $extraserviceKind[$extraservice['extra_servicecode']];
								// 保险
								if($extraserviceKindRow['extra_service_group'] == 'C0') {
									$InsuranceSign = 'Y';
									break;
								}
								// 报关标记
								if($extraserviceKindRow['extra_service_group'] == 'A0') {
									$InsuranceSign = 'Y';
									break;
								}
							}
						}
						// 保险标记
						$v['InsuranceSign'] = $InsuranceSign;
						// 报关标记
						$v['CustomerSign'] = $CustomerSign;
						//扣件标记
						$v['hold_sign'] = strtoupper($v['hold_sign']);
						//偏远标记
						$v['oda_sign'] = strtoupper($v['oda_sign']);
				
						//取出错误信息
						/*$orderWrongMsg = Service_OrderProcessing::getByField($v['order_id'], 'order_id');
						 $v["orderWrongMsg"] = $orderWrongMsg["ops_note"];*/
						$condition = array(
								"order_id" => $v['order_id'],
						);
						$orderWrongMsg = Service_OrderProcessing::getByCondition(
								$condition,
								array("order_processing.ops_note", "order_processing.ops_status"),
								20,
								1,
								array('order_processing.order_id'));
						foreach($orderWrongMsg as $wk => $wv){
							$v["orderWrongMsg"] = $wv['ops_note'];
						}
				
				
						$rows[$k] = $v;
					}
				}
			}else{
				$count = Service_CsdOrderfba::getByCondition($condition, 'count(*)');
				$return['total'] = $count;
				if($count){
					// 获取所有产品数据
					$productKind = Service_CsiProductkind::getAll();
					$productKindArr = array();
					foreach($productKind as $k => $row) {
						$productKindArr[$row['product_code']] = $row;
					}
					$rows = Service_CsdOrderfba::getByCondition($condition, "*", $pageSize, $page, $orderBy);
					foreach($rows as $k => $v){
					
						if($v['print_date']=='0000-00-00 00:00:00'||strtotime($v['print_date'])<strtotime('2000-01-01')){
							$v['print_date'] = '';
						}
						//打印标记
						$v['print_sign'] = 'N';
						if($v['print_date']){
							$v['print_sign'] = 'Y';
						}
						$v['country_code'] = strtoupper($v['country_code']);
						$v['country_name'] = isset($countrys[$v['country_code']])?$countrys[$v['country_code']]['country_cnname']:$v['country_code'];
					
						$v['order_status_title'] = isset($statusArr[$v['order_status']])?$statusArr[$v['order_status']]['name']:$v['order_status'];
						//来源标记
						$v['order_create_code'] = 'W';
						// 产品
						$v['product_code'] = "FBA";
						$v['country_name'] = isset($countrys[$v['consignee_countrycode']])?$countrys[$v['consignee_countrycode']]['country_cnname']:$v['consignee_countrycode'];
						
						$rows[$k] = $v;
					}
				}		
			}
            
//                 print_r($rows);exit;
				if(empty($rows)){
					$return['state'] = 0;
					$return['message'] = "没有找到记录，请调整搜索条件";
				}else{
					$return['data'] = $rows;
					$return['state'] = 1;
					$return['message'] = "";
				}
                
            // 是否重新统计
            $reTongji = new Zend_Session_Namespace('reTongji');
            
            $return['reTongji'] = $reTongji->reTongji;
            die(Zend_Json::encode($return));
        }


        // 目的国 家
        $countryArr = Service_IddCountry::getByCondition(null, '*', 0, 9999, "country_code");
        
        $this->view->countryArr = $countryArr;

        $this->view->productKind = Process_ProductRule::getProductKind();
        $this->view->yesOrNO = Common_Status::YesOrNo();
        
        $this->view->jsfile = "order/js/order/order_list_list_b2c.js";
        $this->view->tplfile = $this->tplDirectory . "order_list_list_b2c.tpl";
        echo Ec::renderTpl($this->tplDirectory . "order_list_list_common.tpl", 'layout');
    }

    /**
     * 订单分类
     */
    public function getOrderTagAction()
    {
        $condition = array(
            'company_code' => Common_Company::getCompanyCode()
        );
        $condition['customer_channelid'] = Service_User::getChannelid();
        $configRow = Service_OrderTag::getByCondition($condition);
        
        $userTags = array();
        foreach($configRow as $k => $v){
            $userTags[$v['order_status']][] = array(
                'k' => $v['ot_id'],
                'text' => $v['tag_name'],
                'ot_id' => $v['ot_id']
            );
        }
        $this->view->user_tag = $userTags;
        // print_r($userTags);exit;
    }

    /**
     * 各个状态订单数量统计
     */
    public function getStatisticsAction()
    {
        $platform = $this->getRequest()->getParam('platform', '');
        
        $condition = array(); 

        $condition['customer_id'] = Service_User::getCustomerId();
        $condition['customer_channelid'] = Service_User::getChannelid();

        $tongji = Service_CsdOrder::getByCondition($condition, 'order_status,count(*) count', 0, 0, '', 'order_status');
        //加上FBA的统计
        $fba    = Service_CsdOrderfba::getByCondition($condition, 'order_status,count(*) count', 0, 0, '', 'order_status');
        $tongji=array_merge($tongji,$fba);
        //
        $reTongji = new Zend_Session_Namespace('reTongji');
        $reTongji->reTongji = 0;
        die(Zend_Json::encode($tongji));
    }
}