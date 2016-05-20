<?php
if(!defined('IN_SYS')) {
	die('Access Denied');
}

include_once dirname(__FILE__) . '/wechat-php-sdk/wechat.class.php';

class YuyuWeChat extends Wechat {
	const FILE_API_URL_PREFIX = 'http://file.api.weixin.qq.com/cgi-bin';
	const MEDIA_UPLOAD_URL = '/media/upload?';	
	const MEDIA_DOWNLOAD_URL = '/media/get?';	
	
	//reply并终止程序
	public function reply_exit($msg=array(),$return = false) {
		$this->reply($msg, $return);
		exit;
	}
	
	/**
	 * POST 请求 用于多媒体文件上传。
	 * @param string $url
	 * @param array $mediaArray
	 * @return string content
	 */
	private function http_post_media($url,$mediaArray){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
		}

		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($oCurl, CURLOPT_POST,true);
		curl_setopt($oCurl, CURLOPT_POSTFIELDS,$mediaArray);
		
		//设置代理服务器，用fiddler2监测
		//$proxy = "127.0.0.1:8888";
		//curl_setopt($oCurl,CURLOPT_PROXY,$proxy);
		
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);			
		if(intval($aStatus["http_code"])==200){
			$this->log($aStatus["total_time"]."\n".$mediaArray['media']);	
			return $sContent;
		}else{
			return false;
		}
	}

	/**
	 * 上传多媒体文件
	 * @param string $filepath 媒体文件相对于根目录的物理位置，比如"images/example.jpg"; 
	 * @param string $type 媒体文件类型：image、voice、video或thumb
	 * @return array('type'=>'','media_id'=>'','created_at'=>'')或array('type'=>'','thumb_media_id'=>'','created_at'=>'')
	 */
	public function upload_media($filepath, $type){
		if (!$this->access_token && !$this->checkAuth()) return false;
		$mediaArray = array("media" => $filepath);
		$url = self::FILE_API_URL_PREFIX.self::MEDIA_UPLOAD_URL.'access_token='.$this->access_token.'&type='.$type;		
		$result = $this->http_post_media($url, $mediaArray);
		if ($result) {
			$json = json_decode($result,true);
			if (isset($json['errcode'])) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				return false;
			}
			return $json;
		}
		return false;
	}
	
	/**
	 * 保存多媒体文件
	 * @param string $filepath 媒体文件相对于根目录的物理位置，比如"images/example.jpg"; 
	 * @param string $type 媒体文件类型：image、voice、video或thumb
	 * @return array('type'=>'','media_id'=>'','created_at'=>'')或array('type'=>'','thumb_media_id'=>'','created_at'=>'')
	 */
	public function save_media($mediaId, $savedFile){
		if (!$this->access_token && !$this->checkAuth()) return false;
		$url = self::FILE_API_URL_PREFIX.self::MEDIA_DOWNLOAD_URL.'access_token='.$this->access_token.'&media_id='.$mediaId;
		$oCurl = curl_init ($url);
		$fp = fopen ($savedFile, "w");
		curl_setopt ($oCurl, CURLOPT_FILE, $fp);
		curl_setopt ($oCurl, CURLOPT_HEADER, 0);
		curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		fclose ($fp);
		if(intval($aStatus["http_code"])==200){
			$this->log($aStatus["total_time"]."\n".$savedFile);
			return $savedFile;
		}else{
			return false;
		}
	}

	/**
	 * 设置回复图片
	 * @param array $imageData 
	 * 数组结构:
	 *  array(
	 *  	"MediaId"=>"lZA5HNR8bYjUNjo2boEncZ1-kfvJOsNxhe2oxOS3Zz3Y4Zs01_8I_wqzf7WQUQ0l",
	 *  )
	 */
	public function image($imageData=array())
	{
		$msg = array(
			'ToUserName' => $this->getRevFrom(),
			'FromUserName'=>$this->getRevTo(),
			'MsgType'=>self::MSGTYPE_IMAGE,
			'CreateTime'=>time(),
			'Image'=>$imageData
		);
		$this->Message($msg);
		return $this;
	}
	
	/**
	 * 设置回复语音
	 * @param array $voiceData 
	 * 数组结构:
	 *  array(
	 *  	"MediaId"=>"9Yjhnlkg6fCvUIy9bTBxl4y5O-uXoHq_VMQc48h2VuPQu0dWJc0HsolHjzwbf3M1",
	 *  )
	 */
	public function voice($voiceData=array())
	{
		$msg = array(
			'ToUserName' => $this->getRevFrom(),
			'FromUserName'=>$this->getRevTo(),
			'MsgType'=>self::MSGTYPE_VOICE,
			'CreateTime'=>time(),
			'Voice'=>$voiceData
		);
		$this->Message($msg);
		return $this;
	}
	
	/**
	 * 设置回复视频
	 * @param array $voiceData 
	 * 数组结构:
	 *  array(
	 *  	"MediaId"=>"qo7ehcA25Gx2RUOw6LztGyd582mGdpojIjfbVAdxJPUTzs41QqSb2lfBHRjlet0h",
	 *  	"Title"=>"标题",
	 *  	"Description"=>"描述",
	 *  )
	 */
	public function video($videoData=array())
	{
		$msg = array(
			'ToUserName' => $this->getRevFrom(),
			'FromUserName'=>$this->getRevTo(),
			'MsgType'=>self::MSGTYPE_VIDEO,
			'CreateTime'=>time(),
			'Video'=>$videoData
		);
		$this->Message($msg);
		return $this;
	}
	
	/**
	 * 封装wechat.class.php的valid函数 
	 */
	public function yuyu_valid(){
		if(isset($_GET["signature"])&&isset($_GET["timestamp"])&&isset($_GET["nonce"])){
			$return = $this->valid(true);
			if (isset($_GET["echostr"])){
				if ($return){
					$this->log('验证微信接口配置信息成功。');
				} else {
					$this->log('验证微信接口配置信息失败，请在GlobalDefine.php中设置正确的WEIXIN_TOKEN。');
				}
				die($return);
			} else {				
				if (!$return) {
					$this->log('微信消息来源验证失败，请在GlobalDefine.php中设置正确的WEIXIN_TOKEN。通过微信接口配置信息验证后，在非生产环境中，可以关闭WEIXIN_VALID。');
					die('no access!');
				}
			}
		} else {
            die('not from weixin server');
        }
	}

	/**
	 * 简单的系统自检
	 */
	public static function check_host(){
		echo "php ".PHP_VERSION." is ok<br>";
		if(function_exists('curl_init')){
			echo "curl_init is ok<br>";
		}else{
			echo "no curl_init <br>";
		}
		if(function_exists('fsockopen')){
			echo "fsockopen is ok<br>";
		}
		else{
			echo "fsockopen is no<br>>";
		}
		if(function_exists('file_get_contents')){
			echo "file_get_contents is ok <br>";
		}
		else{
			echo "file_get_contents is not ok<br>";
		}
		if(function_exists('file_exists')){
			echo "file_exists is ok <br>";
		}
		else{
			echo "file_exists is not ok<br>";
		}
		if(function_exists('fopen')){
			echo "fopen is ok <br>";
		}
		else{
			echo "fopen is not ok<br>";
		}
		if(function_exists('fwrite')){
			echo "fwrite is ok <br>";
		}
		else{
			echo "fwrite is not ok<br>";
		}
		if(function_exists('mkdir')){
			echo "mkdir is ok <br>";
		}
		else{
			echo "mkdir is not ok<br>";
		}
	}

 /**
 * 删除指定目录下的所有文件
 * @param String $dir  要进行操作的路径
 * 适合范围，只有用于文件夹内不存在子文件夹的情况下
 */
	public static function dir_clear($dir) {
		$directory = dir($dir);                //创建一个dir类，用来读取目录中的每一个文件
		while($entry = $directory->read()) {   //循环每一个文件,并取得文件名$entry
			$filename = $dir.'/'.$entry;       //取得完整的文件名，带路径的
			if(is_file($filename)) {           //如果是文件，则执行删除操作
				@unlink($filename);
			}
		}
		$directory->close();                   //关闭读取目录文件的类
	}
}

