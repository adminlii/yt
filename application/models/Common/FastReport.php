<?php
/**
 * 标签
 * @author Administrator
 */
class Common_FastReport
{

    protected $_authToken = ''; // key
    private $_client = null; // Common_ReportSoap
    public $_error = '';

    private function getClient()
    {
        if(empty($this->_client)){
            $this->setClient();
        }
        
        return $this->_client;
    }

    public function __construct($authToken)
    {
        $this->_authToken = $authToken;
    }

    public function setAuthToken($authToken)
    {
        $this->_authToken = $authToken;
    }

    private function setClient()
    {
        $wsdl = APPLICATION_PATH . '/../data/wsdl/ReportServiceHandler.wsdl';
        
        // $this->_authToken = 'ukwh6lNEFNfBDGt6xue0uG7CkgNHIyyL';
        
        // 超时
        $timeout = 1000;
        
        $streamContext = stream_context_create(array(
            'ssl' => array(
                'verify_peer' => false,
                'allow_self_signed' => true
            ),
            'socket' => array(),
            'Content-Type' => 'charset:UTF-8',
        ));
        
        $options = array(
            "trace" => true,
            "connection_timeout" => $timeout,
//             "exceptions" => true,
//             "soap_version" => SOAP_1_1,
//             "features" => SOAP_SINGLE_ELEMENT_ARRAYS,
//             "stream_context" => $streamContext,
//             "Content-Type" => "charset:UTF-8",	
//             "charset" => "UTF-8",
            "encoding" => "UTF-8"
        );
        $client = new Common_ReportSoap($wsdl, $options);
        $this->_client = $client;
    }

    public function GetVersion()
    {
        $req = array(
            'authToken' => $this->_authToken
        );
        $client = $this->getClient();
        $rs = $client->GetVersion($req);
        
        // echo $client->__getLastRequest();exit;
        
        $rs = Common_Common::objectToArray($rs);
        $rs = array_pop($rs);
        $rs = Zend_Json::decode($rs);       
        return $rs;
    }

    public function MakeLableFileToBase64($configInfoJson, $orderInfoJson, $pdfPrintInfoJson)
    {
        $return = array('ask'=>0,"message"=>Ec::Lang('失败'));
        
        try{

            $req = array();
            $client = $this->getClient();
            $authToken = array(
                    'authToken' => $this->_authToken
            );
            $req['authToken'] = $this->_authToken;
            $req['configInfoJson'] = $configInfoJson;
            $req['orderInfoJson'] = $orderInfoJson;
            $req['ReportTypeNameJosn'] = $pdfPrintInfoJson;
//             header("Content-type: text/html; charset=utf-8");
//             print_r($req); die;
//             echo "<br/>2.1->"; print_r(date('Y-m-d H:i:s:S'));
            $rs = $client->GetReportString($req);
//             echo "<br/>2.2->"; print_r(date('Y-m-d H:i:s:S'));
//             header("Content-type: text/html; charset=utf-8");
//             print_r($rs);exit;
            
            $return['rs'] = $rs;
            $return['ask'] = 1;
            $return['message'] = Ec::Lang('Success');
        }catch (Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }


    public function PrintLabel($params,$method){
        $result = array("ack"=>0,"message"=>"","data"=>"");
        $url = "https://202.104.134.94/ChinaPost/api/LabelPrintService/MergeLabelByTrackingNumbers?type=json";
        //$url = "http://112.126.68.251:8088/v5/api/LabelPrintService/MergeLabelByTrackingNumbers?type=json";
        //$url ="http://192.168.10.48:3001/V3/api/LabelPrintService/MergeLabelByTrackingNumbers?type=json";
        $username = 'tmsUser';
        //$password = '1234567890';
        $password = '123456';
        $apiToken = base64_encode("{$username}:{$password}");
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type:application/json;charset=utf-8",
                "Authorization: Basic {$apiToken}",
            ));
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params); // Post提交的数据包
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($ch);
            $data = Common_Common::objectToArray(json_decode($data));
            //$data = curl_error($ch);
            $result["ack"] = 1;
            $result["data"] = $data;
        } catch (Exception  $e) {
            $result["message"] = $e->getMessage();
        }

        Ec::showError("**************start*************\r\n"
            . print_r($params, true)
            . "\r\n"
            . print_r($data, true)
            . "**************end*************\r\n",
            'YunExpress_API/Create_response_getLabelinfo'.date("Ymd"));

        return $result;
    }


    public function CreatePdfFile($pdfData,$trackingCodes){
        $mdTrackNum = "";
        foreach($trackingCodes as $trackingCode){
            $mdTrackNum .= $trackingCode;
        }
        $filename = md5($mdTrackNum);
        $file_extension = 'pdf';
        $file_name = $filename.'.'.$file_extension;
        $condition = array();
        $condition['find_key'] = $file_name;
        $condition['status'] = 1;
        $labellogRs	=Service_SaveLabellog::getByCondition($condition,'*',0,1,'createtime desc');
        $labelsave = new Process_LabelSave('../label_temp/PDF',1);
        $saveDir = $labelsave->getSavePath();
        if(empty($labellogRs)){
        	//兼容之前的内容
        	$filepath = rtrim($saveDir,'\\/').DIRECTORY_SEPARATOR."PDF/".$file_name;
        	if(!file_exists($filepath)){
        		$rs = $labelsave->save($file_name, base64_decode($pdfData));
        		if(!$rs['ask']){
        			//记录日志
        			Ec::showError("**************start*************\r\n"
        					. "\r\n" . print_r($file_name, true)
        					. "\r\n" . print_r($rs, true)
        					. "**************end*************\r\n",
        					'TMS_API/Create_label_info'.date("Ymd"));
        		}else{
        			$labelsave->saveFileLog($file_name,$rs['data']['filePath'],$rs['data']['extension']);
        			$filepath  = rtrim($saveDir,'\\/').DIRECTORY_SEPARATOR.$rs['data']['filePath'];
        		}
        	}
        }else{
        	$filepath  = rtrim($saveDir,'\\/').DIRECTORY_SEPARATOR.$labellogRs[0]['savepath'];
        }
        return $filepath;
    }

    
    function utf8_unicode($name){
    	$name = iconv('UTF-8', 'UCS-2', $name);
    	$len  = strlen($name);
    	$str  = '';
    	for ($i = 0; $i < $len - 1; $i = $i + 2){
    		$c  = $name[$i];
    		$c2 = $name[$i + 1];
    		if (ord($c) > 0){   //两个字节的文字
    			$str .= '\u'.base_convert(ord($c), 10, 16).str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
    			//$str .= base_convert(ord($c), 10, 16).str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
    		} else {
    			$str .= '\u'.str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT);
    			//$str .= str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT);
    		}
    	}
    	$str = strtoupper($str);//转换为大写
    	return $str;
    }
}