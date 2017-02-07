<?php

/**
 * @desc 对接系统后台的api方法
 */
class Common_ApiServiceAdmin
{
    protected $_token = 'zt6280xj6RYx.2REVvZ0!s4c';

    /**
     *
     * @param $req
     * @throws Exception
     */
    private function authentication($req)
    {
    	$return = array('ret'=>-1,'msg'=>'','data'=>array());
    	do{
    		try {
    			$req = $this->filterReq($req);
    			if(empty($req['token'])){
    				$return['ret'] = 5;
    				$return['msg'] = Ec::Lang('token值缺失');
    				break;
    			}
    			if($req['token']!=$this->_token){
    				$return['ret'] = 7;
    				$return['msg'] = Ec::Lang('token不正确');
    				break;
    			}
    			$return['ret'] = 0;
    		} catch (Exception $e) {
    			$return['ret'] = -13;
    			$return['msg'] = Ec::Lang('服务器繁忙请稍后尝试');
    			break;
    		}
    	}while(0);
    	return $return;
    }
	
    /**
     * 遍历请求串，先过滤字符，因为是用于接口，所以只考虑1维数组
     */
    private function filterReq($req){
    	array_walk($array, function (&$value,$key){
    		$value = trim(htmlspecialchars(addslashes($value)));
    	});
    	return $req;
    }
    /**
     * 接口入口
     *
     * @param $req
     * @return array
     */
    public function callService($req)
    {
    	$return = array('ret'=>-1,'msg'=>'','data'=>array());
    	do{
	        try {
	            // 数据验证
	            $authrs = $this->authentication($req);
	            if($authrs['ret']!=0){
	            	$return = $authrs;
	            	break;
	            }
	            $service = $req ['service'];
	            unset($req ['service']);	           
	            $return = $this->$service ($req);
	        } catch (Exception $e) {
	        	$return['ret'] = -13;
	        	$return['msg'] = Ec::Lang('服务器繁忙请稍后尝试');
	        	break;
	        }
        }while(0);
        return $return;
    }
    
    /**
     * @todo 获取标签图片
     * 
     */
    public function getLabel($req){
    	$return = array('ret'=>-1,'msg'=>'','data'=>array());
    	do{
    		try {
    			
    			$shipper_hawbcode = $req['order_num'];
    			if(empty($shipper_hawbcode)){
    				$return['ret'] = 15;
    				$return['msg'] = Ec::Lang('查询单号为空');
    				break;
    			}
    			$type = 'org';
    			$order = Service_CsdOrder::getByField($shipper_hawbcode, 'shipper_hawbcode');
    			if(! $order){
    				//去fba里查
    				$type = 'fba';
    				$order = Service_CsdOrderfba::getByField($shipper_hawbcode, 'shipper_hawbcode');
    				if(! $order){
    					$return['ret'] = 16;
    					$return['msg'] = Ec::Lang('订单不存在');
    					break;
    				}
    			}
    			if($type=='org'){
    				//如果订单不是已预报给出提示
    				if($order['order_status']=='D'){
    					$return['ret'] = 23;
    					$return['msg'] = Ec::Lang('该订单是问题件');
    					break;
    				}
    				if($order['order_status']=='S'){
    					$return['ret'] = 24;
    					$return['msg'] = Ec::Lang('该订单还在预报中');
    					break;
    				}
    				if(!empty($order['small_hawbcode'])){
    					$order['server_hawbcode'] = $order['small_hawbcode'];
    				}
    				$printParam["Data"][] = $order['server_hawbcode'];
    				$printParam["Version"] = "0.0.0.3";
    				$printParam["RequestTime"] = date("Y-m-d H:i:s");
    				$printParam["RequestId"] = "a2b23daa-a519-48cc-b5c6-e0ebbfeada2b";
    				$pdfPrintParamJson = Zend_Json::encode($printParam);
    				$process = new Common_FastReport ('');
    				//$return = $process->MakeLableFileToBase64($configInfoJson, $orderInfoJson, $pdfPrintInfoJson);
    				$res = $process->PrintLabel($pdfPrintParamJson, "POST");
    				if($res['ack'] == 1) {
    					$pdfData = $res["data"]["Data"];
    					$return['ret'] = 0;
    					$return['data'] = $pdfData;
    					break;
    				} else {
    					$return['ret'] = 1001;
    					$return['msg'] = $res['message'];;
    					break;
    				}
    		   
    			}else{
    				//创建文件
    				$savepath = APPLICATION_PATH.'/../public/fba/print/';
    				do{
    					$filename = date('YmdHis').'_'.rand(1, 10000);
    				}while(file_exists($savepath.$filename.'.pdf'));
    				$htmlFileName = "http://".$_SERVER['HTTP_HOST'].'/default/index/printfba1?orderId='.$order['order_id'];
    				$pdfFileName  = $savepath.$filename.'.pdf';
    				//shell调用xml
    				if(!file_exists($pdfFileName)){
    					if(ENVIRONMENT=='dev')
    						shell_exec("wkhtmltopdf --page-height 150 --page-width 100 --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 {$htmlFileName} {$pdfFileName}");
    					else
    						exec("/usr/local/wkhtmltox/bin/./wkhtmltopdf --page-height 150 --page-width 100 --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 {$htmlFileName} {$pdfFileName}");
    				}
    				//创建失败
    				if(!file_exists($pdfFileName)){
    					$return['ret'] = 204;
    					$return['msg'] =  Ec::Lang('系统繁忙，请稍后重试');
    					break;
    				}else{
    					$pdfData = base64_encode(file_get_contents($pdfFileName));
    					$return['ret'] = 0;
    					$return['data'] = $pdfData;
    					break;
    				}
    			}
    		} catch (Exception $e) {
    			$return['ret'] = -13;
    			$return['msg'] = Ec::Lang('服务器繁忙请稍后尝试');
    			break;
    		}
    	}while(0);
    	return $return;
    }

