<?php
class Product_SellerItemSupQtyController extends Ec_Controller_Action
{

    public function testAction()
    {
        $userAccount = $this->getParam('acc','');
        $item_id = $this->getParam('item_id','');
        $product_sku = $this->getParam('sku','');
        $wh_code = $this->getParam('wh_code','');
        
        $sql = "select * from seller_item_supply_qty where supply_type='1' and status='1'";
        if($userAccount){
            $sql.=" and user_account='{$userAccount}'";
        }
        if($item_id){
            $sql.=" and item_id='{$item_id}'";
        }
        if($product_sku){
            $sql.=" and sku='{$product_sku}'";
        }
        if($wh_code){
            $sql.=" and supply_warehouse='{$wh_code}'";
        }
        $rows = Common_Common::fetchAll($sql);
        
        echo ("查找需要按仓补货的产品\n" . $sql . "<br/>");
        echo ("共有" . count($rows) . "条需要按仓补货的记录<br/>");
        $process = new Common_SupplyQtyProcess();
        // print_r($rows);exit;
        foreach($rows as $k => $v){
            $wh_code = $v['supply_warehouse'];
            $userAccount = $v['user_account'];
            $product_sku = $v['sku'];
            if(empty($product_sku)){
                continue;
            }
            if(empty($wh_code)){
                continue;
            }
            $qty = $process->getWarehouseInventory($userAccount, $product_sku, $wh_code, $v);
            $v['qty'] = $qty;
            $rows[$k] = $v;
            // Common_SupplyQtyProcess::log("平台:[{$v['platform']}]账号:[{$v['user_account']}],SKU:[{$v['sku']}],仓库:[{$v['supply_warehouse']}],实际可用库存:[{$qty}]===========平台可销售数");
            // 更新补货数为产品可用数
            $sql = "update seller_item_supply_qty set qty={$qty} where id='{$v['id']}';";
            echo $sql . "<br/>";
            Common_Common::query($sql);
        }
    }
}