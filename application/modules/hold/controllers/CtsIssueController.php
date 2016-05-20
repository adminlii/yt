<?php
class Hold_CtsIssueController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "hold/views/";
        $this->serviceClass = new Service_CtsIssue();
    }

    public function listAction()
    {
    	$statusArr = array(
    			"L"=>"全部",
    			"N"=>"需回复",
    			"A"=>"待客服处理",
    			"S"=>"待仓库放货",
    			"C"=>"已处理",
    			"K"=>"未读通知",
    			"O"=>"已处理",
    	);
    	$customerId = Service_User::getCustomerId();
    	$channelId = Service_User::getChannelid();
        if ($this->_request->isPost()) {
            $page = $this->_request->getParam('page', 1);
            $pageSize = $this->_request->getParam('pageSize', 20);

            $page = $page ? $page : 1;
            $pageSize = $pageSize ? $pageSize : 20;

            $return = array(
                "state" => 0,
                "message" => "No Data"
            );
            
            $logUser = Service_User::getLoginUser();
            $condition["issue_kind_code"] = $this->_request->getParam("issue_kind_code","");
            $condition["customer_id"] = $customerId;
            $condition["shipper_channel_id"] = $channelId;
            $condition["code"] = $this->_request->getParam("code","");
            $tetemp = $condition["issue_status"] = $this->_request->getParam("issue_status","");
            
            if($tetemp == 'K'){
            	$condition["issue_status"] = 'N';
            	$condition["isu_interactionsign"] = 'P';
            }
            if($condition["issue_status"] == 'L'){
            	unset($condition["issue_status"]);
            }
			
            $count = $this->serviceClass->getByJoinCondition($condition, 'count(*)');
            $return['total'] = $count;

            if ($count) {
                $showFields=array(
                'issue_id',
                'issue_kind_code',
                'shipper_hawbcode',
                'server_hawbcode',
                'product_code',
                'st_id_process',
                'isu_createdate',
                'issue_status',
                );
                $rows = $this->serviceClass->getByJoinCondition($condition,$showFields, $pageSize, $page, array('cts_issue.issue_id desc'));
                foreach($rows as $key=>$val){
                	$rows[$key]["issue_status_cnname"] = $statusArr[$val["issue_status"]];
                	if($condition["issue_status"] == 'N' && $tetemp == 'N'){
                		$tem_kind = Service_CtsCustomerIssuekind::getByField($val["issue_kind_code"],"issuekind_code",array("isu_interactionsign"));
                		if($tem_kind["isu_interactionsign"] == 'P'){
                			unset($rows[$key]);
                		}
                		
                	}
                }
                foreach($rows as $k=>$v){
                    $v = isset($v)?$v:'';
                    $rows[$k] = $v;
                }
                $return['data'] = $rows;
                $return['state'] = 1;
                $return['message'] = "";
            }
            die(Zend_Json::encode($return));
        }
//         $statusArr = Service_CtsIssuestatus::getAll();
//         $countArray = Service_CtsIssueCount::getByCondition(array("customer_id"=>$logUser["customer_id"],"shipper_channel_id"=>$logUser["customer_channelid"]),"*",0,1,"");
        // TODO DB2
        $db = Common_Common::getAdapterForDb2();
        $sql = "select sum(isc_waitforreply) as isc_waitforreply,sum(isc_replied) as isc_replied,sum(isc_unrelease) as isc_unrelease,sum(isc_notice) as isc_notice from cts_issue_count where 1 = 1";
        if(isset($customerId) &&  !empty($customerId) && $customerId >0){
        	$sql .= " and customer_id = ".$customerId;
        }
        
        if(isset($channelId) &&  !empty($channelId) && $channelId >0){
        	$sql .= " and shipper_channel_id = ".$channelId;
        }
        $countArray = $db->fetchRow($sql);
        $countre = array(
			"isc_waitforreply"=>!empty($countArray["isc_waitforreply"])?$countArray["isc_waitforreply"]:'0',
			"isc_replied"=>!empty($countArray["isc_replied"])?$countArray["isc_replied"]:'0',
			"isc_unrelease"=>!empty($countArray["isc_unrelease"])?$countArray["isc_unrelease"]:'0',
			"isc_notice"=>!empty($countArray["isc_notice"])?$countArray["isc_notice"]:'0',
        );
        foreach($statusArr as $keya=>$vala){
        switch($keya){
                case 'N':
                   	$statusArr[$keya] = $vala."(".$countre["isc_waitforreply"].")";
                    break;
                case 'A':
                   $statusArr[$keya] = $vala."(".$countre["isc_replied"].")";
                    break;
                case 'S':
                	$statusArr[$keya] = $vala."(".$countre["isc_unrelease"].")";
                    break;
                case 'K':
                	$statusArr[$keya] = $vala."(".$countre["isc_notice"].")";
                    break;
                case 'C':
                    break;
                default:
            }
        }
        
        $this->view->statusArr = $statusArr;
        
        $kind = Service_CtsCustomerIssuekind::getAll();
        $this->view->kind = $kind;
        
        
        echo Ec::renderTpl($this->tplDirectory . "cts_issue_index.tpl", 'layout');
    }

    public function editAction()
    {
        $return = array(
            'state' => 0,
            'message' => '',
            'errorMessage'=>array('Fail.')
        );

        if ($this->_request->isPost()) {
            $params = $this->_request->getParams();
            $row = array(
                
              'issue_id'=>'',
            );
            $row=$this->serviceClass->getMatchEditFields($params,$row);
            $paramId = $row['issue_id'];
            if (!empty($row['issue_id'])) {
                unset($row['issue_id']);
            }
            $errorArr = $this->serviceClass->validator($row);

            if (!empty($errorArr)) {
                $return = array(
                    'state' => 0,
                    'message'=>'',
                    'errorMessage' => $errorArr
                );
                die(Zend_Json::encode($return));
            }

            if (!empty($paramId)) {
                $result = $this->serviceClass->update($row, $paramId);
            } else {
                $result = $this->serviceClass->add($row);
            }
            if ($result) {
                $return['state'] = 1;
                $return['message'] = array('Success.');
            }

        }
        die(Zend_Json::encode($return));
    }

    public function getByJsonAction()
    {
        $result = array('state' => 0, 'message' => 'Fail', 'data' => array());
        $paramId = $this->_request->getParam('paramId', '');
        if (!empty($paramId) && $rows = $this->serviceClass->getByField($paramId, 'issue_id')) {
            $rows=$this->serviceClass->getVirtualFields($rows);
            $result = array('state' => 1, 'message' => '', 'data' => $rows);
        }
        die(Zend_Json::encode($result));
    }

    public function deleteAction()
    {
        $result = array(
            "state" => 0,
            "message" => "Fail."
        );
        if ($this->_request->isPost()) {
            $paramId = $this->_request->getPost('paramId');
            if (!empty($paramId)) {
                if ($this->serviceClass->delete($paramId)) {
                    $result['state'] = 1;
                    $result['message'] = 'Success.';
                }
            }
        }
        die(Zend_Json::encode($result));
    }
    
    public function getResponseMessageAction(){
    	$id = $this->_request->getParam("isId","");
    	if($id == '0' || empty($id)){
    		echo 'params is err';
    		exit;
    	}
    	
    	//问题
    	$issu = Service_CtsIssue::getByField($id);
    	
    	//获取消息内容
    	$responseMessage = Service_CtsIssueResponse::getByCondition(array("issue_id"=>$id),"*",0,1,"");
    	
    	//问题类型
    	$kind = Service_CtsCustomerIssuekind::getByField($issu["issue_kind_code"],"issuekind_code","*");
    	$this->view->kind = $kind;
		
    	//是否可以回复
    	$isRespon = Service_XtdInteractionSign::getByField($kind["isu_interactionsign"],"isu_interactionsign","*");
    	$responseMark = 1;
    	if($isRespon["isu_interactionsign"] == 'C'){
    		$responseMark = 2;//可确认放行
    	}
    	if($isRespon["isu_interactionsign"] == 'P' || $issu["issue_status"] != 'N'){
    		$responseMark = 3;//只读
    	}
    	$this->view->responseMark = $responseMark;
    	
    	$this->view->responseMessage = $responseMessage;
    	$this->view->issu = $issu;
    	
    	//更新问题状态为已读
    	Service_CtsIssueResponse::update(array("refer_sign"=>"Y"), $id,"issue_id");
    	
    	echo Ec::renderTpl($this->tplDirectory . "cts_issue_message.tpl", 'layout');
    }
    
    public function saveMessageAction(){
    	$return = array("state"=>0,"message"=>"");
    	if($this->_request->isPost()){
    		$content = $this->_request->getParam("message_content","");
    		$id = $this->_request->getParam("id","");
    		$db = Common_Common::getAdapter();
    		try {
    			$db->beginTransaction();
    			$user = Service_User::getLoginUser();
    			$row = array(
    					"issue_id"=>$id,
    					"issue_response_code"=>"C",
    					"message_type"=>"WB",
    					"message_sendsign"=>"Y",
    					"replay_name"=>$user["user_name"],
    					"message_content"=>$content,
    					"replay_createdate"=>date("Y-m-d H:i:s"),
    			);
    			Service_CtsIssueResponse::add($row);
    			
    			Service_CtsIssue::update(array("issue_status"=>"A"), $id);
    			
    			$return["state"] = 1;
    			$db->commit();
    		} catch (Exception $e) {
    			$db->rollBack();
    			$return["message"] = $e->getMessage();
    		}
    		
    	}
    	die(Zend_Json::encode($return));
    }
    
    public function removeHoldAction(){
    	$return = array("state"=>0,"message"=>"");
    	if($this->_request->isPost()){
    		$id = $this->_request->getParam("id","");
    		$db = Common_Common::getAdapter();
    		$db->beginTransaction();
    		$user = Service_User::getLoginUser();
    		try {
    			$user = Service_User::getLoginUser();
    			$issu = Service_CtsIssue::getByField($id);
    			if(empty($issu)){
    				$return["state"] = 1;
    				die(Zend_Json::encode($return));
    			}
    			$date = date("Y-m-d H:i:s");
    			$uprow = array(
    					"issue_status"=>"O",
    					"isu_lastfeedbackdate"=>$date,
    					"isu_unholddate"=>$date,
    					"isu_releasedate"=>$date,
    					"isu_webreplysign"=>"Y",
    					"isu_holdsign"=>"N",
    			);
    			Service_CtsIssue::update($uprow, $id);
    			
    			$sql = "update bsn_holding set holdcase_flag = 'Y' where bs_id = ".$issu['bs_id']." and issue_code = '".$issu['issue_kind_code']."'";
    			$row = $db->query($sql);
    			if(!$row){
    				throw new Exception("放行失败.请联系客服处理.");
    			}
    			
    			$reesponseRow = array(
    					"issue_id"=>$id,
    					"issue_response_code"=>"C",
    					"message_type"=>"WB",
    					"message_sendsign"=>"Y",
    					"replay_name"=>$user["user_name"],
    					"message_content"=>"我已了解并认同网站放行规则，确认对此问题放行。",
    					"replay_createdate"=>date("Y-m-d H:i:s"),
    			);
    			Service_CtsIssueResponse::add($reesponseRow);
    			$return["state"] = 1;
    			$db->commit();
    		} catch (Exception $e) {
    			$db->rollBack();
    			$return["message"] = $e->getMessage();
    		}
    	
    	}
    	die(Zend_Json::encode($return));
    }
    
}