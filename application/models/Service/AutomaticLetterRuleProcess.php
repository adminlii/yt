<?php
/**
 * 自动发信规则--处理类
 * @author Frank
 * @date 2014-10-11 10:09:32
 */
class Service_AutomaticLetterRuleProcess
{
	private static $log_name = 'letter_err_';
	/**
	 * 运行规则，查找订单进行自动发信
	 */
    public function runLetterRule(){
    	$i = 1;
    	echo $i++ . '、进入自动发信服务<br/><br/>';
    	
    	/*
    	 * 查询规则
    	 */
    	echo $i++ . '、查询规则<br/><br/>';
    	$rule_con = array(
    				'status'=>'1'
    				);
    	$result_rule = Service_AutomaticLetterRule::getByCondition($rule_con, '*', 100, 1, "rule_level desc");
//     	print_r($result_rule);
		
    	if(empty($result_rule)){
    		$err_message = '未设置发信规则，退出';
    		echo $i++ . '、' . $err_message . '<br/><br/>';
    		Ec::showError($err_message,self::$log_name);
    		return;
    	}
    	
    	/*
    	 * 继续查询规则条件
    	 */
    	echo $i++ . '、继续查询规则条件<br/><br/>';
    	$ruleIds = array();
    	foreach ($result_rule as $key_ra => $value_ra) {
    		$ruleIds[] = $value_ra['alr_id'];
    	}
    	
    	$condition = array(
    				'alr_id_arr'=>$ruleIds,
    			);
    	$result_rule_condition = Service_AutomaticLetterRuleCondition::getByCondition($condition);
    	if(empty($result_rule_condition)){
    		$err_message = '发信规则条件未设置，退出';
    		echo $i++ . '、' . $err_message . '<br/><br/>';
    		Ec::showError($err_message,self::$log_name);
    		return;
    	}
    	
    	echo $i++ . '、组织数据准备查询符合规则的订单<br/><br/>';
    	//已规则ID为键值保存规则条件
    	$condtion_ruleid_arr = array();
    	foreach ($result_rule_condition as $key_rca => $value_rca) {
    		$condtion_ruleid_arr[$value_rca['alr_id']][] = $value_rca;
    	}
    	
    	//把规则条件，放入到规则里
    	$ruleListNew = array();
    	foreach ($result_rule as $k => $r){
    		$ruleListNew[$k]=$r;
    		$ruleListNew[$k]['condition']=$condtion_ruleid_arr[$r['alr_id']];
    	}
    	print_r($ruleListNew);
    	echo '<br/><br/>';
    	
    	/*
    	 * 并列每个规则下的conditions，得到查找条件
    	 */
    	echo $i++ . '、开始循环查询符合规则的订单<br/><br/>';
    	foreach ($ruleListNew as $key_rln => $value_rln) {
    		
    		$rule_condition = $value_rln['condition'];
    		$sql_condition = array();
    		try {
    			//验证模板是否正常
    			$obj_template_content = null;
    			$con_template_content = array(
    					'alt_id'=>$value_rln['alt_id'],
    					'language_code'=>$value_rln['language_code']
    					);
    			$result_template_content = Service_AutomaticLetterTemplateContent::getByCondition($con_template_content);
    			if(empty($result_template_content)){
    				throw new Exception('发信模板信息已丢失，发信规则：' . print_r($value_rln,true));
    			}else{
    				$obj_template_content = $result_template_content[0];
    				if($obj_template_content['altc_id'] != $value_rln['altc_id']){
    					$update_message = '发信规则【' . $value_rln['rule_name'] . '】，模板内容ID变更，更新为' . $obj_template_content['altc_id'];
    					echo $i++ . '、' . $update_message . '<br/><br/>';
    					Ec::showError($update_message,self::$log_name);
    					Service_AutomaticLetterRule::update(array('altc_id'=>$obj_template_content['altc_id']), $value_rln['alr_id'], 'alr_id');
    				}
    			}
    			
    			//拼接查询条件
	    		foreach ($rule_condition as $key_rc => $value_rc) {
	    			$con_type = $value_rc['condition_type'];
	    			$con_value = $value_rc['set_value'];
	    			switch ($con_type){
	    				case 'order_platform':
	    					if($con_value != 'ebay'){
	    						throw new Exception('发信平台暂时只支持eBay，退出，发信规则：' . print_r($value_rln,true));
	    					}
	    					$sql_condition['platform'] = $con_value;
	    					break;
	    					
	    				case 'user_account':
	    					$sql_condition['user_account_arr'] = explode(";", $con_value);
	    					break;
	    					
	    				case 'order_site':
	    					$sql_condition['site_arr'] = explode(";", $con_value);
	    					break;
	    					
	    				case 'shipping_method_platform':
	    					$sql_condition['shipping_method_platform_arr'] = explode(";", $con_value);
	    					break;
	    					
	    				case 'consignee_country':
	    					$sql_condition['consignee_country_arr'] = explode(";", $con_value);
	    					break;
	    					
	    				case 'letter_scene':
	    					if($con_value == 'order_check_out'){//订单出库时
	    						$sql_condition['order_status'] = '4';
	    						$sql_condition['automatic_letter_status'] = '0';
	    					}else if($con_value == 'order_expected_arrive'){//订单预计到货
	    						$sql_condition['order_status'] = '4';
	    						$sql_condition['automatic_letter_status_arr'] = array('0','1');
	    						$sql_condition['order_eta_date_greater_than'] = date('Y-m-d H:i:s');
	    					}else{
	    						throw new Exception('未定义的发送场景：' . $con_type . ';发信规则：' . print_r($value_rln,true));
	    					}
	    					
	    					break;
	    					
	    				default:
	    					throw new Exception('未定义条件类型：' . $con_type . ';发信规则：' . print_r($value_rln,true));
	    					break;
	    					
	    			}
	    		}
	    		
	    		$result_order = Service_Orders::getByCondition($sql_condition);
// 	    		print_r($result);
// 	    		echo '<br/><br/>';
	    		
	    		if(!empty($result_order)){
	    			echo $i++ . '、发信规则【' . $value_rln['rule_name'] . '】，匹配订单数据 ' . count($result_order) . ' 条<br/><br/>';
	    			foreach ($result_order as $v){
	    				$response = $this->letter($obj_template_content, $v, $value_rln);
	    				print_r($response);
	    				echo '<br><br>';
	    				if(!$response['aks']){
	    					$err_message =  '规则匹配正常，调用发信失败：' .print_r($response,true);
	    					Ec::showError($err_message,self::$log_name);
	    				}
	    			}
	    		}else{
	    			echo $i++ . '、发信规则【' . $value_rln['rule_name'] . '】，无匹配订单数据<br/><br/>';
	    		}
	    		
	    	} catch (Exception $e) {
	    		$err_message = '异常信息：' . $e->getMessage();
	    		echo $i++ . '、 ' . $err_message . '<br/><br/>';
	    		Ec::showError($err_message,self::$log_name);
	    	}
    	}
    	
    	
    }
    
