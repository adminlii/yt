<?php
/**
 * __CLASS__ 获取当前类名
 * __FUNCTION__ 当前函数名（confirm）
 * __METHOD__ 当前方法名 （bankcard::confirm）  
 * get_class(class name);//取得当前语句所在类的类名
 * get_class_methods(class name);//取得class name 类的所有的方法名，并且组成一个数组
 * get_class_vars(class name);//取得class name 类的所有的变亮名，并组成一个数组
 * @author Max
 *
 */
class Common_Service
{
    public  function getFields()
    {
        $row = array(
            'demo' => 'demo'
        );
        return $row;
    }

    /**
     * @param array $params
     * @param int $type
     * @return array
     */
    public  function getMatchFields($params = array(), $type = 1)
    {
        $row =$this->getFields();
        $fieldsArr = array();
        foreach ($row as $key => $val) {
            if (isset($params[$key]) && $params[$key] != $val) {
                $fieldsArr[$val] = trim($params[$key]);
            } else {
                $fieldsArr[$val] = '';
                if ($type == '1') {
                    unset($fieldsArr[$val]);
                }
            }
        }
        return $fieldsArr;
    }

    /**
     * @param array $params
     * @param int $type
     * @return array
     */
    public  function getVirtualFields($params = array(), $type = 0)
    {
        $fieldsArr =$this->getFields();
        $convertFieldsArr = array();
        foreach ($fieldsArr as $key => $val) {
            if (isset($params[$val])) {
                $convertFieldsArr[$key] = $params[$val];
            }
        }
        return $convertFieldsArr;
    }

    /**
     * @param array $params
     * @param int $type
     * @return array
     */
    public  function getFieldsAlias($params = array(), $type = 0)
    {
        $fieldsArr =$this->getFields();
        $convertFieldsArr = $row = array();
        $params = array_combine($params, $params);
        foreach ($fieldsArr as $key => $val) {
            $convertFieldsArr[$val] = $key;
            if (isset($params[$val])) {
                $row[] = $val . ' as ' . $key;
            }
        }
        if ($type) {
            return $convertFieldsArr;
        }
        return $row;
    }

    /**
     * @param array $editFields
     * @param array $params
     * @return array
     */
    public function getEditFields($editFields = array(), $params = array())
    {
        foreach ($editFields as $key => $val) {
            if (isset($params[$key])) {
                $editFields[$key] = $params[$key];
            }
        }
        return $editFields;
    }


    /**
     * @param array $params
     * @param array $editFields
     * @return array
     */
    public function getMatchEditFields($params = array(), $editFields = array())
    {
        $row = $this->getFields();
        $fieldsArr = array();
        foreach ($row as $key => $val) {
            if (isset($params[$key]) && $params[$key] != $val) {
                $fieldsArr[$val] = trim($params[$key]);
            } else {
                $fieldsArr[$val] = '';
            }
            if (!isset($editFields[$val])) {
                unset($fieldsArr[$val]);
            }
        }
        return $fieldsArr;
    }
    
    
    /**
     * 对请求的数据进行整理
     * @param array $params
     * @return array
     */
    public  function getRequestFields($params = array())
    {
        unset($params['module']);
        unset($params['controller']);
        unset($params['action']);
        
        $fieldsArr = array();
        foreach($params as $key => $val){
            if(is_array($val)){
                $key = preg_replace('/(_arr)$/', '', $key);
                $fieldsArr[$key . '_arr'] = $val;
            }else{
                $fieldsArr[$key] = $val;
            }
        }
        
        return $fieldsArr;
    }
}