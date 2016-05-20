<?php
class Product_SupplyController extends Ec_Controller_Action
{

    public function preDispatch()
    {}

    public function listAction()
    {
        set_time_limit(0);
        Zend_Registry::set('SAPI_DEBUG', true);
        $supProcess = new Common_SupplyQtyProcess();
        $supProcess->init(false); // 初始化补货数
        $sql = "select * from seller_item_supply_qty where sync_status='0' and status='1';";
        $rows = Common_Common::fetchAll($sql);
        print_r($rows);
    }
}