<?php
@define('IN_SYS', TRUE);

function autoLoad($currPath) {
	if (is_dir($currPath)) {
		$handler = opendir ($currPath);
		while (($filename = readdir( $handler )) !== false) {
			if ($filename != "." && $filename != ".." && $filename[0] != '.' && $filename != '_notes') {//	_notes 是dw创建的站点路径信息
				if(is_file($currPath . '/' . $filename)) {		
					require_once $currPath . '/' . $filename;
				}
				if(is_dir($currPath . '/' . $filename)) {		
					autoLoad($currPath . '/' . $filename);
				}
			}
		}
		closedir($handler);
	}
}

autoLoad(dirname(__FILE__));

$account = 'YUYU';
$options = array(
		'token'=>WEIXIN_TOKEN,
		'debug'=>(DEBUG == 'true')?true:false,
		'logcallback'=>'yuyuLog',
		'appid'=>$GLOBALS['APP_INFO'][$account]['appId'],
		'appsecret'=>$GLOBALS['APP_INFO'][$account]['appSecret'],	
	);
$weObj = new YuyuWeChat($options);
?>