<?php
class Service_TemplateGroupProcess extends Common_Service
{
	private $groups = array();
	public static function getCatList(){
		$companyCode = Common_Company::getCompanyCode();
		$groups = self::_getCatList($companyCode,0);
		
		print_r($groups);exit;
	}
	
	private static function _getCatList($companyCode,$pid='0'){
		$topCondition = array("group_pid"=>$pid,"company_code"=>$companyCode);
		$groupList = Service_TemplateGroup::getByCondition($topCondition);
		foreach($groupList as $k=> $group){
			$subGroupList = self::_getCatList($group['group_pid'],$companyCode);
			$groupList[$k]['sub'] = $subGroupList;
		}
		return $groupList;
	}
}