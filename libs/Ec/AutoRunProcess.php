<?php
/**
 * User: Max
 * Date: 2014-11-28 16:25:40
 */
// set_error_handler('error_function',E_WARNING);
class Ec_AutoRunProcess
{

    protected $_user_account = '';

    protected $_company_code = '';

    protected $_run_control_row = array();

    public function setRunControl($runControlRow)
    {
        $this->_run_control_row = $runControlRow;
        $this->_user_account = $runControlRow['user_account'];
        $this->_company_code = $runControlRow['company_code'];
    }

    /**
     * 生成运行控制
     *
     * @param unknown_type $row            
     * @throws Exception
     */
    public function getLoadDataControl()
    {
        $row = $this->_run_control_row;
        $now = date("Y-m-d H:i:s", strtotime('-15 minutes'));
        $row = $this->_run_control_row;
        $time = date("H:i:s");
        $startTime = $row['start_time'];
        $endTime = $row['end_time'];
        if($time < $startTime || $time > $endTime){ // 不在时间段之内，抛出异常,程序终止
            throw new Exception('不在时间段之内,不允许运行');
        }
        
        $platform = $row['platform'];
        
        $condition = array(
            'company_code' => $row['company_code'],
            'user_account' => $row['user_account'],
            'app_type' => $row['run_app'],
            'platform' => $row['platform'],
            'status' => '1'
        );
        $last_run_time = strtotime($row['last_run_time']) + $row['run_interval_minute'] * 60;
        $exist = Service_LoadDataControl::getByCondition($condition);
        $return = false;
        // 如果没有同店铺API在运行，则插入记录开始运行
        if($exist){ // 如果存在，则返回错误
            $exist = array_pop($exist); // 取得数据
            if(strtotime($exist['run_start_time']) + 3600 < strtotime(date('Y-m-d H:i:s'))){ // 运行超过1个小时，重新运行
                return $exist;
            }else{
                throw new Exception("该任务已经有一个线程在运行-->\n" .print_r($exist, true));
            }
        }else{
            $now = date('Y-m-d H:i:s');
            $now_time = strtotime($now);
            
            $minites = 15; // 当前时间的前15分钟之内的数据不下载
            if($last_run_time >= $now_time - $minites * 60){
                throw new Exception('运行时间小于当前时间 -' . $minites . ' minites');
            }
            $load_start_time = date('Y-m-d\TH:i:s', strtotime($row['last_run_time']) - 15 * 60 - 8 * 3600); // 开始时间
            $load_end_time = date('Y-m-d\TH:i:s', $last_run_time - 8 * 3600); // 结束时间
            
            $load_data_control = array(
                'platform' => $platform,
                'company_code' => $row['company_code'],
                'app_type' => $row['run_app'],
                'load_start_time' => $load_start_time,
                'load_end_time' => $load_end_time,
                'run_start_time' => date("Y-m-d H:i:s"),
                'user_account' => $row['user_account'],
                'status' => '1',
                'currt_run_count' => '1'
            );
            if(! $loadId = Service_LoadDataControl::add($load_data_control)){
                throw new Exception('Inner Error' . __CLASS__ . __METHOD__);
            }
            $load_data_control['ldc_id'] = $loadId;
            return $load_data_control;
        }
    }
}