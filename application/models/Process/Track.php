<?php
class Process_Track
{

    public static function getTrackDetail($server_hawbcode)
    {
        $return = array(
            'ask' => 0,
            'message' => Ec::Lang('服务商跟踪号不存在')
        );
        $return['server_hawbcode'] = $server_hawbcode;
        $return['code_type']="server_hawbcode";
        try{
            $userId = Service_User::getUserId();
            $result = Service_CsdOrder::getByCondition(array("server_hawbcode"=>$server_hawbcode));
            if(! $result[0]){
            	$result = Service_CsdOrder::getByCondition(array("shipper_hawbcode"=>$server_hawbcode));
            	if(! $result[0]){
            		
            		//查询fba
            		$result = Service_CsdOrderfba::getByCondition(array("shipper_hawbcode"=>$server_hawbcode));
            		if(!$result[0]){
            			throw new Exception(Ec::Lang('服务商跟踪号不存在'));
            		}else{
            			$return['code_type'] = 'FBA';
            		}
            		
            	}else{
            		$return['code_type']="shipper_hawbcode";
            	}
            }else{
            	$return['code_type']="server_hawbcode";
            }   
            if($return['code_type']=="server_hawbcode"&&empty($result[0]["server_hawbcode"])){
            	throw new Exception(Ec::Lang('服务商跟踪号不存在或未预报成功'));
            }
            $result[0]['detail'] = array();
            
            $return['ask'] = 1;
            $return['message'] = Ec::Lang('获取跟踪信息完毕');
            $return['data'] = $result[0];
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    
    /**
     * 获取轨迹数据
     * @param unknown_type $server_hawbcode
     * @throws Exception
     * @return multitype:number unknown NULL Ambigous <mixed, multitype:, string>
     */
    public static function getTrackDetailForApi($server_hawbcode)
    {
    	// 轨迹返回对象
    	$return = array(
    			'Code' => $server_hawbcode,
    			'Country_code' => '', 	// 目的国家
    			'New_date' => '',		// 最近的轨迹时间
    			'New_Comment' => '',	// 最近的轨迹内容
    			'Status' => '',			// 最近的轨迹状态
    			'Detail' => array(),	// 轨迹明细
    	);
    	
    	try {
    		
    		// 根据单号查询轨迹数据
    		$result = Service_TakTrackingbusiness::getByField($server_hawbcode, 'server_hawbcode');
            if(!$result){
            	$result = Service_TakTrackingbusiness::getByField($server_hawbcode, 'shipper_hawbcode');
	            if(!$result) {
	            	// 不存在业务时，直接返回
	               	return $return;
	            }
            }
            
            // 轨迹业务
            $return['Country_code'] = $result['country_code'];
            $return['New_date'] = $result['new_track_date'];
            $return['New_Comment'] = $result['new_track_comment'];
            $return['Status'] = $result['track_status'];
            
            // 明细数据
            $detail = array();
            // 查询明细
    		$con_detail = array('tbs_id' => $result['tbs_id'], 'show_sign' => 'Y');
    		$result_detail = Service_TakTrackdetails::getByConditionJoinAttach($con_detail, '*', 0, 1, "track_occur_date desc");
    		foreach($result_detail as $k=>$v) {
    			$detail[] = array(
    					'Occur_date' => $v['track_occur_date'],
    					'Status' => '',
    					'Comment' => empty($v['track_code']) ? $v['track_description'] : $v['track_cnname'].' '.$v['track_enname'],
    					);
    		}
    		
    		$return['Detail'] = $detail;
    	} catch(Exception $e){
    		// 异常返回空数据
    	}
    	
    	return $return;
    }
}