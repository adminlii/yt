<?php
class Test_YunExpressTestController extends Ec_Controller_Action
{
	public function preDispatch(){
	}
	
	/**
	 *
	 */
	public function foreOrderAction() {
		$url = "http://112.74.65.48/api/Order/PacketOrder";
		 
		$json = "
		{
		\"CustomerCode\": \"1002\",
		\"packageMessage\": [
		{
		\"ChannelCode\": \"DHLCN\",
		\"ForecastNumber\": \"YT1530910000005200001\",
		\"Weight\": 0.5,
		\"Length\": 4.0,
		\"Width\": 5.0,
		\"Height\": 6.0,
		\"ShippingCountryCode\": \"GB\",
		\"ShippingFirstName\": \"jack\",
		\"ShippingLastName\": \" Mr\",
		\"ShippingCompany\": \"YunTu\",
		\"ShippingAddress1\": \"guandong dasha\",
		\"ShippingAddress2\": \"\",
		\"ShippingAddress3\": \"\",
		\"ShippingCity\": \"ShenZhen\",
		\"ShippingState\": \"Guangdong\",
		\"ShippingZip\": \"511144\",
		\"ShippingPhone\": \"15216456468\",
			\"ShippingTaxId\": \"\",
			\"ShippingStateCode\": \"GD\",
			\"SenderCountryCode\": \"CN\",
			\"SenderFirstName\": \"Yun\",
			\"SenderLastName\": \"Tu\",
			\"SenderCompany\": \"\",
			\"SenderAddress\": \"\",
			\"SenderCity\": \"\",
			\"SenderState\": \"\",
			\"SenderZip\": \"\",
			\"SenderPhone\": \"\",
			\"applicationInfos\": [
			{
				\"ApplicationEnName\": \" clothes\",
				\"ApplicationCnName\": \"\",
				\"Qty\": 1,
				\"UnitWeight\": 0.2,
				\"UnitPrice\": 0.5,
				\"HSCode\": \"542212\",
				\"SKU\": \"\",
				\"Remark\": \"beizhu\",
				\"Currency\": \"USD\",
				\"SalesUrl\": \"www.yunexpress.com\"
			},
			{
				\"ApplicationEnName\": \"pants\",
				\"ApplicationCnName\": \"\",
				\"Qty\": 1,
				\"UnitWeight\": 0.1,
				\"UnitPrice\": 0.5,
				\"HSCode\": \"431\",
				\"SKU\": \"\",
				\"Remark\": \"\",
				\"Currency\": \"USD\",
				\"SalesUrl\": \"\"
			}
			]
			}
			]
		}";
		
// 		print_r($json);die;
		
		$tuCurl = curl_init();
		curl_setopt($tuCurl, CURLOPT_URL, $url);
		curl_setopt($tuCurl, CURLOPT_POST, 1);
		curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($tuCurl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $json);
		//     	curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Content-Type: application/xml; charset=utf-8", "Content-length: ".strlen($xml)));
		curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Content-Type: application/json; charset=utf-8", "Content-length: ".strlen($json)));
	
		
