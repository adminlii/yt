<?php
class Service_MessageTemplateProcess extends Common_Service
{
	
	public function createTemplateTransaction($row){
		$result = array (
				"ask" => 0,
				"message" => "Create Template Fail" 
		);
		$db = Common_Common::getAdapter ();
		$db->beginTransaction ();
		try {
			$result = $this->createTemplate ( $row );
			$db->commit ();
		} catch ( Exception $e ) {
			$db->rollback ();
			$result = array (
					"ask" => 0,
					"message" => $e->getMessage (),
					'errorCode' => $e->getCode () 
			);
		}
		return $result;
	}
	
	public function createTemplate($row){

		$this->validate($row);

		$templateRow = $row['template'];
		$templateContentRows = $row['content'];
		$templateRow['create_time'] = date('Y-m-d H:i:s');
		$templateRow['update_time'] = date('Y-m-d H:i:s');
		if(!$templateId=Service_MessageTemplate::add($templateRow)){
			throw new Exception('Inner Error.');
		}
		foreach($templateContentRows as $templateContentRow){
			$templateContentRow['template_id'] = $templateId;			
			if(!Service_MessageTemplateContent::add($templateContentRow)){
				throw new Exception('Inner Error..');
			}
		}

		$cacheFile = APPLICATION_PATH.'/../data/tpl_c/groupMessageTpl.txt';
		@unlink($cacheFile);
		$result = array (
				"ask" => 1,
				"message" => "Create Template Success!",
				'tid' => $templateId
		);
		return $result;		
		
	}

	public function updateTemplateTransaction($row,$tid){
		$result = array (
				"ask" => 0,
				"message" => "Update Template Fail" 
		);
		$db = Common_Common::getAdapter ();
		$db->beginTransaction ();
		try {
			$result = $this->updateTemplate ( $row ,$tid);
			$db->commit ();
		} catch ( Exception $e ) {
			$db->rollback ();
			$result = array (
					"ask" => 0,
					"message" => $e->getMessage (),
					'errorCode' => $e->getCode () 
			);
		}
		return $result;
	}
	public function validate($row){
		//判断必填项
		
		
	}
	public function updateTemplate($row,$tid){

		$this->validate($row);
		$templateRow = $row['template'];
		$templateContentRows = $row['content'];
		$templateRow['update_time'] = date('Y-m-d H:i:s');
		if(!Service_MessageTemplate::update($templateRow,$tid,'template_id')){
			throw new Exception('Inner Error.');
		}
		Service_MessageTemplateContent::delete($tid,'template_id');
		
		foreach($templateContentRows as $templateContentRow){
			$templateContentRow['template_id'] = $tid;
			if(!Service_MessageTemplateContent::add($templateContentRow)){
				throw new Exception('Inner Error..');
			}
		}
	    $cacheFile = APPLICATION_PATH.'/../data/tpl_c/groupMessageTpl.txt';
		@unlink($cacheFile);
		$result = array (
				"ask" => 1,
				"message" => "Update Template Success!",
				'tid' => $tid
		);
		return $result;	
	}
	public function deleteTemplateTransaction($tid){
	   $result = array (
	      "ask" => 0,
	      "message" => "Delete Template Fail"
	   );
	   $db = Common_Common::getAdapter ();
	   $db->beginTransaction ();
	   try {
	      $result = $this->deleteTemplate ($tid);
	      $db->commit ();
	   } catch ( Exception $e ) {
	      $db->rollback ();
	      $result = array (
	         "ask" => 0,
	         "message" => $e->getMessage (),
	         'errorCode' => $e->getCode ()
	      );
	   }
	   return $result;
	}
	public function deleteTemplate($tid){
      if(! Service_MessageTemplate::delete($tid, 'template_id')){
         throw new Exception('Inner Error.');
      }
      if(! Service_MessageTemplateContent::delete($tid, 'template_id')){
         throw new Exception('Inner Error...');
      }
      
      $cacheFile = APPLICATION_PATH . '/../data/tpl_c/groupMessageTpl.txt';
      @unlink($cacheFile);
      $result = array(
         "ask" => 1,
         "message" => "Delete Template Success!",
         'tid' => $tid
      );
      return $result;
   }

	public static function getTemplateList(){

		$db = Common_Common::getAdapter ();
		$tableTemplate = new DbTable_MessageTemplate();
		$table0 = $tableTemplate->info('name');
		
		$tableContent = new DbTable_MessageTemplateContent();
		$table1 = $tableContent->info('name');
		
		$sql = $db->quoteInto(
				'SELECT a.template_id,b.template_content_id,a.template_name,a.template_short_name, CONCAT(a.template_name," [",b.language_code,"]") name,b.language_code language,b.template_content content,b.template_subject subject,a.company_code FROM `'.$table0.'` a INNER JOIN `'.$table1.'` b on a.template_id=b.template_id where a.company_code=? and a.status = "2" order by a.template_name;',
				Common_Company::getCompanyCode()
		);
// 		echo $sql;exit;
		$result = $db->query($sql);
		
		// 使用PDOStatement对象$result将所有结果数据放到一个数组中
		return $result->fetchAll();
		
	}
}