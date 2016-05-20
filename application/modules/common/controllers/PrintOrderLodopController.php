<?php
class Common_PrintOrderLodopController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "common/views/print-order-lodop/"; 

        $this->view->lodopToken = '196421061011095010011211256128';
        $this->view->lodopKey = '688858710010010811411756128900';
    }
    /**
     * 打印面单
     */
    public function printAction(){
        $refId = $this->getParam('ref_id','301077531957-0');
        $paper = $this->getParam('paper','A4');
        
        $order = Service_Orders::getByField($refId,'refrence_no_platform');
        $order['paper'] = $paper;
        $this->view->order = $paper;
        $this->view->refId = $refId;
        $this->view->title = '下架单打印';
        $this->view->w = 200;//纸张宽度
        $this->view->h = 200;//纸张高度
        $html = $this->view->render($this->tplDirectory . $paper.".tpl");
        $html =  preg_replace('/\s/',' ',$html);//去除换行
        $this->view->html = $html;
        echo $this->view->render($this->tplDirectory . $paper.".js");
    }
    /**
     * 普通打印面单
     */
    public function printBatchAction(){
        $refIds = $this->getParam('ref_id',array());
        $paper = $this->getParam('paper','A4');
    
        
        $order['paper'] = $paper;
        $this->view->order = $paper; 
        $this->view->title = '下架单打印-'.time();
        
        $this->view->w = 210;//A4纸张宽度
        $this->view->h = 297;//A4纸张高度
        $htmlArr = array();
        foreach($refIds as $refId){
            $html = $this->view->render($this->tplDirectory . "pickup-batch.tpl");
            $html =  preg_replace('/\s/',' ',$html);//去除换行
            $htmlArr[] = $html;
        }
        
        $chunk = array_chunk($htmlArr, 10);
        $this->view->htmlArrChunk = $chunk;
        echo $this->view->render($this->tplDirectory . "pickup-batch.js");
    }
    /**
     * 一票一件打印面单
     */
    public function printBatchOnePieceAction(){
        $refIds = $this->getParam('ref_id',array());
        $paper = $this->getParam('paper','A4');    
    
        $this->view->order = $paper;
        $this->view->title = '下架单打印-'.time();
    
        $this->view->w = 200;//纸张宽度
        $this->view->h = 100;//纸张高度
        $htmlArr = array();
        foreach($refIds as $refId){            
            //             订单信息
            $order = Service_Orders::getByField($refId,'refrence_no_platform');
            
            //             产品信息
            $con = array('order_id'=>$order['order_id']);
            $orderProduct = Service_OrderProduct::getByCondition($con);
            //            配货信息
            $productPickup = Service_OrderProcessNew::getOrderPickup($refId);
            //             收货地址信息
            $address = Service_ShippingAddress::getByField($refId,'OrderID');
            //             发货地址信息
            $warehouse = Service_Warehouse::getByField($order['warehouse_id'],'warehouse_id');
            
            $html = $this->view->render($this->tplDirectory ."pickup-one.tpl");
            $html =  preg_replace('/\s/',' ',$html);//去除换行
            $htmlArr[] = $html;
            
            $updateRow = array(//标记打印次数
                    'has_print_pickup_label' => $order['has_print_pickup_label']+1
            );
            Service_Orders::update($updateRow, $order['order_id'],'order_id');
            //日志
            $logRow = array(
                    'ref_id' => $refId,
                    'log_content' => '订单打印下架单'
            );
            Service_OrderLog::add($logRow);
        }
        $chunk = array_chunk($htmlArr, 10);
        $this->view->htmlArrChunk = $chunk;
        $this->view->refIds = $refIds;
        echo $this->view->render($this->tplDirectory . "pickup-batch.js");
    }

    /**
     * 一票多件打印面单
     */
    public function printBatchMultPieceAction(){
        $refIds = $this->getParam('ref_id',array());
        $paper = $this->getParam('paper','A4');
    
    
        $order['paper'] = $paper;
        $this->view->order = $paper;
        $this->view->title = '下架单打印-'.time();
    
        $this->view->w = 200;//纸张宽度
        $this->view->h = 200;//纸张高度
        $htmlArr = array();
        foreach($refIds as $refId){
            //             订单信息
            $order = Service_Orders::getByField($refId,'refrence_no_platform');
            
            //             产品信息
            $con = array('order_id'=>$order['order_id']);
            $orderProduct = Service_OrderProduct::getByCondition($con);
            
            //             配货信息
            $productPickup = Service_OrderProcess::getOrderPickup($refId);
            
            //             收货地址信息
            $address = Service_ShippingAddress::getByField($refId,'OrderID');
            
            //             发货地址信息
            $warehouse = Service_Warehouse::getByField($order['warehouse_id'],'warehouse_id');
            
            $html = $this->view->render($this->tplDirectory ."pickup-mult.tpl");
            $html =  preg_replace('/\s/',' ',$html);//去除换行
            $htmlArr[] = $html;

            $updateRow = array(//标记打印次数
                    'has_print_pickup_label' => $order['has_print_pickup_label']+1
            );
            Service_Orders::update($updateRow, $order['order_id'],'order_id');
            //日志
            $logRow = array(
                    'ref_id' => $refId,
                    'log_content' => '订单打印下架单'
            );
            Service_OrderLog::add($logRow);
        }
        $chunk = array_chunk($htmlArr, 10);
        $this->view->htmlArrChunk = $chunk;
        $this->view->refIds = $refIds;
        echo $this->view->render($this->tplDirectory . "pickup-batch.js");
    }
    /**
     * 单个订单下架
     */
    public function pickupAction(){
        $refId = $this->getParam('ref_id', ''); 
        
        $order = Service_Orders::getByField($refId, 'refrence_no_platform');
        $address = Service_ShippingAddress::getByField($refId, 'OrderID');
        $con = array(
                'OrderID' => $refId
        );
        $orderProduct = Service_OrderProduct::getByCondition($con);
        $order['address'] = $address;
        $order['order_product'] = $orderProduct;
        //配货产品
        $orderProductPickup = Service_OrderProcessNew::getOrderPickup($refId,true);
        $order['order_product_pickup'] = $orderProductPickup;
         
        $this->view->order = $order;
        echo $this->view->render($this->tplDirectory . "pickup.js");
                
        $updateRow = array(//标记打印次数
                'has_print_pickup_label' => $order['has_print_pickup_label']+1
        );
        Service_Orders::update($updateRow, $order['order_id'],'order_id');
        //日志
        $logRow = array(
                'ref_id' => $refId,
                'log_content' => '订单打印下架单'
        );
        Service_OrderLog::add($logRow);
    }
    /**
     * 批量下架
     * 打印配货单和发货单
     */
    public function pickupBatchAction(){
        
    }
    
    public function testAction(){
        echo Ec::renderTpl($this->tplDirectory . "test.html",'layout');
//         echo $this->view->render($this->tplDirectory . "getScript.html");
    }
    

    /**
     * 打印条码
     */
    public function printProductLabelAction(){
    	$refIds = $this->getParam('product',array());
    	$paper = $this->getParam('paper','A4');
    
    
    	$order['paper'] = $paper;
    	$this->view->order = $paper;
    	$this->view->title = '产品条码打印-'.time();
    
    	$this->view->w = 70;//A4纸张宽度
    	$this->view->h = 30;//A4纸张高度
    	$htmlArr = array();
    	
    	foreach($refIds as $productId=>$qty){
    		//过滤
    		if(!preg_match('/^[0-9]+$/', $qty)){
    			continue;
    		}
    		$product = Service_Product::getByField($productId,'product_id');
    		for($i=0;$i<$qty;$i++){
    			$product['cat_id2'] = empty($product['cat_id2'])?'':$product['cat_id2'];
    			$category = Service_ProductCategoryOms::getByField($product['cat_id2'],'ig_id');
    			$product['ig_name'] = '';
    			if($category){
    				$product['ig_name'] = $category['ig_name_en'];
    			}
    			$this->view->row = $product;
    			$html = $this->view->render($this->tplDirectory . "product-label.tpl");
    			$html =  preg_replace('/\s+/',' ',$html);//去除换行
    			$html =  preg_replace('/\'/','"',$html);//引号
//     			echo $html;exit;
    			$htmlArr[] = $html;
    		}
    	}
    
    	$chunk = array_chunk($htmlArr, 30);
    	$this->view->htmlArrChunk = $chunk;
    	$this->view->count = count($htmlArr);
//     	echo $this->tplDirectory . "product-label.js";exit;
    	echo $this->view->render($this->tplDirectory . "product-label.js");
    }
}