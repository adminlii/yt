<?php
class Pickup_PickupController extends Ec_Controller_Action
{

    public function preDispatch()
    {
        $this->tplDirectory = "pickup/views/pickup/";
        
    }

    public function listAction()
    {
    	
    	// TODO DB2
    	$db2 = Common_Common::getAdapterForDb2();
        if($this->_request->isPost()){
            //set_time_limit(0);
            $ac = $this->_request->getParam('ac', 'list');
            $page = $this->_request->getParam('page', 1);
            $pageSize = $this->_request->getParam('pageSize', 20);
            
            $page = $page ? $page : 1;
            $pageSize = $pageSize ? $pageSize : 20;
            
            $return = array(
                "state" => 0,
                "message" => "No Data"
            );
            $condition = $this->getRequest()->getParams();
            
            $customer_id = Service_User::getCustomerId();
            $customer_channelid = Service_User::getChannelid();
            $countSql = "
            		SELECT
							count(*)
						FROM
							pickup_order o            		
            		
            		";
            $rowSql = "
            		SELECT
							pickup_order_id 提货编号,
							status_type,
							(
								SELECT
									status_cnname
								FROM
									pickup_status s
								WHERE
									s.status_type = o.status_type
							) 申请状态,
							o.create_date 申请时间,
						CASE WHEN o.pickup_type_code = 'M'
						THEN
							(
								SELECT
									CONCAT(
										'提货员:',
										CAST(st_name AS CHAR),
										'(',
										CAST(st_code AS CHAR),
										') ',
										st_telephone
									)
								FROM
									hmr_staff h,
									hmr_staffattach sh
								WHERE
									h.st_id = sh.st_id
								AND h.st_id = o.driver_id
							)
						ELSE
							(
								CONCAT(
									'快递取件【',
									(
										SELECT
											pickup_type_cnname
										FROM
											pickup_type p
										WHERE
											p.pickup_type_code = o.pickup_type_code
									),
									'】 ',  
            						'<a href=\"javascript:;\" class=\"trackBtn\">',          						
									track_number,
            						'</a>'
								)
							)
						END 取件方式,
						(select pickup_server_note from pickup_server_time t where t.pickup_server_id = o.pickup_server_id) 期望时间,
						(select arrivalbatch_labelcode from bsn_arrivalbatch ba where ba.arrivalbatch_id = o.arrivalbatch_id) 到货总单,
						(select pickup_range_note from pickup_range a inner join user_address b on a.pickup_range_id=b.pickup_range_id where b.address_id = o.address_id) tip
						FROM
							pickup_order o
            
            		";
            $where = " WHERE 1=1 "; 
            // 到货日期
            $time_start = trim($this->_request->getParam('time_start', ''));
            $time_end = trim($this->_request->getParam('time_end', ''));
            $status_type = trim($this->_request->getParam('status_type', ''));
            
            if($customer_id){
            	$where .= " and customer_id='{$customer_id}' ";
            }
            $order_id = trim($this->_request->getParam('pickup_order_id', ''));
            if($order_id){
            	$where .= " and pickup_order_id='{$order_id}' ";
            }

            if($time_start){
            	$where .= " and create_date>='{$time_start}' ";
            }
            if($status_type){
            	$where .= " and status_type='{$status_type}' ";
            }
            
            if($time_end){
            	$time_end = $time_end.' 23:59:59';
            	$where .= " and create_date<='{$time_end}' ";
            }        
            
//              echo $where;exit;
            if($ac != 'export'){
            	$countSql .= $where;
            	$orderBy = ' ORDER BY pickup_order_id desc ';
            	$limit = " limit " . (($page - 1) * $pageSize) . "," . $pageSize;
            	
            	$rowSql .= $where . $orderBy . $limit;
//             	echo $rowSql;exit;
            	$count = $db2->fetchOne($countSql);
            	$return['total'] = $count; 
                if($count){
                    $rows = $db2->fetchAll($rowSql);
                    foreach($rows as $k => $v){
                    	foreach($v as $kk=>$vv){
                    		$vv = is_null($vv)?'':$vv;
                    		$v[$kk] = $vv;
                    	}                    	                      
                        $rows[$k] = $v;
                    } 
//                     var_dump($rows);exit;
                    $return['data'] = $rows;
                    $return['state'] = 1;
                    $return['ask'] = 1;
                    $return['message'] = "";
                }
                die(Zend_Json::encode($return));
            }else{
                //
            }
        }
        $this->view->start = date('Y-m-d', strtotime('-1day'));
        $this->view->end = date('Y-m-d');
        $sql = "select * from pickup_status;";
        $statusArr = $db2->fetchAll($sql);
        $this->view->statusArr = $statusArr;
        echo Ec::renderTpl($this->tplDirectory . "pickup_list.tpl", 'layout');
    }

