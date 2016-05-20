<?php
class Test_TomsController extends Ec_Controller_Action
{
	public function preDispatch(){
		echo '<meta http-equiv="content-type" content="text/html; charset=utf-8"/>';
	}


    public function testAction(){
        $url="http://xxx/api/svn";
        $param = array(
            'page' => 1,
            'pageSize' => 2,
        );
        $paramsArr = array('service' => 'loadOrder', 'token' => 'token',  'language' => 'zh_CN', 'params' => $param);
        $rs = self::curlRequestForWms($url, $paramsArr);
        Common_Common::myEcho(print_r($rs, true));
    }



    /**
     * @desc 请求WMS
     * @param $url
     * @param array $params $array = array('service'=>'createOrder','token'=>'','language'=>'zh_CN/en_US','params'=>array('order'=>''));
     * @return array|mixed
     */
    public static function curlRequestForWms($url, $params = array())
    {
        // initialise a CURL session
        $connection = curl_init();
        // set method as POST
        curl_setopt($connection, CURLOPT_POST, 1);
        // curl_setopt ( $connection, CURLOPT_HTTPHEADER, array (
        // 'Content-type: application/x-www-form-urlencoded'
        // ) );
        curl_setopt($connection, CURLOPT_CUSTOMREQUEST, "POST");
        // echo $params;exit;
        curl_setopt($connection, CURLOPT_POSTFIELDS, json_encode($params));

        curl_setopt($connection, CURLOPT_URL, $url);

        // stop CURL from verifying the peer's certificate
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);

        // set it to return the transfer as a string from curl_exec
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);

        // Send the Request
        $response = curl_exec($connection);
        // close the connection
        curl_close($connection);

        // return the response
        // echo $response;exit;
        $responseArr = json_decode($response, true);

        //响应标志,需要WMS统一返回此标记，Success 表示成功，Failure 表示失败
        if (empty ($responseArr)) {
            $responseArr = array("ask" => 'Failure', "message" => 'Internal error', "data" => array(), "count" => 0);
            Ec::showError($response, 'curlRequestForWms_' . date('Y-m-d'));
        }
        return $responseArr;
    }


}