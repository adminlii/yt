<?php
class Default_ZcController extends Ec_Controller_DefaultAction
{

    public $_authCode = 1; // 是否用验证码
    public $host = 'localhost'; // 连接数据库地址
    public $user = 'root'; // 连接数据库用户名
    public $password = ''; // 连接数据库密码
    public $wms_db = 'wms'; // 连接数据库数据库名
    public $oms_db = 'oms'; // 连接数据库数据库名
    public function preDispatch()
    {
        $this->view->authCode = 1;
        //$this->tplDirectory = "default/views/register/";
        
    }

    public function indexAction()
    {
	   // $host = Zend_Registry::get('wms_db');
        if($this->getRequest()->isPost()){
	        $myy=mysqli_connect($this->host,$this->user,$this->password);
	        $pd=mysqli_query($myy,"select user_code from ".$this->oms_db.".user where user_code='{$_POST['user_code']}'");
			while ( $aa=mysqli_fetch_row($pd) )
	            {
		            $pd1=$aa[0];
	            }
		mysqli_close($myy);
	        if(empty($pd1)){
		        
            $params = $this->getRequest()->getParams();
//             print_r($params);
//echo $params['user_password_confirm'];
			$sb=2;
            $return = array(
                'ask' => 2,
                'message' => $sb
            );
            try{        
                    
                $row = array(
                    'user_code' => trim($this->getParam('user_code', '')) ,
                    'company_code' => Common_GenCompanyCode::genCompanyCode(),
                    'is_admin' => '1',
                    'up_id' => '1',
                    //'user_password' => '123456',
                    'user_password' => $this->getParam('user_password', ''),
                    //'user_password_confirm' => $this->getParam('user_password_confirm', ''),
                    'user_name' => $this->getParam('user_name', ''),
                    'user_status' => '2',
                    'user_email' => $this->getParam('user_code', '').'@ruston.cc',
                    //'user_email' => $this->getParam('user_email', ''),
                    'user_mobile_phone' => $this->getParam('user_mobile_phone', ''),
                    'user_phone' => $this->getParam('user_phone', ''),
                    'user_add_time' => date('Y-m-d H:i:s'),
                    'user_last_login' => date('Y-m-d H:i:s'),
                    'user_update_time' => date('Y-m-d H:i:s'),
                    'user_password_update_time' => date('Y-m-d H:i:s'),
                	'user_sources'=>$this->getParam('user_sources', ''),
                	'platform_token'=>$this->getParam('platform_token', '')
                );
               
                if(empty($row['user_sources'])){
                	unset($row['user_sources']);
                }
                if(empty($row['platform_token'])){
                	unset($row['platform_token']);
                }               
                
                $emailExist = Service_User::getByField($row['user_email'], 'user_email');
                /*if($emailExist){
                    //throw new Exception('邮箱已被使用');
                    $return['ask'] = 5;
                	$return['message'] = '邮箱已被使用';
                    //die(Zend_Json::encode($return));
                    die(Zend_Json::encode($return));
                }*/
                $row['user_password'] = Ec_Password::getHash($row['user_password']);
                $userId = Service_User::add($row);
                //公司
                
                $companyRow = array(
                    'company_code' => $row['company_code'],
                    'company_name' => (empty($_POST['company_name']))?'':$_POST['company_name'],
                    //'company_name' => '',
                	'company_update_time'=>date('Y-m-d H:i:s')
                );
                Service_Company::add($companyRow);
                
                //推送客户信息至WMS
                $obj = new Common_ThirdPartWmsAPI();
                $return_wms = $obj->createCompany($row['company_code']);
                if($return_wms['ask']!='Success'){
	                $return['ask'] =$return_wms['ask'];
                    $return['message'] =$return_wms['message'];
	                die(Zend_Json::encode($return));
                }
                
                //发送验证邮件
                //$row['company_name']=$company_name;
                //$this->sendEmail($row);
                
                $return['ask'] = 1;
                $return['message'] = '账号注册成功';
                
                if($return['ask']==1){
	                $asd=array('customer_status'=>'2');
	                $token = md5(md5(strrev(md5($companyRow['company_code']))));
       				$key = md5(md5(md5(strrev(md5($companyRow['company_code'])))));
	                 $my=mysqli_connect($this->host,$this->user,$this->password);
	                 mysqli_query($my,"SET AUTOCOMMIT=0");//设置为不自动提交，因为MYSQL默认立即执行
					 mysqli_query($my,"BEGIN");//开始事务定义
					 $a=mysqli_query($my,"update ".$this->oms_db.".user set user_status=1 where user_code='{$row['user_code']}'");
					 $b=mysqli_query($my,"update ".$this->oms_db.".company set company_status=2 where company_code='{$companyRow['company_code']}'");
					 $c=mysqli_query($my,"update ".$this->wms_db.".customer set customer_status=2,customer_activate_code='AE' where customer_code='{$companyRow['company_code']}'");
					 $d=mysqli_query($my,"insert into ".$this->oms_db.".customer_balance(customer_code,customer_id) select customer_code,customer_id from ".$this->wms_db.".customer where customer_code='{$companyRow['company_code']}'");
					
					 $e=mysqli_query($my,"update ".$this->oms_db.".company set app_token='{$token}',app_key='{$key}' where company_code='{$companyRow['company_code']}'");
					 //$f=mysqli_query($my,"update oms_bd.company set app_key='{$key}' where company_code='{$companyRow['company_code']}'");
					 //echo $a.$b.$c.$d.$e.$f.'asdf';
					 //echo $token.'<hr>'.$key;
					 //echo "update oms_bd.company set app_token='{$token}' where company_code='{$companyRow['company_code']}'"."update oms_bd.company set app_key='{$key}' where company_code='{$companyRow['company_code']}'";
					 $del="a";
					 if($a!=1 || $b!=1|| $c!=1|| $d!=1|| $e!=1)
						{
							$del='b';
							mysqli_query($my,"ROLLBACK");//判断执行失败回滚
						}
					if($del=='a'){
							mysqli_query($my,"COMMIT");//执行事务
							$roww['cb_value']='1000000';
	    					echo Service_CustomerBalanceWms::update($roww,$companyRow['company_code'], "customer_code");
					}
							
					if($del=='b'){
						Service_User::delete($row['user_code'], "user_code");
					}
					mysqli_close($my);
				}
				if($del=='b'){
					$return['ask'] = 3;
                	$return['message'] = '账号注册失败';
				}else{
					$return['token'] = $token;
                	$return['key'] = $key;
                	$return['user_code'] = $row['user_code'];
				}
				
                $this->view->jumpMsg = $row['user_code'] . "账号注册成功";
            }catch(Exception $e){
                $return['message'] = $sb;
                //$return['message'] = $e->getMessage();
            }

            }else{
            //if($return['message']==2){
	            $my=mysqli_connect($this->host,$this->user,$this->password);
	            //echo "select * from oms_bd.user where user_code='{$_POST['user_code']}'";
	            $a=mysqli_query($my,"select u.user_code,c.app_token,c.app_key from ".$this->oms_db.".user u left join ".$this->oms_db.".company c 
on u.company_code=c.company_code where user_code='{$_POST['user_code']}'");
	            while ( $aa=mysqli_fetch_row($a) )
	            {
		            $return['ask']='1';
		            $return['message']='成功';
		            $return['token']=$aa[1];
		            $return['key']=$aa[2];
		            $return['user_code']=$aa[0];
	            }
	            mysqli_close($my);
            }
            die(Zend_Json::encode($return));
        }
        echo $this->view->render($this->tplDirectory . 'register.tpl');
    }
}