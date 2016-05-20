<?php
class Service_ValueAddedType extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_ValueAddedType|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_ValueAddedType();
        }
        return self::$_modelClass;
    }

    /**
     * @param $row
     * @return mixed
     */
    public static function add($row)
    {
        $model = self::getModelInstance();
        return $model->add($row);
    }


    /**
     * @param $row
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function update($row, $value, $field = "vat_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "vat_id")
    {
        $model = self::getModelInstance();
        return $model->delete($value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @param string $colums
     * @return mixed
     */
    public static function getByField($value, $field = 'vat_id', $colums = "*")
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
        
        $validateArr[] = array("name" =>EC::Lang('代码'), "value" =>$val["vat_code"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('业务类型'), "value" =>$val["vat_business_type"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('状态'), "value" =>$val["vat_status"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('英文名称'), "value" =>$val["vat_name_en"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('中文名称'), "value" =>$val["vat_name_cn"], "regex" => array("require",));
        return  Common_Validator::formValidator($validateArr);
    }


    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'vat_id',
              'E1'=>'vat_code',
              'E2'=>'vat_business_type',
              'E3'=>'vat_status',
              'E4'=>'vat_name_en',
              'E5'=>'vat_name_cn',
              'E6'=>'vat_note',
              'E7'=>'vat_add_time',
              'E8'=>'vat_update_time',
        );
        return $row;
    }

    /**
     * 校验代码不能重复
     * @param $val
     * @return array
     */
    public static function validatorRepeat($val, $paramId)
    {
    	$error = array();
    
    	// 代码不能重复
    	$row = Service_ValueAddedType::getByField($val["vat_code"], 'vat_code');
    	if(!empty($row)) {
    		if(emptY($paramId)) {
    			$error[] = EC::Lang('代码') . EC::Lang('repeat');
    		} else if($row['vat_id'] != $paramId) {
    			$error[] = EC::Lang('代码') . EC::Lang('repeat');
    		}
    	}
    	
    	// 中文名称不能重复
    	$row = Service_ValueAddedType::getByField($val["vat_name_cn"], 'vat_name_cn');
    	if(!empty($row)) {
    		if(emptY($paramId)) {
    			$error[] = EC::Lang('中文名称') . EC::Lang('repeat');
    		} else if($row['vat_id'] != $paramId) {
    			$error[] = EC::Lang('中文名称') . EC::Lang('repeat');
    		}
    	}
    	
    	// 英文名称不能重复
    	$row = Service_ValueAddedType::getByField($val["vat_name_en"], 'vat_name_en');
    	if(!empty($row)) {
    		if(emptY($paramId)) {
    			$error[] = EC::Lang('英文名称') . EC::Lang('repeat');
    		} else if($row['vat_id'] != $paramId) {
    			$error[] = EC::Lang('英文名称') . EC::Lang('repeat');
    		}
    	}
    	
    	return $error;
    }
}