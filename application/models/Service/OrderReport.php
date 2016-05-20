<?php
class Service_OrderReport {

    /**
     * 订单导出，基本版
     * @param unknown_type $orderIds
     */
    public function reportFileMake($orderIds,$fileName,$suffix='xlsx'){
        ini_set('memory_limit', '500M');
        $dataList = array();
        $con = array();
        $dataList = Service_OrderProduct::getByCondition($con,'*',10000 ,1);        	
        $file = Service_ExcelExport::exportToFile($dataList, '订单导出',$fileName,$suffix);

        return $file;
    }
    
}