    public function createAction()
    {
		if ($this->_request->isPost ()) {
			$return = array (
					'ask' => 0,
					'message' => 'Fail.', 
					'pickup_order_id'=>'',
					
			);
			$err = array();
			$tip = 'Fail.';
			// TODO DB2
    		$db = Common_Common::getAdapterForDb2();
			$db->beginTransaction();
			try {
				$params = $this->_request->getParams ();
				$address_id = $params ['address_id']?$params ['address_id']:'';

				//====================================数据校验 start
				$add = Service_UserAddress::getByField ( $address_id, 'address_id' );
				if(!$add){
					$err[] = Ec::Lang('必须有提货地址');
				}
				$detail = $this->getParam('detail',array());
				$detailArr = array();
				foreach($detail as $column=>$v){
					foreach($v as $kk=>$vv){
						$detailArr[$kk][$column] = $vv;
					}
				}
				
				// php hack
				if(! empty($detailArr)){
					array_unshift($detailArr, array());
					unset($detailArr[0]);
				}
				
				foreach($detailArr as $v){
				
					if(!preg_match('/^[0-9]+$/', $v['pieces'])){
						$err[] = Ec::Lang('票数需为整数');
					}
				
					if(!preg_match('/^[0-9]+(\.[0-9]+)?$/', $v['weight'])){
						$err[] = Ec::Lang('重量需为数字');
					}
				}
				if(!preg_match('/^[0-9]+$/', $params['bags'])){
					$err[] = Ec::Lang('总袋数需为整数');
				}
				
				if(!preg_match('/^[0-9]+$/', $params['pieces'])){
					$err[] = Ec::Lang('总票数需为整数');
				}
				
				if(!preg_match('/^[0-9]+(\.[0-9]+)?$/', $params['weight'])){
					$err[] = Ec::Lang('总重量需为数字');
				}
				
				
				if(!empty($err)){
					throw new Exception('数据不合法');
				}
				//====================================数据校验 end
				$add ['driver_id'] = '';
				$add ['pickup_type_code'] = '';
				if ($add && $add ['pickup_range_id']) {
					$sql = "select * from pickup_range where pickup_range_id='{$add['pickup_range_id']}';";
					$pickup_range = Common_Common::fetchRow ( $sql );
					if ($pickup_range && $pickup_range ['pickup_driver_id']) {
						$add ['driver_id'] = $pickup_range ['pickup_driver_id'];
					}
					if($pickup_range&&$pickup_range['pickup_og_id']){
						$sql = "select * from pickup_organization where pickup_og_id='{$pickup_range['pickup_og_id']}';";
						$pickup_organization = Common_Common::fetchRow($sql);
						if($pickup_organization){
							$add ['pickup_type_code'] = $pickup_organization['pickup_type_code'];
						}
					}
				}
				$row = array (
						'pickup_order_id' => $params ['pickup_order_id'],
						'tms_id' => Service_User::getTmsId (),
						'customer_id' => Service_User::getCustomerId (),
						'status_type' => 'C',
						'address_id' => $params ['address_id'],
						'address_name' => $add ['address_name'],
						'pickup_og_id' => $add ['pickup_og_id'],
						'bags' => $params ['bags'],
						'pieces' => $params ['pieces'],
						'weight' => $params ['weight'],
						'pickup_server_id' => $params ['pickup_server_id'],
						'pickup_type_code' => $add['pickup_type_code'],
						'driver_id' => $add ['driver_id']
				);

				
				$paramId = $row ['pickup_order_id'];
				if (! empty ( $row ['pickup_order_id'] )) {
					unset ( $row ['pickup_order_id'] );
				}
				$row = Common_Common::arrayNullToEmptyString ( $row );
				$format = 'Y-m-d H:i:s';
				$row ['modify_date'] = date ( $format );
				if (! empty ( $paramId )) {
					throw new Exception('提货单正在处理中,不允许修改');
					$new_pick_up = false;
					$order = Service_PickupOrder::getByField($paramId,'pickup_order_id');
					$status = strtoupper($order['status_type']);
					switch ($status){
						case 'C':
				
							break;
						case 'N':
				
							break;
						default:
							throw new Exception('提货状态不正确,不允许修改');
					}
					unset($row['status_type']);
					$result = Service_PickupOrder::update ( $row, $paramId,'pickup_order_id' );
					$pickup_order_id = $paramId;
					$tip = Ec::Lang('编辑提货单成功');
				} else {
					$new_pick_up = true;
					$row ['create_date'] = date ( $format );
					$result = Service_PickupOrder::add ( $row );
					$pickup_order_id = $result;
					$tip = Ec::Lang('新建提货单成功');
				}
				
				if($new_pick_up){
					//换号 start
					$tms_id = Service_User::getTmsId ();
					$sql = "select * from pickup_customer_config where tms_id='{$tms_id}' and pickup_sign='L';";
					$pickup_customer_config = $db->fetchRow($sql);
					if(!$pickup_customer_config){
						throw new Exception(Ec::Lang('pickup_customer_config未配置'));
					}
					$customer_document_type_id = $pickup_customer_config['customer_document_type_id'];
					// 号码池
					$sql = "select * from atd_regist_code_available a inner join atd_regist_document_head b on a.regist_code_id=b.regist_code_id and a.customer_document_type_id=b.customer_document_type_id  where a.customer_document_type_id='{$customer_document_type_id}' and b.regist_status='Y' limit 1;";
					$atd_regist_code_available = $db->fetchRow($sql);
					if(! $atd_regist_code_available){
						throw new Exception(Ec::Lang('服务商单号不足') );
					}
					// 从号码池中删除
					$sql = "delete from atd_regist_code_available where code_id='{$atd_regist_code_available['code_id']}'";
					$db->query($sql);
					// 插入已用号码池
					$used = array(
							'code_id' => $atd_regist_code_available['code_id'],
							'regist_code_id' => $atd_regist_code_available['regist_code_id'],
							'customer_document_type_id' => $atd_regist_code_available['customer_document_type_id'],
							'regist_code' => $atd_regist_code_available['regist_code'],
							'bs_id' => $pickup_order_id,
							'used_date' => date('Y-m-d H:i:s')
					);
					$db->insert('atd_regist_code_used', $used);					
					//换号 end
					//更新跟踪号
					$upRow = array('track_number'=>$atd_regist_code_available['regist_code']);
					Service_PickupOrder::update($upRow, $pickup_order_id,'pickup_order_id');
				}
				Service_PickupDetail::delete($pickup_order_id,'pickup_order_id');
				foreach($detailArr as $v){
					$detail = array (
							'product_code' => $v ['product_code'],
							'pieces' => $v ['pieces'],
							'weight' => $v ['weight'] 
					);
					$detail ['pickup_order_id'] = $pickup_order_id;
					Service_PickupDetail::add ( $detail );
				} 
				$zjsService = new Common_ZjsEdiService();
				$rs = $zjsService->receive($pickup_order_id);
				$return['rs'] = $rs;
				if(!$rs['ask']){
					throw new Exception($rs['message']);
				}
				
				$db->commit();
				$return ['ask'] = 1;
				$return ['message'] = $tip;
				$return['pickup_order_id'] = $pickup_order_id;
			} catch (Exception $e) {
				$db->rollBack();
				$return ['message'] = $e->getMessage();
			}
			$return['err'] = $err;
			die ( Zend_Json::encode ( $return ) );
		}
		$pickup_order_id = $this->getParam('pickup_order_id','');
		if($pickup_order_id){
			$order = Service_PickupOrder::getByField($pickup_order_id,'pickup_order_id');
			$this->view->order = $order;
			$con = array('pickup_order_id'=>$pickup_order_id);
			$detail = Service_PickupDetail::getByCondition($con);
			$this->view->detail = $detail;
		}
        $this->view->productKind = Process_ProductRule::getProductKind();
		echo Ec::renderTpl ( $this->tplDirectory . "pickup_create.tpl", 'layout' );
	}

