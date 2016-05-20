<?php /* Smarty version Smarty-3.1.13, created on 2016-04-25 15:32:14
         compiled from "F:\yt20160425\application\modules\platform\js\order\order_list_list.js" */ ?>
<?php /*%%SmartyHeaderCode:6022571dc7fe984cf9-54647643%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'be773841277e4758875f8f5851bf687403a9ccc1' => 
    array (
      0 => 'F:\\yt20160425\\application\\modules\\platform\\js\\order\\order_list_list.js',
      1 => 1459158283,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6022571dc7fe984cf9-54647643',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_571dc7fe9bb803_72932312',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_571dc7fe9bb803_72932312')) {function content_571dc7fe9bb803_72932312($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'F:\\yt20160425\\libs\\Smarty\\plugins\\block.t.php';
?>
paginationPageSize = 20;
EZ.url = '/platform/order/';
var orderArr = [];
function getDetail(order_id_arr){
	var status = $("#status").val();
	//订单明细
	$.ajax({
		type: "POST",
		async: true,
		dataType: "json",
		url: '/platform/order/get-list-detail-list',
		data: {'order_id_arr':order_id_arr,'order_status':status},
		success: function (results) {
			$.each(results,function(k,json){

				if(json.ask){
					var orderId = json.data.order_id;
					var html = '';
					var val = json.data;
					var more_than_x = false;
					//要显示的产品数量
					var show_count = 1;
					
					$.each(json.data.order_product, function(k, v) {
			            var target = (v.url&&v.url!='null'?" target='_blank'":"");
			            var pic = v.pic&&v.pic!='null'?v.pic:'/images/base/noimg.jpg';
			            var url = (v.url&&v.url!='null'?v.url:"javascript:;");
			           
			            html +='';
			            var clazz = '';
			            if(k>=show_count){
			            	more_than_x = true;
			            	clazz = 'more_than_x';
				            html+='<div style="clear:both;display:none;" class="product_data_line '+clazz+'">';
			            }else{
				            html+='<div style="clear:both;" class="product_data_line '+clazz+'">';
			            }

			            
		                html +='<div style="float:left;width:75%;text-align:left;" class="ellipsis"><a href="'+url+'" '+target+' style="line-height: 25px;" class="imgPreview" url="'+pic+'"><img width="1" src="'+pic+'">'+ v.product_title+'</a></div>' ;
			            
			            html +='<span class="unitPrice" style="width:120px;float:right;">'+$.getMessage('unit_price')+':';//单价
		                html += '<b>'+(v.unit_price ? v.unit_price:'0') + " " + (v.currency_code ? v.currency_code :'')+'</b>';
			            html +='</span>';


			            if(json.data.platform=='amazon'){
			            	v.op_record_id = v.op_ref_tnx;
			            }
	        			v.sku = v.sku!=undefined?v.sku:v.product_sku;
	        			if(v.op_record_id){
				        	html += '<div class="recordNo" style="clear:both;">recordNo :'+(v.op_record_id?v.op_record_id:'')+'</div>'; 
	        			}

	        			is_show = true;
		        		$.each(v.sub_product,function(sub_k,sub){
				            html += '<div style="clear:both;">';				            
				            html += '<div class="itemId" style="display:none;">ItemID:'+(v.op_ref_item_id?v.op_ref_item_id:'')+'</div>';				            
				            var tmp_sku = is_show?'SKU: <a href="javascript:;"><b class="rmaSku platform_sku" acc="'+json.data.user_account+'" comp="'+json.data.company_code+'" sku="'+v.sku+'">' + (v.sku)+'</b></a>':'&nbsp;';

		        			is_show = false;
		    	            html += '<div class="sku">' + tmp_sku;
    			            html +='</div>';
		        			html += '<div class="warehouseSkuWrap"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
对应品名<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:'+'<a  href="javascript:;" >'+sub.pcr_product_sku + '</a>'+'</div>';
		    	            
    			            html += '<div class="qty">Qty: <b class="rmaWarehouseSkuQty">' + (v.op_quantity*sub.pcr_quantity)+'</b></div>' ;
    			            
    			            html+='</div>';
    			            
		        		});	
			            html+='</div>';		        		
					});
					if(more_than_x){
			            html+='<div style="text-align:center;"><a href="javascript:;" data_show="0" class="product_data_hide" style="line-height: 20px;"><span class="tips_title">显示过多的</span> <b style="color:red;">'+(json.data.order_product.length-show_count)+'</b> SKU</a></div>';
			        }
					$('#country_'+json.data.order_id).html(json.data.Country+'['+json.data.CountryName+']');  
					$('#consignee_'+json.data.order_id).html('收件人信息:姓名:'+json.data.Name+'  国家:'+json.data.Country+'['+json.data.CountryName+']'+'  收件地址:'+json.data.Street1+' '+json.data.Street2+'');

					
					$('#order_detail_'+json.data.order_id).html(html);
				};
			});
			
		}
	});
	
}


/*跳轉至訂單管理界面*/
$(function(){
	$("table").on("click","#code",function(){
		var code=$(this).text();
		leftMenu('order_list','<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
订单管理<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
','/order/order-list/list?shipper_hawbcode='+code+'&code_type=refer');
	});
});




EZ.getListData = function(json) {
    paginationTotal = json.total;  
	//设置订单条数
	setPageTotal(json.total);
	var html = '';
	$(".checkAll").attr('checked', false);
	var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
	orderArr = json.data;
	$.each(json.data, function(key, val) {

		// alert(val.order_product[0].Site);
		html += "<tr class='"+"' id='order_wrap_"+val.order_id+"'>";
		html += '<td class="ec-center"  valign="top"><input type="checkbox" class="checkItem" name="orderId[]" ref_id="'+val.refrence_no_platform+'" value="' + val.order_id + '"/></td>';
		        	

		/**单头信息 开始**/
    	html += '<td style="word-wrap:break-word;" class="order_line" valign="top">';
    	if(val.refrence_no){
			html += '<p style="" class="refrence_no ellipsis">'+'订单号:<a id='+'code'+ ' href="javascript:;" class="order_no_copy0 ">' + (val.refrence_no) +'</a></p>'; 			
		} 
		html += '<p class="refrence_no_platform ellipsis "><span id="rmaOrderNo' + val.order_id + '" href="javascript:;" class="orderDetail" url="/order/order/detail/orderId/'+val.order_id+'" >'+ val.refrence_no_platform + '</span></p>';
		
		html += '<span style="display:none;" id="consignee_'+val.order_id+'"></span>';
		
		html += '<p style="" class="buyerId ellipsis">'+$.getMessage('buyer_name')+'<!--买家名称-->:' +  val.buyer_name +'</p>';
		html += '<p style="" class="buyerId ellipsis">买家ID: ' +(val.buyer_id ? val.buyer_id :'') +'</p>';
		html += '<p style="" class="buyerMail ellipsis">Email: ' +val.buyer_mail+'</p>';
		
		
  


		
		if($('#status').val()==''){
			html += '<p style="" class="order_status_title ellipsis">'+$.getMessage('sys_common_status')+'<!-- 订单状态 -->:<b>' + (statusArr[val.order_status].name) +'</b></p>';  			
		}

		html+='<p>';
		html += ' <span class="logIcon " ref_id="'+val.refrence_no_platform+'" style="float: left;cursor: pointer;" title="'+$.getMessage('orders_operation_log')+'"></span>';
		  
		if(val.amountpaid&&val.amountpaid>0){
	        html += ' <span class="iconmoney2 " id="eBayPaymentStatus_'+val.order_id+'" style="cursor: pointer;float:left;padding: 0 2px;" title="'+$.getMessage('paid')+'"><!--已付款--></span>';
		}
        
        if(val.platform_ship_status==1){
            html += '<span class="iconshipebay" style="cursor: pointer;float:left;" title="平台已标记发货"></span>';
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
		
		
		html+='</p>';
		html += '<p style="clear:both;"></p>';
		
		
		html += '</td>';
		/**单头信息 结束**/
		

		/**订单详情信息 开始**/
        html += '<td valign="top">';
        //此处定义一个样式，用以区别是否为合并订单，拆分订单(order_merge_x)
        html += '<div class="product_data order_detail order_merge_'+val.is_merge+'" id="order_detail_'+val.order_id+'"><div style="text-align: center;margin-top:30px;"><img src="/images/base/loading.gif"/></div></div>'; 
        
        html +='<div style="width: 100%; clear:both; margin-top: 1px;padding-top:2px;">';
        if(val.order_desc){
			html += '<div style="width: 99%; border-radius: 2px; border-bottom: 1px dashed #4378f3;border-top: 1px dashed #4378f3; clear: both;" class="note_span"  id="note_'+val.order_id+'"><b  style="color: #4378f3;">'+$.getMessage('guestbook')+'<!--客户留言--></b>:' + (val.order_desc) +'</div>';
		}
		
		if(val.abnormal_reason&&val.abnormal_reason!='null'){
			html += '<p style="word-wrap:break-word;">';
			html += '<b style="color:#ff2240;">'+$.getMessage('exception_information')+'<!--异常信息--></b>:<span>' + (val.abnormal_reason) +'</span>';		
			html += '</p>';
		}
        html += '</div>';
        
        html += '</td>';

		/**订单详情信息 结束**/


		/**订单金额 开始**/

        html += '<td style="" valign="top">';
        var pay_title='';        
        pay_title+= $.getMessage('order_amount') + '：' + ((val.amountpaid!='' && val.amountpaid!=null)?val.amountpaid:'0.000')+"<br/>";//<!--订单金额-->
        pay_title+= $.getMessage('turnover') + '：' + ((val.subtotal!='' && val.subtotal!=null)?val.subtotal:'0.000')+"<br/>";//<!--交易额-->
        pay_title+= $.getMessage('freight') + '：' + ((val.ship_fee!='' && val.ship_fee!=null)?val.ship_fee:'0.000')+"<br/>";//<!--运费-->
//        pay_title+= $.getMessage('transaction') + '：<br/>' + ((val.finalvaluefee!='' && val.finalvaluefee!=null)?val.finalvaluefee:'0.000')+"<br/>";//<!--交易费-->
//        pay_title+= $.getMessage('platform_fees') + '：<br/>' + ((val.platform_fee!='' && val.platform_fee!=null)?val.platform_fee:'0.000')+"<br/>";//<!--平台费-->
        pay_title+= $.getMessage('sys_currency') + '：' + ((val.currency!='' && val.currency!=null)?val.currency:'USD')+"";//<!--币种-->
        html +=pay_title;
        html += '</td>';
		/**订单金额 结束**/

		/**配送信息 开始**/
        html += '<td style="word-wrap: break-word;" valign="top">';
        html += '<p class="ellipsis" d_title="'+$.getMessage('country_name')+'">'+$.getMessage('s_country_name')+'<!--国家-->:<b><span  id="country_'+val.order_id+'" code="'+val.consignee_country+'">' + (val.consignee_country?val.consignee_country:"") + '</span></b></p>';
        html += '<p class="ellipsis" d_title="'+$.getMessage('distribution_platform')+'" id="shipping_method_platform_'+val.order_id+'" shipping_method_platform="'+val.shipping_method_platform+'" shipping_method="'+val.shipping_method+'">'+$.getMessage('s_distribution_platform')+'<!--平台配送-->:'+val.shipping_method_platform+" </p>";
        if(val.warehouse_id){
        	html += '<p style="" class="ellipsis" d_title="'+$.getMessage('ship_warehouse')+'">'+$.getMessage('s_ship_warehouse')+'<!--发运仓库-->:' + (warehouseJson[val.warehouse_id] ? warehouseJson[val.warehouse_id].warehouse_code:$.getMessage('unallocated')+'<!--未分配-->');
    		html += '</p>';	
        }
		if(val.shipping_method){
	        html += '<p class="ellipsis" >运输方式:'+(val.shipping_method?val.shipping_method:$.getMessage('unallocated')+'<!--未分配-->')+'</p>';    
		}
		if(val.shipping_method_no){
			var trackNo = (val.shipping_method_no&&val.shipping_method_no!='null' ? val.shipping_method_no:$.getMessage('none')+'<!--暂无-->');
	        var trackNo_title = trackNo;	       
			html += '<p style="" class="ellipsis" d_title="'+$.getMessage('trackingNo')+'">'+ $.getMessage('s_trackingNo')+'<!--跟踪号-->:'+ trackNo_title+'</p>';
		}
       
        html += '</td>';

		/**配送信息 结束**/

		/**时间 开始**/
        html += '<td  valign="top">';		
		html += $.getMessage('order_date_create')+'<!--创建-->:' + (val.date_create_platform?val.date_create_platform:'' ) + '<br/>';
//		html += $.getMessage('order_date_pay')+'<!--付款-->:' + (val.date_paid_platform?val.date_paid_platform:'') + '<br/>';
		html += $.getMessage('order_date_audit')+'<!--审核-->:' + (val.date_release?val.date_release:'') + '<br/>';
		html += $.getMessage('order_date_delivery')+'<!--发货-->:' + (val.date_warehouse_shipping ?val.date_warehouse_shipping:'') + '<br/>';;
        html += '</td>';
		/**时间 开始**/
        
		html += "</tr>";

	});

	return html;
}

function loadData(page, pageSize) {
    EZ.listDate.myLoading();
    $.ajax({
        type: "POST",
        async: true,
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
            //设置订单条数
        	setPageTotal(json.total);
        	
            if (!isJson(json)) {
                paginationTotal = 0;
                EZ.listDate.EzWmsSetSearchData({msg: 'Returns the data type error.'});
                return;
            }
            
        	orders = json.data;
        	
            if (json.state == '1') {
            	//EZ.listDate.html(EZ.getListData(json));
            	EZ.listDate.empty();
                $(EZ.getListData(json)).appendTo(EZ.listDate);
            	var order_id_arr = [];
            	$.each(json.data,function(k,v){
            		order_id_arr.push(v.order_id);
            	});
            	//获取明细
            	getDetail(order_id_arr);

                //分多次查询
                /*
                var order_id_arr = [];
                var num = pageSize > 25 ? 25 : pageSize;
                $.each(json.data,function(k,v){
                    if(order_id_arr.push(v.order_id)>num){
                        getDetail(order_id_arr);
                        order_id_arr = new Array();
                    }
                });
                if(order_id_arr.length){
                    getDetail(order_id_arr);
                }
                */
            } else {
                EZ.listDate.EzWmsSetSearchData({state: 1});
            }
        }
    });
}

$(function(){
	$('#print_dialog').dialog({
		autoOpen: false,
		width: 800,
		maxHeight: 400,
		modal: true,
		show: "slide",
		buttons: [
//{
//text: '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
货物标签<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
',
//click: function () {
//$(this).dialog("close");
//var type = 'label';
//$('#listForm').attr('action','/platform/order/print?type='+type).attr('target','_blank').submit();
//}
//},
{
	text: '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
确定<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
',
	click: function () {
		$('#pring_order_id_wrap').html('');	
		$('.checkItem:checked').each(function(){
			var order_id =$(this).val();
			$('#pring_order_id_wrap').append('<input type="hidden" name="order_id[]" value="'+order_id+'"/>');
		})
		var params = $('#printForm').serialize();
		$('#printForm').attr('onsubmit','return true;').submit();
		//$(this).dialog("close");
	}
},
{
	text: 'Close(<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
关闭<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
)',
	click: function () {
		$(this).dialog("close");
	}
}
],
close: function () {
	//$(this).remove();
	$('#printForm').attr('onsubmit','return false;')
}
	});
})
function printDialog() {
	$('#print_dialog').dialog('open');
}
var op = '';
var opTitle = '';

