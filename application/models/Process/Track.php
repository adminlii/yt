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
            $result = Service_TakTrackingbusiness::getByField($server_hawbcode, 'server_hawbcode');
            if(! $result){
            	$result = Service_TakTrackingbusiness::getByField($server_hawbcode, 'shipper_hawbcode');
	            if(! $result){
	                throw new Exception(Ec::Lang('服务商跟踪号不存在'));
	            }else{
	            	$return['code_type']="shipper_hawbcode";
	            }
            }
            $con_detail = array(
                'tbs_id' => $result['tbs_id']
            );
            if(!$userId){//未登录客户过滤条件
                $con_detail['show_sign'] = 'Y';
            }
            
            // TODO DB2
            $db2 = Common_Common::getAdapterForDb2();
            
            $result_detail = Service_TakTrackdetails::getByCondition($con_detail, '*', 0, 1, "track_occur_date desc");
            foreach($result_detail as $k=>$v){
                $attach = Service_TakTrackattach::getByField($v['trk_id'],'trk_id');
                $v['track_description'] = $attach['track_description'];
                $v['track_description_en'] = $attach['track_description_en'];						
                if($v['track_code']){
                	$sql = "select * from tak_trackcode where track_code='{$v['track_code']}'";
//                 	echo $sql;exit;
					$row = $db2->fetchRow($sql);
					if($row){
                		$v['track_description'] = $row['track_cnname'];						
                		$v['track_description_en'] = $row['track_enname'];						
					}
                }
                $result_detail[$k] = $v;
            }
            $result['detail'] = $result_detail;
            
            $return['ask'] = 1;
            $return['message'] = Ec::Lang('获取跟踪信息完毕');
            $return['data'] = $result;
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