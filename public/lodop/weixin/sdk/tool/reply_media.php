<?php
if(!defined('IN_SYS')) {
	die('Access Denied');
}

$testData = array(
		'image' => 'CCI4ccM7SwpsIKVIMctVHlpSabKDc8WUCrJIbLsCPX73cjsTwus6IpkOU0aWv8lC',	
		'voice' => 'nVj2Xkw5uASaCZ2a4Zk4EJGMNh3sozI-K_e8MQtpxpR_Crk7MOAkqHHbgtNt3k0Z',
		'video' => 'nAPv5FeGJcTFHMEmjwHPdDWWKcw1HJovNLp1DgJQ6eV2G5Jg-Ym_MLRCc24V7BRu',					
		'thumb' => 'VK9nFNimrudRgk2IiNsuMn0mAt_bwRdZoGZfKpV0h4vfGQONQdnvZ6XsSncF11Xz'
);//填写你自己的mediaId

$type = $weObj->getRev()->getRevType();
switch($type) {
	case Wechat::MSGTYPE_TEXT:
			switch($keyword = strtolower($weObj->getRevContent())){
				case 'image':
				case 'thumb':
					$imageData = array("MediaId"=>$testData[$keyword]);
					$weObj->image($imageData)->reply_exit();	
					break;
				case 'voice':
					$voiceData = array("MediaId"=>$testData[$keyword]);
					$weObj->voice($voiceData)->reply_exit();	
					break;
				case 'video':
					$videoData = array(
					"MediaId"=>$testData[$keyword],
					"Title"=>"标题",
					"Description"=>"描述"
					);
					$weObj->video($videoData)->reply_exit();
					break;				
			}
			$weObj->text("hello, world!")->reply_exit();
			break;
	default:
			$weObj->text("help info")->reply_exit();
}
?>