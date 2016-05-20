<?php
/**
  * wechat php test
  */

require("HttpClient.class.php");
//define your token
define("TOKEN", "ruston2014");
$wechatObj = new wechatCallbackapiTest();
$wechatObj->valid();

class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
    {
	//get post data, May be due to the different environments
	$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
	if (!empty($postStr)){
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[%s]]></MsgType>
				<Content><![CDATA[%s]]></Content>
				<FuncFlag>0</FuncFlag>
			</xml>";             
		$body = "{
    "touser": "opJSPt_Lbxr62nx0Eigsbkql0giA", 
    "msgtype": "text", 
    "text": {
        "content": "Hello World"
    }
}";
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=oCL3xHw-MTyQ4Gb0xe7N5dLicYWJKxJ_Fbvs5134i9xSQODEuEfZTvFCJTlztPoH";
		HttpClient::quickPost($url, $body);
		if(!empty( $keyword ))
                {
              		$msgType = "text";
                	$contentStr = "Welcome to wechat world!";
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
                } else {
                	echo "Input something...";
                }

        } else {
                $body = "{
    "touser": "opJSPt_Lbxr62nx0Eigsbkql0giA",
    "msgtype": "text",
    "text": {
        "content": "Hello World"
    }
}";
                $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=oCL3xHw-MTyQ4Gb0xe7N5dLicYWJKxJ_Fbvs5134i9xSQODEuEfZTvFCJTlztPoH";
                HttpClient::quickPost($url, $body);

        	echo "11";
        	exit;
        }
    }
		
	private function checkSignature() {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
	$token = TOKEN;
	$tmpArr = array($token, $timestamp, $nonce);
	sort($tmpArr, SORT_STRING);
	$tmpStr = implode( $tmpArr );
	$tmpStr = sha1( $tmpStr );
		
	  if( $tmpStr == $signature ){
	    return true;
	  } else {
	    return false;
	  }
	}
}

?>