// 		    	print_r($tuCurl);die;
		$tuData = curl_exec($tuCurl);
		curl_close($tuCurl);
	
		header("Content-type: text/html; charset=utf-8");
		print_r($tuData);
		echo "<>";
	}
	
	/**
	 *
	 */
	public function getChannleListAction() {
		$url = "http://114.119.9.45:1180/api/Order/GetChannleList";
// 		print_r($json);die;
		
		$tuCurl = curl_init();
		curl_setopt($tuCurl, CURLOPT_URL, $url);
// 		curl_setopt($tuCurl, CURLOPT_POST, 1);
		curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($tuCurl, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
// 		curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $json);
		//     	curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Content-Type: application/xml; charset=utf-8", "Content-length: ".strlen($xml)));
		curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Content-Type: application/json; charset=utf-8", "Content-length: ".strlen($json)));
	
		
// 		    	print_r($tuCurl);die;
		$tuData = curl_exec($tuCurl);
		curl_close($tuCurl);
	
		header("Content-type: text/html; charset=utf-8");
		print_r($tuData);
		echo "<>";
	}
	

	/**
	 *
	 */
	public function getForecastAction() {
		$url = "http://114.119.9.45:1180/api/Order/GetForecastByForecastNumber";
			
		$json = "
		[{
		\"CustomerCode\": \"1002\",
		\"ForecastNumber\": \"YT1530910000005200001\"
		}]";
	
		// 		print_r($json);die;
	
		$tuCurl = curl_init();
		curl_setopt($tuCurl, CURLOPT_URL, $url);
		curl_setopt($tuCurl, CURLOPT_POST, 1);
		curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($tuCurl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $json);
		//     	curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Content-Type: application/xml; charset=utf-8", "Content-length: ".strlen($xml)));
		curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Content-Type: application/json; charset=utf-8", "Content-length: ".strlen($json)));
	
	
		// 		    	print_r($tuCurl);die;
		$tuData = curl_exec($tuCurl);
		curl_close($tuCurl);
	
		header("Content-type: text/html; charset=utf-8");
		print_r($tuData);
		echo "<>";
	}

	/**
	 *
	 */
	public function getLabelAction() {
		$url = "http://114.119.9.45:1180/api/Order/GetLabelsByForecastNumber";
			
		$json = "
		[{
		\"CustomerCode\": \"1001\",
		\"ForecastNumber\": \"YT1530318888801938\"
		}]";
	
		// 		print_r($json);die;
	
		$tuCurl = curl_init();
		curl_setopt($tuCurl, CURLOPT_URL, $url);
		curl_setopt($tuCurl, CURLOPT_POST, 1);
		curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($tuCurl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $json);
		//     	curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Content-Type: application/xml; charset=utf-8", "Content-length: ".strlen($xml)));
		curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Content-Type: application/json; charset=utf-8", "Content-length: ".strlen($json)));
	
	
		// 		    	print_r($tuCurl);die;
		$tuData = curl_exec($tuCurl);
		curl_close($tuCurl);
	
		header("Content-type: text/html; charset=utf-8");
		print_r($tuData);
		echo "<>";
	}

	/**
	 *
	 */
	public function getTrackingNumberAction() {
		$url = "http://114.119.9.45:1180/api/Order/GetTrackingNumber";
			
		$json = "
		[{
		\"CustomerCode\": \"1002\",
		\"ForecastNumber\": \"YT1530910000005200001\"
		}]";
	
		// 		print_r($json);die;
	
		$tuCurl = curl_init();
		curl_setopt($tuCurl, CURLOPT_URL, $url);
		curl_setopt($tuCurl, CURLOPT_POST, 1);
		curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($tuCurl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $json);
		//     	curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Content-Type: application/xml; charset=utf-8", "Content-length: ".strlen($xml)));
		curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Content-Type: application/json; charset=utf-8", "Content-length: ".strlen($json)));
	
	
		// 		    	print_r($tuCurl);die;
		$tuData = curl_exec($tuCurl);
		curl_close($tuCurl);
	
		header("Content-type: text/html; charset=utf-8");
		print_r($tuData);
		echo "<>";
	}

	/**
	 *
	 */
	public function getYunLabelAction() {
		$url = "http://api.yunexpress.com/LMS.API.Lable/Api/PrintUrl";
			
		$json = '["004473P22302878"]';	
		// 		print_r($json);die;
	
		$tuCurl = curl_init();
		curl_setopt($tuCurl, CURLOPT_URL, $url);
		curl_setopt($tuCurl, CURLOPT_POST, 1);
		curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($tuCurl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $json);
		//     	curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Content-Type: application/xml; charset=utf-8", "Content-length: ".strlen($xml)));
		curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Content-Type: application/json; charset=utf-8", "Content-length: ".strlen($json)));
	
	
		// 		    	print_r($tuCurl);die;
		$tuData = curl_exec($tuCurl);
		curl_close($tuCurl);
	
		header("Content-type: text/html; charset=utf-8");
		print_r($tuData);
		echo "<>";
	}
	
	/**
	 *
	 */
	public function receiveAction() {
// 		$url = "http://yuntoms.eccang.com/default/api/receive";
		$url = "http://112.74.66.52:8080/default/api/receive";
			
// 		[CustomerCode] => 1002
// 		[ForecastNumber] => YT1533010000005200001
// 		[ChannelCode] => DHLCN
// 		[CoNumber] => Co2015112710020000054
// 		[NotifyType] => 1006
// 		[PushStatus] => 1010
// 		[notifyResult] => Array
// 		(
// 				[LabelUrl] => [{"LabelType":1001,"LabelSize":1003,"LabelFormat":1001,"Url":"http://114.119.9.45:855/Home/DHLMainLabel/Co2015112710020000054"},{"LabelType":1002,"LabelSize":1003,"LabelFormat":1001,"Url":"http://114.119.9.45:855/Home/DHLSubLabel/Co2015112710020000054"}]
// 				[TrackNumber] => 7197471540
// 				[VenderNumber] =>
// 				[Result] =>
// 		)
		
// 		[Message] =>
		$a = Array
			(
			    'CustomerCode' => '1002',
			    'ForecastNumber' => 'YT15352100010200013',
			    'ChannelCode' => 'SGBL',
			    'CoNumber' => 'YT15352100010200013',
			    'NotifyType' => '1004',
			    'PushStatus' => '1010',
			    'notifyResult' => '',
			    'notifyResult' => array(
			    			'LabelUrl' => array(
			    				'0' => array(
					    			'LabelType' => '1001',
					    			'LabelSize' => '1003',
					    			'Url' => 'http://t.tinydx.com:812/UploadFiles/SPLUSZ/Co201512191002000009920151219.jpeg',
			    				)
			    			),
			    			'TrackNumber' => '781999963414',
			    		),
			    'Message' => "美国邮编格式:5位数字或5位数字加'-'，再加4位数字：如22162或221621010"
			);
		$json = json_encode($a);
// 				print_r($json);die;
	
		$tuCurl = curl_init();
		curl_setopt($tuCurl, CURLOPT_URL, $url);
		curl_setopt($tuCurl, CURLOPT_POST, 1);
		curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($tuCurl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $json);
		//     	curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Content-Type: application/xml; charset=utf-8", "Content-length: ".strlen($xml)));
		curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Content-Type: application/json; charset=utf-8", "Content-length: ".strlen($json)));
	
	
		// 		    	print_r($tuCurl);die;
		$tuData = curl_exec($tuCurl);
		curl_close($tuCurl);
	
		header("Content-type: text/html; charset=utf-8");
		print_r($tuData);
		echo "<>";
	}
	

	/**
	 *
	 */
	public function labelAction() {
		
		$return = $this->excuteGetService();
		$path = APPLICATION_PATH . "/../data/html/" . 'aaaaa';
	    			$label_url = 'http://112.74.65.48/default/index/get-label/code/' . 'aaaaa' . '.html';
	    			
	    			$html = Process_LabelImages::imagesUrlToBase64($return['data'], "");
	    			
	    			//创建文件夹
	    			Common_Common::mkdirs($path);
	    			file_put_contents($path . "/" . "0.html",  $html);
	    			
		header("Content-type: text/html; charset=utf-8");
		print_r($return);
		echo "<>";
	}
	

	/**
	 * 执行GET方法
	 * @param unknown_type $url
	 * @return multitype:number string mixed NULL
	 */
	private function excuteGetService($url = "http://114.119.9.45:855/Home/DHLMainLabel/Co2015112710020000054") {
	
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