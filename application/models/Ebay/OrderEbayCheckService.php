<?php
/**
 * 功能目的:
 * 验证ebay订单号是否存在,如果不存在,将订单号存入table_cron_load_ebay_order表,系统单独下载该订单
 * @author Administrator
 *
 */
class Ebay_OrderEbayCheckService extends Ec_AutoRun
{

    private $_user_account = '';

    private $_company_code = '';

    public function checkEbayOrder($loadId)
    {
        // 得到当前同步订单的关键参数
        $param = $this->getLoadParam($loadId);
        // Ec::showError(var_export($param,1),'params'.time());
        $userAccount = $param["user_account"];
        $companyCode = $param["company_code"];
        $this->_user_account = $userAccount;
        $this->_company_code = $companyCode;
        
        $start = $param["load_start_time"];
        $end = $param["load_end_time"];
        $count = $param["currt_run_count"];
        try{
            $orderCount = $this->callEbay($start, $end);
            $this->countLoad($loadId, 2, $orderCount); // 运行结束
            return array(
                'ask' => '1',
                'message' => "eBay Time : " . $start . " ~ " . $end . ',' . $userAccount . ' order count ' . $orderCount
            );
        }catch(Exception $e){
            Common_ApiProcess::log("下载订单异常:" . $e->getMessage());
            $this->countLoad($loadId, 3, 0);
            Ec::showError("账号：" . $userAccount . '发生错误，eBay时间：' . $start . ' To ' . $end . ',错误原因：' . $e->getMessage(), 'runOrder_Fail_');
            return array(
                'ask' => '0',
                'message' => $e->getMessage()
            );
        }
    }

    public function setUserAccount($userAccount){
        $this->_user_account = $userAccount;
    }

    public function setCompanyCode($companyCode){
        $this->_company_code = $companyCode;
    }
    /**
     * 请求ebay
     *
     * @param unknown_type $userAccount            
     * @param unknown_type $start            
     * @param unknown_type $end            
     * @param unknown_type $orderIds            
     * @throws Exception
     * @return number
     */
    public function callEbay($start, $end)
    {
        $userAccount = $this->_user_account;
        if(empty($userAccount)){
            throw new Exception(' userAccount Empty');
        }
        $companyCode = $this->_company_code;
        if(empty($companyCode)){
            throw new Exception(' companyCode Empty');
        }
        $token = Ebay_EbayLib::getUserToken($this->_user_account,$this->_company_code);
        if(! $token){
            throw new Exception($userAccount . ' UserToken Ivalid');
        }
        Common_ApiProcess::log("开始下载订单:[{$userAccount}][{$start}~{$end}]");
        // throw new Exception("1\n");
        $TotalNumberOfEntries = 0;
        // 第一次运行EBAY API 并获取总条数，
        $page = 0;
        $orderCount = 0;
        while(true){
            $page ++;
            $data = Ebay_EbayLib::getEbayOrdersId($token, $start, $end, $page);
            if(! isset($data['GetOrdersResponse']['Ack'])){
                $data['GetOrdersResponse']['Ack'] = 'Failure';
            }
            if($data['GetOrdersResponse']['Ack'] == 'Failure'){
                throw new Exception(print_r($data['GetOrdersResponse'], true));
            }
            $response = $data['GetOrdersResponse'];
            
            $total = $response['PaginationResult']['TotalNumberOfEntries'];
            Common_ApiProcess::log("共{$total}条记录，当前第{$page}页");
            
            if($TotalNumberOfEntries == 0){
                $TotalNumberOfEntries = $total;
            }elseif($TotalNumberOfEntries != $total){
                Common_ApiProcess::log("userAccount:{$userAccount},companyCode:{$companyCode},{$start}~{$end},load订单发生了交叉异常。。。。");
                $page = 0;
                $TotalNumberOfEntries = $total;
                continue;
            }
            
            $response = $response['OrderArray'];
            if(isset($response['Order'])){
                $response = $response['Order'];
                $dataOA = array();
                if(isset($response['OrderID'])){ // 只有一个订单
                    $dataOA[] = $response;
                }else{
                    $dataOA = $response;
                }
                $orderCount += count($dataOA); // 统计数量
                foreach($dataOA as $k => $v){
                    $this->_checkOrderIDExist($v['OrderID']);
                }
            }
            if($data['GetOrdersResponse']['HasMoreOrders'] != 'true'){ // 不成功或者没有下一页
                                                                       // 程序终止,注意：返回的HasMoreItems
                                                                       // 是字符串类型
                break;
            }
        }
        return $orderCount;
    }

    /**
     * 验证订单是否已经下载,避免漏单
     * 
     * @param unknown_type $order_sn            
     */
    private function _checkOrderIDExist($order_sn)
    {
        try{
            $db = Common_Common::getAdapter();
            $exist = Service_EbayOrder::getByField($order_sn, 'order_sn');
            if(! $exist){
                // 加入订单更新任务列表
                $table = Ebay_EbayServiceCommon::table_cron_load_ebay_order();
                $arr = array(
                    'order_sn' => $order_sn,
                    'user_account' => $this->_user_account,
                    'company_code' => $this->_company_code
                );
                $db->insert($table, $arr);
                Ec::showError($order_sn,'_checkOrderIDExist_');
                
                
            }
        }catch(Exception $e){
            //
        }
    }
}