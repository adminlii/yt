<?php
class Order_AsnPrintController extends Ec_Controller_Action {
	public function preDispatch() {
		$this->tplDirectory = "order/views/asn-print/";
		$this->_db=Common_Common::getAdapter();
		$this->_db2=Common_Common::getAdapterForDb2();
	}
	public function indexAction() {
		$order_id_arr = $this->getParam('orderId',array());
		
		
		try {
			if(empty($order_id_arr)){
				throw new Exception('请传入订单号');
			}

			//运输方式
			$pk_sql="SELECT  product_code,CONCAT(product_code,' | ',product_cnname) name FROM csi_productkind;";
			$pks=$this->_db2->fetchAll($pk_sql);
			$pk=array();
			foreach ($pks as $pkK=>$pkV){
				$pk[$pkV['product_code']]=$pkV['name'];
			}
			
			//国家
			$cts=Service_IddCountry::getAll();
			foreach ($cts as $ck=>$cv){
				$country[$cv['country_code']]=$cv['country_cnname'];
			}
			
			//附加服务
			$ser_sql="SELECT extra_service_kind,extra_service_cnname FROM atd_extraservice_kind;";
			$services=$this->_db2->fetchAll($ser_sql);
			$service=array();
			foreach ($services as $sk=>$sv){
				$service[$sv['extra_service_kind']]=$sv['extra_service_cnname'];
			}
			
			$con = array();
			$mailCargoTypes = Service_AtdMailCargoType::getByCondition($con);
			$_mailCargoTypes=array();
			if(!empty($mailCargoTypes)){
				foreach ($mailCargoTypes as $v){
					$_mailCargoTypes[$v['mail_cargo_code']]=$v['mail_cargo_enname'];
				}
			}
			//交货清单
			$data=array();
			foreach ($order_id_arr as $k => $v){
				$es_sql="SELECT extra_servicecode FROM csd_extraservice WHERE order_id='{$v}';";
				$result=$this->_db->fetchAll($es_sql);
			   	
			   	$mark='';
				if ($result){
					foreach ($result as $rk=>$rv){
						$mark .= $service[$rv['extra_servicecode']].',';
					}
					$mark=trim($mark,',');
				}
				
				$order_sql="SELECT
							product_code,
							shipper_hawbcode 预报单号,
							server_hawbcode 转单号,
							order_pieces 件数,
							country_code 目的地,
							order_weight 重量,
							mail_cargo_type 物品类型 
							FROM csd_order 
							WHERE order_id='{$v}';";
				$orderInfo  = Service_CsdOrder::getByCondition(array('order_id'=>$v));
				$data[$k]=$orderInfo[0];
				
				$data[$k]['销售产品']=$pk[$data[$k]['product_code']];
				switch ($data[$k]['mail_cargo_type']){
					case '1':
						$data[$k]['物品类型']=$_mailCargoTypes[1];
						;break;
					case '2':
						$data[$k]['物品类型']=$_mailCargoTypes[2];
						;break;
					case '3':
						$data[$k]['物品类型']=in_array($data[$k]['product_code'], array("G_DHL","TNT"))?'文件':$_mailCargoTypes[3];
						;break;
					case '4':
						$data[$k]['物品类型']=in_array($data[$k]['product_code'], array("G_DHL","TNT"))?'物品':$_mailCargoTypes[4];
						;break;
				}
				$data[$k]['备注']=$mark;
				$data[$k]['目的地']=$country[$data[$k]['country_code']];
			}
			
			$pageSize = 22;
			$this->view->pageSize = $pageSize;
			$dataArr = array_chunk($data, $pageSize);
			// 			print_r($data);exit;
			$this->view->dataArr = $dataArr;
			$this->view->data = $data;
			
			
			
			
			/* $sql = "
					SELECT
						shipper_hawbcode 预报单号,
						server_hawbcode 转单号,
						(
						SELECT
						CONCAT(
						pk.product_code,
						' | ',
						pk.product_cnname
						)
						FROM
						csi_productkind pk
						WHERE
						pk.product_code = co.product_code
						) 销售产品,
						co.order_pieces 件数,
						co.country_code 目的地,
						co.order_weight 重量,
						(
						SELECT
						GROUP_CONCAT(k.extra_service_cnname)
						FROM
						csd_extraservice e,
						atd_extraservice_kind k
						WHERE
						e.extra_servicecode = k.extra_service_kind and e.order_id = co.order_id
						) 备注
						FROM
						csd_order co
					
						where co.order_id in ({$order_id_arr});
					";
			
			$data = $this->_db2->fetchAll($sql); */
			
		} catch (Exception $e) {
			header("Content-type: text/html; charset=utf-8");
			echo $e->getMessage();exit;
		}
		$customer_id = Service_User::getCustomerId();
		$customer_channelid = Service_User::getChannelid();
		$sql = "select * from csi_shipperchannel where customer_channelid = '{$customer_channelid}' ;";
// 		$sql = "select * from csi_shipperchannel;";
		$csi_shipperchannel = $this->_db2->fetchRow($sql);
		if($csi_shipperchannel){
			$this->view->customer_channel = $csi_shipperchannel['customer_channelcode'];
			
		}
		$this->view->csi_customer = Service_User::getCustomer();
		$this->view->customer_code = Service_User::getCustomerCode();
		echo Ec::renderTpl ( $this->tplDirectory . "asn-print.tpl", 'layout' );
	}
	
}