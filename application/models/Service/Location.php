<?php
class Service_Location extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_Location|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_Location();
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
    public static function update($row, $value, $field = "lc_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "lc_id")
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
    public static function getByField($value, $field = 'lc_id', $colums = "*")
    {
        $model = self::getModelInstance();
        return $model->getByField($value, $field, $colums);
    }

    public static function getByWhere($where)
    {
        $model = self::getModelInstance();
        return $model->getByWhere($where);
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

    public static function getLeftJoinWarehouseAreaByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
        $model = self::getModelInstance();
        return $model->getLeftJoinWarehouseAreaByCondition($condition, $type, $pageSize, $page, $order);
    }

    public static function getLeftJoinInventoryBatchByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
        $model = self::getModelInstance();
        return $model->getLeftJoinInventoryBatchByCondition($condition, $type, $pageSize, $page, $order);
    }
    
    /**
     * @param $val
     * @return array
     */
    public static function validator($val)
    {
        $validateArr = $error = array();

        $validateArr[] = array("name" => EC::Lang('lc_code'), "value" => $val["lc_code"], "regex" => array("require",));
        $validateArr[] = array("name" => EC::Lang('status'), "value" => $val["lc_status"], "regex" => array("positive"));
        $validateArr[] = array("name" => EC::Lang('warehouse'), "value" => $val["warehouse_id"], "regex" => array("positive"));
        $validateArr[] = array("name" => EC::Lang('locationTypeCode'), "value" => $val["lt_code"], "regex" => array("require",));
        $validateArr[] = array("name" => EC::Lang('waCode'), "value" => $val["wa_code"], "regex" => array("require",));
        $validateArr[] = array("name" => EC::Lang('putawaySort'), "value" => $val["lc_sort"], "regex" => array("positive"));
        return Common_Validator::formValidator($validateArr);
    }


    /**
     * @desc 批量导入货架号
     */
    public static function uploadShelfTransaction($companyCode ,$file)
    {
        $return = array(
            'state' => 0,
            'message' => array()
        );
        if ($file['error']) {
            $return['message'] = array('请选择xls文件');
            return $return;
        }
        $fileName = $file['name'];
        $filePath = $file['tmp_name'];
        $pathinfo = pathinfo($fileName);
        if (!isset($pathinfo["extension"]) && $pathinfo["extension"] != "xls") {
            $return['message'] = array('请选择xls文件');
            return $return;
        }

        $fileData = Common_Upload::readUploadFile($fileName, $filePath);
        if (!isset($fileData[1]) || !is_array($fileData[1])) {
            $result['message'] = array('上传失败，无法解析文件内容;');
            return $result;
        }
        /**
         * 格式化数据
         */
        $keys = array(
            '仓库代码' => 'warehouse_code',
            '货位号' => 'lc_code',
            '分区代码' => 'wa_code',
            '货位类型' => 'lt_code',
        );
        $data = $error = array();
        foreach ($fileData as $key => $val) {
            $newVal = array();
            foreach ($keys as $k => $v) {
                $newVal[$v] = isset($val[$k]) ? $val[$k] : '';
            }
            $whCode = preg_replace('/([0-9a-zA-Z]+)\s+.*/', '\\1', $newVal['warehouse_code']);
            if (empty($whCode)) {
                $error[] = '第 ' . $key . ' 行,仓库代码不能为空';
                continue;
            }
            if (empty($newVal['lc_code'])) {
                $error[] = '第 ' . $key . ' 行,货位号不能为空';
                continue;
            }
            if (empty($newVal['wa_code'])) {
                $error[] = '第 ' . $key . ' 行,分区代码不能为空';
                continue;
            }
            if (empty($newVal['lt_code'])) {
                $error[] = '第 ' . $key . ' 行,货位类型不能为空';
                continue;
            }
            $whRow = Service_Warehouse::getByField($whCode, 'warehouse_code', array('warehouse_code', 'warehouse_id'));
            if (empty($whRow)) {
                $error[] = '第 ' . $key . ' 行,仓库代码不存在或无法识别';
                continue;
            }
            $count = Service_Location::getByCondition(array('company_code'=>$companyCode,'warehouse_id' => $whRow['warehouse_id'], 'lc_code' => $newVal['lc_code']), 'count(*)');
            if ($count) {
                $error[] = '第 ' . $key . ' 行,货位号已存在';
                continue;
            }
            $areaRows = Service_WarehouseArea::getByCondition(array('company_code'=>$companyCode,'warehouse_id' => $whRow['warehouse_id'], 'wa_code' => $newVal['wa_code']), '*', 1);
            if (empty($areaRows)) {
                $error[] = '第 ' . $key . ' 行,分区代码不存在或无法识别';
                continue;
            }

            $typeRows = Service_LocationType::getByCondition(array('company_code'=>$companyCode,'warehouse_id' => $whRow['warehouse_id'], 'lc_code' => $newVal['lc_code']), '*', 1);
            if (empty($typeRows)) {
                $error[] = '第 ' . $key . ' 行,货位类型不存在或无法识别';
                continue;
            }

            $data[$key]['warehouse_id'] = $whRow['warehouse_id'];
            $data[$key]['lc_code'] = strtoupper($newVal['lc_code']);
            $data[$key]['wa_code'] = $areaRows[0]['wa_code'];
            $data[$key]['lt_code'] = $typeRows[0]['lt_code'];
        }
        unset($fileData, $newVal);
        if (!empty($error) || empty($data)) {
            $return['state'] = 0;
            $return['message'] = $error;
            return $return;
        }
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try {
            $date = date('Y-m-d H:i:s');
            foreach ($data as $key => $row) {
                $count = Service_Location::getByCondition(array('company_code'=>$companyCode,'warehouse_id' => $row['warehouse_id'], 'lc_code' => $row['lc_code']), 'count(*)');
                if ($count) {
                    throw new Exception('第 ' . $key . ' 行,货位号"' . $row['lc_code'] . '"重复');
                }
                $row['lc_add_time'] = $date;
                if (!Service_Location::add($row)) {
                    throw new Exception('第 ' . $key . ' 行,添加失败');
                }
            }
            $db->commit();
            $return['state'] = 1;
            $return['message'] = array('操作成功');
        } catch (Exception $e) {
            $db->rollBack();
            $return['message'] = array($e->getMessage());
        }
        return $return;
    }


    /**
     * 批量增加货位
     * @param unknown_type $row
     */
    public static function batchAdd($row){
    	$return = array(
            'state' => 0,
            'message' => array()
        );
    	$db = Common_Common::getAdapter();
        $db->beginTransaction();
        try {
	    	foreach ($row as $key => $value) {
	    		$count = Service_Location::getByCondition(array('company_code'=>$companyCode,'warehouse_id' => $value['warehouse_id'], 'lc_code' => $value['lc_code']), 'count(*)');
	    		if ($count) {
	    			throw new Exception('"货位号" ' . $value['lc_code'] . '" 重复');
	    		}
	    		if (!Service_Location::add($value)) {
	    			throw new Exception('第 ' . $key . ' 行,添加失败');
	    		}
	    	}
	    	$db->commit();
	    	$return['state'] = 1;
    		$return['message'] = array('批量添加货位成功');
    	} catch (Exception $e) {
    		$db->rollBack();
    		$return['message'] = array($e->getMessage());
    	}
    	return $return;
    }
    
    
    /**
     * @param array $params
     * @return array
     */
    public function getFields()
    {
        $row = array(

            'E0' => 'lc_id',
            'E1' => 'lc_code',
            'E2' => 'lc_note',
            'E3' => 'lc_status',
            'E4' => 'warehouse_id',
            'E5' => 'lt_code',
            'E6' => 'wa_code',
            'E7' => 'lc_sort',
            'E8' => 'lc_add_time',
            'E9' => 'lc_update_time',
        	'E10'=> 'company_code',
            'E06' => 'warehouse_area.wa_name',
        	
        );
        return $row;
    }

}