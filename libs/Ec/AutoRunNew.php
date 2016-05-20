<?php
class Ec_AutoRunNew
{

    protected $_user_account = '';

    protected $_company_code = '';

    public function setUserAccount($user_account)
    {
        $this->_user_account = $user_account;
    }

    public function setCompanyCode($company_code)
    {
        $this->_company_code = $company_code;
    }

    public function init($run_app)
    {
        $con = array(
            // 'last_run_time_more' => $now,
            'run_app' => $run_app,
            'user_account' => $this->_user_account,
            'company_code' => $this->_company_code,
            'status' => '1'
        );
        $rows = Service_RunControl::getByCondition($con);
        foreach($rows as $key => $row){
            $comp = $row['company_code'];
            $acc = $row['user_account'];
            try {
                $process = new Ec_AutoRunProcess();
//                 $process->setUserAccount($acc);
//                 $process->setCompanyCode($comp);
                $process->setRunControl($row);

                $loadId = $this->_genLoadDataControl();

                $last_run_time = strtotime($exist['load_start_time']) + $row['run_interval_minute'] * 60;
                $return = $this->$row['run_app']($exist['ldc_id']); // 执行方法
                
                $method = $row['run_app'];
                if(! method_exists($this, $method)){ // 判断方法是否实现
                    throw new Exception('方法未被继承实现' . __CLASS__ . __METHOD__);
                }
                $return = $this->$method($loadId); // 执行方法,方法名称存储于run_control表中的run_app字段
                
                if((is_array($return) && $return['ask'] == '1') || $return === true){ // 提示返回成功，更新最后运行时间
                    $last_run_time_now = date('Y-m-d H:i:s', $last_run_time);
                    $updateRow = array(
                            'last_run_time' => $last_run_time_now
                    );
                    if(! Service_RunControl::update($updateRow, $row['run_id'], 'run_id')){
                        throw new Exception('Inner Error');
                    }
                }
                
            } catch (Exception $e) {
                Common_ApiProcess::log("公司代码:[{$comp}],账号:[{$comp}]发生错误，错误原因:".$e->getMessage());
            }
            
        }
    }

    /**
     * 更新运行结果
     * @param unknown_type $loadId
     * @param unknown_type $finsh
     * @param unknown_type $allCount
     */
    public function countLoad($loadId, $finsh, $allCount)
    { // 公共方法
        $rows = Service_LoadDataControl::getByField($loadId, 'ldc_id');
        $load_data_control = array(
            'run_end_time' => date("Y-m-d H:i:s"),
            'ldc_id' => $loadId,
            'status' => $finsh,
            'all_order_count' => $allCount
        );
        Service_LoadDataControl::update($load_data_control, $loadId, 'ldc_id');
    }

    /**
     * 获取运行控制
     * @param unknown_type $loadId
     * @return mixed
     */
    public function getLoadParam($loadId)
    { // 公共方法
        $rows = Service_LoadDataControl::getByField($loadId, 'ldc_id');
        return $rows;
    }

    /**
     *
     * @param string $app_type
     *            调用方法
     * @param string $account
     *            运行账号 为空表示所有账号
     */
    public function run($app_type)
    { // 运行主方法
        $this->init($app_type);
        return $this;
    }

    /**
     * ebay时间转本地时间
     *
     * @param unknown_type $ebayTime            
     * @return string
     */
    public static function getLocalTime($ebayTime)
    {
        return date("Y-m-d H:i:s", strtotime($ebayTime));
    }

    /**
     * 本地时间转ebay时间
     *
     * @param unknown_type $localTime            
     * @return string
     */
    public static function getEbayTime($localTime)
    {
        return date("Y-m-d H:i:s", strtotime($localTime) - 8 * 3600);
    }

    /**
     * 日志输出
     *
     * @param unknown_type $str            
     */
    public static function sapiDebug($str)
    {
        if(PHP_SAPI == 'cli'){
            echo "[" . date('Y-m-d H:i:s') . "]" . $str . "\n";
        }
    }
}