function opProcess(){

	alertTip('Loading...',300); 
	var refIds = [];
	$("#listForm .checkItem:checked").each(function(){
		refIds.push($(this).val());
	});
	var param = {'order_id':refIds,'op':op};				

	$.ajax({
		type : "POST",
		url : "/platform/order/verify",
		data : param,
		dataType : 'json',
		success : function(json) {
			$('#dialog-auto-alert-tip').dialog('close');						 
			var html = json.message;
			if(json.ask){
				if(json.rs){	
					html+='<p>已处理订单</p>';
					html+="<ul style='list-style-type:decimal;padding:5px 0 5px 30px;'>";
					$.each(json.rs,function(k,v){
						html+="<li>"+v.shipper_hawbcode+"</li>";
					})
					html+="</ul>";
				}
			}

			if(json.err){
				html+="<ul style='list-style-type:decimal;padding:5px 0 5px 30px;'>";
				$.each(json.err,function(k,v){
					html+="<li>"+v+"</li>";
				})
				html+="</ul>";
			}

			if(json.ask){
				switch(op){
					case 'export':
						jConfirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
are_you_sure<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', opTitle+'<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
订单<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', function(r) {
							if(r){
								$('#listForm').attr('action','/platform/order/export/').attr('target','_blank').submit();
							}
						});					

						break;
					case 'print':
						printDialog();

						break;
					default:
						initData(paginationCurrentPage - 1);
					tongji();
					alertTip(html,600,500);
				}
			}else{
				alertTip(html,600,500);
			}

		}
	});	
}

