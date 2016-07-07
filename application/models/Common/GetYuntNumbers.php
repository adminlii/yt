<?php

/**
 * 中邮单号生成规则
 * YT + YY(两位年) + XXX(三位一年中的第几天) + 客户编码 + X(一位分公司编码, 默认为2) + XXXXX(五位流水号) 
 */
class GetYuntNumbers
{
    private $applicationCode, $prefix,$customerCode, $time, $day, $ruleStr, $branchEncoding = 2;

    public function __construct($applicationCode = '', $customerCode = '')
    {
        $this->prefix = 'EMS';
        $this->time = date('y');
        //$this->customerCode = $customerCode;
        $this->customerCode = "";
        $this->day = date('z');
        $this->branchEncoding = 2;
        $this->applicationCode = $applicationCode;
    }

    public function getCode()
    {
        $sequence = $this->getSequence();
        $this->customerCode = "";
        return strtoupper($this->prefix . $this->time . $this->day . $this->customerCode . $this->branchEncoding . $sequence);
    }

    private function getCnt()
    {
        /* $condition = array(
            'application_code' => $this->applicationCode,
        ); */
        $condition = array(
        		'application_code' => 'CURRENT_ORDER_SYS_COUNT',
        );
        if (empty($this->customerCode)) {
            $this->customerCode = Service_User::getCustomerId();
        }
        //$condition['customer_code'] = $this->customerCode;
        $condition['customer_code'] = 'SYS';
        $application = Service_Application::getByCondition($condition, '*');
        $date = date('Ymd');
        $time = date('Y-m-d H:i:s');
        if (empty($application)) {
            $row = array(
                'application_code' => $this->applicationCode,
                'current_number' => $date . '-0',
                'app_add_time' => $time,
                'customer_code' => $this->customerCode,
            );
            $row['application_id'] = Service_Application::add($row);
            $application = $row;
        }else{
            $application = $application[0];
        }
        
        $this->ruleStr = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        if (!empty($application['current_number']) && isset($application['current_number'])) {
            $arr = explode('-', $application['current_number']);
            if ($date == $arr[0]) {
                $count = $arr[1] + 1;
            } else {
                $count = 1;
            }
        } else {
            $count = 1;
        }
        $update = array('current_number' => $date . '-' . $count, 'app_update_time' => $time);
        Service_Application::update($update, $application['application_id']);
       
        return $count;
    }

    private function getSequence()
    {
        return sprintf('%05s', $this->getCnt());
    }

    public function timeSlice()
    {
        return date('ymd');
    }


}

class Common_GetYuntNumbers
{
    /**
     * @param string $applicationCode 应用代码
     * @param string $customerCode 客户
     * @return string
     */
    public static function getCode($applicationCode = '', $customerCode = '')
    {
        $obj = new GetYuntNumbers($applicationCode, $customerCode);
        return $obj->getCode();
    }
}