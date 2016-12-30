<?php
/**
 * 物流信息推送大网API公共方法
 * @author Administrator
 *
 */
class API_Common_TraceServiceCommonClass
{
	private $apiUrl = 'http://211.156.193.145:8009/LogisticsAPI/api/gateway';
    private $partner_id = 'test';
    private $partner_secret = 'test';
    //推送EMS
    public function sendEmsApi($orderid,$param){
    	$callResult = array("ack"=>0,'msg'=>'','data'=>'');
    	$postArr = array();
    	$postArr['partner_id'] = $this->partner_id;
    	$postArr['msg_type'] = "TRACE_INFO";
    	$postArr['msg_id'] = $orderid.time().mt_rand(1,99999);
    	$postArr['msg_content'] = $param	;
    	$postArr['format'] = "xml";
    	//
    	$postArr['data_digest'] = $this->makeSign($param);
    	$postArr['charset'] = "UTF8";
    	$rs = $this->curl_send($this->apiUrl,$postArr,array(),'post');
    	
    	if(is_array($rs)){
    		$callResult['ack'] = -1;
    		$callResult['msg'] = $rs['error'];
    	}else if(empty($rs)){
    		$callResult['ack'] = -2;
    		$callResult['msg'] = '没有消息返回';
    	}
    	$callResult['ack'] = 1;
    	$callResult['data'] = Common_Common::xml_to_array($rs);
    	Ec::showError("**************start*************\r\n"
    			. print_r($postArr, true)
    			. "\r\n" . print_r($rs, true)
    			. "**************end*************\r\n",
    			'Trace_API/sendEms_info'.date("Ymd"));
    	return $callResult;
    }
    
    private function makeSign($param){
    	
    	$param.=$this->partner_secret;
    	echo $param.'<br>';
    	return base64_encode(md5($param,1));
    }
    //更新主表
    public  function updateFba($id,$data){
    	$db = Common_Common::getAdapter();
    	$where = $db->quoteInto('order_id = ?', $id);
    	$db->update('csd_orderfba', $data, $where);
    }
    
    //查询 订单 上次物流记录
    public function getLastQueryTrace($shipper_hawbcode){
    	if(empty($shipper_hawbcode))
    		return array();
    	$sql = "select * from get_trace_log where shipper_hawbcode='{$shipper_hawbcode}'";
    	return Common_Common::fetchRow($sql);
    }
    
    //更新物流记录
    public  function updateTrace($shipper_hawbcode,$msg){
    	$db = Common_Common::getAdapter();
    	$where = $db->quoteInto('shipper_hawbcode = ?', $shipper_hawbcode);
    	$set = array (
    			'msg' =>$msg,
    			'updatedate'=>date('Y-m-d H:i:s'),
    	);
    	$db->update('get_trace_log', $set, $where);
    }
    //插入物流记录
    public function addTrace($shipper_hawbcode,$msg){
    	$db = Common_Common::getAdapter();
    	$row['shipper_hawbcode']= $shipper_hawbcode;
    	$row['msg']= $msg;
    	$row['createdate']= date('Y-m-d H:i:s');
    	$row['updatedate']= $row['createdate'];
    	$db->insert('get_trace_log', $row);
    }
    //插入失败日志表
    public function addErrorlog($shipper_hawbcode,$msg){
    	$msg = addslashes($msg);
    	$db = Common_Common::getAdapter();
    	$row['shipper_hawbcode']= $shipper_hawbcode;
    	$row['msg_content']= $msg;
    	$row['createdate']= date('Y-m-d H:i:s');
    	$row['notifydate']= $row['createdate'];
    	$db->insert('notifytrace_errlog', $row);
    }
    //更新失败日志表
    public  function updateErrorLog($id,$updateTime,$num){
    	$db = Common_Common::getAdapter();
    	$where = $db->quoteInto('id = ?', $id);
    	$set = array (
    			'notifydate'=>$updateTime,
    			'num'=>$num+1,
    	);
    	$db->update('notifytrace_errlog', $set, $where);
    }
    //修改主表状态
    public function  curl_send($url,$data='',$header=array(),$type='get',$authentication=false){
    	$curl = curl_init();
    	curl_setopt($curl,CURLOPT_URL,$url);
    	curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
    	curl_setopt($curl ,CURLOPT_SSL_VERIFYPEER,false);
    	curl_setopt($curl ,CURLOPT_SSL_VERIFYHOST,FALSE);
    	curl_setopt($curl,CURLOPT_USERAGENT,'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Trident/6.0)');
    	curl_setopt($curl,CURLOPT_FOLLOWLOCATION,1);
    	curl_setopt($curl,CURLOPT_AUTOREFERER,1);
    	if('post' === $type){
    		curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    		curl_setopt($curl,CURLOPT_POST,1);
    		curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
    	}
    	if(!empty($header)){
    		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    	}
    	if($authentication){
    		curl_setopt($curl, CURLOPT_USERPWD, $authentication);
    	}
    	$result = curl_exec($curl);
    	if(curl_errno($curl) != 0){
    		$error = '发送CURL时发生错误:'.curl_error($curl).'(code:'.curl_errno($curl).')'.PHP_EOL;
    		curl_close($curl);
    		return array("error"=>$error);
    	}
    	curl_close($curl);
    	return $result;
    }
    
    //映射
   	public function getEventCode($code){
   		$_code = 'O_005';
   		if($code>1000){
   			$code = (string)$code ; 
   			if($code{0}==1){
   				$_code = 'O_'.substr($code, 1);
   			}else if($code{0}==2){
   				$_code = 'C_'.substr($code, 1);
   			}
   		}else if($code>0&&$code<100){
   			$_code = false;
   		}
   		return $_code;
   	}
}