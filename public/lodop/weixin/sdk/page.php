<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>鱼渔微信开发包 - www.yidongapi.com</title>
<?php
require_once dirname(__FILE__) . '/common/Common.php';

switch(@$_GET['action']) {
	case 'check_host':
		if ($weObj->debug) {
			$weObj->log('系统自检开始。');
			die($weObj->check_host());
		}
		break;
	case 'upload_media':
		$testData = array(
		array(
		'type' => 'image',
		'filepath' => 'media/image/example.jpg'
		),
		array(
		'type' => 'voice',
		'filepath' => 'media/voice/example.mp3'
		),
		array(
		'type' => 'video',
		'filepath' => 'media/video/example.mp4'
		),
		array(
		'type' => 'thumb',
		'filepath' => 'media/thumb/example.jpg'
		)
		);
		foreach ($testData as $item){
			if (!$return = $weObj->upload_media("@".S_ROOT.$item['filepath'], $item['type'])) die("@".S_ROOT.$item['filepath']." wrong");
			$weObj->log($return["type"]."\n".(($item['type'] == 'thumb')?$return["thumb_media_id"]:$return["media_id"]));
			sleep(3);
		}
		die("done");
		break;
	case 'delete_logs':
		$weObj->dir_clear(S_ROOT.'data/log');
		die('done');
		break;
	case 'access_token':
		die($weObj->checkAuth());
		break;
	default:
		echo "no action";
}
?>