    /**
     * 发信
     * @param unknown_type $template_content_row	发信模板数据
     * @param unknown_type $order					订单信息
     * @param unknown_type $letter_rule				规则值
     */
    public function letter($template_content_row,$order,$letter_rule){
    	echo '=====================<br/><br/>';
    	//标题头
    	$subject = $template_content_row['template_title'];
    	//内容-
    	$content_original = $template_content_row['template_content'];
    	$content = $content_original;
    	
    	//获得centent里的占位符
    	$i = 1001;
    	echo $i++ . '、订单：'. $order['refrence_no_platform'] .' 开始准备发信<br/><br/>';
    	echo $i++ . '、获取Content里的占位符<br/><br/>';
    	$operateArr = $this->getConentOprateVal($content);
    	print_r($operateArr);
    	echo '<br/><br/>';
    	
    	echo $i++ . '、获取占位符对应的数据<br/><br/>';
    	try {
	    	if(!empty($operateArr)){
		    	$templateOper = Service_MessageTemplateOperateProcess::getMsgTemplateOperatesValFromOrder($order, $operateArr);
	    	}
    	} catch (Exception $e) {
    		$err_message = '正文占位符数据获取异常：' . $e->getMessage() . ' 模板：' . print_r($template_content_row,true) . ' 订单ID：' . $order['order_id'] . ' 规则ID：' . $letter_rule['alr_id'];
    		echo $err_message . '<br/><br/>';
    		echo $e->getMessage();
    		$return = array(
    					'ask'=>'0',
    					'message'=>$err_message
    				);
    		return $return; 
    	}
    	
    	//替换centont里的字符
    	echo $i++ . '、替换Content的占位符<br/><br/>';
    	$operate_err_arr = array();
    	foreach ($templateOper as $o){
    		if($o['ask']==1){
    			$content = str_replace("{{".$o['code']."}}", $o['message'], $content);
    		}else{
    			$operate_err_arr[] = $o;
    		}
    	}
    	echo $i++ . '、检查是否有替换失败的占位符<br/><br/>';
    	if(!empty($operate_err_arr)){
    		$err_message = '存在获取失败的占位符，' . print_r($operate_err_arr,true) . ' 模板：' . print_r($template_content_row,true) . ' 订单ID：' . $order['order_id'] . ' 规则ID：' . $letter_rule['alr_id'];
    		$return = array(
    				'ask'=>'0',
    				'message'=>print_r($operate_err_arr,true),
    				'ref_no'=> $order['refrence_no_platform'],
    		);
    		return $return;
    	}
    	
    	print_r($subject);
    	echo '<br/><br/>';
    	print_r($content_original);
    	echo '<br/><br/>';
    	print_r($content);
    	echo '<br/><br/>';
    	echo $i++ . '、调用发信方法<br/><br/>';
    	$result = Service_EbayMessageProcess::autoLetterSaveEbayFeedbackMessageTransaction($order, $letter_rule, $subject, $content, $content_original);
    	
    	echo '=====================<br/><br/>';
    	return $result;
    }
    
    /**
     * 获得content里的变量
     */
    private function getConentOprateVal($content){
    	$operateArr=array();
    	$pattern  = '/\{\{\w+\}\}/' ;
    	preg_match_all( $pattern ,$content,$matches );
    	$matches=$matches[0];
    	if(empty($matches)){
    		return $operateArr;
    	}
    	foreach ($matches as $m){
    		$_v=$m;
    		$_v=str_replace("{{", "", $_v);
    		$_v=str_replace("}}", "", $_v);
    		$operateArr[]=$_v;
    	}
    	return $operateArr;
    }
}