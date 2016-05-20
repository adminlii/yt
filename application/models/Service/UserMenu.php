<?php
class Service_UserMenu extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_UserMenu|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_UserMenu();
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
    public static function update($row, $value, $field = "um_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "um_id")
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
    public static function getByField($value, $field = 'um_id', $colums = "*")
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
        //print_r($model->getByCondition($condition, $type, $pageSize, $page, $order));
        return $model->getByCondition($condition, $type, $pageSize, $page, $order);
    }

    /**
     * @param $val
     * @return array
     */
    public static function validator($val)
    {
        $validateArr = $error = array();
        
        $validateArr[] = array("name" =>EC::Lang('menuName'), "value" =>$val["um_title"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('menuNameEn'), "value" =>$val["um_title_en"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('sort'), "value" =>$val["um_sort"], "regex" => array("require","integer",));
        return  Common_Validator::formValidator($validateArr);
    }


    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'um_id',
              'E1'=>'um_title',
              'E2'=>'um_title_en',
              'E3'=>'um_url',
              'E4'=>'um_css',
              'E5'=>'um_color',
              'E6'=>'um_sort',
              'E7'=>'us_id',
              'E8'=>'parent_id',
        );
        return $row;
    }

    public static  function getMenu(){
        $userRightArray = Common_DataCache::getUserRight();
        $userMenuArray = Common_DataCache::getUserMenu();
        $lang = Ec::getLang(1);
        $userMenu = array();
        foreach ($userRightArray as $key => $val) {
            if ($val['ur_type'] == '0') {
                continue;
            }
            if (!isset($userMenu[$userMenuArray[$val['um_id']]['um_sort'] . '-' . $val['um_id']]['menu'])) {
                $userMenu[$userMenuArray[$val['um_id']]['um_sort'] . '-' . $val['um_id']]['menu'] = $userMenuArray[$val['um_id']];
                $userMenu[$userMenuArray[$val['um_id']]['um_sort'] . '-' . $val['um_id']]['menu']['value'] = $userMenuArray[$val['um_id']]['um_title' . $lang];
            }
            $userMenu[$userMenuArray[$val['um_id']]['um_sort'] . '-' . $val['um_id']]['item'][$key] = $val;
            $userMenu[$userMenuArray[$val['um_id']]['um_sort'] . '-' . $val['um_id']]['item'][$key]['value'] = $val['ur_name' . $lang];
        }
        ksort($userMenu);
        return $userMenu;
    }

    public static  function getHeaderMenu(){
        $userRightArray = Common_DataCache::getUserRight();
        $userMenuArray = Common_DataCache::getUserMenu();
        $lang = Ec::getLang(1);
        $userMenu = array();
        foreach ($userRightArray as $key => $val) {
            if ($val['ur_type'] == '0') {
                continue;
            }
            if($val['ur_common']=='1'){
                if (!isset($userMenu[$userMenuArray[$val['um_id']]['um_sort'] . '-' . $val['um_id']]['menu'])) {
                    $userMenu[$userMenuArray[$val['um_id']]['um_sort'] . '-' . $val['um_id']]['menu'] = $userMenuArray[$val['um_id']];
                    $userMenu[$userMenuArray[$val['um_id']]['um_sort'] . '-' . $val['um_id']]['menu']['value'] = $userMenuArray[$val['um_id']]['um_title' . $lang];
                }
                $userMenu[$userMenuArray[$val['um_id']]['um_sort'] . '-' . $val['um_id']]['item'][$key] = $val;
                $userMenu[$userMenuArray[$val['um_id']]['um_sort'] . '-' . $val['um_id']]['item'][$key]['value'] = $val['ur_name' . $lang];
            }
        }
        ksort($userMenu);
        return $userMenu;
    }

}