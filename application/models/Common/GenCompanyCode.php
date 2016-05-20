<?php
/**
 * 生成公司代码
 * @author Administrator
 *
 */
class Common_GenCompanyCode
{

    private function getNextPre($pre)
    {
        $notArr = array(
            'L',
            'O',
            'I'
        );
        $pre = Chr(Ord(strtoupper($pre)) + 1);
        
        if(in_array($pre, $notArr)){
            return $this->getNextPre($pre);
        }
        return $pre;
    }
    
    // 生成客户ID
    public static function genCompanyCodeLetter()
    {
        $pre = 'A';
        $dig = '001';
        $objConfig = new Table_Config();
        $customerRow = $objConfig->getByField('CURRENCY_CUSTOMER_ID', 'config_attribute');
        if(! empty($customerRow)){
            $pre = substr($customerRow['config_value'], 0, 1);
            $dig = substr($customerRow['config_value'], - 3);
        }else{
            $customerRow = array(
                'config_attribute' => 'CURRENCY_CUSTOMER_ID',
                'config_value' => $pre . $dig,
                'config_description' => '当前生成客户ID值',
                'config_add_time' => date('Y-m-d H:i:s'),
                'config_update_time' => date('Y-m-d H:i:s')
            );
            $customerRow['config_id'] = $objConfig->add($customerRow);
        }
        
        $userCode = $pre . str_pad($dig, 3, '0', STR_PAD_LEFT);
        
        $dig += 1;
        
        if($dig >= 100){
            $self = new Common_GenCompanyCode();
            $pre = $self->getNextPre($pre);
            $dig = '001';
        }
        $dig = str_pad($dig, 3, '0', STR_PAD_LEFT);
        
        $update = array(
            'config_value' => $pre . $dig
        );
        $objConfig->update($update, $customerRow['config_id']);
        
        return $userCode;
    }

    // 生成客户ID
    public static function genCompanyCode()
    {
    	$dig = 1;
    	$customerRow = Service_Config::getByField('CURRENCY_COMPANY_ID', 'config_attribute');
    	if(! empty($customerRow)){
    		$dig = $customerRow['config_value']+1;
    		$update = array(
    				'config_value' => $dig
    		);
    		Service_Config::update($update, $customerRow['config_id'],'config_id');
    	}else{    		
    		$customerRow = array(
    				'config_attribute' => 'CURRENCY_COMPANY_ID',
    				'config_value' => $dig,
    				'config_description' => '当前生成客户ID值',
    				'config_add_time' => date('Y-m-d H:i:s'),
    				'config_update_time' => date('Y-m-d H:i:s')
    		);
    		Service_Config::add($customerRow);
    	}
    
    	$userCode = sprintf('%06d',$dig);
    	return $userCode;
    }
}
