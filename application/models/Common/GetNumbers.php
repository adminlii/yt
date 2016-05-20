<?php
class GetNumbers
{
    private $applicationCode, $customerCode, $time, $prefix, $ruleStr, $separator;

    public function __construct($applicationCode = '', $customerCode = '', $prefix = '', $separator ='')
    {
        $this->time = $this->timeSlice();
        $this->customerCode = $customerCode;
        $this->applicationCode = $applicationCode;
        $this->prefix = $prefix;
        $this->separator = $separator;
    }

    public function getCode()
    {
        $sequence = $this->getSequence();
        return strtoupper($this->prefix . $this->ruleStr . $this->separator . $this->time . $this->separator . $sequence);
    }

    private function getCnt()
    {
        $condition = array(
            'application_code' => $this->applicationCode,
        );
        if (empty($this->customerCode)) {
            $this->customerCode = Service_User::getCustomerId();
        }
        $condition['customer_code'] = $this->customerCode;
        
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
        
        $this->prefix = isset($application['prefix']) && !empty($application['prefix']) ? $application['prefix'] : $this->prefix;
        $rule = isset($application['rule']) ? $application['rule'] : 2; //使用客户代码
        switch ((int)$rule) {
            case 1: //随机四位
                $this->ruleStr = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                break;
            case 2: //使用客户代码
                $this->ruleStr = $this->customerCode;
                break;
            default: //空
                $this->ruleStr = '';
                break;
        }
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
        return sprintf('%04s', $this->getCnt());
    }

    public function timeSlice()
    {
        return date('ymd');
    }


}

class Common_GetNumbers
{
    /**
     * @param string $applicationCode 应用代码
     * @param string $customerCode /跟客户无关就传$warehouseId
     * @param string $prefix 前缀(当表字段不为空是，使用表中的值)
     * @return string
     */
    public static function getCode($applicationCode = '', $customerCode = '', $prefix = '', $separator = '-')
    {
        $obj = new GetNumbers($applicationCode, $customerCode, $prefix, $separator);
        return $obj->getCode();
    }
}