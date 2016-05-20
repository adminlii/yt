<?php
class Table_Orders {
	protected $_table = null;
	public function __construct() {
		$this->_table = new DbTable_Orders ();
	}
	public function getAdapter() {
		return $this->_table->getAdapter ();
	}
	public static function getInstance() {
		return new Table_Orders ();
	}
	
	/**
	 *
	 * @param
	 *        	$row
	 * @return mixed
	 */
	public function add($row) { // 重新订单计数开关
		$reTongji = new Zend_Session_Namespace ( 'reTongji' );
		$reTongji->reTongji = 1;
		// 系统单号
		if (! isset ( $row ['refrence_no_sys'] )) {
			$refrence_no_sys = Common_GetNumbers::getCode ( 'CURRENT_ORDER_SYS_COUNT', 'SYS' ); // 系统单号
			$row ['refrence_no_sys'] = $refrence_no_sys;
		}
		$return = $this->_table->insert ( $row );
		// 更新统计信息
		// Common_ApiProcess::orderTongjiSingle($row['refrence_no_platform'],'refrence_no_platform');
		
		// 更新关键字
		Service_OrderKeywordProcess::updateOrderKeyword ( $row ['refrence_no_platform'], 'refrence_no_platform' );
		
		return $return;
	}
	
	/**
	 *
	 * @param
	 *        	$row
	 * @param
	 *        	$value
	 * @param string $field        	
	 * @return mixed
	 */
	public function update($row, $value, $field = "order_id") {
		// 重新订单计数开关
		if (isset ( $row ['order_status'] )) {
			$reTongji = new Zend_Session_Namespace ( 'reTongji' );
			$reTongji->reTongji = 1;
		}
		$row ['date_last_modify'] = date ( 'Y-m-d H:i:s' );
		$where = $this->_table->getAdapter ()->quoteInto ( "{$field}= ?", $value );
		$return = $this->_table->update ( $row, $where );
		
		// 更新统计信息
		if ($field == 'order_id' || $field == 'refrence_no_platform' || $field == 'refrence_no_sys') {
			// Common_ApiProcess::orderTongjiSingle($value,$field);
		}
		// 更新关键字
		Service_OrderKeywordProcess::updateOrderKeyword ( $value, $field );
		return $return;
	}
	
	/**
	 *
	 * @param
	 *        	$value
	 * @param string $field        	
	 * @return mixed
	 */
	public function delete($value, $field = "order_id") {
		// 重新订单计数开关
		$reTongji = new Zend_Session_Namespace ( 'reTongji' );
		$reTongji->reTongji = 1;
		$where = $this->_table->getAdapter ()->quoteInto ( "{$field}= ?", $value );
		return $this->_table->delete ( $where );
	}
	
	/**
	 *
	 * @param
	 *        	$value
	 * @param string $field        	
	 * @param string $colums        	
	 * @return mixed
	 */
	public function getByField($value, $field = 'order_id', $colums = "*") {
		$select = $this->_table->getAdapter ()->select ();
		$table = $this->_table->info ( 'name' );
		$select->from ( $table, $colums );
		$select->where ( "{$field} = ?", $value );
		return $this->_table->getAdapter ()->fetchRow ( $select );
	}
	public function getAll() {
		$select = $this->_table->getAdapter ()->select ();
		$table = $this->_table->info ( 'name' );
		$select->from ( $table, "*" );
		return $this->_table->getAdapter ()->fetchAll ( $select );
	}
	
