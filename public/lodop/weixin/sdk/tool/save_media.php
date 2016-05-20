<?php
if(!defined('IN_SYS')) {
	die('Access Denied');
}

$testData = array(
		'image' => 'CCI4ccM7SwpsIKVIMctVHlpSabKDc8WUCrJIbLsCPX73cjsTwus6IpkOU0aWv8lC',	
		'voice' => 'nVj2Xkw5uASaCZ2a4Zk4EJGMNh3sozI-K_e8MQtpxpR_Crk7MOAkqHHbgtNt3k0Z',			
		'thumb' => 'VK9nFNimrudRgk2IiNsuMn0mAt_bwRdZoGZfKpV0h4vfGQONQdnvZ6XsSncF11Xz'
);//改成你自己的mediaId
$dir = S_ROOT.'download/example/';

$type = $weObj->getRev()->getRevType();
switch($type) {
	case Wechat::MSGTYPE_TEXT:
			switch($keyword = strtolower($weObj->getRevContent())){
				case 'image':
				case 'thumb':	
					if ($savedFile = $weObj->save_media($testData[$keyword], $dir.round(getMillisecond()).'.jpg')){
						$weObj->text("Saved to ".$savedFile)->reply_exit();
					}					
					break;
				case 'voice':
					if ($savedFile = $weObj->save_media($testData[$keyword], $dir.round(getMillisecond()).'.mp3')){
						$weObj->text("Saved to ".$savedFile)->reply_exit();
					}
					break;
				default:
					$weObj->text("hello, world!")->reply_exit();
			}
			break;
	default:
			$weObj->text("help info")->reply_exit();
}
?>