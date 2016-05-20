<?php
if(!defined('IN_SYS')) {
	die('Access Denied');
}

$type = $weObj->getRev()->getRevType();
switch($type) {
	case Wechat::MSGTYPE_TEXT:
		$response = $weObj->getRevContent();
		$weObj->text($response)->reply_exit();
		break;
	case Wechat::MSGTYPE_IMAGE:
		$response = $weObj->getRevPic();
		$weObj->text($response)->reply_exit();
		break;
	case Wechat::MSGTYPE_VOICE:
		if (strlen($response = $weObj->getRevContent())>0){
			$weObj->text($response)->reply_exit();
		} else {
			$response = $weObj->getRevVoice();
			$response = json_encode($response);
			$weObj->text($response)->reply_exit();
		}
		break;
	case Wechat::MSGTYPE_VIDEO:
		$response = $weObj->getRevVideo();
		$response = json_encode($response);
		$weObj->text($response)->reply_exit();
		break;
	case Wechat::MSGTYPE_LOCATION:
		$response = $weObj->getRevGeo();
		$response = json_encode($response);
		$weObj->text($response)->reply_exit();
		break;
	case Wechat::MSGTYPE_LINK:
		$response = $weObj->getRevLink();
		$response = json_encode($response);
		$weObj->text($response)->reply_exit();
		break;
	case Wechat::MSGTYPE_EVENT:
		list($event, $eventKey) = $weObj->getRevEvent();
		switch($event){
			case 'subscribe':
				if ($sceneId = $weObj->getRevSceneId()){
					$ticket = $weObj->getRevTicket();
					$response = array('EventKey'=>$sceneId, 'Ticket'=>$ticket);
					$response = json_encode($response);
					$response = SUBSCRIBE_WELCOME . json_encode($response);
					$weObj->text($response)->reply_exit();
				} else {
					$weObj->text(SUBSCRIBE_WELCOME)->reply_exit();
				}				
				break;
			case 'unsubscribe':
				//帐号解绑、统计等数据库操作。
				break;
			case 'SCAN':
				$sceneId = $weObj->getRevSceneId();
				$ticket = $weObj->getRevTicket();
				$response = array('EventKey'=>$sceneId, 'Ticket'=>$ticket);
				$response = json_encode($response);
				$weObj->text($response)->reply_exit();
				break;
			case 'LOCATION':
				$response = $weObj->getRevEventGeo();
				$response = json_encode($response);
				$weObj->text($response)->reply_exit();
				break;
			case 'CLICK':
				break;
			case 'VIEW':
				break;
		}
		break;
	default:
		$weObj->text("help info")->reply_exit();
}
?>