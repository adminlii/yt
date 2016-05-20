<?php
class Product_PlatformUserController extends Ec_Controller_Action
{

    public function preDispatch()
    {
        $this->tplDirectory = "product/views/platform-user/";
        $this->serviceClass = new Service_PlatformUser();
    }

    public function listAction()
    {
        $supply_type_arr = Common_Type::supplyTypeArr();
        $status_arr = Common_Type::supplyStatusArr();
        
        if($this->_request->isPost()){
            $page = $this->_request->getParam('page', 1);
            $pageSize = $this->_request->getParam('pageSize', 20);
            
            $page = $page ? $page : 1;
            $pageSize = $pageSize ? $pageSize : 20;
            
            $return = array(
                "state" => 0,
                "message" => "No Data"
            );
            
            $condition = array();
            
            $platform = $this->getParam('platform', '');
            $account = $this->getParam('user_account', '');
            $supply_type = $this->getParam('supply_type', '');
            $status = $this->getParam('status', '');
            
            foreach($condition as $k => $v){
                if(! is_array($v)){
                    $condition[$k] = trim($v);
                }
            }
            
            $sql = "select TYPE from platform_user a left join platform_user_supply_set b on a.pu_id=b.pu_id ";
            
            $where = ' where 1=1 ';
            if($platform != ''){
                $where .= " and a.platform='{$platform}'";
            }
            if($account != ''){
                $where .= " and a.user_account='{$account}'";
            }
            if($supply_type != ''){
                $where .= " and b.supply_type='{$supply_type}'";
            }

            if($status != ''){
                $where .= " and b.status='{$status}'";
            }
            
            $sql .= $where;
            $db = Common_Common::getAdapter();
            $countSql = str_replace('TYPE', 'count(*)', $sql);
            
            $count = $db->fetchOne($countSql);
            $return['total'] = $count;
            
            if($count){
                
                $start = ($page - 1) * $pageSize;
                $limit = " limit $start,$pageSize";
                $sql .= $limit;
                $rowsSql = str_replace('TYPE', 'b.*,a.pu_id,a.platform,a.user_account,a.platform_user_name', $sql);
                // echo $rowsSql;exit;
                $rows = $db->fetchAll($rowsSql);
                $data = array();
                foreach($rows as $k => $v){
                    $v['platform'] = strtoupper($v['platform']);
                    $v['supply_type_title'] = '未设置';
                    $v['supply_qty'] = isset($v['supply_qty'])?$v['supply_qty']:'';
                    if(isset($v['supply_type'])){
                        $v['supply_type_title'] = $supply_type_arr[$v['supply_type']];
                        if($v['supply_type']=='1'){
                            $v['supply_type_title'].=','.Ec::Lang('supply_warehouse').':'.$v['supply_warehouse'];
                        }else{
                            //$v['supply_type_title'].=','.Ec::Lang('supply_qty').':'.$v['supply_qty'];                            
                        }
                    }
                    
                    $v['status_title'] = isset($v['status'])?$status_arr[$v['status']]:'未设置';
                    $rows[$k] = $v;
                }
                // print_r($rows);exit;
                $return['data'] = $rows;
                $return['state'] = 1;
                $return['message'] = "";
            }
            die(Zend_Json::encode($return));
        }
        $con = array(
            'status' => '1'
        );
        $platformUsers = Service_PlatformUser::getByCondition($con,'*',0,0,'platform asc');
        foreach($platformUsers as $v){
            $platforms[] = strtoupper($v['platform']);
        }
        // print_r($platformUsers);exit;
        $this->view->platformUsers = $platformUsers;
        $this->view->platforms = array_unique($platforms);
        
        $con = array(
            'warehouse_status' => '1'
        );
        $warehouse = Service_Warehouse::getByCondition($con);
        
        $warehouseArr = array();
        foreach($warehouse as $v){
            $warehouseArr[$v['warehouse_id']] = $v;
        }
//         print_r($warehouseArr);exit;
        $this->view->warehouseArr = $warehouseArr;
        $this->view->warehouseJson = Zend_Json::encode($warehouseArr);
        
        $this->view->supply_type_arr = $supply_type_arr;
       
        $this->view->status_arr = $status_arr;
        
        echo Ec::renderTpl($this->tplDirectory . "platform_user_list.tpl", 'layout');
    }

