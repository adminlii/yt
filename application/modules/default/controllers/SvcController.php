<?php

class Default_SvcController extends Zend_Controller_Action {
    public function init(){
        $action = $this->_request->getActionName();
        $this->tplDirectory = "default/views/default/";
    }

    public function indexAction () {
        $this->_forward('wsdl');
    }
    
    public function webServiceDebugAction()
    {
    	//set_time_limit(0);
    	$input = file_get_contents('php://input');
    	if(!empty($input)){
    		$info = "req:\n".print_r($input,true);
    		ob_start();
    
    	    $server = new SoapServer(APPLICATION_PATH . "/../data/wsdl/Ec.wsdl");
            $server->setClass('Common_Svc');
            $server->handle();
    
    		$out = ob_get_contents();
    		ob_end_clean();
    		$info .= "\n";
    		$info .= "res:\n".print_r($out,true);
    		Ec::showError($info, '_svc_res_info_' . date('Y-m-d') . "_");
    		echo $out;
    	}else{
    		echo 'Invalid SOAP request';
    	}
    	exit();
    }
    

    public function webServiceAction () {
        if($this->_request->isPost()){
        	$input = file_get_contents('php://input');
        	file_put_contents(APPLICATION_PATH.'/../data/log/_all_req'.date('Y-m-d').'.txt', date("Y-m-d H:i:s")."\n".$input."\n\n",FILE_APPEND);
        	 
            $server = new SoapServer(APPLICATION_PATH . "/../data/wsdl/Ec.wsdl");
            $server->setClass('Common_Svc');
            $server->handle();
        }else{
            echo 'Error Request';
        }
        
    }

    public function wsdlAction () {
        $host = $this->_request->getHttpHost();
        header("Content-type: text/xml; Charset=utf-8");        
        $content =  file_get_contents(APPLICATION_PATH . "/../data/wsdl/Ec.wsdl");
        $content = preg_replace('/www\.eb\.com/',$host,$content);
        echo $content;exit;
    }
    public function wsdlFileAction () {
        $host = $this->_request->getHttpHost();
        $content =  file_get_contents(APPLICATION_PATH . "/../data/wsdl/Ec.wsdl");
        $content = preg_replace('/www\.eb\.com/',$host,$content);
        $fileName = preg_replace('/([a-zA-Z_0-9]+)\.([a-zA-Z_0-9]+)\.([a-zA-Z_0-9]+)/e', 'strtolower(\\1)', $host);
        $fileName = APPLICATION_PATH.'/../data/cache/'.$fileName.'-Ec.wsdl';
        if(!file_exists($fileName)){
            file_put_contents($fileName, $content);
        }
        Common_Common::downloadFile($fileName);
        exit;
    }
    /**
     * 初始化OMS基础数据
     * 从WMS中获取，保存到OMS
     */
    public function initAction() {
    	$pw = $this->getParam('pw','');
    	$config = Service_Config::getByField('OMS_INIT','config_attribute');
    	if(!$config){
			$config = array (
					'config_attribute' => 'OMS_INIT',
					'config_value' => '13579246810',
					'config_description' => '初始化OMS基础数据',
					'config_add_time' => date('Y-m-d H:i:s'),
					'config_update_time' => date('Y-m-d H:i:s'),
			);
			Service_Config::add($config);
    	}
		$pwExist = $config['config_value'];
    	
    	if($pw==$pwExist&&$this->getRequest()->isPost()){
    		
    		set_time_limit ( 0 );
    		$wmsService = new Common_ThirdPartWmsAPI ();
    		$wmsProcess = new Common_ThirdPartWmsAPIProcess ();
    		// 同步国家
    		$rs = $wmsProcess->syncCountry();
    		$rs['service'] = 'syncCountry';
    		print_r($rs);
    		// 同步揽收地址
    		$rs = $wmsProcess->syncReceivingArea();
    		$rs['service'] = 'syncReceivingArea';
    		print_r($rs);
    		
    		// 同步仓库
    		$rs = $wmsProcess->syncWarehouse();
    		$rs['service'] = 'syncWarehouse';
    		print_r($rs);
    		// 同步运输方式
    		$rs = $wmsProcess->syncWarehouseShipment();
    		$rs['service'] = 'syncWarehouseShipment';
    		print_r($rs);
    		// 同步品类
    		$rs = $wmsProcess->syncCategory();
    		$rs['service'] = 'syncCategory';
    		print_r($rs);
    		// 产品单位
    		$rs = $wmsProcess->syncProductUom();
    		$rs['service'] = 'syncProductUom';
    		print_r($rs);
    		// 质检项
    		$rs = $wmsProcess->syncQcOption();
    		$rs['service'] = 'syncQcOption';
    		print_r($rs);
    		
    		// 同步费用类型到OMS
    		$rs = $wmsProcess->syncFeeType();
    		$rs['service'] = 'syncFeeType';
    		print_r($rs);
    		// 同步订单操作节点到OMS
    		$rs = $wmsProcess->syncOrderOperationType();
    		$rs['service'] = 'syncOrderOperationType';
    		print_r($rs);
    		
    		// 同步客户
    		$rs = $wmsProcess->createAllCustomer ();
    		$rs['service'] = 'createAllCustomer';
    		print_r($rs);
    		// 同步产品
    		//$wmsProcess->createAllProduct ();
    		echo '===========================';
    		exit ();
    	}
    	echo Ec::renderTpl ( $this->tplDirectory . "base_data_init.tpl", 'layout' );
    }
}