    public function getTrackDetail($req){
    	$return = array('ret'=>-1,'msg'=>'','data'=>array());
    	do{
    		try {
    			$code = $req['order_code'];
    			$order_code = trim($code);
    			if(empty($order_code)){
    				$return['ret'] = 24;
    				$return['msg'] = Ec::Lang('没有请求单号');
    				break;
    			}	
    			$rsArr = array();
    			$rs = Process_Track::getTrackDetail($order_code);
    			if($rs['ask']){
    				$_server_hawbcode = $rs['data']['server_hawbcode'];
    				if(!empty($rs['data']['small_hawbcode'])){
    					$_server_hawbcode = $rs['data']['small_hawbcode'];
    				}
    			}
	 
    			$obj  = 	new API_YunExpress_ForApiService();
    			//插入头程
    			$gettrackDetail_rs = $obj->gettrackDetail(2,array("server_code"=>$_server_hawbcode));
    			if($gettrackDetail_rs['ack']==1){
    				$data = Common_Common::xml_to_array($gettrackDetail_rs['data']);
    				//头插入
    				$tracklist = $data["trace"];
				if(is_array($tracklist)&&!is_array($tracklist[0])){
					$_tracklist  = array();	
					$_tracklist[0] = $tracklist; 
					$tracklist = $_tracklist; 
				}	
				//print_r($tracklist);
    				//更换key值
    				foreach ($tracklist as $key=>$val){
    					$tracklist[$key]['Datetime'] = $val['acceptTime'];
    					$tracklist[$key]['Location'] = $val['acceptAddress'];
    					$tracklist[$key]['Info']     = $val['remark'];
    				}
    				if(is_array($tracklist)&&count($tracklist)>0){
    					$rs['data']['detail'] = array_merge(array_reverse($tracklist),$rs['data']['detail']);
    				}
    			}
    			//根据订单获取渠道号
    			//不直接插最简单的order_prossing 找渠道 是因为没有给他设置索引
    			//$orderinfo = Service_CsdOrder::getByField($server_hawbcode,$rs['code_type']);
    			/* if(empty($orderinfo))
    			 continue; */
    			if($rs['code_type']=='FBA'){
    				$_server_hawbcode = $rs['data']['shipper_hawbcode'];
    				$rs['data']['country_code'] = $rs['data']['consignee_countrycode'];
    				$rs['data']['consignee_name'] = $rs['data']['storage'];
    				$channcel = "YunTu";
    			}else{
    				$codeType = Common_Common::getProductAllByCode($rs['data']['product_code']);
    				$channcel = $codeType['ccode'];
    			}			
    			$gettrackDetail_rs = $obj->gettrackDetail(1,array("server_code"=>$_server_hawbcode,"channel"=>$channcel));
    			if($gettrackDetail_rs['ack']==1){
    				$data = json_decode($gettrackDetail_rs['data'],1);
    				//头插入
    				$tracklist = $data["Data"]["TEvent"];
    				if(is_array($tracklist)&&count($tracklist)>0){
    					//倒序合并
    					$rs['data']['detail'] = array_merge(array_reverse($tracklist),$rs['data']['detail']);
    				}
    			}
    			//print_r($rs['detail']);
    			$return = $rs;		
    		} catch (Exception $e) {
    			$return['ret'] = -13;
    			$return['msg'] = Ec::Lang('服务器繁忙请稍后尝试');
    			break;
    		}
    	}while(0);
    	return $return;
    }
}