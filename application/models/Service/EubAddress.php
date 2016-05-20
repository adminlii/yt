<?php
class Service_EubAddress extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_EubAddress|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_EubAddress();
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
    public static function update($row, $value, $field = "eadd_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "eadd_id")
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
    public static function getByField($value, $field = 'eadd_id', $colums = "*")
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
        
        $validateArr[] = array("name" =>EC::Lang('EUB类型'), "value" =>$val["eub_type"], "regex" => array("require",));
        if($val["eub_type"] == '1'){
        	$validateArr[] = array("name" =>EC::Lang('揽收-联系人'), "value" =>$val["pname"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('揽收-公司名'), "value" =>$val["pcompany"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('揽收-国家'), "value" =>$val["pcountry"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('揽收-省'), "value" =>$val["pprovince"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('揽收-城市'), "value" =>$val["pcity"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('揽收-区'), "value" =>$val["pdis"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('揽收-街道'), "value" =>$val["pstreet"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('揽收-邮政编码'), "value" =>$val["pzip"], "regex" => array("require",));
//         	$validateArr[] = array("name" =>EC::Lang('揽收-移动电话'), "value" =>$val["pmobile"], "regex" => array("require",));
//         	$validateArr[] = array("name" =>EC::Lang('揽收-固定电话'), "value" =>$val["ptel"], "regex" => array("require",));
//         	$validateArr[] = array("name" =>EC::Lang('揽收-Email'), "value" =>$val["pemail"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('退货(EN)-联系人'), "value" =>$val["rname_en"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('退货(EN)-公司名'), "value" =>$val["rcompany_en"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('退货(EN)-国家名'), "value" =>$val["rcountry_en"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('退货(EN)-省'), "value" =>$val["rprovince_en"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('退货(EN)-城市'), "value" =>$val["rcity_en"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('退货(EN)-街道'), "value" =>$val["rstreet_en"], "regex" => array("require",));
//         	$validateArr[] = array("name" =>EC::Lang('退货(EN)_邮编'), "value" =>$val["rzip_en"], "regex" => array("require",));
//         	$validateArr[] = array("name" =>EC::Lang('退货(EN)-电话'), "value" =>$val["rmobile_en"], "regex" => array("require",));
//         	$validateArr[] = array("name" =>EC::Lang('退货(EN)-Email'), "value" =>$val["remail_en"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('退货-联系人'), "value" =>$val["rname"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('退货-公司名'), "value" =>$val["rcompany"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('退货-国家名'), "value" =>$val["rcountry"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('退货-省'), "value" =>$val["rprovince"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('退货-城市'), "value" =>$val["rcity"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('退货-街道'), "value" =>$val["rstreet"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('退货-邮编'), "value" =>$val["rzip"], "regex" => array("require",));
//         	$validateArr[] = array("name" =>EC::Lang('退货-电话'), "value" =>$val["rmobile"], "regex" => array("require",));
//         	$validateArr[] = array("name" =>EC::Lang('退货-Email'), "value" =>$val["remail"], "regex" => array("require",));
        }else if($val["eub_type"] == '2'){
        	$validateArr[] = array("name" =>EC::Lang('发货-联系人'), "value" =>$val["line_sname"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('发货-公司名'), "value" =>$val["line_scompany"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('发货-国家'), "value" =>$val["line_scountry"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('发货-省'), "value" =>$val["line_sprovince"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('发货-城市'), "value" =>$val["line_scity"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('发货-区'), "value" =>$val["line_sdis"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('发货-街道'), "value" =>$val["line_sstreet"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('发货-邮政编码'), "value" =>$val["line_szip"], "regex" => array("require",));
//         	$validateArr[] = array("name" =>EC::Lang('发货-移动电话'), "value" =>$val["line_smobile"], "regex" => array("require",));
//         	$validateArr[] = array("name" =>EC::Lang('发货-固定电话'), "value" =>$val["line_stel"], "regex" => array("require",));
//         	$validateArr[] = array("name" =>EC::Lang('发货-Email'), "value" =>$val["line_semail"], "regex" => array("require",));
        	
        	$validateArr[] = array("name" =>EC::Lang('揽收-联系人'), "value" =>$val["line_pname"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('揽收-公司名'), "value" =>$val["line_pcompany"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('揽收-国家'), "value" =>$val["line_pcountry"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('揽收-省'), "value" =>$val["line_pprovince"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('揽收-城市'), "value" =>$val["line_pcity"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('揽收-区'), "value" =>$val["line_pdis"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('揽收-街道'), "value" =>$val["line_pstreet"], "regex" => array("require",));
        	$validateArr[] = array("name" =>EC::Lang('揽收-邮政编码'), "value" =>$val["line_pzip"], "regex" => array("require",));
//         	$validateArr[] = array("name" =>EC::Lang('揽收-移动电话'), "value" =>$val["line_pmobile"], "regex" => array("require",));
//         	$validateArr[] = array("name" =>EC::Lang('揽收-固定电话'), "value" =>$val["line_ptel"], "regex" => array("require",));
//         	$validateArr[] = array("name" =>EC::Lang('揽收-Email'), "value" =>$val["line_pemail"], "regex" => array("require",));
        }
        
        return  Common_Validator::formValidator($validateArr);
    }


    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'eadd_id',
              'E1'=>'ref_id',
              'E2'=>'pname',
              'E3'=>'pcompany',
              'E4'=>'pcountry',
              'E5'=>'pprovince',
              'E6'=>'pcity',
              'E7'=>'pdis',
              'E8'=>'pstreet',
              'E9'=>'pzip',
              'E10'=>'pmobile',
              'E11'=>'ptel',
              'E12'=>'pemail',
              'E13'=>'rname_en',
              'E14'=>'rcompany_en',
              'E15'=>'rcountry_en',
              'E16'=>'rprovince_en',
              'E17'=>'rcity_en',
              'E18'=>'rdis_en',
              'E19'=>'rstreet_en',
              'E20'=>'rzip_en',
              'E21'=>'rmobile_en',
              'E22'=>'remail_en',
              'E23'=>'rname',
              'E24'=>'rcompany',
              'E25'=>'rcountry',
              'E26'=>'rprovince',
              'E27'=>'rcity',
              'E28'=>'rdis',
              'E29'=>'rstreet',
              'E30'=>'rzip',
              'E31'=>'rmobile',
              'E32'=>'remail',
        );
        return $row;
    }

}