<?php
class Common_RegionController extends Ec_Controller_Action {
	public function preDispatch() {
		$this->tplDirectory = "common/views/region/";
		$this->serviceClass = new Service_Region ();
	}
	public function listAction() {
	}
	public function editAction() {
	}
	public function getByJsonAction() {
	}
	public function deleteAction() {
	}
	public function treeAction() {
        echo Ec::renderTpl($this->tplDirectory . "region_tree.tpl", 'layout');
	}
	
	/**
	 * 多级分类
	 */
	public function getRegionAction() {
		$pid = $this->getParam ( 'pid', '1' );
		$pid = empty ( $pid ) ? '0' : $pid;
		$con = array (
				'parent_id' => $pid 
		);
		$data = Service_Region::getByCondition ( $con );
		// print_r($data);exit;
		echo Zend_Json::encode ( $data );
		exit ();
	}

	/**
	 * 多级分类
	 */
	public function getRegionForReceivingAction() {
	    $pid = $this->getParam ( 'pid', '1' );
	    $pid = empty ( $pid ) ? '0' : $pid;
	    $con = array (
	            'parent_id' => $pid
	    );
	    $data = Service_Region::getByCondition ( $con );
	    
	    //允许的区域 start	   
	    $allowArea = Service_ReceivingProcess::getSupportArea();
	    //允许的区域 end
	    
	    $result = array();
	    foreach($data as $v){
	        if(!in_array($v['region_id'],$allowArea)){
	            continue;
	        }
	        $result[] = $v;
	    }
	    echo Zend_Json::encode ( $result );
	    exit ();
	}
}