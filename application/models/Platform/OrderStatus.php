<?php
class Platform_OrderStatus {
	
    public static function getStatusArr($platform = ''){
    	//根据不同平台,部分需要改变不同的显示名称
    	$messageBtn = Ec::Lang('station_letters','auto');
    	if($platform == 'b2c'){
    		$messageBtn = Ec::lang('sys_send_mail','auto');
    	}
    	
    	$statusArr = array(
    			'2' => array(
    					'name' => Ec::Lang('待生成订单','auto'),//待发货审核
    					'actions' => array(
    							'<input type="button" class="orderVerify baseBtn " value="'. Ec::Lang('order_verify','auto').'">',//发货审核
    					        
    							'<input type="button" class="allotBtn baseBtn" value="'.Ec::Lang('分配运输方式','auto').'" style="float:right;">',
    					        '<input type="button" class="biaojifahuoSelect biaojifahuoSelectForEbay baseBtn" style="float:right;" value="'.Ec::Lang('select_orders_shipped_mark','auto').'">',//选择订单标记发货
    					        '<input type="button" class="holdBtn baseBtn" value="'.Ec::Lang('order_freeze','auto').'" style="float:right;" >',//冻结
    					            					
    					            					
    					)
    			),
    			'3' => array(
    					'name' => Ec::Lang('已生成订单','auto'),//待发货
    					'actions' => array(
    					        '<input type="button" class="biaojifahuoSelect biaojifahuoSelectForEbay baseBtn" style="float:right;" value="'.Ec::Lang('select_orders_shipped_mark','auto').'">',//选择订单标记发货
    					),
    					'process_again' =>array(
    							
    					)
    			),
    			
    			'5' => array(
    					'name' => Ec::Lang('order_status_5','auto'),//冻结中
    					'actions' => array(
//     							'<input type="button" status="2" class="updateStatus baseBtn" value="'.Ec::Lang('order_to_verify','auto').'">',//转待发货审核
    							'<input type="button" class="allotBtn baseBtn" value="'.Ec::Lang('分配运输方式','auto').'" style="float:right;">',
    							'<input type="button" class="orderVerify baseBtn " value="'. Ec::Lang('order_verify','auto').'">',//发货审核
    					        '<input type="button" class="biaojifahuoSelect biaojifahuoSelectForEbay baseBtn" style="float:right;" value="'.Ec::Lang('select_orders_shipped_mark','auto').'">',//选择订单标记发货
				       )
    			),
    			
    			'7' => array(
    					'name' => Ec::Lang('order_status_7','auto'),//问题件
    					'actions' => array(
//     							'<input type="button" status="2" class="updateStatus baseBtn" value="'.Ec::Lang('order_to_verify','auto').'">',//转待发货审核
    							'<input type="button" class="orderVerify baseBtn " value="'.Ec::Lang('order_verify','auto').'">',//发货审核
    							'<input type="button" class="allotBtn baseBtn" value="'.Ec::Lang('分配运输方式','auto').'" style="float:right;">',
    					            					
    					        '<input type="button" class="biaojifahuoSelect biaojifahuoSelectForEbay baseBtn" style="float:right;" value="'.Ec::Lang('select_orders_shipped_mark','auto').'">',//选择订单标记发货
    					            					
    					        '<input type="button" class="holdBtn baseBtn" value="'.Ec::Lang('order_freeze','auto').'" style="float:right;" >',//冻结
    					            					
    					        ),
    					'abnormal_type' =>array()
    			),
    			'1' => array(
    					'name' => Ec::Lang('付款未完成','auto'),//问题件
    					'actions' => array(
//     							'<input type="button" status="2" class="updateStatus baseBtn" value="'.Ec::Lang('order_to_verify','auto').'">',//转待发货审核
    							'<input type="button" class="orderVerify baseBtn " value="'.Ec::Lang('order_verify','auto').'">',//发货审核    					           					
    					        '<input type="button" class="holdBtn baseBtn" value="'.Ec::Lang('order_freeze','auto').'" style="float:right;" >',//冻结
    					            					
    					        )
    			),
    			
    	);
    	return $statusArr;
    }    
}