<?php
class Shipment_BusinessController extends Ec_Controller_Action
{

    public function preDispatch()
    {
        $this->tplDirectory = "shipment/views/business/";
    }

    public function listAction()
    {
        // 目的国 家
        $countryArr = Common_DataCache::getCountry();
        if($this->_request->isPost()){
            set_time_limit(0);
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
            // 运单
            $shipper_hawbcode = trim($this->_request->getParam('shipper_hawbcode', ''));
            // 总单
            $arrivalbatch_code = trim($this->_request->getParam('arrivalbatch_code', ''));
//             echo $arrivalbatch_code;exit;
            // 到货日期
            $arrival_start = trim($this->_request->getParam('arrival_start', ''));
            $arrival_end = trim($this->_request->getParam('arrival_end', ''));
            
            // 目的国家
            $country_code = trim($this->getParam('country_code', ''));
            // 运输方式
            $product_code = trim($this->getParam('product_code', ''));
            
            // TODO DB2
            $db2 = Common_Common::getAdapterForDb2();
            
            $countSql = "                    
                        SELECT
                            count(*) 
                            FROM
                            bsn_business a INNER JOIN 
                            bsn_expressexport b   
                            on a.bs_id = b.bs_id                                      
                    ";
            $rowSql = "
                     SELECT
                        b.*,
                        a.*
                        FROM
                        bsn_business a INNER JOIN 
                        bsn_expressexport b   
                        on a.bs_id = b.bs_id               
                    ";
            $where = ' WHERE 1=1';
            $where .= " and a.operation_status != 'E' ";
            if($customer_id){
                $where.=" and a.customer_id='{$customer_id}' ";
            }
            
            if($customer_channelid){
                $where.=" and a.customer_channelid='{$customer_channelid}' ";
            }
            if($shipper_hawbcode){//单号
                $where .= " and( b.shipper_hawbcode='{$shipper_hawbcode}' or b.serve_hawbcode='{$shipper_hawbcode}') ";
            }elseif($arrivalbatch_code){//到货总单
                $sql = "select * from bsn_arrivalbatch where arrivalbatch_labelcode='{$arrivalbatch_code}';";
                $arrivalBatchRow = $db2->fetchRow($sql);
                if($arrivalBatchRow){
                    $arrivalbatch_id = $arrivalBatchRow['arrivalbatch_id'];
                    $where .= " and a.arrivalbatch_id='{$arrivalbatch_id}' ";
                }else{
                    // 绝逼不会存在的一个总单ID
                    $where .= " and a.arrivalbatch_id='101010101010101010101010101010101.*^' ";
                }
                // print_r($where);exit;
            }else{//其他条件                 
                if($arrival_start){
                    $where .= " and a.arrival_date>='{$arrival_start}' ";
                }
                if($arrival_end){
                    $where .= " and a.arrival_date<='{$arrival_end}' ";
                }
                if($country_code){
                    $where .= " and a.destination_countrycode='{$country_code}' ";
                }
                if($product_code){
                    $where .= " and a.product_code='{$product_code}' ";
                }
            }
            
            if($ac != 'export'){
                $countSql .= $where;
                $orderBy = ' order by a.arrival_date desc ';
                $limit = " limit " . (($page - 1) * $pageSize) . "," . $pageSize;
                
                $rowSql .= $where . $orderBy . $limit;
                // echo $countSql;exit;
                // print_r($condition);exit;
                $orderBy = array();
                $count = $db2->fetchOne($countSql);
                $return['total'] = $count;
                if($count){
                	
                	// 销售产品
                	$csi_productkind_arr = Common_DataCache::getProductKind();
                	// 货物类型
                	$atd_cargo_type_arr = Common_DataCache::getCargoType();
                	
                    $rows = $db2->fetchAll($rowSql);
                    foreach($rows as $k => $v){
                
                        $sql = "select * from tak_trackingbusiness where sys_bs_id='{$v['bs_id']}';";
                        $tak_trackingbusiness = $db2->fetchRow($sql);
                
                        $v['product_cnname'] = $csi_productkind_arr[$v['product_code']] ? $csi_productkind_arr[$v['product_code']]['product_cnname'] : '';
                        $v['cargo_type_cnname'] = $atd_cargo_type_arr[$v['mail_cargo_type']] ? $atd_cargo_type_arr[$v['mail_cargo_type']]['cargo_type_cnname'] : '';
                        $v['tak_trackingbusiness'] = $tak_trackingbusiness;
                
                        $v['country'] = $countryArr[$v['destination_countrycode']] ? $countryArr[$v['destination_countrycode']] : null;
                        $rows[$k] = $v;
                    }
                    // print_r($rows);exit;
                    $return['data'] = $rows;
                    $return['state'] = 1;
                    $return['message'] = "";
                }
                die(Zend_Json::encode($return));
            }else{
//                 print_r($this->_request->getParams());exit;
                //导出
                $orderBy = ' order by a.arrival_date desc ';
                $limit = " limit " . (($page - 1) * $pageSize) . "," . $pageSize;
                
                $rowSql .= $where . $orderBy ;
                // echo $countSql;exit;
                // print_r($condition);exit;
                $orderBy = array();
                $rows =  $db2->fetchAll($rowSql);
                $dataList = array();
                
                // 销售产品
                $csi_productkind_arr = Common_DataCache::getProductKind();
                // 货物类型
                $atd_cargo_type_arr = Common_DataCache::getCargoType();
                
                foreach($rows as $k => $v){
                    
                    $sql = "select * from tak_trackingbusiness where sys_bs_id='{$v['bs_id']}';";
                    $tak_trackingbusiness = $db2->fetchRow($sql);
                    
                    $v['productKind'] = $productKind;
                    $v['atd_cargo_type'] = $atd_cargo_type;
                    $v['tak_trackingbusiness'] = $tak_trackingbusiness;
                    
                    $v['country'] = $countryArr[$v['destination_countrycode']] ? $countryArr[$v['destination_countrycode']] : null;
                    $rows[$k] = $v;
                    $arr = array(
                        '到货日期' => $v['arrival_date'],
                        '参考号' => $v['shipper_hawbcode'],
                        '跟踪号' => $v['serve_hawbcode'],
                        '运输方式' => $csi_productkind_arr[$v['product_code']] ? $csi_productkind_arr[$v['product_code']]['product_cnname'] : '',
                        '目的地' => $v['country']?$v['country']['country_cnname']:$v['destination_countrycode'],
                        '实重' => $v['checkin_grossweight'],
                        '体积重' => $v['checkin_volumeweight'],
                        '计费重' => $v['shipper_chargeweight'],                            
                        '件数' => $v['shipper_pieces'],
                        '类型' => $atd_cargo_type_arr[$v['mail_cargo_type']] ? $atd_cargo_type_arr[$v['mail_cargo_type']]['cargo_type_cnname'] : '',
                        '状态' => $v['tak_trackingbusiness']?($v['tak_trackingbusiness']['new_track_date'].' / '.$v['tak_trackingbusiness']['new_track_location'].' / '.$v['tak_trackingbusiness']['new_track_comment']):'',
                       
                    );
                    $dataList[] = $arr;
                } 
                if(empty($dataList)){
                    $dataList[] = array('无数据'=>'');
                }                
                $fileName = Service_ExcelExport::exportToFile($dataList, '运单'.$arrivalbatch_code);
                Common_Common::downloadFile($fileName);
                exit();
            }
            
        }
        $this->view->countryArr = $countryArr;
        $this->view->productKind = Process_ProductRule::getProductKind();
        print_r(Process_ProductRule::getProductKind());
        $this->view->start = date('Y-m-d',strtotime('-1day'));
        $this->view->end = date('Y-m-d');
        echo Ec::renderTpl($this->tplDirectory . "business_list.tpl", 'layout');
    }

    public function tongjiAction()
    {}
}