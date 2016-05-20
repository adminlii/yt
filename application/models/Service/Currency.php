<?php
class Service_Currency extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_Currency|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_Currency();
        }
        return self::$_modelClass;
    }

    /**
     * @param $value
     * @param string $field
     * @param string $colums
     * @return mixed
     */
    public static function getByField($value, $field = 'currency_id', $colums = "*")
    {
        $model = self::getModelInstance();
        return $model->getByField($value, $field, $colums);
    }

    /**
     * @return mixed
     */
    public static function getAll()
    {
        $model = self::getModelInstance();
        return $model->getAll();
    }

    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public static function getByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
        $model = self::getModelInstance();
        return $model->getByCondition($condition, $type, $pageSize, $page, $order);
    }

    /**
     * @param $val
     * @return array
     */
    public static function validator($val)
    {
        $validateArr = $error = array();
        
        $validateArr[] = array("name" =>EC::Lang('currencyName'), "value" =>$val["currency_name_en"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('currencyNameEn'), "value" =>$val["currency_name"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('currencyCode'), "value" =>$val["currency_code"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('currencyDecimalPlaces'), "value" =>$val["currency_decimal_places"], "regex" => array("positive"));
        $validateArr[] = array("name" =>EC::Lang('currencyRate'), "value" =>$val["currency_rate"], "regex" => array("require","integer",));
        return  Common_Validator::formValidator($validateArr);
    }
    /**
     * @desc 同步汇率
     * @return array
     */
    public static function syncCurrency()
    {
    	$result = array('state' => 0, 'message' => '操作失败');
    	$return = Common_CustomerFeeProcess::GetCurrency();
    	$date = date('Y-m-d H:i:s');
    	if (is_array($return) && !empty($return)) {
    		$userId = Service_User::getUserId();
    		foreach ($return as $code => $rate) {
    			if ($rate < 0 || !is_numeric($rate)) {
    				continue;
    			}
    			if ($row = self::getByField($code, 'currency_code')) {
    				if($rate==$row['currency_rate']){
    					continue;
    				}
    				$result['state'] = 1;
    				if (self::update(array('currency_rate' => $rate, 'currency_official_rate' => $rate, 'currency_update_time' => $date), $row['currency_id'], 'currency_id')) {
    					$log = array(
    							'currency_code' => $row['currency_code'],
    							'currency_rate' => $row['currency_rate'],
    							'to_currency_rate' => $rate,
    							'user_id' => $userId,
    							'cl_note' => '操作API同步汇率',
    					);
    					Service_CurrencyLog::add($log);
    				}
    			}
    		}
    	}
    	if ($result['state'] == '1') {
    		$result['message'] = "同步成功";
    	}
    	return $result;
    }

    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'currency_id',
              'E1'=>'currency_name_en',
              'E2'=>'currency_name',
              'E3'=>'currency_code',
              'E4'=>'currency_symbol_left',
              'E5'=>'currency_symbol_right',
              'E6'=>'currency_decimal_point',
              'E7'=>'currency_thousands_point',
              'E8'=>'currency_decimal_places',
              'E9'=>'currency_rate',
              'E10'=>'currency_update_time',
        );
        return $row;
    }

}