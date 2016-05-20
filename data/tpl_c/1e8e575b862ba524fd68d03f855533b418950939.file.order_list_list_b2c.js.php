<?php /* Smarty version Smarty-3.1.13, created on 2014-07-22 11:10:33
         compiled from "E:\Zend\workspaces\ruston_oms\application\modules\order\js\order\order_list_list_b2c.js" */ ?>
<?php /*%%SmartyHeaderCode:1837653cdd62926ed96-48738846%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1e8e575b862ba524fd68d03f855533b418950939' => 
    array (
      0 => 'E:\\Zend\\workspaces\\ruston_oms\\application\\modules\\order\\js\\order\\order_list_list_b2c.js',
      1 => 1405996338,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1837653cdd62926ed96-48738846',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_53cdd6292dff11_77350585',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53cdd6292dff11_77350585')) {function content_53cdd6292dff11_77350585($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'E:\\Zend\\workspaces\\ruston_oms\\libs\\Smarty\\plugins\\block.t.php';
?>

function getDetail(order_id_arr){
	//订单明细
	$.ajax({
		type: "POST",
		async: false,
		dataType: "json",
		url: '/order/order-list/get-list-detail-list',
		data: {'order_id_arr':order_id_arr},
		success: function (results) {
			$.each(results,function(k,json){

				if(json.ask){
					var orderId = json.data.order_id;
					var html = '';
					var val = json.data;
					
					$.each(json.data.order_product, function(k, v) {
			            var target = (v.url&&v.url!='null'?" target='_blank'":"");
			            var pic = v.pic&&v.pic!='null'?v.pic:'/images/base/noimg.jpg';
			            var url = (v.url&&v.url!='null'?v.url:"javascript:;");
			            
			           
			            html +='';
			            html+='<div style="clear:both;" class="product_data_line">';
			            	html+='<div style="clear:both;">';
			            	html +='<div style="float:left;width:75%;text-align:left;" class="ellipsis"><a href="'+url+'" '+target+' style="line-height: 25px;">'+ v.product_title+'</a></div>' ;
			            	html+='</div>';
			            html+='</div>';

			            if(json.data.platform=='amazon'){
			            	v.op_record_id = v.op_ref_tnx;
			            }
	        			v.sku = v.sku!=undefined?v.sku:v.product_sku;
//			        	if(v.sub_product){
//			        		$.each(v.sub_product,function(sub_k,sub){
//
//					            html += '<div style="clear:both;">';
//					            
////					        	html += '<div class="recordNo">recordNo :'+(v.op_record_id?v.op_record_id:'')+'</div>';   
//					              
//					            html += '<div class="itemId" style="display:none;">ItemID:'+(v.op_ref_item_id?v.op_ref_item_id:'')+'</div>';
//					            
//			    	            html += '<div class="sku">SKU: <b class="rmaSku">' + (v.sku)+'</b>' ;
//	    			            html +='</div>';
//			        			html += '<div class="warehouseSkuWrap">仓库SKU:<a  href="javascript:;" class="inventoryBtn warehouseSku" title="点击查看库存" warehouse_code="'+(val.warehouse_id&&warehouseJson[val.warehouse_id] ? warehouseJson[val.warehouse_id].warehouse_code:'')+'"  sku="'+sub.pcr_product_sku+'">'+sub.pcr_product_sku + '</a></div>';
//			    	            
//	    			            html += '<div class="qty">Qty: <b class="rmaWarehouseSkuQty">' + (v.op_quantity*sub.pcr_quantity)+'</b></div>' ;
//
//			    	           
//	    			            
//	    			            html+='</div>';
//	    			            
//			        		})
//			        		
//			        	}else{
    			            html+='<div style="clear:both;">';

//				        	html += '<div class="recordNo">recordNo :'+(v.op_record_id?v.op_record_id:'')+'</div>';				              
				            html += '<div class="itemId" style="display:none;">ItemID:'+(v.op_ref_item_id?v.op_ref_item_id:'')+'</div>';
				            
		    	            html += '<div class="sku">SKU: <b class="rmaSku">' + (v.sku!=undefined?v.sku:v.product_sku)+'</b>' ;
    			            html +='</div>';
		        			//html += '<div class="warehouseSkuWrap">仓库SKU:<a  href="javascript:;" class="inventoryBtn warehouseSku" title="点击查看库存" warehouse_code="'+(val.warehouse_id&&warehouseJson[val.warehouse_id]? warehouseJson[val.warehouse_id].warehouse_code:'')+'" sku="'+v.sku+'">'+v.sku + '</a></div>';
		    	            
    			            html += '<div class="qty">Qty: <b class="rmaWarehouseSkuQty">' + (v.op_quantity)+'</b></div>' ;

		    	            
    			            
    			            html += '</div>';
//			        	}
			        });
					
					/**/
					//$('#country_'+json.data.order_id).html(json.data.consignee_country_code+'['+json.data.consignee_country_name+']');  
					//$('#consignee_'+json.data.order_id).html('收件人信息:姓名:'+json.data.Name+'国家:'+json.data.Country+'['+json.data.CountryName+']'+'收件地址:'+json.data.Street1+' '+json.data.Street2+'');

					
					$('#order_detail_'+json.data.order_id).html(html);
				};
			});
		}
	});
}



