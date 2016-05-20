<?php
class Return_ReturnController extends Ec_Controller_Action
{
	
	public function preDispatch()
	{
		$this->tplDirectory="return/views/return/";	
		
	}
	public function listAction()
	{
		
		if ($this->_request->isPost()){
			$ac=$this->_request->getParam('ac','');
			$aa=$this->_request->getParam('aa','');
			$page = $this->_request->getParam('page', 1);
            $pageSize = $this->_request->getParam('pageSize', 20); 
			$country_code = $this->_request->getParam('country_code','');
			$shipper_hawbcode=trim($this->_request->getParam('shipper_hawbcode',''));
			$serve_hawbcode=trim($this->_request->getParam('return_hawbcode',''));
			$transferstatus_code=$this->_request->getParam('transferstatus_code','');
			$start_time=$this->_request->getParam('create_date_from','');
			$end_time=$this->_request->getParam('create_date_end','');
			
			$page = $page ? $page : 1;
			$pageSize = $pageSize ? $pageSize : 20;
				
			$return=array(
					"state"=>0,
					"message"=>'No Data'
			);
			
			$type="be.shipper_hawbcode,be.serve_hawbcode,bb.destination_countrycode,bb.transferstatus_code,bb.return_time,bb.return_note";
			$sql="SELECT _type_  FROM bsn_business bb LEFT JOIN bsn_expressexport be ON bb.bs_id=be.bs_id";
			$con=array();
			if ($country_code){
				$con[]="bb.destination_countrycode='{$country_code}'";
			}
			
			if ($shipper_hawbcode){
				$con[]="be.shipper_hawbcode='{$shipper_hawbcode}'";
			}
			
			if ($serve_hawbcode){
				$con[]= "be.serve_hawbcode='{$serve_hawbcode}'";
			}
			
			if ($transferstatus_code){
				$con[]="bb.transferstatus_code='{$transferstatus_code}'";
			}else{
				$con[]="bb.transferstatus_code!='S'";
			}
			
			if($start_time){
				$con[]="bb.return_time>='{$start_time}'";
			}
			
			if($end_time){
				$time_end = $time_end.' 23:59:59';
				$con[]="bb.return_time<='{$end_time}'";
			}
			
			foreach ($con as $k=>$v){
				if (isset($v) && !empty($v)){
					if ($k==0){
						$where.=' WHERE '.$v;
						continue;
					}
					$where .=' AND '.$v;
				}
			}
			$db2=Common_Common::getAdapterForDb2();
			$countSql=$sql.$where;
			$countSql=str_replace('_type_', 'count(*)', $countSql);
			$return['total']=$db2->fetchOne($countSql);
		
			$rowSql=$sql.$where.' ORDER BY bb.return_time DESC, bb.transferstatus_code DESC'." limit " . (($page - 1) * $pageSize) . "," . $pageSize;
			//echo $sql;die;
			$rowSql=str_replace('_type_', $type, $rowSql);
			$re=$db2->fetchAll($rowSql);
			$sql="SELECT country_code,country_cnname FROM idd_country;";
			$coArr=$db2->fetchAll($sql);
			$sql="SELECT transferstatus_code,transferstatus_cnname FROM atd_transferstatus;";
			$statusArr=$db2->fetchAll($sql);
			//转换国家、类型
			if ($re){
				foreach ($re as $key=>$value){
				    foreach ($statusArr as $kk =>$vv){
						 if ($value['transferstatus_code']==$vv['transferstatus_code']){
							$re[$key]['transferstatus_code']=$vv['transferstatus_cnname']; 
						
						}
						
					}
					
				 	foreach ($coArr as $ck =>$cv){
						if($value['destination_countrycode']==$cv['country_code']){
							$re[$key]['destination_countrycode']=$cv['country_cnname'];
						}
						
					} 
				}
				$return['state']=1;
				$return['data']=$re;
				//$return['total']=count($re);
				$return['message']='Success';
				
				if ($ac=='export'){
					header("Content-type: text/html; charset=utf-8");
					//检验数据
					set_time_limit(0);
					ini_set('memory_limit', '500M');
					// 当数据为空，直接返回
				
				    
					if(empty($re)) {
						header("Content-type: text/html; charset=utf-8");
						echo "No Data";
						exit;
					}
					//
					// 导出csv格式报表
					//
					$rows=array();
					foreach($re as $k=>$v){
						$rows[$k]['客户单号']=$v['shipper_hawbcode'];
						$rows[$k]['跟踪号']=$v['serve_hawbcode'];
						$rows[$k]['国家']=$v['destination_countrycode'];
						$rows[$k]['退件时间']=$v['return_time'];
						$rows[$k]['退件类型']=$v['transferstatus_code'];
						$rows[$k]['退件备注']=$v['return_note'];
					}
					$dateFileName = date('ymdHis');
					$fileName = "return_".$dateFileName;
					header("Content-Disposition: attachment; filename=" . $fileName . ".csv");
					header('Content-Type:APPLICATION/OCTET-STREAM');
					echo Common_Export::exportCsv($rows);
					exit;
				}
			}
			
			die(json_encode($return));
	  
	}
	//国家
	    $countryArr = Service_IddCountry::getAll();
		$this->view->countryArr = $countryArr;
		//状态
		$sql="SELECT transferstatus_code,transferstatus_cnname FROM atd_transferstatus;";
		$db2=Common_Common::getAdapterForDb2();
		$re=$db2->fetchAll($sql);
		if ($re){
			$returnStatus=array();
			foreach ($re as $k=>$v){
				if ($v['transferstatus_code']!='S'){
					$returnStatus[$v['transferstatus_code']]=$v['transferstatus_cnname'];
				}
			}
			$this->view->status=$returnStatus;
				
		}
		echo Ec::renderTpl($this->tplDirectory."return.tpl",'layout');
	}
	
	
	
}



/* if ($ac=='export'){
	header("Content-type: text/html; charset=utf-8");
	//检验数据
	set_time_limit(0);
	ini_set('memory_limit', '500M');
	// 当数据为空，直接返回


	if(empty($re)) {
		header("Content-type: text/html; charset=utf-8");
		echo "No Data";
		exit;
	}
	//
	// 导出csv格式报表
	//
	$rows=array();
	foreach($re as $k=>$v){
		$rows[$k]['客户单号']=$v['shipper_hawbcode'];
		$rows[$k]['跟踪号']=$v['serve_hawbcode'];
		$rows[$k]['国家']=$v['destination_countrycode'];
		$rows[$k]['退件时间']=$v['return_time'];
		$rows[$k]['退件类型']=$v['transferstatus_code'];
		$rows[$k]['退件备注']=$v['return_note'];
	}
	$dateFileName = date('ymdHis');
	$fileName = "return_".$dateFileName;
	header("Content-Disposition: attachment; filename=" . $fileName . ".csv");
	header('Content-Type:APPLICATION/OCTET-STREAM');
	echo Common_Export::exportCsv($rows);
	exit; */