	/**
	 *
	 * @param array $condition        	
	 * @param string $type        	
	 * @param int $pageSize        	
	 * @param int $page        	
	 * @param string $orderBy        	
	 * @return array string
	 */
	public function getByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "", $groupBy = '') {
		$select = $this->_table->getAdapter ()->select ();
		$table = $this->_table->info ( 'name' );
		$select->from ( $table, $type );
		
		$select->where ( "1 =?", 1 );
		
		/* CONDITION_START */
		
		if (isset ( $condition ["order_id_arr"] ) && ! empty ( $condition ["order_id_arr"] )) {
			$select->where ( "order_id in (?)", $condition ["order_id_arr"] );
		}
		if (isset ( $condition ["not_order_id"] ) && $condition ["not_order_id"] != "") {
			$select->where ( "order_id != ?", $condition ["not_order_id"] );
		}
		if (isset ( $condition ["platform"] ) && $condition ["platform"] != "") {
			$select->where ( "platform = ?", $condition ["platform"] );
		}
		
		if (isset ( $condition ["ot_id"] ) && $condition ["ot_id"] != "") {
			$select->where ( "ot_id = ?", $condition ["ot_id"] );
		}
		if (isset ( $condition ["order_type"] ) && $condition ["order_type"] != "") {
			$select->where ( "order_type = ?", $condition ["order_type"] );
		}
		
		if (isset ( $condition ["create_type"] ) && $condition ["create_type"] != "") {
			$select->where ( "create_type = ?", $condition ["create_type"] );
		}
		
		if (isset ( $condition ["order_status"] ) && $condition ["order_status"] != "") {
			$select->where ( "order_status = ?", $condition ["order_status"] );
		}
		if (isset ( $condition ["sub_status"] ) && $condition ["sub_status"] != "") {
			$select->where ( "sub_status = ?", $condition ["sub_status"] );
		}
		
		if (isset ( $condition ["order_status_arr"] ) && $condition ["order_status_arr"] != "") {
			$select->where ( "order_status in (?)", $condition ["order_status_arr"] );
		}
		if (isset ( $condition ["create_method"] ) && $condition ["create_method"] != "") {
			$select->where ( "create_method = ?", $condition ["create_method"] );
		}
		if (isset ( $condition ["customer_id"] ) && $condition ["customer_id"] != "") {
			$select->where ( "customer_id = ?", $condition ["customer_id"] );
		}
		if (isset ( $condition ["company_code"] ) && $condition ["company_code"] != "") {
			$select->where ( "company_code = ?", $condition ["company_code"] );
		}
		if (isset ( $condition ["shipping_method"] ) && $condition ["shipping_method"] != "") {
			$select->where ( "shipping_method = ?", $condition ["shipping_method"] );
		}
		if (isset ( $condition ["shipping_method_platform"] ) && $condition ["shipping_method_platform"] != "") {
			$select->where ( "shipping_method_platform = ?", $condition ["shipping_method_platform"] );
		}
		if (isset ( $condition ["warehouse_id"] ) && $condition ["warehouse_id"] != "") {
			$select->where ( "warehouse_id = ?", $condition ["warehouse_id"] );
		}
		
		if (isset ( $condition ["warehouse_id_arr"] ) && ! empty ( $condition ["warehouse_id_arr"] )) {
			$select->where ( "warehouse_id in (?)", $condition ["warehouse_id_arr"] );
		}
		
		if (isset ( $condition ["order_desc"] ) && $condition ["order_desc"] != "") {
			$select->where ( "order_desc = ?", $condition ["order_desc"] );
		}
		if (isset ( $condition ["operator_id"] ) && $condition ["operator_id"] != "") {
			$select->where ( "operator_id = ?", $condition ["operator_id"] );
		}
		if (isset ( $condition ["refrence_no"] ) && $condition ["refrence_no"] != "") {
			$select->where ( "refrence_no = ?", $condition ["refrence_no"] );
		}
		if (isset ( $condition ["refrence_no_arr"] ) && ! empty ( $condition ["refrence_no_arr"] )) {
			$select->where ( "refrence_no in (?)", $condition ["refrence_no_arr"] );
		}
		if (isset ( $condition ["refrence_no_platform"] ) && $condition ["refrence_no_platform"] != "") {
			$select->where ( "refrence_no_platform = ?", $condition ["refrence_no_platform"] );
		}
		if (isset ( $condition ["refrence_no_platform_arr"] ) && ! empty ( $condition ["refrence_no_platform_arr"] )) {
			$select->where ( "refrence_no_platform in (?)", $condition ["refrence_no_platform_arr"] );
		}
		
		if (isset ( $condition ["shipping_address_id"] ) && $condition ["shipping_address_id"] != "") {
			$select->where ( "shipping_address_id = ?", $condition ["shipping_address_id"] );
		}
		if (isset ( $condition ["currency"] ) && $condition ["currency"] != "") {
			$select->where ( "currency = ?", $condition ["currency"] );
		}
		if (isset ( $condition ["refrence_no_warehouse"] ) && $condition ["refrence_no_warehouse"] != "") {
			$select->where ( "refrence_no_warehouse = ?", $condition ["refrence_no_warehouse"] );
		}
		
		if (isset ( $condition ["refrence_no_warehouse_arr"] ) && ! empty ( $condition ["refrence_no_warehouse_arr"] )) {
			$select->where ( "refrence_no_warehouse in (?)", $condition ["refrence_no_warehouse_arr"] );
		}
		if (isset ( $condition ["shipping_method_no"] ) && $condition ["shipping_method_no"] != "") {
			$select->where ( "shipping_method_no = ?", $condition ["shipping_method_no"] );
		}
		
		if (isset ( $condition ["shipping_method_no_arr"] ) && ! empty ( $condition ["shipping_method_no_arr"] )) {
			$select->where ( "shipping_method_no in (?)", $condition ["shipping_method_no_arr"] );
		}
		if (isset ( $condition ["sync_status"] ) && $condition ["sync_status"] != "") {
			$select->where ( "sync_status = ?", $condition ["sync_status"] );
		}
		
		if (isset ( $condition ["sync_status_arr"] ) && ! empty ( $condition ["sync_status_arr"] )) {
			$select->where ( "sync_status in (?)", $condition ["sync_status_arr"] );
		}
		
		if (isset ( $condition ["createDateFrom"] ) && $condition ["createDateFrom"] != "") {
			$select->where ( "date_create >= ?", $condition ["createDateFrom"] );
		}
		
		if (isset ( $condition ["createDateEnd"] ) && $condition ["createDateEnd"] != "") {
			$select->where ( "date_create <= ?", $condition ["createDateEnd"] );
		}
		if (isset ( $condition ["payDateFrom"] ) && $condition ["payDateFrom"] != "") {
			$select->where ( "date_paid_platform >= ?", $condition ["payDateFrom"] );
		}
		
		if (isset ( $condition ["payDateEnd"] ) && $condition ["payDateEnd"] != "") {
			$select->where ( "date_paid_platform <= ?", $condition ["payDateEnd"] );
		}
		
		if (isset ( $condition ["shipDateFrom"] ) && $condition ["shipDateFrom"] != "") {
			$select->where ( "date_warehouse_shipping >= ?", $condition ["shipDateFrom"] );
		}
		
		if (isset ( $condition ["shipDateEnd"] ) && $condition ["shipDateEnd"] != "") {
			$select->where ( "date_warehouse_shipping <= ?", $condition ["shipDateEnd"] );
		}
		
		if (isset ( $condition ["verifyDateFrom"] ) && $condition ["verifyDateFrom"] != "") {
			$select->where ( "date_release >= ?", $condition ["verifyDateFrom"] );
		}
		
		if (isset ( $condition ["verifyDateEnd"] ) && $condition ["verifyDateEnd"] != "") {
			$select->where ( "date_release <= ?", $condition ["verifyDateEnd"] );
		}
		
		if (isset ( $condition ["priceFrom"] ) && $condition ["priceFrom"] != "") {
			$select->where ( "amountpaid >= ?", $condition ["priceFrom"] );
		}
		
		if (isset ( $condition ["priceEnd"] ) && $condition ["priceEnd"] != "") {
			$select->where ( "amountpaid <= ?", $condition ["priceEnd"] );
		}
		
		if (isset ( $condition ["date_create_platform_start"] ) && $condition ["date_create_platform_start"] != "") {
			$select->where ( "date_create_platform >= ?", $condition ["date_create_platform_start"] );
		}
		
		if (isset ( $condition ["user_account_arr"] ) && ! empty ( $condition ["user_account_arr"] )) {
			$select->where ( "user_account in (?)", $condition ["user_account_arr"] );
		}
		
		if (isset ( $condition ["user_account"] ) && $condition ["user_account"] != '') {
			$select->where ( "user_account = ? ", $condition ["user_account"] );
		}
		if (isset ( $condition ["buyer_id"] ) && ! empty ( $condition ["buyer_id"] )) {
			$select->where ( "buyer_id = ? ", $condition ["buyer_id"] );
		}
		
		if (isset ( $condition ["buyer_id_arr"] ) && ! empty ( $condition ["buyer_id_arr"] )) {
			$select->where ( "buyer_id in (?) ", $condition ["buyer_id_arr"] );
		}
		
		if (isset ( $condition ["buyer_name"] ) && ! empty ( $condition ["buyer_name"] )) {
			$select->where ( "buyer_name = ? ", $condition ["buyer_name"] );
		}
		if (isset ( $condition ["buyer_mail"] ) && ! empty ( $condition ["buyer_mail"] )) {
			$select->where ( "buyer_mail = ? ", $condition ["buyer_mail"] );
		}
		if (isset ( $condition ["third_part_ship"] ) && $condition ["third_part_ship"] != '') {
			$select->where ( "third_part_ship = ? ", $condition ["third_part_ship"] );
		}
		
		if (isset ( $condition ["site"] ) && $condition ["site"] != '') {
			$select->where ( "site = ? ", $condition ["site"] );
		}
		
		if (isset ( $condition ["consignee_country"] ) && $condition ["consignee_country"] != '') {
			$select->where ( "consignee_country = ? ", $condition ["consignee_country"] );
		}
		
		if (isset ( $condition ["is_merge"] ) && $condition ["is_merge"] != '') {
			$select->where ( "is_merge = ? ", $condition ["is_merge"] );
		}
		
		if (isset ( $condition ["has_buyer_note"] ) && $condition ["has_buyer_note"] != '') {
			$select->where ( "has_buyer_note = ? ", $condition ["has_buyer_note"] );
		}
		if (isset ( $condition ["is_one_piece"] ) && $condition ["is_one_piece"] != '') {
			$select->where ( "is_one_piece = ? ", $condition ["is_one_piece"] );
		}
		
		if (isset ( $condition ["has_warehouse"] ) && $condition ["has_warehouse"] != '') {
			if ($condition ["has_warehouse"] > 0) {
				$select->where ( "warehouse_id is not null and warehouse_id != ''" );
			} else {
				$select->where ( "warehouse_id is null or warehouse_id = ''" );
			}
		}
		
		if (isset ( $condition ["has_operator_note"] ) && $condition ["has_operator_note"] != '') {
			if ($condition ["has_operator_note"] > 0) {
				$select->where ( "operator_note is not null and operator_note != ''" );
			} else {
				$select->where ( "operator_note is null or operator_note = ''" );
			}
		}
		
		if (isset ( $condition ["refrence_no_sys"] ) && $condition ["refrence_no_sys"] != "") {
			$select->where ( "refrence_no_sys = ?", $condition ["refrence_no_sys"] );
		}
		if (isset ( $condition ["process_again"] ) && $condition ["process_again"] != "") {
			$select->where ( "process_again = ?", $condition ["process_again"] );
		}
		if (isset ( $condition ["abnormal_type"] ) && $condition ["abnormal_type"] != "") {
			$select->where ( "abnormal_type = ?", $condition ["abnormal_type"] );
		}
		if (isset ( $condition ["keyword"] ) && $condition ["keyword"] != "") {
			$select->where ( "keyword like ?", "%{$condition['keyword']}%" );
		}
		if (isset ( $condition ["platform_ship_status"] ) && $condition ["platform_ship_status"] != "") {
			$select->where ( "platform_ship_status = ?", $condition ["platform_ship_status"] );
		}
		
		if (isset ( $condition ["case_exist"] ) && $condition ["case_exist"] != '') {
			if ($condition ["case_exist"] > 0) {
				$select->where ( "ebay_case_type is not null " );
			} else {
				$select->where ( "ebay_case_type is null " );
			}
		}
		
		if (isset ( $condition ["is_split_order"] ) && $condition ["is_split_order"] != '') {
			if ($condition ["is_split_order"] > 0) {
				$select->where ( "is_merge = ? ", $condition ["is_split_order"] );
			} else {
				$select->where ( "is_merge != '4' " );
			}
		}
		
		if (isset ( $condition ["has_pickup"] ) && $condition ["has_pickup"] != "") {
			$select->where ( "has_pickup = ?", $condition ["has_pickup"] );
		}
		if (isset ( $condition ["has_print_pickup_label"] ) && $condition ["has_print_pickup_label"] != "") {
			if ($condition ["has_print_pickup_label"] == '0') {
				$select->where ( "has_print_pickup_label = 0" );
			} else {
				$select->where ( "has_print_pickup_label != 0" );
			}
		}
		
		// --------------------------------------------
		
		// print_r($condition);
// 		echo ($select->__toString());
// 		exit;
		/* CONDITION_END */
		
		if ('count(*)' == $type) {
			return $this->_table->getAdapter ()->fetchOne ( $select );
		} else {
			if (! empty ( $orderBy )) {
				$select->order ( $orderBy );
			}
			if (! empty ( $groupBy )) {
				$select->group ( $groupBy );
				// file_put_contents(APPLICATION_PATH.'/../data/log/order_list_group.txt',
				// "\n\n".$select->__toString()."\n".';',FILE_APPEND);
			}
			if ($pageSize > 0 and $page > 0) {
				$start = ($page - 1) * $pageSize;
				$select->limit ( $pageSize, $start );
			}
			$sql = $select->__toString ();
			// file_put_contents(APPLICATION_PATH.'/../data/log/order_list_test.txt',
			// "\n\n".$sql."\n".';',FILE_APPEND);
			// echo ($select);exit;
			return $this->_table->getAdapter ()->fetchAll ( $sql );
		}
	}

	/**
	 *
	 * @param array $condition
	 * @param string $type
	 * @param int $pageSize
	 * @param int $page
	 * @param string $orderBy
	 * @return array string
	 */
	public function getByConditionJoinCsdOrder($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "", $groupBy = '') {
		$select = $this->_table->getAdapter ()->select ();
		$table = $this->_table->info ( 'name' );
		$select->from ( $table, $type );
		$select->joinInner("csd_order", "csd_order.shipper_hawbcode = {$table}.refrence_no", array());
		
		$select->where ( "1 =?", 1 );
	
		/* CONDITION_START */
	
		if (isset ( $condition ["csd_order_status"] ) && ! empty ( $condition ["csd_order_status"] )) {
			$select->where ( "csd_order.order_status = ?", $condition ["csd_order_status"] );
		}
		
		if (isset ( $condition ["order_id_arr"] ) && ! empty ( $condition ["order_id_arr"] )) {
			$select->where ( "order_id in (?)", $condition ["order_id_arr"] );
		}
		if (isset ( $condition ["not_order_id"] ) && $condition ["not_order_id"] != "") {
			$select->where ( "order_id != ?", $condition ["not_order_id"] );
		}
		if (isset ( $condition ["platform"] ) && $condition ["platform"] != "") {
			$select->where ( "platform = ?", $condition ["platform"] );
		}
	
		if (isset ( $condition ["ot_id"] ) && $condition ["ot_id"] != "") {
			$select->where ( "ot_id = ?", $condition ["ot_id"] );
		}
		if (isset ( $condition ["order_type"] ) && $condition ["order_type"] != "") {
			$select->where ( "order_type = ?", $condition ["order_type"] );
		}
	
		if (isset ( $condition ["create_type"] ) && $condition ["create_type"] != "") {
			$select->where ( "create_type = ?", $condition ["create_type"] );
		}
	
		if (isset ( $condition ["order_status"] ) && $condition ["order_status"] != "") {
			$select->where ( "orders.order_status = ?", $condition ["order_status"] );
		}
		if (isset ( $condition ["sub_status"] ) && $condition ["sub_status"] != "") {
			$select->where ( "sub_status = ?", $condition ["sub_status"] );
		}
	
		if (isset ( $condition ["order_status_arr"] ) && $condition ["order_status_arr"] != "") {
			$select->where ( "orders.order_status in (?)", $condition ["order_status_arr"] );
		}
		if (isset ( $condition ["create_method"] ) && $condition ["create_method"] != "") {
			$select->where ( "create_method = ?", $condition ["create_method"] );
		}
		if (isset ( $condition ["customer_id"] ) && $condition ["customer_id"] != "") {
			$select->where ( "customer_id = ?", $condition ["customer_id"] );
		}
		if (isset ( $condition ["company_code"] ) && $condition ["company_code"] != "") {
			$select->where ( "company_code = ?", $condition ["company_code"] );
		}
		if (isset ( $condition ["shipping_method"] ) && $condition ["shipping_method"] != "") {
			$select->where ( "shipping_method = ?", $condition ["shipping_method"] );
		}
		if (isset ( $condition ["shipping_method_platform"] ) && $condition ["shipping_method_platform"] != "") {
			$select->where ( "shipping_method_platform = ?", $condition ["shipping_method_platform"] );
		}
		if (isset ( $condition ["warehouse_id"] ) && $condition ["warehouse_id"] != "") {
			$select->where ( "warehouse_id = ?", $condition ["warehouse_id"] );
		}
	
		if (isset ( $condition ["warehouse_id_arr"] ) && ! empty ( $condition ["warehouse_id_arr"] )) {
			$select->where ( "warehouse_id in (?)", $condition ["warehouse_id_arr"] );
		}
	
		if (isset ( $condition ["order_desc"] ) && $condition ["order_desc"] != "") {
			$select->where ( "order_desc = ?", $condition ["order_desc"] );
		}
		if (isset ( $condition ["operator_id"] ) && $condition ["operator_id"] != "") {
			$select->where ( "operator_id = ?", $condition ["operator_id"] );
		}
		if (isset ( $condition ["refrence_no"] ) && $condition ["refrence_no"] != "") {
			$select->where ( "refrence_no = ?", $condition ["refrence_no"] );
		}
		if (isset ( $condition ["refrence_no_arr"] ) && ! empty ( $condition ["refrence_no_arr"] )) {
			$select->where ( "refrence_no in (?)", $condition ["refrence_no_arr"] );
		}
		if (isset ( $condition ["refrence_no_platform"] ) && $condition ["refrence_no_platform"] != "") {
			$select->where ( "refrence_no_platform = ?", $condition ["refrence_no_platform"] );
		}
		if (isset ( $condition ["refrence_no_platform_arr"] ) && ! empty ( $condition ["refrence_no_platform_arr"] )) {
			$select->where ( "refrence_no_platform in (?)", $condition ["refrence_no_platform_arr"] );
		}
	
		if (isset ( $condition ["shipping_address_id"] ) && $condition ["shipping_address_id"] != "") {
			$select->where ( "shipping_address_id = ?", $condition ["shipping_address_id"] );
		}
		if (isset ( $condition ["currency"] ) && $condition ["currency"] != "") {
			$select->where ( "currency = ?", $condition ["currency"] );
		}
		if (isset ( $condition ["refrence_no_warehouse"] ) && $condition ["refrence_no_warehouse"] != "") {
			$select->where ( "refrence_no_warehouse = ?", $condition ["refrence_no_warehouse"] );
		}
	
		if (isset ( $condition ["refrence_no_warehouse_arr"] ) && ! empty ( $condition ["refrence_no_warehouse_arr"] )) {
			$select->where ( "refrence_no_warehouse in (?)", $condition ["refrence_no_warehouse_arr"] );
		}
		if (isset ( $condition ["shipping_method_no"] ) && $condition ["shipping_method_no"] != "") {
			$select->where ( "shipping_method_no = ?", $condition ["shipping_method_no"] );
		}
	
		if (isset ( $condition ["shipping_method_no_arr"] ) && ! empty ( $condition ["shipping_method_no_arr"] )) {
			$select->where ( "shipping_method_no in (?)", $condition ["shipping_method_no_arr"] );
		}
		if (isset ( $condition ["sync_status"] ) && $condition ["sync_status"] != "") {
			$select->where ( "sync_status = ?", $condition ["sync_status"] );
		}
	
		if (isset ( $condition ["sync_status_arr"] ) && ! empty ( $condition ["sync_status_arr"] )) {
			$select->where ( "sync_status in (?)", $condition ["sync_status_arr"] );
		}
	
		if (isset ( $condition ["createDateFrom"] ) && $condition ["createDateFrom"] != "") {
			$select->where ( "date_create >= ?", $condition ["createDateFrom"] );
		}
	
		if (isset ( $condition ["createDateEnd"] ) && $condition ["createDateEnd"] != "") {
			$select->where ( "date_create <= ?", $condition ["createDateEnd"] );
		}
		if (isset ( $condition ["payDateFrom"] ) && $condition ["payDateFrom"] != "") {
			$select->where ( "date_paid_platform >= ?", $condition ["payDateFrom"] );
		}
	
		if (isset ( $condition ["payDateEnd"] ) && $condition ["payDateEnd"] != "") {
			$select->where ( "date_paid_platform <= ?", $condition ["payDateEnd"] );
		}
	
		if (isset ( $condition ["shipDateFrom"] ) && $condition ["shipDateFrom"] != "") {
			$select->where ( "date_warehouse_shipping >= ?", $condition ["shipDateFrom"] );
		}
	
		if (isset ( $condition ["shipDateEnd"] ) && $condition ["shipDateEnd"] != "") {
			$select->where ( "date_warehouse_shipping <= ?", $condition ["shipDateEnd"] );
		}
	
		if (isset ( $condition ["verifyDateFrom"] ) && $condition ["verifyDateFrom"] != "") {
			$select->where ( "date_release >= ?", $condition ["verifyDateFrom"] );
		}
	
		if (isset ( $condition ["verifyDateEnd"] ) && $condition ["verifyDateEnd"] != "") {
			$select->where ( "date_release <= ?", $condition ["verifyDateEnd"] );
		}
	
		if (isset ( $condition ["priceFrom"] ) && $condition ["priceFrom"] != "") {
			$select->where ( "amountpaid >= ?", $condition ["priceFrom"] );
		}
	
		if (isset ( $condition ["priceEnd"] ) && $condition ["priceEnd"] != "") {
			$select->where ( "amountpaid <= ?", $condition ["priceEnd"] );
		}
	
		if (isset ( $condition ["date_create_platform_start"] ) && $condition ["date_create_platform_start"] != "") {
			$select->where ( "date_create_platform >= ?", $condition ["date_create_platform_start"] );
		}
	
		if (isset ( $condition ["user_account_arr"] ) && ! empty ( $condition ["user_account_arr"] )) {
			$select->where ( "user_account in (?)", $condition ["user_account_arr"] );
		}
	
		if (isset ( $condition ["user_account"] ) && $condition ["user_account"] != '') {
			$select->where ( "user_account = ? ", $condition ["user_account"] );
		}
		if (isset ( $condition ["buyer_id"] ) && ! empty ( $condition ["buyer_id"] )) {
			$select->where ( "buyer_id = ? ", $condition ["buyer_id"] );
		}
	
		if (isset ( $condition ["buyer_id_arr"] ) && ! empty ( $condition ["buyer_id_arr"] )) {
			$select->where ( "buyer_id in (?) ", $condition ["buyer_id_arr"] );
		}
	
		if (isset ( $condition ["buyer_name"] ) && ! empty ( $condition ["buyer_name"] )) {
			$select->where ( "buyer_name = ? ", $condition ["buyer_name"] );
		}
		if (isset ( $condition ["buyer_mail"] ) && ! empty ( $condition ["buyer_mail"] )) {
			$select->where ( "buyer_mail = ? ", $condition ["buyer_mail"] );
		}
		if (isset ( $condition ["third_part_ship"] ) && $condition ["third_part_ship"] != '') {
			$select->where ( "third_part_ship = ? ", $condition ["third_part_ship"] );
		}
	
		if (isset ( $condition ["site"] ) && $condition ["site"] != '') {
			$select->where ( "site = ? ", $condition ["site"] );
		}
	
		if (isset ( $condition ["consignee_country"] ) && $condition ["consignee_country"] != '') {
			$select->where ( "consignee_country = ? ", $condition ["consignee_country"] );
		}
	
		if (isset ( $condition ["is_merge"] ) && $condition ["is_merge"] != '') {
			$select->where ( "is_merge = ? ", $condition ["is_merge"] );
		}
	
		if (isset ( $condition ["has_buyer_note"] ) && $condition ["has_buyer_note"] != '') {
			$select->where ( "has_buyer_note = ? ", $condition ["has_buyer_note"] );
		}
		if (isset ( $condition ["is_one_piece"] ) && $condition ["is_one_piece"] != '') {
			$select->where ( "is_one_piece = ? ", $condition ["is_one_piece"] );
		}
	
		if (isset ( $condition ["is_split_order"] ) && $condition ["is_split_order"] != '') {
			if ($condition ["is_split_order"] > 0) {
				$select->where ( "is_merge = ? ", $condition ["is_split_order"] );
			} else {
				$select->where ( "is_merge != '4' " );
			}
		}
	
	
		// --------------------------------------------
	
		// print_r($condition);
// 				echo ($select->__toString());
		// 		exit;
		/* CONDITION_END */
	
		if ('count(*)' == $type) {
			return $this->_table->getAdapter ()->fetchOne ( $select );
		} else {
			if (! empty ( $orderBy )) {
				$select->order ( $orderBy );
			}
			if (! empty ( $groupBy )) {
				$select->group ( $groupBy );
				// file_put_contents(APPLICATION_PATH.'/../data/log/order_list_group.txt',
				// "\n\n".$select->__toString()."\n".';',FILE_APPEND);
			}
			if ($pageSize > 0 and $page > 0) {
				$start = ($page - 1) * $pageSize;
				$select->limit ( $pageSize, $start );
			}
			$sql = $select->__toString ();
			// file_put_contents(APPLICATION_PATH.'/../data/log/order_list_test.txt',
			// "\n\n".$sql."\n".';',FILE_APPEND);
			// echo ($select);exit;
			return $this->_table->getAdapter ()->fetchAll ( $sql );
		}
	}
	
	/**
	 *
	 * @param array $condition        	
	 * @param string $type        	
	 * @param int $pageSize        	
	 * @param int $page        	
	 * @param string $orderBy        	
	 * @return array string
	 */
	public function getByConditionPaypal($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "") {
		$select = $this->_table->getAdapter ()->select ();
		$table = $this->_table->info ( 'name' );
		$select->from ( $table, $type );
		
		$opTable = new DbTable_ShippingAddress ();
		$tableShippingAddress = $opTable->info ( 'name' );
		if ('count(*)' == $type) {
			$select->joinInner ( $tableShippingAddress, $table . '.refrence_no_platform = ' . $tableShippingAddress . '.OrderID', null );
		} else {
			$select->joinInner ( $tableShippingAddress, $table . '.refrence_no_platform = ' . $tableShippingAddress . '.OrderID', array (
					'ShippingAddress_Id',
					'Name',
					'Street1',
					'Street2',
					'CityName',
					'StateOrProvince',
					'Country',
					'CountryName',
					'Phone',
					'PostalCode',
					'AddressID',
					'AddressOwner',
					'ExternalAddressID',
					'OrderID',
					'Plat_code',
					'company_code',
					'create_date_sys',
					'modify_date_sys' 
			) );
		}
		
		$select->where ( "1 =?", 1 );
		
		/* CONDITION_START */
		
		if (isset ( $condition ["order_id_arr"] ) && ! empty ( $condition ["order_id_arr"] )) {
			$select->where ( "order_id in (?)", $condition ["order_id_arr"] );
		}
		if (isset ( $condition ["platform"] ) && $condition ["platform"] != "") {
			$select->where ( "platform = ?", $condition ["platform"] );
		}
		if (isset ( $condition ["order_status"] ) && $condition ["order_status"] != "") {
			$select->where ( "order_status = ?", $condition ["order_status"] );
		}
		if (isset ( $condition ["create_method"] ) && $condition ["create_method"] != "") {
			$select->where ( "create_method = ?", $condition ["create_method"] );
		}
		if (isset ( $condition ["customer_id"] ) && $condition ["customer_id"] != "") {
			$select->where ( "customer_id = ?", $condition ["customer_id"] );
		}
		if (isset ( $condition ["company_code"] ) && $condition ["company_code"] != "") {
			$select->where ( "company_code = ?", $condition ["company_code"] );
		}
		if (isset ( $condition ["shipping_method"] ) && $condition ["shipping_method"] != "") {
			$select->where ( "shipping_method = ?", $condition ["shipping_method"] );
		}
		if (isset ( $condition ["shipping_method_platform"] ) && $condition ["shipping_method_platform"] != "") {
			$select->where ( "shipping_method_platform = ?", $condition ["shipping_method_platform"] );
		}
		if (isset ( $condition ["warehouse_id"] ) && $condition ["warehouse_id"] != "") {
			$select->where ( "warehouse_id = ?", $condition ["warehouse_id"] );
		}
		if (isset ( $condition ["order_desc"] ) && $condition ["order_desc"] != "") {
			$select->where ( "order_desc = ?", $condition ["order_desc"] );
		}
		if (isset ( $condition ["operator_id"] ) && $condition ["operator_id"] != "") {
			$select->where ( "operator_id = ?", $condition ["operator_id"] );
		}
		if (isset ( $condition ["refrence_no"] ) && $condition ["refrence_no"] != "") {
			$select->where ( "refrence_no = ?", $condition ["refrence_no"] );
		}
		if (isset ( $condition ["refrence_no_platform"] ) && $condition ["refrence_no_platform"] != "") {
			$select->where ( "refrence_no_platform = ?", $condition ["refrence_no_platform"] );
		}
		if (isset ( $condition ["refrence_no_platform_arr"] ) && ! empty ( $condition ["refrence_no_platform_arr"] )) {
			$select->where ( "refrence_no_platform in (?)", $condition ["refrence_no_platform_arr"] );
		}
		
		if (isset ( $condition ["shipping_address_id"] ) && $condition ["shipping_address_id"] != "") {
			$select->where ( "shipping_address_id = ?", $condition ["shipping_address_id"] );
		}
		if (isset ( $condition ["currency"] ) && $condition ["currency"] != "") {
			$select->where ( "currency = ?", $condition ["currency"] );
		}
		if (isset ( $condition ["refrence_no_warehouse"] ) && $condition ["refrence_no_warehouse"] != "") {
			$select->where ( "refrence_no_warehouse = ?", $condition ["refrence_no_warehouse"] );
		}
		
		if (isset ( $condition ["refrence_no_warehouse_arr"] ) && ! empty ( $condition ["refrence_no_warehouse_arr"] )) {
			$select->where ( "refrence_no_warehouse in(?)", $condition ["refrence_no_warehouse_arr"] );
		}
		if (isset ( $condition ["shipping_method_no"] ) && $condition ["shipping_method_no"] != "") {
			$select->where ( "shipping_method_no = ?", $condition ["shipping_method_no"] );
		}
		
		if (isset ( $condition ["shipping_method_no_arr"] ) && ! empty ( $condition ["shipping_method_no_arr"] )) {
			$select->where ( "shipping_method_no in (?)", $condition ["shipping_method_no_arr"] );
		}
		if (isset ( $condition ["sync_status"] ) && $condition ["sync_status"] != "") {
			$select->where ( "sync_status = ?", $condition ["sync_status"] );
		}
		
		if (isset ( $condition ["sync_status_arr"] ) && ! empty ( $condition ["sync_status_arr"] )) {
			$select->where ( "sync_status in (?)", $condition ["sync_status_arr"] );
		}
		
		if (isset ( $condition ["createDateFrom"] ) && $condition ["createDateFrom"] != "") {
			$select->where ( "date_create_platform >= ?", $condition ["createDateFrom"] );
		}
		
		if (isset ( $condition ["createDateEnd"] ) && $condition ["createDateEnd"] != "") {
			$select->where ( "date_create_platform <= ?", $condition ["createDateEnd"] );
		}
		if (isset ( $condition ["payDateFrom"] ) && $condition ["payDateFrom"] != "") {
			$select->where ( "date_paid_platform >= ?", $condition ["payDateFrom"] );
		}
		
		if (isset ( $condition ["payDateEnd"] ) && $condition ["payDateEnd"] != "") {
			$select->where ( "date_paid_platform <= ?", $condition ["payDateEnd"] );
		}
		
		if (isset ( $condition ["priceFrom"] ) && $condition ["priceFrom"] != "") {
			$select->where ( "amountpaid >= ?", $condition ["priceFrom"] );
		}
		
		if (isset ( $condition ["priceEnd"] ) && $condition ["priceEnd"] != "") {
			$select->where ( "amountpaid <= ?", $condition ["priceEnd"] );
		}
		
		if (isset ( $condition ["date_create_platform_start"] ) && $condition ["date_create_platform_start"] != "") {
			$select->where ( "date_create_platform >= ?", $condition ["date_create_platform_start"] );
		}
		
		if (isset ( $condition ["user_account_arr"] ) && ! empty ( $condition ["user_account_arr"] )) {
			$select->where ( $table . ".user_account in (?)", $condition ["user_account_arr"] );
		}
		
		if (isset ( $condition ["user_account"] ) && $condition ["user_account"] != '') {
			$select->where ( $table . ".user_account = ? ", $condition ["user_account"] );
		}
		if (isset ( $condition ["buyer_id"] ) && ! empty ( $condition ["buyer_id"] )) {
			$select->where ( "buyer_id = ? ", $condition ["buyer_id"] );
		}
		
		if (isset ( $condition ["buyer_id_arr"] ) && ! empty ( $condition ["buyer_id_arr"] )) {
			$select->where ( "buyer_id in (?) ", $condition ["buyer_id_arr"] );
		}
		if (isset ( $condition ["third_part_ship"] ) && $condition ["third_part_ship"] != '') {
			$select->where ( "third_part_ship = ? ", $condition ["third_part_ship"] );
		}
		
		if (isset ( $condition ["site"] ) && $condition ["site"] != '') {
			$select->where ( "site = ? ", $condition ["site"] );
		}
		
		if (isset ( $condition ["consignee_country"] ) && $condition ["consignee_country"] != '') {
			$select->where ( "consignee_country = ? ", $condition ["consignee_country"] );
		}
		
		if (isset ( $condition ["is_merge"] ) && $condition ["is_merge"] != '') {
			$select->where ( "is_merge = ? ", $condition ["is_merge"] );
		}
		
		if (isset ( $condition ["refrence_no_sys"] ) && $condition ["refrence_no_sys"] != "") {
			$select->where ( "refrence_no_sys = ?", $condition ["refrence_no_sys"] );
		}
		// --------------------------------------------
		
		/* CONDITION_END */
		
		if ('count(*)' == $type) {
			return $this->_table->getAdapter ()->fetchOne ( $select );
		} else {
			if (! empty ( $orderBy )) {
				$select->order ( $orderBy );
			}
			if ($pageSize > 0 and $page > 0) {
				$start = ($page - 1) * $pageSize;
				$select->limit ( $pageSize, $start );
			}
			$sql = $select->__toString ();
			// print_r($sql);
			return $this->_table->getAdapter ()->fetchAll ( $sql );
		}
	}
}