$(function(){
	$('.opBtn').live('click',function(){
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pls_select_orders<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
			return false;
		}
		var this_ = $(this);
		op = $(this).attr('op');
		opTitle = $(this).val();
		jConfirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
are_you_sure<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', opTitle+'<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
订单<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', function(r) {
			if(r){
				opProcess();
			}
		});
	})


	$('.printBtn').live('click',function(){
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pls_select_orders<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
			return false;
		}
		var this_ = $(this);
		op = $(this).attr('op');
		opTitle = $(this).val();

		opProcess();
	})

	$('.exportBtn').live('click',function(){
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pls_select_orders<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
			return false;
		}
		var this_ = $(this);
		op = $(this).attr('op');
		opTitle = $(this).val();

		opProcess();
	})
})

$(function(){
<?php if ($_GET['shipper_hawbcode']){?>
//订单创建成功，链接到订单列表界面,默认查询客户订单号
$('#code').val('<?php echo $_GET['shipper_hawbcode'];?>
');
setTimeout(function(){ $('.statusTagAll:visible').click(); },5);
<?php }?>


<?php if ($_GET['ac']=='upload'){?>
//批量上传成功,链接到订单列表界面,默认查询草稿状态订单
setTimeout(function(){ $('.statusTagD:visible').click(); },5);
<?php }?>
})

<?php }} ?>