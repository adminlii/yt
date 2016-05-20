<?php
class Service_AutomaticLetterTemplate extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_AutomaticLetterTemplate|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_AutomaticLetterTemplate();
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
     * 添加自动发信模板
     * @param unknown_type $alt_row
     * @param unknown_type $altc_rows
     */
    public static function addTemplate($alt_row, $altc_rows){
    	$result = array(
    				"ask" => 1,
    				"message" => 'Create Template Success',
    				'errorCode' => ''
    			);
    	$db = Common_Common::getAdapter();
    	$db->beginTransaction();
    	try {
    		$alt_row['create_time'] = date('Y-m-d H:i:s');
    		$alt_id = Service_AutomaticLetterTemplate::add($alt_row);
    		
    		foreach ($altc_rows as $key => $value) {
    			$value['alt_id'] = $alt_id;
    			Service_AutomaticLetterTemplateContent::add($value);
    		}
    		
    		$db->commit();
    	} catch (Exception $e) {
    		$db->rollback();
    		$result = array (
    				"ask" => 0,
    				"message" => $e->getMessage (),
    				'errorCode' => $e->getCode ()
    		);
    	}
    	
    	return $result;
    }
    
    /**
     * 修改自动发信模板
     * @param unknown_type $alt_id
     * @param unknown_type $alt_row
     * @param unknown_type $altc_rows
     */
    public static function editTemplate($alt_id, $alt_row, $altc_rows){
    	$result = array(
    			"ask" => 1,
    			"message" => 'Update Template Success',
    			'errorCode' => ''
    	);
    	$db = Common_Common::getAdapter();
    	$db->beginTransaction();
    	try {
    		Service_AutomaticLetterTemplate::update($alt_row,$alt_id);
    	
    		Service_AutomaticLetterTemplateContent::delete($alt_id,'alt_id');
    		
    		foreach ($altc_rows as $key => $value) {
    			$value['alt_id'] = $alt_id;
    			Service_AutomaticLetterTemplateContent::add($value);
    		}
    	
    		$db->commit();
    	} catch (Exception $e) {
    		$db->rollback();
    		$result = array (
    				"ask" => 0,
    				"message" => $e->getMessage (),
    				'errorCode' => $e->getCode ()
    		);
    	}
    	return $result;
    }

    /**
     * @param $row
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function update($row, $value, $field = "alt_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "alt_id")
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
    public static function getByField($value, $field = 'alt_id', $colums = "*")
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
        
        $validateArr[] = array("name" =>EC::Lang('模板名称'), "value" =>$val["template_name"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('模板简称'), "value" =>$val["template_short_name"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('平台'), "value" =>$val["platform"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('修改人'), "value" =>$val["user_id"], "regex" => array("require","integer",));
        $validateArr[] = array("name" =>EC::Lang('状态'), "value" =>$val["status"], "regex" => array("require",));
//         $validateArr[] = array("name" =>EC::Lang('创建时间'), "value" =>$val["create_time"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('修改时间'), "value" =>$val["lastupdate"], "regex" => array("require",));
        return  Common_Validator::formValidator($validateArr);
    }

    /**
     * 获得模板相关的插入变量
     */
    public static function getTemplateOperate(){
    	$con = array(
    			'operate_key'=>'letter',
    			);
    	$result = Service_MessageTemplateOperate::getByCondition($con);
    	
    	return $result;
    }

    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'alt_id',
              'E1'=>'template_name',
              'E2'=>'template_short_name',
              'E3'=>'company_code',
              'E4'=>'platform',
              'E5'=>'user_id',
              'E6'=>'status',
              'E7'=>'create_time',
              'E8'=>'lastupdate',
        	  'E9'=>'language',
        );
        return $row;
    }

}