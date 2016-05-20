<?php
class Common_AddressCheck_Validate
{
    // 产品代码
    private $_pk_code = '';
    // 国家代码
    private $_ct_code = '';

    private $_db = null;

    private $_err = array();
    // 要验证的元素
    private $_validateElements = array();
    // 订单头
    private $_order = array();
    // 发件人信息
    private $_shipper = array();
    // 收件人信息
    private $_consignee = array();
    // 申报信息
    private $_invoice = array();
    // 订单明细
    private $_order_product = array();

    public function __construct()
    {
        $this->_db = Common_Common::getAdapterForDb2();
    }

    public function setPkCode($product_code)
    {
        $this->_pk_code = $product_code;
    }

    public function setCtCode($country_code)
    {
        $this->_ct_code = $country_code;
    }
    
    public function setConsignee($consignee)
    {
        $this->_consignee = $consignee;
    }
    
    public function setInvoice($invoice)
    {
        $this->_invoice = $invoice;
    }
    
    public function setVolume($volume){
    	$this->_volume=$volume;
    }

    /**
     * 根据产品代码和国家代码，找到匹配的地址校验规则
     *
     * @param unknown_type $pk_code            
     * @param unknown_type $ct_code            
     */
    private function _getValidateElements($pk_code, $ct_code)
    {
        $sql = "select a.*,b.cace_name,b.cace_ename from cfg_address_check a 
        		inner join cfg_address_check_summary s ON a.cacs_id = s.cacs_id
                inner join cfg_address_check_element b on b.cace_value=a.field_name
                where 
                1=1 
                and product_code='{$pk_code}'                 
                and country_code='{$ct_code}' 
                order by 
                field_name asc ,
                check_type asc
            ;";
//         print_r($sql);die;
        return $this->_db->fetchAll($sql);
    }

    /**
     * 获取验证规则
     */
    public function getCheckElements()
    {
        if(empty($this->_pk_code)){
            $this->_err[] = Ec::Lang('产品代码不可为空');
        }
        if(empty($this->_ct_code)){
            $this->_err[] = Ec::Lang('国家代码不可为空');
        }
        if($this->_pk_code && $this->_ct_code){
            $pk_code = $this->_pk_code;
            $ct_code = $this->_ct_code;
            $validateElements = $this->_getValidateElements($pk_code, $ct_code);
            if(empty($validateElements)){
                $ct_code = ''; // 取全部产品
                $validateElements = $this->_getValidateElements($pk_code, $ct_code);
            }
            $validateElementArr = array();
            foreach($validateElements as $v){
                $validateElementArr[$v['field_name']][] = $v;
            }
             //print_r($validateElementArr);die();
            $this->_validateElements = $validateElementArr;
        }
    }
    
    // 内容校验
    private function _validateElementSingle($validateArr,$key, $val)
    {
//     	print_r($validateArr);die;
        // E:必填校验、D:数据校验、W:非填写校验、P(通用邮编校验)，S(通用州全称校验)，A(通用州简称校验)。
        foreach($validateArr as $v_element){
            switch($v_element['check_type']){
                case 'D': //
                    $check_data = $v_element['check_data']; // 正则表达式
                    $check_data = trim($check_data);
                    if(!preg_match('/\/.+\//', $check_data)){//兼容历史设置
                    	$check_data = '/'.$check_data.'/';
                    }
                    if(! preg_match($check_data, $val)){
                        $this->_err[] =  "[{$val}] ". $v_element['cace_name'] . ' ' . $v_element['error_code_cn'];
                    }
                    break;
                case 'W': //
                    $check_data = $v_element['check_data']; // 正则表达式
                    $check_data = trim($check_data);
                    if(!preg_match('/\/.+\//', $check_data)){//兼容历史设置
                    	$check_data = '/'.$check_data.'/';
                    }
                    if(preg_match($check_data, $val)){
                        $this->_err[] = "[{$val}] ". $v_element['cace_name'] . ' ' . $v_element['error_code_cn'];
                    }
                    break;
                case 'E':
                    if($val === ''){
                        $this->_err[] = "[{$val}] ". $v_element['cace_name'] . ' ' . $v_element['error_code_cn'];
                    }
                    
                    break;
                case 'U':
                    if($val !== ''){
                        $this->_err[] = "[{$val}] ". $v_element['cace_name'] . ' ' . $v_element['error_code_cn'];
                    }
                    break;
                case 'P':    
                	if(empty($val)) {
                		break;
                	}
                	            	
                	// 通用邮编规则校验
                	$sql = "select postcode_rule from pbr_country_postcode_rule where country_code = '{$this->_ct_code}'";
                	$postcode_rule = $this->_db->fetchOne($sql);
                    if(!empty($postcode_rule)){
                    	$check_data = $postcode_rule; // 正则表达式
                    	$check_data = trim($check_data);
                    	if(!preg_match('/\/.+\//', $check_data)){//兼容历史设置
                    		$check_data = '/'.$check_data.'/';
                    	}
                    	
                    	if(!preg_match($check_data, $val)){
                        	$this->_err[] = "[{$val}] ". $v_element['cace_name'] . ' ' . $v_element['error_code_cn'];
                    	}
                    }
                    break;
                case 'S':                	
                	if(empty($val)) {
                		break;
                	}                	
                    $sql = "select state_id from idd_state where country_code = '{$this->_ct_code}' and (state_enname = '{$val}' OR state_shortname = '$val')";
                    $state = $this->_db->fetchOne($sql);
                	if(empty($state)) {
                		$this->_err[] = "[{$val}] ". $v_element['cace_name'] . ' ' . $v_element['error_code_cn'];
                	}
                    break;
                case 'A':
                	if(empty($val)) {
                		break;
                	}
                	
                    $sql = "select state_shortname from idd_state where country_code = '{$this->_ct_code}' and (state_enname = '{$val}' OR state_shortname = '$val')";
                    $state = $this->_db->fetchOne($sql);
                	if(empty($state)) {
                		$this->_err[] = "[{$val}] ". $v_element['cace_name'] . ' ' . $v_element['error_code_cn'];
                	} else {
                		$this->_consignee['consignee_province'] = $state;
                	}
                    break;
                default:
                    $this->_err[] = "[{$val}] ". $v_element['cace_name'] . ' ' . $v_element['error_code_cn'] . Ec::Lang('校验类型错误');
            }
        }
    }

