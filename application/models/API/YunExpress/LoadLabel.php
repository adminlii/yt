<?php
/**
 * 下载中邮标签
 * @author Administrator
 *
 */
class API_YunExpress_LoadLabel
{

    /**
     * @desc 加载标签
     * @return string
     */
    public function loadLabel()
    {
        // 返回结果
    	$return = array('ack' => '0', 
		    			'message' => '', 
    			);
    	
    	try {
    		
    		// 获取需要下载标签的渠道
    		$sql = "select config_value from config where config_attribute = 'LOAD_LABEL_PRODUCTCODE'";
    		$config = Common_Common::fetchOne($sql);
    		
    		if(empty($config)) {
    			Common_Common::myEcho("未配置标签下载渠道...");
    			return;
    		}
    		
    		$pageSize = 20;
    		$page = 1;
    		
    		$product_code_arr = explode(",", $config);
    		foreach($product_code_arr as $k => $product_code) {
    			
    			// 统计所有需要下载的标签
    			$count_sql = "SELECT
							count(1)
						FROM
							csd_order co
						WHERE
							not exists (
								SELECT
									1
								FROM
									order_label l
								WHERE
									l.order_code = co.shipper_hawbcode
							)
						AND co.product_code = '{$product_code}' and co.create_date > ADDDATE(now(),INTERVAL -2 day)";
    			
    			$count = Common_Common::fetchOne($count_sql);
    			$totalPage = ceil($count / $pageSize);
    			
    			while($page <= $totalPage) {
    				
    				// 取当页数据
    				$start = $page == 1 ? 0 : $page * $pageSize - 1;
    				
	    			$sql = "SELECT
								co.refer_hawbcode
							FROM
								csd_order co
							WHERE
								not exists (
									SELECT
										1
									FROM
										order_label l
									WHERE
										l.order_code = co.shipper_hawbcode
								)
							AND co.product_code = '{$product_code}' and co.create_date > ADDDATE(now(),INTERVAL -2 day) limit {$start}, {$pageSize};";
	    			
	    			$rows = Common_Common::fetchAll($sql);
	    			
	    			// 提取所有单号
	    			$codes = array();
	    			foreach($rows as $k => $row) {
	    				$codes[] = $row['refer_hawbcode'];
	    			}
	    			
	    			// 执行服务
	    			$result = $this->excuteService(json_encode($codes));
	    			
	    			// 保存标签
	    			$r = $this->saveLabel($result);
	    			
	    			$page++;
    			}
    		}
    		
    		
    	} catch (Exception $e) {
    		$return['ack'] = 0;
    		$return['message'] = $e->getMessage();
    	}
    	
    	return $return;
        
    }
    
    /**
     * 保存标签数据
     * @param unknown_type $result
     */
    private function saveLabel($result) {
    	
    	if($result['ack'] != 1) {
    		Common_Common::myEcho("标签下载失败...");
    		return;
    	}
    	
    	// 获取标签
    	$item = $result['data']['Item'];
    	foreach($item as $k => $row) {
    		
    		$order_code = $row['LabelPrintInfos']['0']['OrderNumber'];
    		if($row['LabelPrintInfos']['0']['ErrorCode'] != '100') {
    			Common_Common::myEcho($order_code . " 获取标签数据失败..." . print_r($row, true));
    			continue;
    		}
    		
    		$return = $this->excuteGetService($row['Url']);
    		if($return['ack'] == 0) {
    			Common_Common::myEcho($order_code . " 标签下载失败...");
    			continue;
    		}
    		
    		try {
    			
    			$csd_order = Service_CsdOrder::getByField($order_code, 'refer_hawbcode');
    			
    			// 文件
    			$path = APPLICATION_PATH . "/../data/pdf/" . $csd_order['shipper_hawbcode'];
    			$label_url = 'http://112.74.65.48/default/index/get-label/code/' . $csd_order['shipper_hawbcode'] . '.pdf';
    			
    			//创建文件夹
    			Common_Common::mkdirs($path);
    			file_put_contents($path . "/" . "0.pdf",  $return['data']);
    			
    			// 转成图片保存
    			//删除原标签
    			Service_OrderLabel::delete($csd_order['shipper_hawbcode'],'order_code');
    		
    			//加入到标签里面
    			$labelRow = array(
    					"order_code" => $csd_order['shipper_hawbcode'],
    					"path" => $path,
    					"ol_label_url" => $label_url,
    					"sm_code" => '',
    					"ol_label_url_ori" => $row['Url'],
    			);
    			Service_OrderLabel::add($labelRow);
    			
    			Common_Common::myEcho($order_code . " 标签保存成功...");
    			
    		} catch(Exception $e) {
    			$result["state"] = 0;
    			$result["message"] = "保存标签失败." . $e->getMessage();
    			return $result;
    		}
    	}
    }

    private function excuteService($params) {
    	
    	$url = "http://api.yunexpress.com/LMS.API.Lable/Api/PrintUrl";
    	
    	$result = array("ack"=>0,"message"=>"","data"=>"");
    	try {
    		
    		$tuCurl = curl_init();
    		curl_setopt($tuCurl, CURLOPT_URL, $url);
    		curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
    		curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
    		curl_setopt($tuCurl, CURLOPT_POST, 1);
    		curl_setopt($tuCurl, CURLOPT_CUSTOMREQUEST, 'POST');
    		curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $params);
    			
    		curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Content-Type: application/json; charset=utf-8", "Content-length: ".strlen($params)));
    
    		// print_r($tuCurl);die;
    		$data = curl_exec($tuCurl);
    			
    		Ec::showError("**************start*************\r\n"
    				. print_r($params, true)
    				. "\r\n"
    						. print_r($data, true)
    						. "**************end*************\r\n",
    						'Label/load'.date("Ymd"));
    			
    		$data = Common_Common::objectToArray(json_decode($data));
    		$result["ack"] = 1;
    		$result["data"] = $data;
    	} catch (Exception  $e) {
    		$result["message"] = $e->getMessage();
    	}
    
    	return $result;
    }

    private function excuteGetService($url) {
    	
    	$result = array("ack"=>0,"message"=>"","data"=>"");
    	
    	try {
    		
    		$tuCurl = curl_init();
    		curl_setopt($tuCurl, CURLOPT_URL, $url);
    		curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
    		curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
    		curl_setopt($tuCurl, CURLOPT_CUSTOMREQUEST, 'GET');
    
    		// print_r($tuCurl);die;
    		$data = curl_exec($tuCurl);
    		$result["ack"] = 1;
    		$result["data"] = $data;
    	} catch (Exception  $e) {
    		$result["message"] = $e->getMessage();
    	}
    
    	return $result;
    }
       
}