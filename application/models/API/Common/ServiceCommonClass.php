<?php
/**
 * API公共查询类
 * @author Administrator
 *
 */
class API_Common_ServiceCommonClass
{
    /**
     * @desc 根据渠道代码获取对应的渠道数据
     * @param $formalCode
     * @return string
     */
    public static function getServiceChannelByFormalCode($formalCode)
    {
        $db = Common_Common::getAdapterForDb2();
        //获取 API代码
        $smSql = "SELECT
						sc.formal_code, sc.server_channelid, sc.server_product_code, s.as_code, s.as_docking_mode
					FROM
						csi_servechannel sc
					INNER JOIN csi_serveproperty sp ON sc.server_id = sp.server_id
					INNER JOIN api_service s ON s.as_id = s.as_id
					WHERE
						sc.formal_code ='{$formalCode}';";
//         print_r($smSql);
        $scRow = $db->fetchRow($smSql);
        if (empty($scRow)) {
            return array();
        }
        
        return $scRow;
    }
    
    /**
     * @desc
     * @param string $serviceCode
     * @return string
     */
    public static function getForApiServiceClass($serviceCode = "")
    {
    	if (empty($serviceCode)) {
    		return '';
    	}
    	$serviceCode = strtoupper($serviceCode);
    	$serviceClass = array(
    			"DEDHL" => 'API_DEDHL_ForApiService',
    			"EUBOFFLINE" => 'API_Epacket_ForApiService',
    			"EUBOFFLINE-CS" => 'API_Epacket_ForApiService',
    			"4PX" => 'API_4PX_ForApiService',
    			"YUNEXPRESS" => 'API_YunExpress_ForApiService',
    			"SGPPS"=>'API_SGPPS_ForApiService',
    	);
    	return isset($serviceClass[$serviceCode]) ? $serviceClass[$serviceCode] : '';
    }
}