    public function saveSupplyTypeAction()
    {
        $return = array(
            'ask' => 0,
            'message' => 'Fail.'
        );
        $pu = $this->getParam('pu', array());
        
        try{
            $data = array();
            foreach($pu as $pu_id => $v){
                $v['supply_qty'] = 1000;
                $data[$pu_id] = array(
                        'pu_id' => $pu_id,
                        'supply_type' => $v['supply_type'],
                        'supply_warehouse' => $v['supply_warehouse'],
                        'supply_qty' => $v['supply_qty'],
                        'status' => $v['status'],
                        'op_user_id' => Service_User::getUserId()
                );
                if(empty($v['supply_qty'])){
//                     throw new Exception('请选择仓库/填写补货数');
                }
            }
            
            foreach($data as $pu_id => $v){
                $platformUser = Service_PlatformUser::getByField($pu_id, 'pu_id');
                if(!$platformUser){
                    throw new Exception('Inner Error');
                }
                switch($v['supply_type']){
                    case '1':
                        if(empty($v['supply_warehouse'])){
                            throw new Exception('请选择仓库-->'."platform[{$platformUser['platform']}],user_account:[{$platformUser['user_account']}][{$platformUser['platform_user_name']}]");
                        }
                        break;
                    case '2':
                        if(!preg_match('/^[0-9]+$/', $v['supply_qty'])){
                            throw new Exception('请填写补货数(需为数字)-->'."platform[{$platformUser['platform']}],user_account:[{$platformUser['user_account']}][{$platformUser['platform_user_name']}]");
                        }
                        break;
                    default:
                        throw new Exception(Ec::Lang('supply_type_err'));
                }
                $exist = Service_PlatformUserSupplySet::getByField($pu_id, 'pu_id');
                $v = Common_ApiProcess::nullToEmptyString($v);
                if($exist){
                    $v['update_time'] = now();
                    Service_PlatformUserSupplySet::update($v, $exist['puss_id'], 'puss_id');
                }else{
                    $v['create_time'] = now();
                    $v['update_time'] = now();
                    Service_PlatformUserSupplySet::add($v);
                }
            }

            //补货开关,店铺补货设置初始化补货任务~
            foreach($data as $pu_id => $v){
                $status = $v['status'];
                $platformUser = Service_PlatformUser::getByField($pu_id, 'pu_id');
                if(! $platformUser){
                    throw new Exception('Inner Error');
                }
                $platform = strtolower($platformUser['platform']);
                $acc = $platformUser['user_account'];
                $comp = $platformUser['company_code'];
                switch($platform){
                    case 'ebay':
                        Amazon_Common::Ebay_reviseInventory_Switch($platform, $acc, $status, $comp);
                        break;
                    case 'amazon':
                        Amazon_Common::Amazon__POST_INVENTORY_AVAILABILITY_DATA_Switch($platform, $acc, $status, $comp);                        
                        break;
                }
            }
            $return['ask'] = 1;
            $return['message'] = 'Success';
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        
        echo Zend_Json::encode($return);
    }
    

    public function productOnlineSupplyQtyInitAction()
    {
        set_time_limit(0);
        $return = array(
            'ask' => 0,
            'message' => 'Fail.'
        );
        $pu_id = $this->getParam('pu_id', '');
        $key = $this->getParam('key', '');
        
        $trial_operation = Service_Config::getByField('SYSTEM_SUPPER_ADMIN_KEY', 'config_attribute');
        if(! $trial_operation){
            $trial_operation = array(
                'config_attribute' => 'SYSTEM_SUPPER_ADMIN_KEY',
                'config_value' => 'eccang',
                'config_description' => '解锁码',
                'config_add_time' => now(),
                'config_update_time' => now()
            );
            Service_Config::add($trial_operation);
        }
        
        try{
            if($trial_operation['config_value'] != $key){
                throw new Exception('解锁码错误');
            }
            
            $platformUser = Service_PlatformUser::getByField($pu_id, 'pu_id');
            if(! $platformUser){
                throw new Exception('账号不存在');
            }
            $pUserSet = Service_PlatformUserSupplySet::getByField($pu_id, 'pu_id');
            if(! $pUserSet){
                throw new Exception('账号未设置补货策略');
            }
            $con = array(
                'platform' => $platformUser['platform'],
                'user_account' => $platformUser['user_account']
            );
            $rows = Service_SellerItemSupplyQty::getByCondition($con);
            foreach($rows as $row){
                $updateRow = array();
                if($pUserSet['supply_type']=='1'){//按仓补货
                    $updateRow['supply_type'] = $pUserSet['supply_type'];
                    $updateRow['supply_warehouse'] = $pUserSet['supply_warehouse'];
                }else{//自定义数量补货
                    $updateRow['supply_type'] = $pUserSet['supply_type'];
                    $updateRow['supply_warehouse'] = $pUserSet['supply_warehouse'];   
                    if(empty($row['qty'])){
                        $updateRow['qty'] = '100';
                    }                 
                }
                $updateRow['status'] = $pUserSet['status'];
                //同步状态改为待同步
                $updateRow['sync_status'] = '0';
                Service_SellerItemSupplyQty::update($updateRow, $row['id'],'id');
            }
            $return['ask'] = 1;
            $return['message'] = 'Success';
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        
        echo Zend_Json::encode($return);
    }
}