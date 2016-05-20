<?php
class Fee_FeeController extends Ec_Controller_Action
{

    public function preDispatch()
    {
        $this->tplDirectory = "fee/views/fee/";
        $customer_id = Service_User::getCustomerId();
//         $sql = "select customer_id , fund_mode , csp_amount a from stm_customersurplus where customer_id ='{$customer_id}';";
		$sql = "
				select a.customer_id,a.sm_code , a.sc_time_balances from seq_surplusorder a ,
				(select sm_code sm_code, max(sc_surplusorderid) sc_surplusorderid  from  seq_surplusorder sur WHERE sur.customer_id = '{$customer_id}'  group by sm_code) b 
				where 
				a.sc_surplusorderid = b.sc_surplusorderid and a.customer_id='{$customer_id}'
				";
		
		// TODO DB2
		$db2 = Common_Common::getAdapterForDb2();
        $row1 = $db2->fetchAll($sql);
//         echo $sql;exit;
        $sql = "select customer_id , sum(cca_total) -sum(cca_reclaim) c, sum(cca_remain)-sum(cca_reclaim) d from cdt_customer_credit_assign where cca_enddate >=NOW() and cca_begindate <= NOW() and  customer_id ='{$customer_id}' group by customer_id ;";
		
        $row2 = $db2->fetchRow($sql);
        $tmp = array();
        foreach($row1 as $v){
            $tmp[$v['sm_code']] = $v;
        }
        // print_r($tmp);exit;
        $zonglan = array(
            'a' => $tmp['EE'] ? $tmp['EE']['sc_time_balances'] : 0,
            'b' => $tmp['DE'] ? $tmp['DE']['sc_time_balances'] : 0,
            'c' => $row2 ? $row2['c'] : 0,
            'd' => $row2 ? $row2['d'] : 0
        );
        $this->view->zonglan = $zonglan;
        // exit;
        $param = $this->_request->getParams();
        $this->view->action = $param['action'];
    }

    public function feeIncomeAction()
    {
    	$tms_id = Service_User::getTmsId();
    	$sql = "select * from web_newsconfig where tms_id='{$tms_id}' and news_type='AC';";
    	$row = Common_Common::fetchRow($sql);
    	$this->view->content = '';
    	if($row){
    		$this->view->content = $row['news_note']; 
    	}
    	echo Ec::renderTpl($this->tplDirectory . "fee_income.tpl", 'layout');
    }
    