function loadData(page, pageSize) {
    EZ.listDate.myLoading();
    $.ajax({
        type: "POST",
        async: false,
        dataType: "json",
        url: EZ.url + "list/a/a/page/" + page + "/pageSize/" + pageSize,
        data: EZ.searchObj.serializeArray(),
        error: function () {
            paginationTotal = 0;
            EZ.listDate.EzWmsSetSearchData({msg: 'The URL request error.'});
            return;
        },
        success: function (json) {
            paginationTotal = json.total;
            if (!isJson(json)) {
                paginationTotal = 0;
                EZ.listDate.EzWmsSetSearchData({msg: 'Returns the data type error.'});
                return;
            }
            if (json.state == '1') {
            	EZ.listDate.html(EZ.getListData(json));
            	
            	var order_id_arr = [];
            	$.each(json.data,function(k,v){
            		order_id_arr.push(v.order_id);
            	});
            	//获取明细
            	getDetail(order_id_arr);
            	//隐藏多行的SKU
            	hideExcessSku();
            } else {
                EZ.listDate.EzWmsSetSearchData({state: 1});
            }
            if(json.reTongji){
            	setTimeout(function(){tongji();},500);
            }
            
        }
    });
}


EZ.getListData = function(json) {
	//设置订单条数
	setPageTotal(json.total);

    var clone = $('#fix_header_content .clone').clone();
    $('#fix_header').html(clone);

	var html = '';
	if($('.chooseTag').size()<1){
		$('.statusTag2').addClass('chooseTag');
		var opHtml = '';
		$.each(statusArr['2']['actions'],function(k,v){
			opHtml += v;
		})
		$(".opDiv").html(opHtml);
	}
	$(".checkAll").attr('checked', false);
	var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;

    //买家ID
	$.each(json.data, function(key, val) {	
		var orderId = val.order_id;
		var clazz = key%2==1?'table-module-b2':'table-module-b1';
		html += "<tr class='"+clazz+"' id='order_wrap_"+val.order_id+"'>";
		html += '<td class="ec-center"><input type="checkbox" class="checkItem" name="orderId[]" ref_id="'+val.refrence_no_platform+'" value="' + val.order_id + '"/></td>';
		        	

		/**单头信息 开始**/
    	html += '<td style="word-wrap:break-word;" class="order_line" valign="top">';
    	
    	var order_code_title = "\"" + $.getMessage("order_code_title") + val.refrence_no_platform + "\"";
    	var order_tag_a = "<a href='javascript:void(0)' " +
        "onclick='leftMenu(\"Order\"," + order_code_title + ",\"/order/order-list/detail/paramId/" + val.order_id + "\")'>" + 
        val.refrence_no_platform + "</a>";
		//html += '<p class="refrence_no_platform ellipsis"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
order_code<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:<a  href="javascript:;" class="orderDetail" url="/order/order/create/ref_id/'+val.refrence_no_platform+'" >'+ val.refrence_no_platform + '</a></p>';
		html += '<p class="refrence_no_platform ellipsis"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
order_code<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:'+ order_tag_a + '</p>';
		
		
		
		html += '<p style="" class="refrence_no ellipsis"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
refrence_no<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:' + ((val.refrence_no)?val.refrence_no:"") +'</p>'; 			
		
		html += '<span style="display:none;" id="consignee_'+val.order_id+'"></span>';

//    		html += '<td style="word-wrap:break-word;">' + (val.order_desc == undefined ? '' : val.order_desc) + '</td>';
		html += '<p style="" class="buyerId ellipsis"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:' +  val.consignee_name +'</p>'; 
		html += '<p style="" class="buyerMail ellipsis"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_email<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: ' +val.consignee_email+'</p>'; 
//		html += '<p style="" class="userAccount ellipsis">卖家: ' +  user_account_arr_json[val.user_account].platform_user_name + '</p>'; 
		
 
		if(val.refrence_no_warehouse){
			//html += '<p style="" class="refrence_no_warehouse ellipsis">仓库单号:' + (val.refrence_no_warehouse) +'</p>'; 			
		} 
		if($('#status').val()==''){
			html += '<p style="" class="order_status_title ellipsis"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
order_status<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:' + (statusArr[val.order_status].name) +'</p>';  			
		}

		html+='<p>';
		
        if(val.sync_status==1){
//            html += '<span class="iconshipamazon" style="cursor: pointer;float:left;" title="已标记发货并同步到Amazon"></span>';
        } 
        if(val.order_desc){
//        	html += '<span class="iconshipamazon" style="cursor: pointer;float:left;" title="订单备注"></span>';
		}
		if(val.operator_note){
//			html += '<span class="iconshipamazon" style="cursor: pointer;float:left;" title="客服留言"></span>';			
		} 
		if(val.abnormal_reason&&val.abnormal_reason!='null'){
//			html += '<span class="iconshipamazon" style="cursor: pointer;float:left;" title="异常信息"></span>';
		}
        html += ' <span class="logIcon " ref_id="'+val.refrence_no_platform+'" style="float: left;cursor: pointer;" title="View Log"></span>';   
		html+='</p>';

		html += '<p style="clear:both;"></p>';
		
		
		html += '</td>';
		/**单头信息 结束**/
		

		/**订单详情信息 开始**/
        html += '<td valign="top">';
        //此处定义一个样式，用以区别是否为合并订单，拆分订单(order_merge_x)
        html += '<div  class="product_data order_detail order_merge_'+val.is_merge+'" id="order_detail_'+val.order_id+'"><div style="text-align: center;margin-top:30px;"><img src="/images/base/loading.gif"/></div></div>'; 
        
        html +='<div style="width: 100%; clear:both; margin-top: 1px;">';
        if(val.order_desc){
			html += '<p class="note_span"  id="note_'+val.order_id+'"><b><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
order_desc<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</b>:' + (val.order_desc) +'</p>';
		}
		if(val.operator_note){
            //html += '<p  class="operator_note_span" id="operator_note_'+val.order_id+'"><b><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
operator_note<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</b>:'+(val.operator_note)+'</p>';   				
		} 
		if(val.sub_status_title&&val.sub_status_title!=''){
            html += '<p><b><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
problem_type<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</b>:'+(val.sub_status_title)+'</p>';   				
		} 
		if(val.cancel_status!=0&&val.cancel_status!='0'&&val.cancel_status!=''){
			if(val.abnormal_reason&&val.abnormal_reason!='null'){
				html += '<p style="word-wrap:break-word;">';
				html += '<b style="color:red;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
why_cancel_order<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</b>:<span>' + (val.abnormal_reason) +'</span>'+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
cancel_order_result<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
-->:'+val.cancel_status_title;		
				html += '</p>';
			}
		}
        html += '</div>';
        
        html += '</td>';

		/**订单详情信息 结束**/

		

		/**配送信息 开始**/
        html += '<td style="word-wrap: break-word;" valign="top">';
        html += '<p class="ellipsis"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
sale_type<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: <b>' + (val.order_type_title) + '</b></p>';
        html += '<p class="ellipsis"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_country<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:<span  id="country_'+val.order_id+'">' + (val.consignee_country_code) + '</span></p>';

		html += '<p style="" class="ellipsis"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
warehouse_name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:' + (val.warehouse_code);
//		html += ' ['+warehouseJson[val.warehouse_id].warehouse_desc+']':'未分配' );
		html += '</p>';

        html += '<p class="ellipsis"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
shipping_method<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:'+(val.shipping_method?val.shipping_method:'')+'</p>';    
		html += '<p style="" class="ellipsis"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
tracking_no<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:' + (val.shipping_method_no&&val.shipping_method_no!='null' ? val.shipping_method_no:'') +'</p>';     
        html += '</td>';

		/**配送信息 结束**/

		/**时间 开始**/
        html += '<td  valign="top">';
        html += '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
order_date_create<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:' + (val.date_create?val.date_create:'' ) + '<br/>';
		html += '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
order_date_release<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:' + (val.date_release?val.date_release:'') + '<br/>';
		html += '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
order_date_ship<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:' + (val.date_warehouse_shipping ?val.date_warehouse_shipping:'') + '<br/>';;
        html += '</td>';
		/**时间 结束**/
        
        /**操作 开始**/
        html += '<td  valign="top">';
        html += '<a href="javascript:;" class="cpyBtn" ref_id="'+val.refrence_no_platform+'" order_type="'+val.order_type+'" url="/order/order/create/cpy/1/ref_id/'+val.refrence_no_platform+'" ><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
cpy<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>';
        if(val.order_status=='2'||val.order_status=='7'){
            html += '&nbsp;&nbsp;<a href="javascript:;" class="orderDetail" ref_id="'+val.refrence_no_platform+'" url="/order/order/create/ref_id/'+val.refrence_no_platform+'" ><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
edit<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>';        	
        }

        html += '</td>';
		/**操作 结束**/
		html += "</tr>";
	
	});
	
	return html;
}<?php }} ?>