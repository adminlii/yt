<?php
require_once dirname(__FILE__) . '/common/Common.php';
if (WEIXIN_VALID == 'true') $weObj->yuyu_valid();

//require_once dirname(__FILE__) . '/tool/reply_media.php';
//require_once dirname(__FILE__) . '/tool/save_media.php';
//require_once dirname(__FILE__) . '/tool/send_and_response.php';
//exit;

$type = $weObj->getRev()->getRevType();
switch($type) {
	case Wechat::MSGTYPE_TEXT:
			$weObj->text("hello, world!")->reply_exit();
			break;
	case Wechat::MSGTYPE_EVENT:
			$weObj->text("click")->reply_exit();
			break;
	case Wechat::MSGTYPE_IMAGE:
			break;
	default:
			$weObj->text("help info")->reply_exit();
}
?>