    private function _validateElements()
    {
        if(empty($this->_validateElements)){
            $this->getCheckElements();
        }
        $caces = $this->_validateElements;
        
        $consignee = array(
            'consignee_name' => $this->_consignee['consignee_name'],
            'consignee_company' => $this->_consignee['consignee_company'],
            'consignee_province' => $this->_consignee['consignee_province'],
            'consignee_city' => $this->_consignee['consignee_city'],
            'consignee_street' => $this->_consignee['consignee_street'],
            'consignee_postcode' => $this->_consignee['consignee_postcode'],
            'consignee_areacode' => $this->_consignee['consignee_areacode'],
            'consignee_telephone' => $this->_consignee['consignee_telephone'],
            'consignee_fax' => $this->_consignee['consignee_fax'],
            'consignee_email' => $this->_consignee['consignee_email'],
            'consignee_doorplate' => $this->_consignee['consignee_doorplate'],
        );
        
        $consignee = array_merge($this->_consignee,$consignee);
        //print_r($consignee);die;
        foreach($consignee as $k => $v){
            if(isset($caces[$k])){ // 校验
                $this->_validateElementSingle($caces[$k],$k, $v);
            }
        }
        
        // 验证申报信息
    	foreach($this->_invoice as $invoice_arr){
    		foreach ($invoice_arr as $k => $v)
            if(isset($caces[$k])){ // 校验
                $this->_validateElementSingle($caces[$k],$k, $v);
            }
        }
        //体积验证
        
        	foreach ($this->_volume as $k=>$v){
        		
        		if (isset($caces[$k])){
        			if (empty($v)){
        				$this->_err[]="体积必须三边都有值";
        				//throw new Exception("体积必须三边都有值");
        			}
        			$this->_validateElementSingle($caces[$k], $k, $v);
        		}else{
        			$this->_volume[$k]=0;
        		}
        	}
        	
     
       
    }

    /**
     * 地址校验
     *
     * @throws Exception
     * @return multitype:number string NULL multitype:
     */
    public function validate()
    {
        $return = array(
            'ask' => 0,
            'message' => ''
        );
        try{
            if(empty($this->_pk_code)){
                $this->_err[] = '请初始化产品代码';
            }
            if(empty($this->_ct_code)){
                $this->_err[] = '请初始化收件人国家';
            }
            if(empty($this->_consignee)){
                $this->_err[] = '请初始化收件人信息';
            }
            if(! empty($this->_err)){
                throw new Exception('初始化数据失败');
            }
            $this->_validateElements();
            
            if(! empty($this->_err)){
                throw new Exception('信息不合法');
            }
            $return['ask'] = 1;
            $return['message'] = 'Success';
            $return['consignee'] = $this->_consignee;
            $return['volume']=$this->_volume;
            //
        }catch(Exception $e){
            //
            $return['message'] = $e->getMessage();
        }
        $return['err'] = $this->_err;
        return $return;
    }
}