	public function cancelAction(){
		$return = array (
				'ask' => 0,
				'message' => 'Fail..' 
		);
		$order_id = $this->getParam ( 'pickup_order_id', '' );
		try {
			if (empty ( $order_id )) {
				throw new Exception ( '参数错误，没有传入参数pickup_order_id' );
			}
			$order = Service_PickupOrder::getByField ( $order_id, 'pickup_order_id' );
			if (! $order) {
				throw new Exception ( '提货单不存在' );
			}
			if($order['customer_id']!=Service_User::getCustomerId()){
				throw new Exception('非法请求...');
			}
			$status_type = strtoupper ( $order ['status_type'] );
			switch ($status_type) {
				case 'C' :
					
					break;
				case 'N' :
					
					break;
				default :
					throw new Exception ( '不允许的操作' );
			}
			$zjsService = new Common_ZjsEdiService();
			$rs = $zjsService->cancel($order_id);
			$return['rs'] = $rs;
			if(!$rs['ask']){
				throw new Exception($rs['message']);
			}
			$updateRow = array (
					'status_type' => 'R' 
			);
			
			Service_PickupOrder::update($updateRow, $order_id, 'pickup_order_id');
			//日志？
			$return['ask'] = 1;
			$return['message'] = 'Success';
		} catch ( Exception $e ) {
			$return ['message'] = $e->getMessage ();
		}
		echo Zend_Json::encode ( $return );
	}
    public function getUserAddressAction(){
		$order_id = $this->getParam ( 'pickup_order_id', '' );
		$con = array (
				'customer_id' => Service_User::getCustomerId () 
		);
		
		$submiters = Service_UserAddress::getByCondition ( $con );
		foreach($submiters as $k=>$v){
			$street = explode('*#*', $v['street']);
			if(count($street)>1){
				$v['street'] = $street[0].'&nbsp;&nbsp;'.$street[1];				
			}
			$submiters [$k] = $v;
		}
		// print_r($submiters);exit;
		if ($order_id) {
			$order = Service_PickupOrder::getByField ( $order_id, 'pickup_order_id' );
// 			print_r($order);exit;
			if ($order) {
				foreach ( $submiters as $k => $v ) {
					if ($order ['address_id'] == $v ['address_id']) {
						$v ['is_default'] = 1;
					} else {
						$v ['is_default'] = 0;
					}
					
					$submiters [$k] = $v;
				}
			}
		}
		// 只有一条发件信息，默认选中
		if (count ( $submiters ) == 1) {
			$submiters [0] ['is_default'] = 1;
		}
// 		print_r($submiters);exit;
		$this->view->user_address_list = $submiters;
		echo $this->view->render ( $this->tplDirectory . "user_address.tpl" );
	}
	