    public function feeListAction()
    {
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
            
            // 到货日期
            $time_start = trim($this->_request->getParam('time_start', ''));
            $time_end = trim($this->_request->getParam('time_end', ''));
            
            $countSql = "                    
                        SELECT						
            			count(*)
						FROM
						stm_shippercurrent sc,
						stm_shippercurrentattach st						                                 
                    ";

            $rowSql = "
                        SELECT
						sc.sc_transactiondate 收款时间,
						( SELECT sm.sm_name FROM xtd_settlementmode sm WHERE sm.sm_code = sc.sm_code ) 结算模式,
						st.sc_transactionno 单据号,
						round(( SELECT sc_time_balances FROM seq_surplusorder s WHERE sc_surplusorderid = st.sc_surplusorderid ) - sc.sc_currencyrate * sc.sc_currentamount,2) 之前余额,
						round(sc.sc_currencyrate * sc.sc_currentamount,2) 收款金额,
						( SELECT round(sc_time_balances,2) FROM seq_surplusorder s WHERE sc_surplusorderid = st.sc_surplusorderid ) 收款后余额,
						st.sc_note 财务备注
						FROM
						stm_shippercurrent sc,
						stm_shippercurrentattach st						           
                    ";
            $where = " WHERE sc.sc_id = st.sc_id and sc.sc_businessincomesign = 'Y'";
            if($customer_id){
                $where .= " and sc.customer_id='{$customer_id}' ";
            }
            
            if($time_start){
                $where .= " and sc.sc_transactiondate>='{$time_start}' ";
            }
            
            if($time_end){
                $time_end = $time_end.' 23:59:59';
                $where .= " and sc.sc_transactiondate<='{$time_end}' ";
            }
            
            // TODO DB2
			$db2 = Common_Common::getAdapterForDb2();
            if($ac != 'export'){
                $countSql .= $where;
                $orderBy = ' ORDER BY sc.sc_transactiondate asc ';
                $limit = " limit " . (($page - 1) * $pageSize) . "," . $pageSize;
                
                $rowSql .= $where . $orderBy . $limit;
                // echo $rowSql;exit;
                // print_r($condition);exit;
                $orderBy = array();
                $count = $db2->fetchOne($countSql);
                $return['total'] = $count;
                if($count){
                    $rows = $db2->fetchAll($rowSql);
                    foreach($rows as $k => $v){
                    	$v = !isset($v)?'':$v;                        
                        $rows[$k] = $v;
                    }
//                     var_dump($rows);exit;
                    $return['data'] = $rows;
                    $return['state'] = 1;
                    $return['message'] = "";
                }
                die(Zend_Json::encode($return));
            }else{
                //
            }
        }
        $this->view->start = date('Y-m-d', strtotime('-1day'));
        $this->view->end = date('Y-m-d');
        echo Ec::renderTpl($this->tplDirectory . "fee_list.tpl", 'layout');
    }

    public function feeDetailListAction()
    {
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
            
            // 总单
            $arrivalbatch_labelcode = trim($this->_request->getParam('arrivalbatch_code', ''));
            // 参考号
            $shipper_hawbcode = trim($this->_request->getParam('shipper_hawbcode', ''));
            
            // 到货日期
            $time_start = trim($this->_request->getParam('time_start', ''));
            $time_end = trim($this->_request->getParam('time_end', ''));

            $countSql = "                    
                        SELECT
                        count(*)
                        FROM
                        bsn_business a,
                        bsn_expressexport b
                                                     
                    ";
            $rowSql = "
                        SELECT
						a.arrival_date 到货时间,
						b.shipper_hawbcode 客户单号,
						b.serve_hawbcode 跟踪单号,
						( SELECT product_cnname FROM csi_productkind pk WHERE pk.product_code = a.product_code ) 销售产品,
						( SELECT ca.cargo_type_cnname FROM atd_cargo_type ca WHERE a.checkin_cargotype = ca.cargo_type ) 货物类型,
						a.destination_countrycode 目的国家,
						a.shipper_chargeweight 计费重量,
						fun_get_income (a.bs_id, 'E1') 运费,
						fun_get_income (a.bs_id, 'H5') 燃油费,
						fun_get_income (a.bs_id, 'E2') 挂号费,
						round(fun_get_income (a.bs_id, 'A') - fun_get_income (a.bs_id, 'E1') - fun_get_income (a.bs_id, 'H5') - fun_get_income (a.bs_id, 'E2'),2) 其它杂费,
						fun_get_income (a.bs_id, 'A') 总费用
						FROM
						bsn_business a,
						bsn_expressexport b          
                    ";
            $where = ' WHERE 1=1';
            $where .= "
                    and a.bs_id = b.bs_id
                    and a.operation_status <> 'E'    
                    ";
            if($customer_id){
                $where .= " and a.customer_id='{$customer_id}' ";
            }
            
            // if($customer_channelid){
            // $where.=" and a.customer_channelid='{$customer_channelid}' ";
            // }
            // TODO DB2
            $db2 = Common_Common::getAdapterForDb2();
            if($shipper_hawbcode){
                $where .= " and (b.shipper_hawbcode='{$shipper_hawbcode}' or b.serve_hawbcode ='{$shipper_hawbcode}') ";
            }elseif($arrivalbatch_labelcode){
                $sql = "select * from bsn_arrivalbatch where arrivalbatch_labelcode='{$arrivalbatch_labelcode}';";
                $arrivalBatchRow = $db2->fetchRow($sql);
                if($arrivalBatchRow){
                    $arrivalbatch_id = $arrivalBatchRow['arrivalbatch_id'];
                    $where .= " and a.arrivalbatch_id='{$arrivalbatch_id}' ";
                }else{
                    // 绝逼不会存在的一个总单ID
                    $where .= " and a.arrivalbatch_id='101010101010101010101010101010101.*^' ";
                }
            }else{
                if($time_start){
                    $where .= " and a.arrival_date>='{$time_start}' ";
                }
                
                if($time_end){
                    $time_end = $time_end.' 23:59:59';
                    $where .= " and a.arrival_date<='{$time_end}' ";
                }
            }
            
            if($ac != 'export'){
                $countSql .= $where;
                //$return['summary'] = Common_Common::fetchRow($sumSql);
                $orderBy = ' order by a.arrival_date desc  ';
                $limit = " limit " . (($page - 1) * $pageSize) . "," . $pageSize;
                
                $rowSql .= $where . $orderBy . $limit;
                // echo $rowSql;exit;
                // print_r($condition);exit;
                $orderBy = array();
                $count = $db2->fetchOne($countSql);
                $return['total'] = $count;
                if($count){
                    $rows = $db2->fetchAll($rowSql);
                    foreach($rows as $k => $v){
                    	$v['运费'] = $v['运费']==0?'0':$v['运费'];
                    	$v['燃油费'] = $v['燃油费']==0?'0':$v['燃油费'];
                    	$v['挂号费'] = $v['挂号费']==0?'0':$v['挂号费'];
                    	$v['其它杂费'] = $v['其它杂费']==0?'0':$v['其它杂费'];
                    	$v['总费用'] = $v['总费用']==0?'0':$v['总费用'];
						$rows [$k] = $v;
					}
                    // print_r($rows);exit;
                    $return['data'] = $rows;
                    $return['state'] = 1;
                    $return['message'] = "";
                }
                die(Zend_Json::encode($return));
            }else{
            	
            	set_time_limit(0);
            	ini_set('memory_limit', '500M');
            	
            	$rowSql .= $where;
            	$rows = $db2->fetchAll($rowSql);
            	
            	// 当数据为空，直接返回
            	if(empty($rows)) {
            		header("Content-type: text/html; charset=utf-8");
            		echo "No Data";
            		exit;
            	}
            	
            	/*
            	 * 导出csv格式报表
            	*/
            	//设置生成的文档名称和path
            	$dateFileName = date('ymdHis');
            	$fileName = "Fee_Detail_".$dateFileName;
            	header("Content-Disposition: attachment; filename=" . $fileName . ".csv");
            	header('Content-Type:APPLICATION/OCTET-STREAM');
            	echo Common_Export::exportCsv($rows);
            	exit;
            }
        }
        // exit;
        $this->view->start = date('Y-m-d', strtotime('-1day'));
        $this->view->end = date('Y-m-d');
        echo Ec::renderTpl($this->tplDirectory . "fee_detail_list.tpl", 'layout');
    }

    public function feeFlowListAction()
    {
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
            
            // 参考号
            $sc_business_hawbcode = trim($this->_request->getParam('sc_business_hawbcode', ''));
            // 到货日期
            $time_start = trim($this->_request->getParam('time_start', ''));
            $time_end = trim($this->_request->getParam('time_end', ''));

            $sumSql = "
                    SELECT
                   sum(a.sc_payment) sum1,
                   sum(a.sc_income) sum2
                    FROM
                    seq_surplusorder a    
                    ";
            $countSql = "                    
                        SELECT
                        count(*)
                        FROM
                        seq_surplusorder a                                                          
                    ";
            $rowSql = "
                        SELECT
						sc_createdate 发生日期,
						(case when sm_code = 'EE' and surplus_type = 'M' and sc_income >0 then '账户充值' when sm_code = 'EE' and surplus_type = 'M' and sc_income = 0 then '账户退款' when sm_code = 'EE' and surplus_type = 'E' and sc_payment = 0 then '业务退费' when sm_code = 'EE' and surplus_type = 'E' and sc_payment > 0 then '业务扣费' when sm_code = 'DE' and surplus_type = 'M' and sc_payment =0 then '押金充值' when sm_code = 'DE' and surplus_type = 'M' and sc_payment > 0 then '押金退款' end) 结算模式,
						sc_business_hawbcode 业务单号,
						round(sc_payment,2) 账户支出,
						round(sc_income,2) 账户收入,
						round(sc_time_balances,2) 当时余额,
						sc_note 财务备注
						FROM
						seq_surplusorder a           
                    ";
            $where = ' WHERE 1=1';
            if($customer_id){
                $where .= " and a.customer_id='{$customer_id}' ";
            }
            
            // if($customer_channelid){
            // $where.=" and a.customer_channelid='{$customer_channelid}' ";
            // }
            
            if($sc_business_hawbcode){
                $where .= " and a.sc_business_hawbcode='{$sc_business_hawbcode}' ";
            }
            if($time_start){
                $where .= " and a.sc_createdate>='{$time_start}' ";
            }
            
            if($time_end){
                $time_end = $time_end.' 23:59:59';
                $where .= " and a.sc_createdate<='{$time_end}' ";
            }
            
            // TODO DB2
            $db2 = Common_Common::getAdapterForDb2();
            if($ac != 'export'){
                $countSql .= $where;
                $sumSql.=$where;
                //$return['summary'] = Common_Common::fetchRow($sumSql);
                $orderBy = ' ORDER BY sc_createdate DESC  ';
                $limit = " limit " . (($page - 1) * $pageSize) . "," . $pageSize;
                
                $rowSql .= $where . $orderBy . $limit;
                // echo $rowSql;exit;
                // print_r($condition);exit;
                $orderBy = array();
                $count = $db2->fetchOne($countSql);
                $return['total'] = $count;
                if($count){
                    $rows = $db2->fetchAll($rowSql);
                    // foreach($rows as $k => $v){
                    // $sql = "SELECT sm.sm_name FROM xtd_settlementmode sm WHERE sm.sm_code = '{$v['sm_code']}';";
                    // $xtd_settlementmode = Common_Common::fetchRow($sql);
                    // $rows[$k] = $v;
                    // }
                    // print_r($rows);exit;
                    $return['data'] = $rows;
                    $return['state'] = 1;
                    $return['message'] = "";
                }
                die(Zend_Json::encode($return));
            }else{
                //
            }
        }
        $this->view->start = date('Y-m-d', strtotime('-1day'));
        $this->view->end = date('Y-m-d');
        echo Ec::renderTpl($this->tplDirectory . "fee_flow_list.tpl", 'layout');
    }

    
    public function feeStatisticsListAction()
    {
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
    		 
    		
    		// 到货日期
    		$time_start = trim($this->_request->getParam('time_start', ''));
    		$time_end = trim($this->_request->getParam('time_end', ''));
    		 
    		$sumSql = "
                    SELECT
                   sum(a.sc_payment) sum1,
                   sum(a.sc_income) sum2
                    FROM
                    seq_surplusorder a
                    ";
    		$countSql = "
                        SELECT
                        count(*)
                        FROM
                        seq_surplusorder a
                    ";
    		/* $rowSql = "
                        SELECT
						sc_createdate 发生日期,
						(case when sm_code = 'EE' and surplus_type = 'M' and sc_income >0 then '账户充值' when sm_code = 'EE' and surplus_type = 'M' and sc_income = 0 then '账户退款' when sm_code = 'EE' and surplus_type = 'E' and sc_payment = 0 then '业务退费' when sm_code = 'EE' and surplus_type = 'E' and sc_payment > 0 then '业务扣费' when sm_code = 'DE' and surplus_type = 'M' and sc_payment =0 then '押金充值' when sm_code = 'DE' and surplus_type = 'M' and sc_payment > 0 then '押金退款' end) 结算模式,
						sc_business_hawbcode 业务单号,
						round(sc_payment,2) 账户支出,
						round(sc_income,2) 账户收入,
						round(sc_time_balances,2) 当时余额,
						sc_note 财务备注
						FROM
						seq_surplusorder a
                    "; */
    		
    		$time_end = $time_end.' 23:59:59';
    		$rowSql="
    				select b.*,round((select s.sc_time_balances from seq_surplusorder s where s.sc_surplusorderid = b.id),2) 当时余额
                    from (
                         SELECT
	                         max(sc_surplusorderid) id,
	                         DATE_FORMAT(sc_createdate,'%Y-%m-%d') 发生日期,
		                     round(sum(case WHEN surplus_type = 'M' THEN sc_surplus_value else 0 end),2) 账户收入,
	                         round(sum(case WHEN surplus_type != 'M' THEN -sc_surplus_value else 0 end),2) 账户支出
                         FROM
	                         seq_surplusorder a
                         WHERE
	                         a.customer_id ='{$customer_id}'
	                         AND
	                         a.sc_createdate>='{$time_start}'
                             AND
                             a.sc_createdate<='{$time_end}'
                         group by DATE_FORMAT(sc_createdate,'%Y-%m-%d')) b 
               
    				";
    
    		
    		// TODO DB2
    		$db2 = Common_Common::getAdapterForDb2();
    		if($ac != 'export'){
    
    			$orderBy = ' ORDER BY 发生日期  DESC  ';
    			
    			$limit = " limit " . (($page - 1) * $pageSize) . "," . $pageSize;
    			$rows = $db2->fetchAll($rowSql);
    			$count=count($rows);
    			$return['total'] = $count;
    			
    			$rowSql =$rowSql.$orderBy.$limit;
    			
    			$rows = $db2->fetchAll($rowSql);
    			

    			if($count){	
    				$return['data'] = $rows;
    				$return['state'] = 1;
    				$return['message'] = "";
    			}
    			die(Zend_Json::encode($return));
    		}else{
    			//
    		}
    	}
    	
    	$this->view->start = date('Y-m-d', strtotime('-1day'));
    	$this->view->end = date('Y-m-d');
    	echo Ec::renderTpl($this->tplDirectory . "fee_statistics_list.tpl", 'layout');
    }
    
    public function feeInfoListAction()
    {
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
            
            // 到货日期
            $time_start = trim($this->_request->getParam('time_start', ''));
            $time_end = trim($this->_request->getParam('time_end', ''));

            $sumSql = "
                    SELECT
                    sum() sum1,
                    sum() sum2                    
                    FROM
                    stm_shippercurrent a inner join 
                    stm_shippercurrentattach b
                    on
                    a.sc_id = b.sc_id    
                    ";
            $countSql = "                    
                        SELECT
                        count(*)
                        FROM
                        stm_shippercurrent a inner join 
                        stm_shippercurrentattach b
                        on
                        a.sc_id = b.sc_id                                    
                    ";
            $rowSql = "
                        SELECT
                        a.*,
                        b.*
                        FROM
                        stm_shippercurrent a inner join 
                        stm_shippercurrentattach b
                        on
                        a.sc_id = b.sc_id              
                    ";
            $where = ' WHERE 1=1';
            if($customer_id){
                $where .= " and a.customer_id='{$customer_id}' ";
            }
            
            // if($customer_channelid){
            // $where.=" and a.customer_channelid='{$customer_channelid}' ";
            // }
            if($time_start){
                $where .= " and a.sc_transactiondate>='{$time_start}' ";
            }
            
            if($time_end){
                $time_end = $time_end.' 23:59:59';
                $where .= " and a.sc_transactiondate<='{$time_end}' ";
            }
            
            // TODO DB2
            $db2 = Common_Common::getAdapterForDb2();
            
            if($ac != 'export'){
                $countSql .= $where;
                $orderBy = ' ORDER BY a.sc_transactiondate asc ';
                $limit = " limit " . (($page - 1) * $pageSize) . "," . $pageSize;
                
                $rowSql .= $where . $orderBy . $limit;
                // echo $rowSql;exit;
                // print_r($condition);exit;
                $orderBy = array();
                $count = $db2->fetchOne($countSql);
                $return['total'] = $count;
                if($count){
                    $rows = $db2->fetchAll($rowSql);
                    // foreach($rows as $k => $v){
                    // }
                    // print_r($rows);exit;
                    $return['data'] = $rows;
                    $return['state'] = 1;
                    $return['message'] = "";
                }
                die(Zend_Json::encode($return));
            }else{
                //
            }
        }
        $this->view->start = date('Y-m-d', strtotime('-1day'));
        $this->view->end = date('Y-m-d');
        echo Ec::renderTpl($this->tplDirectory . "fee_info_list.tpl", 'layout');
    }
    

    public function feeUnpaidListAction()
    {
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
            
            $product_code = trim($this->_request->getParam('product_code', ''));
            // 到货日期
            $time_start = trim($this->_request->getParam('time_start', ''));
            $time_end = trim($this->_request->getParam('time_end', ''));
            
            $countSql = "                    
                        SELECT
                        count(*)
                        FROM
                        bsn_business a,
                        bsn_expressexport b                                  
                    ";
            $rowSql = "
                        SELECT
                        a.bs_id,
                        a.arrival_date,
                        b.shipper_hawbcode,
                        b.serve_hawbcode,
                        ( SELECT product_cnname FROM csi_productkind pk WHERE pk.product_code = a.product_code ) product_cnname,
                        a.destination_countrycode,
                        a.shipper_chargeweight,
                        fun_get_income (a.bs_id, 'A') A,
                        fun_get_income (a.bs_id, 'A') - ( SELECT ifnull(sum(ibr_amount),0) FROM bil_incomebalancerecord i WHERE i.IC_ID IN ( SELECT ic_id FROM bil_income c WHERE c.bs_id = a.bs_id ) AND IPF_CODE = 'S'  ) B
                        FROM
                        bsn_business a,
                        bsn_expressexport b
                                  
                    ";
            $where = ' WHERE 1=1';
            $where .= "
                        and a.bs_id = b.bs_id
                        AND a.operation_status <> 'E'
                        AND EXISTS ( SELECT 1 FROM bil_business s WHERE a.bs_id = s.bs_id AND s.writeoff_sign = 'N' )
                    
                    ";
            if($customer_id){
                $where .= " and a.customer_id='{$customer_id}' ";
            }
            
            if($product_code){
                $where .= " and a.product_code='{$product_code}' ";
            }
            if($time_start){
                $where .= " and a.arrival_date>='{$time_start}' ";
            }
            
            if($time_end){
                $time_end = $time_end.' 23:59:59';
                $where .= " and a.arrival_date<='{$time_end}' ";
            }
            
            // TODO DB2
            $db2 = Common_Common::getAdapterForDb2();
            
            if($ac != 'export'){
                $countSql .= $where;
                $orderBy = ' ORDER BY a.arrival_date DESC  ';
                $limit = " limit " . (($page - 1) * $pageSize) . "," . $pageSize;
                
                $rowSql .= $where . $orderBy . $limit;
                // echo $rowSql;exit;
                // print_r($condition);exit;
                $orderBy = array();
                $count = $db2->fetchOne($countSql);
                $return['total'] = $count;
                if($count){
                    $rows = $db2->fetchAll($rowSql);
                    // foreach($rows as $k => $v){
                    // }
                    // print_r($rows);exit;
                    $return['data'] = $rows;
                    $return['state'] = 1;
                    $return['message'] = "";
                }
                die(Zend_Json::encode($return));
            }else{
                //
            }
        }
        
        $this->view->productKind = Process_ProductRule::getProductKind();
        $this->view->start = date('Y-m-d', strtotime('-1day'));
        $this->view->end = date('Y-m-d');
        echo Ec::renderTpl($this->tplDirectory . "fee_unpaid_list.tpl", 'layout');
    }

    public function feeUnpaidDetailAction()
    {
        $bs_id = $this->_request->getParam('bs_id', '');
        $tms_id = Service_User::getTmsId();
//         $bs_id = '182';
        $sql = "
                SELECT 
                c.serve_hawbcode,
                c.shipper_hawbcode,
                ( SELECT fk_name FROM xtd_customer_feekind fk WHERE fk_code = a.fk_code ) fk_name,
                sum(TRUNCATE ( a.ic_currencyrate * a.ic_amount, 2 ) )ic_amount, 
                sum(( SELECT ifnull(sum(ibr_amount),0) FROM bil_incomebalancerecord i WHERE i.IC_ID = a.ic_id AND IPF_CODE = 'S' ) -  TRUNCATE ( a.ic_currencyrate * a.ic_amount, 2 ) ) ic_amounts
                FROM
                bil_income a,
                bsn_business b,
                bsn_expressexport c
                WHERE
                a.bs_id = b.bs_id
                AND b.bs_id = c.bs_id
                AND b.tms_id = '{$tms_id}' and c.bs_id ='{$bs_id}'                
                group by c.serve_hawbcode,c.shipper_hawbcode,  a.fk_code
               ";
//         echo $sql;exit;

        // TODO DB2
        $db2 = Common_Common::getAdapterForDb2();
        $data = $db2->fetchAll($sql);
//         print_r($data);exit;
        $this->view->data = $data;
        echo $this->view->render($this->tplDirectory . "fee_unpaid_detail.tpl");
    }

    public function feeTongjiListAction()
    {
        if($this->_request->isPost()){
            //set_time_limit(0);
            $ac = $this->_request->getParam('ac', 'list');
            $page = $this->_request->getParam('page', 1);
            $pageSize = $this->_request->getParam('pageSize', 20);
            
            $page = $page ? $page : 1;
            $pageSize = $pageSize ? $pageSize : 20;
            $page = 1;
            $pageSize = 99999999;
            $return = array(
                "state" => 0,
                "message" => "No Data"
            );
            $condition = $this->getRequest()->getParams();
            
            $customer_id = Service_User::getCustomerId();
            $customer_channelid = Service_User::getChannelid();
            
            // 到货日期
            $time_start = trim($this->_request->getParam('time_start', ''));
            $time_end = trim($this->_request->getParam('time_end', ''));
            $sumSql = "
                    SELECT
                        sum(fun_get_income (a.bs_id, 'A')) sum
                        FROM
                        bsn_business a,
                        bsn_expressexport b                    
                    ";
            $rowSql = "
                        SELECT
                        DATE_FORMAT(a.arrival_date, '%Y-%m-%d') arrival_date,
                        ( SELECT product_cnname FROM csi_productkind pk WHERE pk.product_code = a.product_code ) product_cnname,
                        ( SELECT t.cargo_type_cnname FROM atd_cargo_type t WHERE t.cargo_type = a.checkin_cargotype ) cargo_type_cnname,
                        count(1) c,
                        sum(a.shipper_chargeweight) s,
                        sum(fun_get_income (a.bs_id, 'E1')) E1,
                        sum(fun_get_income (a.bs_id, 'H5')) H5,
                        sum(fun_get_income (a.bs_id, 'E2')) E2,
                        sum(fun_get_income (a.bs_id, 'A')) A
                        FROM
                        bsn_business a,
                        bsn_expressexport b
                                     
                    ";
            $where = "
                    WHERE
                        a.bs_id = b.bs_id
                        AND a.operation_status <> 'E'
                    
                    ";
            if($customer_id){
                $where.=" and a.customer_id='{$customer_id}' ";
            }
            if($customer_channelid){
                // $where.=" and a.customer_channelid='{$customer_channelid}' ";
            }
            if($time_start){
                $where .= " and a.arrival_date>='{$time_start}' ";
            }
            
            if($time_end){
                $time_end = $time_end.' 23:59:59';
                $where .= " and a.arrival_date<='{$time_end}' ";
            }
            
            // TODO DB2
            $db2 = Common_Common::getAdapterForDb2();
            
            if($ac != 'export'){
                $groupBy = " GROUP BY DATE_FORMAT(a.arrival_date, '%Y-%m-%d'), a.product_code, a.checkin_cargotype ";
                $orderBy = ' ORDER BY a.arrival_date DESC ';
                $limit = " limit " . (($page - 1) * $pageSize) . "," . $pageSize;

                $sumSql.=$where;
                //$return['summary'] = Common_Common::fetchRow($sumSql);
                
                $rowSql .= $where . $groupBy . $orderBy . $limit;
                // echo $rowSql;exit;
                $rows = $db2->fetchAll($rowSql);
                //
                // print_r($rows);exit;
                if($rows){
                    $return['state'] = 1;
                }
                $return['data'] = $rows;
                $return['message'] = "";
                die(Zend_Json::encode($return));
            }else{
                //
            }
        }
        $this->view->start = date('Y-m-d', strtotime('-1day'));
        $this->view->end = date('Y-m-d');
        echo Ec::renderTpl($this->tplDirectory . "fee_tongji_list.tpl", 'layout');
    }
    
    public function billQueryAction(){
    	if ($this->_request->isPost()){
    		$return=array(
    			'ask'=>0,
    			'message'=>'No Data',
    		);
    		$page = $page ? $page : 1;
    		$pageSize = $pageSize ? $pageSize : 20;
    		$start_time=$this->_request->getParam('time_start','');
    		$end_time=$this->_request->getParam('time_end','');
    		$sql="SELECT sb_billdate,sb_labelcode,sb_amount,sb_note FROM bil_shipperbill WHERE sb_billdate>='{$start_time}' AND sb_billdate<='{$end_time}' AND upload_state='Y';";
    		$db=Common_Common::getAdapterForDb2();
    		$re=$db->fetchAll($sql);
    		if ($re){
    		    $return['state']=1;
    	        $return['data']=$re;
    		}
    		
    		die(Zend_Json::encode($return));
    	}
    	echo Ec::renderTpl($this->tplDirectory.'bill_query.tpl','layout');
    }
    
    
}