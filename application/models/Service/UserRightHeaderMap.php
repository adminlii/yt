<?php
class Service_UserRightHeaderMap extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_UserRightHeaderMap|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_UserRightHeaderMap();
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
    public static function update($row, $value, $field = "urhm_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "urhm_id")
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
    public static function getByField($value, $field = 'urhm_id', $colums = "*")
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

        return Common_Validator::formValidator($validateArr);
    }


    /**
     * @param array $params
     * @return array
     */
    public function getFields()
    {
        $row = array(

            'E0' => 'urhm_id',
            'E1' => 'ur_id',
            'E2' => 'refer_id',
            'E3' => 'urhm_type',
            'E4' => 'urhm_sort',
        );
        return $row;
    }

    /**
     * @获取用户配置导航
     * @默认用职位、当自定义后,用自定义
     * @return array
     */
    public static function getSkyeQuiKey()
    {
        $userAuth = new Zend_Session_Namespace('userAuthorization');
        $userRow = isset($userAuth->user) ? $userAuth->user : array();
        if (empty($userRow)) {
            return array();
        }
        $lang = Ec::getLang(1);
        $obj = new Table_UserRightHeaderMap();
        $data = array();
        $result = $obj->getLeftUserRightByCondition($userRow['user_id'], $userRow['up_id']);
        if (!empty($result)) {
            $type = $result[0]['urhm_type'];
            foreach ($result as $key => $val) {
                if ($val['urhm_type'] == $type) {
                    $data[$val['um_sort'] . '_' . $val['u_sort'] . '_' . $val['ur_sort'] . '_' . $val['urhm_sort'] . '_' . $val['urhm_id']] = array(
                        'urlId' => $val['ur_id'],
                        'title' => $val['ur_name'.$lang],
                        'url' => $val['ur_url'],
                        'icon' => $val['ur_icon'],
                        'id' => $val['urhm_id'],
                        'type' => $val['urhm_type'],
                    );
                }
            }
            unset($result);
        }
        ksort($data);
        return $data;
    }

    public static function updateSkyeQuiKey($ids = array())
    {
        $userId = Service_User::getUserId();
        $obj = new Table_UserRightHeaderMap();
        if (empty($ids)) {
            $result = $obj->deleteByReferIdAndType($userId, '1');
        } else {
            $oldIds = array();
            //当前自定义设置数据
            $data = self::getByCondition(array('urhm_type' => 1, 'refer_id' => $userId), array('ur_id'));
            if ($data) {
                foreach ($data as $val) {
                    $oldIds[] = $val['ur_id'];
                }
            }
            $delIdArr = array_diff($oldIds, $ids);
            $addIdArr = array_diff($ids, $oldIds);
            if (!empty($delIdArr)) {
                $obj->deleteByReferIdAndUrId($userId, $delIdArr);
            }
            if (!empty($addIdArr)) {
                $row = array(
                    'urhm_type' => 1,
                    'refer_id' => $userId,
                );
                foreach ($addIdArr as $val) {
                    $row['ur_id'] = $val;
                    $obj->add($row);
                }
            }
            $result = true;
        }
        return $result;
    }

}