	public function getOrgTimeAction(){
		$pickup_og_id = $this->getParam ( 'pickup_og_id', '' );
		$con = array (
				'pickup_og_id' => $pickup_og_id 
		);
		
		$data = Service_PickupServerTime::getByCondition ( $con );
		echo Zend_Json::encode ( $data );
	}
	
	public function printAction(){
		$pickup_order_id = $this->getParam ( 'pickup_order_id', '' );

		$pickupOrder = Service_PickupOrder::getByField($pickup_order_id,'pickup_order_id');
// 		print_r($pickupOrder);
		// TODO DB2
		$db2 = Common_Common::getAdapterForDb2();
		$pickup_og_id = $pickupOrder['pickup_og_id'];
		$sql = "select * from pickup_organization where pickup_og_id='{$pickup_og_id}';";
		$pickup_organization = $db2->fetchRow($sql);
		$serverTime = Service_PickupServerTime::getByField($pickupOrder['pickup_server_id'],'pickup_server_id');
// 		print_r($pickup_organization);
// 		exit;
		$orderArr = array();
		if($pickupOrder['bags']>1){
			for($i=1;$i<=$pickupOrder['bags'];$i++){
				$o = $pickupOrder;
				$o['track_number'] = $o['track_number']."-{$o['bags']}-{$i}"; 
				$orderArr[$i] = $o;
			}	
		}else{
			$orderArr[] = $pickupOrder;
		}
		
		
		
		$this->view->order = $pickupOrder;
		$this->view->orderArr = $orderArr;
		$this->view->og = $pickup_organization;
		$this->view->serverTime = $serverTime;

		$this->view->kehu = '';
		$this->view->tishi = $serverTime?$serverTime['pickup_server_note']:'';
		echo $this->view->render ( $this->tplDirectory . "pickup_label.tpl" );		
	}
	
	public function queryAction(){
		$pickup_id = $this->getParam ( 'pickup_order_id', '' );
		$zjsService = new Common_ZjsEdiService();
		$data = $zjsService->query($pickup_id);
		die(Zend_Json::encode($data));
	}
}