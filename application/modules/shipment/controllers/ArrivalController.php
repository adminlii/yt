<?php
class Shipment_ArrivalController extends Ec_Controller_Action
{

    public function preDispatch()
    {
        $this->tplDirectory = "shipment/views/arrival/";
    }

    public function listAction()
    {
        // 目的国 家
        $countryArr = Common_DataCache::getCountry();
        $customer_id = Service_User::getCustomerId();
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
            
            $customer_channelid = Service_User::getChannelid();
            // 子账号
            $customer_channelid_new = trim($this->_request->getParam('customer_channelid', ''));
            // 总单
            $arrivalbatch_code = trim($this->_request->getParam('arrivalbatch_code', ''));
            // 到货日期
            $arrival_start = trim($this->_request->getParam('arrival_start', ''));
            $arrival_end = trim($this->_request->getParam('arrival_end', ''));
            
            $countSql = "
                       SELECT
                        count(*)
                        FROM
                        bsn_arrivalbatch a
                    ";
            $rowSql = "
                     SELECT
                        a.*
                        FROM
                        bsn_arrivalbatch a
                    ";
            $where = ' WHERE 1=1';
            $where .= " and a.batch_status != 'E' ";
            if($customer_id){
                $where.=" and a.customer_id='{$customer_id}' ";
            }
            
            if($customer_channelid){
                $where.=" and a.customer_channelid='{$customer_channelid}' ";
            }
            
            
            if($arrivalbatch_code){
                $where .= " and a.arrivalbatch_labelcode='{$arrivalbatch_code}' ";
            }else{
                if($customer_channelid_new){
                    $where .= " and a.customer_channelid='{$customer_channelid_new}' ";
                }
                if($arrival_start){
                    $where .= " and a.arrival_date>='{$arrival_start}' ";
                }
                if($arrival_end){
                    $where .= " and a.arrival_date<='{$arrival_end}' ";
                } 
            }
            
            // TODO DB2
            $db2 = Common_Common::getAdapterForDb2();
            
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
                    $rows = $db2->fetchAll($rowSql);
                    foreach($rows as $k => $v){
                        $sql = "select * from csi_shipperchannel where customer_channelid='{$v['customer_channelid']}';";
                        $csi_shipperchannel = $db2->fetchRow($sql);
                        
                        $sql = "select count(*) from bsn_business where arrivalbatch_id='{$v['arrivalbatch_id']}';";
                        $bsn_business_count = $db2->fetchOne($sql);
                        
                        $v['csi_shipperchannel'] = $csi_shipperchannel;
                        $v['bsn_business_count'] = $bsn_business_count;
                        
                        $rows[$k] = $v;
                    }
                    // print_r($rows);exit;
                    $return['data'] = $rows;
                    $return['state'] = 1;
                    $return['message'] = "";
                }
                
                die(Zend_Json::encode($return));
            }else{
                //导出
                $orderBy = ' order by a.arrival_date desc ';
                $limit = " limit " . (($page - 1) * $pageSize) . "," . $pageSize;
                
                $rowSql .= $where . $orderBy;
                $rows = $db2->fetchAll($rowSql);
                foreach($rows as $k => $v){
                    $sql = "select * from csi_shipperchannel where customer_channelid='{$v['customer_channelid']}';";
                    $csi_shipperchannel = $db2->fetchRow($sql);
                    
                    $sql = "select count(*) from bsn_business where arrivalbatch_id='{$v['arrivalbatch_id']}';";
                    $bsn_business_count = $db2->fetchOne($sql);
                    
                    $v['csi_shipperchannel'] = $csi_shipperchannel;
                    $v['bsn_business_count'] = $bsn_business_count;
                    
                    $rows[$k] = $v;
                }
                $dataList = array();
                $fileName = Service_ExcelExport::exportToFile($dataList, '到货清单');
                Common_Common::downloadFile($fileName);
                exit();
            }
        }
        $this->view->countryArr = $countryArr;
        $this->view->productKind = Common_DataCache::getProductKind();
        
        // TODO DB2
        $db2 = Common_Common::getAdapterForDb2();
        $sql = "select * from csi_shipperchannel where customer_id='{$customer_id}'";
        $channal_id = Service_User::getChannelid();
        if($channal_id){
            $sql.=" and customer_channelid='{$channal_id}'";
        }
//         echo $sql;exit;
        $this->view->csi_shipperchannel = $db2->fetchAll($sql);
        // print_r(Common_Common::fetchAll($sql));exit;

        $this->view->start = date('Y-m-d',strtotime('-1day'));
        $this->view->end = date('Y-m-d');
        echo Ec::renderTpl($this->tplDirectory . "arrival_list.tpl", 'layout');
    }
    
    public function exportDetailAction(){
        // 总单
        $arrivalbatch_code = trim($this->_request->getParam('arrivalbatch_code', ''));
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->forward('list','business','shipment',array('arrivalbatch_code'=>$arrivalbatch_code,'ac'=>'export'));
    }
}