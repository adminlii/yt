<?php
class Service_EbayCustomerFeedback extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_EbayCustomerFeedback|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_EbayCustomerFeedback();
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
    public static function update($row, $value, $field = "ecf_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "ecf_id")
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
    public static function getByField($value, $field = 'ecf_id', $colums = "*")
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
        
        return  Common_Validator::formValidator($validateArr);
    }


    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'ecf_id',
              'E1'=>'ecf_feedback_id',
              'E2'=>'ecf_commenting_user',
              'E3'=>'ecf_commenting_user_score',
              'E4'=>'ecf_comment_text',
              'E5'=>'ecf_comment_time',
              'E6'=>'ecf_comment_type',
              'E7'=>'ecf_role',
              'E8'=>'ecf_transaction_id',
              'E9'=>'ecf_order_line_item_id',
              'E10'=>'ecf_item_id',
              'E11'=>'ecf_item_title',
              'E12'=>'ecf_item_price',
              'E13'=>'ecf_currency_id',
              'E14'=>'ecf_order_id',
              'E15'=>'ecf_message_id',
              'E16'=>'ecf_modify_date_sys',
              'E17'=>'ecf_create_date_sys',
              'E18'=>'ecf_proecss_status',
        	  'E19'=>'ecf_ebay_account',
        	  'E20'=>'ecf_feedback_revised',
        	  'E21'=>'ecf_feedback_response',
        	  'E22'=>'company_code'
        );
        return $row;
    }

}