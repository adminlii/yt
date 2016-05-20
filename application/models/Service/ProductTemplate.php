<?php
class Service_ProductTemplate extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_ProductTemplate|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_ProductTemplate();
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
    public static function update($row, $value, $field = "pt_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "pt_id")
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
    public static function getByField($value, $field = 'pt_id', $colums = "*")
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
        
        $validateArr[] = array("name" =>EC::Lang('文件名称'), "value" =>$val["pt_title"], "regex" => array("require",));
       // $validateArr[] = array("name" =>EC::Lang('上传模板'), "value" =>$val["pt_content"], "regex" => array("require",));
        return  Common_Validator::formValidator($validateArr);
    }


    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'pt_id',
              'E1'=>'pt_title',
              'E2'=>'pt_content',
              'E3'=>'pt_create_date',
        );
        return $row;
    }


    /**
     * 读取上传的excel文件
     * @param unknown_type $fileName
     * @param unknown_type $filePath
     * @return string|mixed|Ambigous <multitype:, string>
     */
    public static function readUploadFile($fileName, $filePath,$sheetName=0)
    {
        return Common_Upload::readUploadFile($fileName, $filePath,$sheetName);
    }

    public static function strReplace($content){
        //$content = preg_replace('/ 　/', "", $content);
       // $content = preg_replace('/[　]{2,}/',' ', $content);
       // $content = preg_replace('/\s(?=\s)/','', $content);
       // $content = preg_replace('/[\n\r\t]/','',$content);
        $content = preg_replace('/&lt;bold&gt;/', "<strong>", $content);
        $content = preg_replace("/&lt;\/bold&gt;/", "</strong>", $content);

        $content = preg_replace('/&lt;red&gt;/', "<span style='color:red'>", $content);
        $content = preg_replace("/&lt;\/red&gt;/", "</span>", $content);

        $content = preg_replace('/&lt;blue&gt;/', "<span style='color:blue'>", $content);
        $content = preg_replace("/&lt;\/blue&gt;/", "</span>", $content);
        return $content;
    }

}