<?php /* Smarty version Smarty-3.1.13, created on 2016-04-20 17:53:16
         compiled from "D:\phpStudy\toms\application\modules\order\js\order\order_create.js" */ ?>
<?php /*%%SmartyHeaderCode:299105717518c225d12-39594797%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5e49cd939decb7c02ab9705af711513e5a51110f' => 
    array (
      0 => 'D:\\phpStudy\\toms\\application\\modules\\order\\js\\order\\order_create.js',
      1 => 1459158283,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '299105717518c225d12-39594797',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'order' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_5717518c70bcb0_54580410',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5717518c70bcb0_54580410')) {function content_5717518c70bcb0_54580410($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'D:\\phpStudy\\toms\\libs\\Smarty\\plugins\\block.t.php';
?>function getSubmiter() {
	var order_id = $('#order_id').val();
	if(order_id==''){// 避免复制订单的时候,order_id置空不默认选中发件人的情况
		order_id = '<?php echo $_GET['order_id'];?>
';
	}
	var param = {};
	param.order_id = order_id;
	var tip_id = 'ttttttt';
	// //loadStart(tip_id);
	$.ajax({
		type : "POST",
		url : "/order/order/get-submiter",
		data : param,
		dataType : 'html',
		success : function(html) {
			// //loadEnd('',tip_id);
			$('#submiter_wrap').html(html);
		}
	});
}

function successTip(tip, order) {
	if(order.order_status=='D'){
		$('<div title="操作提示 (Esc)" id="success-tip"><p align="">' + tip + '</p></div>').dialog({
	        autoOpen: true,
	        closeOnEscape:false,
	        width: 600,
	        maxHeight: 400,
	        modal: true,
	        show: "slide",
	        buttons: [
	            {
	                text: '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
录入下一条订单<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
',
	                click: function () {
	                    $(this).dialog("close");
	                    $('#order_id').val('');
	                    $('#shipper_hawbcode').val('');
	                    setTimeout(function(){
	                    	$('#shipper_hawbcode').focus()
	                    },100);
	                	$('.orderSubmitVerifyBtn').show();
	                	$('.orderSubmitDraftBtn').show();
	                }
	            },
	            {
	                text: '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
查看订单<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
',
	                click: function () {
	                    $(this).dialog("close");
	                    leftMenu('order_list','<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
订单管理<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
','/order/order-list/list?shipper_hawbcode='+order.shipper_hawbcode);
	                }
	            },
	            {
	                text: '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
编辑订单<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
',
	                click: function () {
	                    $(this).dialog("close");
	                    $('#order_id').val(order.order_id);
	                    if(order.order_status!='D'){
	                    	$('.orderSubmitVerifyBtn').hide();
	                    	$('.orderSubmitDraftBtn').hide();
	                    	alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
订单不是草稿不允许编辑<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
	                    }else{
		                	$('.orderSubmitVerifyBtn').show();
		                	$('.orderSubmitDraftBtn').show();
	                    }
	                }
	            }
	            
	        ],
	        close: function () {
	            $(this).remove();
	        },
	        open:function(){
	        	$('.ui-dialog-titlebar-close',$(this).parent()).remove();
	        }
	    });
	}else{
		$('<div title="操作提示 (Esc)" id="success-tip"><p align="">' + tip + '</p></div>').dialog({
	        autoOpen: true,
	        closeOnEscape:false,
	        width: 600,
	        maxHeight: 400,
	        modal: true,
	        show: "slide",
	        buttons: [
	            {
	                text: '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
录入下一条订单<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
',
	                click: function () {
	                    $(this).dialog("close");
	                    $('#order_id').val('');
	                    $('#shipper_hawbcode').val('');
	                    setTimeout(function(){
	                    	$('#shipper_hawbcode').focus()
	                    },100);
	                	$('.orderSubmitVerifyBtn').show();
	                	$('.orderSubmitDraftBtn').show();
	                }
	            },
	            {
	                text: '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
查看订单<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
',
	                click: function () {
	                    $(this).dialog("close");
	                    leftMenu('order_list','<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
订单管理<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
','/order/order-list/list?shipper_hawbcode='+order.shipper_hawbcode);
	                }
	            }
	            
	        ],
	        close: function () {
	            $(this).remove();
	        },
	        open:function(){
	        	$('.ui-dialog-titlebar-close',$(this).parent()).remove();
	        }
	    });
	}
    
}

function formSubmit(status){
	$("#orderForm :input").each(function(){
		var val = $(this).val();
		val = $.trim(val)
		$(this).val(val);
	});
	var param = $("#orderForm").serialize();
	param+='&status='+status;
	
	 loadStart();
	$.ajax({
		type: "POST",
		url: "/order/order/create",
		data: param,
		dataType:'json',
		success: function(json){
			 loadEnd('');
			var html = json.message;
			if(json.ask){
				html+="<br/><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
系统单号<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:"+json.order.shipper_hawbcode;
                $('#shipper_hawbcode').val(json.order.shipper_hawbcode);                
				successTip(html,json.order);
			}else{
				 if(json.err){
					 html+="<ul style='padding:0 25px;list-style-type:decimal;'>";
					 $.each(json.err,function(k,v){
						 html+="<li>"+v+"</li>";
					 })
					 html+="</ul>";
				 }
				alertTip(html);
			}
		}
	});
}
function check_extra_service(){
	$('#insurance_value_div').hide();
	$('#insurance_value_div #insurance_value').attr('disabled',true);
	
	$('.extra_service:checked').each(function(){
		// 投保金额
		var group = $(this).attr('group');
		var type = $(this).val();
		if(group=='C0' && type == 'C2'){
			$('#insurance_value_div').show();
			$('#insurance_value_div #insurance_value').attr('disabled',false);
		}
	})
}
$(function() {
	setTimeout(getSubmiter,100)
	// getSubmiter();
    $(".datepicker").datepicker({ dateFormat: "yy-mm-dd"});
	$('.addInvoiceBtn').live('click',function(){
		var clone = $('#products .table-module-b1').eq(0).clone();
		$(':input',clone).val('');
		$('.unit_code',clone).val('PCE');
		$('.total',clone).html('0');
		$('#products').append(clone);
	});
	

	$('.delInvoiceBtn').live('click',function(){
		if($('.delInvoiceBtn').size()<=1){			
			alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
最后一个申报信息不可删除<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
			return;
		}
		var this_ = $(this);
		jConfirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
are_you_sure<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
删除申报信息<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', function(r) {
    		if(r){
    			this_.parent().parent().remove();
    		}
    	});
       
	});

	$('#country_code').live('change',function(){
		var product_code = $('#product_code').val();
		var country_code = $('#country_code').val();
		var order_id = $('#order_id').val();
		if($.trim(order_id)==''){
			order_id='<?php echo $_GET['order_id'];?>
';
		}
		$('#product_extraservice_wrap').html('');
		if($.trim(product_code)==''){
			return;
		}
		if($.trim(country_code)==''){
			return;
		}
		var tip_id='dddddd';
		// loadStart(tip_id);
		// 附加服务
		$.ajax({
			type: "POST",
			url: "/order/product-rule/optional-serve-type",
			data: {'product_code':product_code,'country_code':country_code,'order_id':order_id},
			dataType:'json',
			success: function(json){
				var html = json.message;
				// loadEnd(html,tip_id);
				if(json.ask){
					var product_extraservice_wrap = '';
					var i = 0;
					$.each(json.data,function(k,v){
						
						// 不同分组的额外服务换行显示
						if(i++ != 0) {
							product_extraservice_wrap+="<br/>"
						}
						
						// 当长度大于0说明为多选项附加服务
						if(v.length > 1) {
							// 根据额外服务KEY，获取数据
							var group = json.group[k];
							product_extraservice_wrap+="<label title='"+group.extra_service_cnname+"' for='xx'><input type='checkbox' class='group_checkbox' group='"+k+"' id='"+ group.extra_service_kind +"'/><label for='"+ group.extra_service_kind +"'>"+group.extra_service_cnname +"</label>";
							$.each(v, function(kk,vv) {
								var checked = vv.checked?'checked':'';
								product_extraservice_wrap+="<input type='radio' class='extra_service' group='"+k+"' id='"+ vv.extra_service_kind +"' disabled='disabled' value='"+vv.extra_service_kind+"' name='extraservice[" + k +"]' "+checked+"/><label for='"+ vv.extra_service_kind +"'>"+vv.extra_service_cnname+"</label>&nbsp;";
							});
							
							if(k == 'C0') {
								product_extraservice_wrap+="<span id='insurance_value_div' style='display:none;'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
投保金额<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：<input type='text' group='"+k+"' class='input_text insurance_value' value='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['insurance_value'];?>
<?php }else{ ?><?php }?>' name='order[insurance_value1]' id='insurance_value' disabled style='width:30px;'/> USD&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:showInsurancExplan()'>投保须知</a></span>";
							}
							product_extraservice_wrap+="</label>";
						} else {
							v = v[0];
							var checked = v.checked?'checked':'';
							product_extraservice_wrap+="<label title='"+v.extra_service_cnname+"'><input type='checkbox' class='extra_service' group='"+k+"' value='"+v.extra_service_kind+"' name='extraservice[" + k +"]' "+checked+"/>"+v.extra_service_cnname+"</label>";
						}
						
					})
					
					$('#product_extraservice_wrap').html(product_extraservice_wrap);

					check_extra_service();
				}
			}
		});		
	});
	
	$('.group_checkbox').live('click', function(obj){
		if($(this).is(':checked')) {
			$(this).parents('label').find("input[type='radio']")
				.attr("disabled",false)
				.attr("checked", false);
			
			$(this).find("input[type='text']")
				.attr("disabled",false);
		} else {
			$(this).parents('label')
				.find("input[type='radio']")
				.attr("disabled",true)
				.attr("checked", false);
			
			$(this).find("input[type='text']")
				.attr("disabled",true)
				.val("");
			
			$('#insurance_value_div').hide();
			$('#insurance_value_div #insurance_value').attr('disabled',true);
			$('#insurance_value_div #insurance_value').val('');
		}
	});

	$('#country_code').live('change',function(){
		var product_code = $('#product_code').val();
		var country_code = $('#country_code').val();
		var order_id = $('#order_id').val();
		if($.trim(order_id)==''){
			order_id='<?php echo $_GET['order_id'];?>
';
		}
		$('#product_extraservice_wrap').html('');
		if($.trim(product_code)==''){
			return;
		}
		if($.trim(country_code)==''){
			return;
		}
		var tip_id='eeee';
		// loadStart(tip_id);
		// 必填项
		$.ajax({
			type: "POST",
			url: "/order/product-rule/web-required",
			data: {'product_code':product_code,'country_code':country_code,'order_id':order_id},
			dataType:'json',
			success: function(json){
				var html = json.message;
				// loadEnd(html,tip_id);
				if(json.ask){
					 $('.msg').text('');
					 $(':input').removeClass('web_require');
					 $.each(json.data,function(k,v){
						 $('.'+v+' ~ .msg').text('*');
						 $('.'+v).addClass('web_require');
					 });
				}else{
					 
				}

			}
		});		
	});
	$('#product_code').live('change',function(){
		var product_code = $(this).val();
		var options = "<option value=''  class='ALL'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
-select-<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>";
		$('#country_code').html(options);
		if($.trim(product_code)==''){
			return;
		}
		var tip_id='bbbbbbb';
		// loadStart(tip_id);
		// 获取支持国家
		$.ajax({
			type: "POST",
			url: "/order/product-rule/get-country",
			data: {'product_code':product_code},
			dataType:'json',
			success: function(json){
				// loadEnd('',tip_id);
				var html = json.message;
				if(json.ask){
					 var default_v = $('#country_code').attr('default');
					 options = '';
					 if(json.data.length>1){
						 options = "<option value=''  class='ALL'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
-select-<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>";						 
					 }
					 // alert(json.data.length);
					 $.each(json.data,function(k,v){
						 options+="<option value='"+v.country_code+"' class='"+v.country_code+"'>"+v.country_code+" ["+v.country_cnname+"  "+v.country_enname+"]</option>";
					 });
					 $('#country_code').html(options);
					 
					 setTimeout( function(){
						 $('#country_code').chosen('destroy');
						 $('#country_code').chosen({search_contains:true});
					 },20);
					 setTimeout(function(){
						 $('#country_code').val(default_v);
						 $('#country_code').change();
					 },10)
					 
				}else{
					 
				}

			}
		});		
	});

	$('.invoice_unitcharge,.quantity').live('keyup',function(){
		var tr = $(this).parent().parent();
		var invoice_quantity = $('.quantity',tr).val();
		var invoice_unitcharge = $('.invoice_unitcharge',tr).val();
		var quantity=parseFloat(invoice_unitcharge)*parseFloat(invoice_quantity);
		var totalQuantity=isNaN(quantity)?0:quantity;
		$('.total',tr).html(totalQuantity);
// alert($(this).val());
	});
	
    // 新增重量=============
	
	$('.weight,.quantity').live('keyup',function(){
		
		var tr = $(this).parent().parent();
		var invoice_quantity = $('.quantity',tr).val();
		var invoice_weight = $('.weight',tr).val();
		var weight=parseFloat(invoice_weight)*parseFloat(invoice_quantity);
	    var totalWeight=isNaN(weight)?0:weight;
		$('.totalWeight',tr).html(totalWeight);
		// alert($(this).val());
	});
	

	$(".orderSubmitBtn").click(function(){
		var status = $(this).attr('status');// D,P
		var tip = '';
		var title = $(this).val()+"?";
		
		// 保险必须填价值
		if($("input[name='extraservice[C0]']:checked").size() > 0 && $('#insurance_value_div #insurance_value').val() == ''&& $("#C2:checked").size() > 0) {
			$('#insurance_value_div #insurance_value').focus();
			alert("投保金额不能为空!");
			return;
		}
		
		/*if(($("#C0").attr("checked")==true)  && ($("#C1:checked").size() <= 0 || $("#C3:checked").size() <= 0)){
			$('#insurance_value_div #insurance_value').focus();
			alert("请选择保险类型!");
			return;
		}*/
		//敏感货物
		if($("#M1:checked").size() > 0 && $("input[name='extraservice[M1]']:checked").size() <= 0) {
			alert("请选择敏感货物类型");
			return;
		}
		
		var shipper_hawbcode = $('#shipper_hawbcode').val();
// if($.trim(shipper_hawbcode)==''){
// tip+='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
客户单号未填写,系统将自动分配一个单号<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
,';
// }
		tip+='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
are_you_sure<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
'; 
		
		jConfirm(tip, title, function(r) {
			if(r){
				formSubmit(status);
			}
		});			
	});
	
	// $('.input_text').val('1');
	$('.extra_service').live('click',function(){
		check_extra_service();
	})
});
$(function(){
	$('select').each(function(){
		$(this).val($(this).attr('default'));
	});
	// $('.msg').html('*');
});

function getTipTpl(){
	return $('<div class="info" style=""><span class="Validform_checktip Validform_wrong"></span><span class="dec"><s class="dec1">◆</s><s class="dec2">◆</s></span></div>');
}
	$('#shipper_hawbcode').blur(function(){
		var order_id = $('#order_id').val();
		var refrence_no = $(this).val();
		var this_ = $(this);
		$.ajax({
			type: "POST",
			url: "/order/order/check-refrence-no",
			data: {'order_id':order_id,'refrence_no':refrence_no},
			dataType:'json',
			success: function(json){
				// loadEnd('',tip_id);
				var html = json.message;
				if(!json.ask){
					this_.siblings('.msg').text(html);
				}else{
					this_.siblings('.msg').text(html);
				}
			}
		});	
	});

	$(function(){
		// alert(0);
		$('#product_code').chosen({width:'300px',search_contains:true});
		$('#country_code').chosen({search_contains:true});
	});

	<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?>
	$(function() {
		$('#product_code').change();
		
		$('#country_code').change();
		
	});
	<?php }?>

	// 新增==============

	// 体积错误提示
		function vol_tip(obj,reg,msg){	
			var reg = reg;	
			var val = $(obj).val();
			var msg =msg;
			
			if(val==''){
				return;
			}
			var tip = getTipTpl();
			if($(obj).prev('.info').size()==0){
				$(obj).prev().after(tip);
			}
			
			var tip = $(obj).prev('.info');
			if(!reg.test(val)){
				$('.Validform_checktip',tip).text(msg);	
				var left = $(obj).position().left;
				var top = $(obj).position().top;
				tip.css({'left':(left-12),'top':(top-3)}).show();		
			}else{
				tip.hide();
			}
			
	}


	// 通用错误提示
		function err_tip(obj,reg,msg){
			
			var reg = reg; 
			var val = $(obj).val();
			if(val==''){
				return;
			}
			var tip = getTipTpl();
			if($(obj).siblings('.info').size()==0){
				$(obj).parent().prepend(tip);
			}else{
				
			}
			var tip = $(obj).siblings('.info');
			if(!reg.test(val)){
				$('.Validform_checktip',tip).text(msg);	
				tip.show();			
			}else{
				tip.hide();
			}
			
		
	}
// 体积
$('.order_volume').live('keyup',function(){
	vol_tip(this,/^\d+(\.\d)?$/,'须为数字,且小数最多为1位');
})

// 电话
$('.order_phone').live('keyup',function(){
    err_tip(this,/^\(\d+\)\d+-\d+$|^\d+\s\d+$/,'格式为(xxx)xxx-xxx 或xxx空格xxxxx');	
})

// 申报价值
   $('.invoice_unitcharge').live('keyup',function(){
	var reg = /^\d+(\.\d{1,2})?$/;
	err_tip(this,reg,'须为数字,且小数最多为2位');		
})

// 重量
$('.weight').live('keyup',function(){
	var reg = /^\d+(\.\d{1,3})?$/;
	err_tip(this,reg,'须为数字,且小数最多为3位');	
})

// 数量
$('.quantity').live('keyup',function(){
	var reg = /^[1-9][0-9]*$/;
	err_tip(this,reg,'须为正整数');
})
	
	// ==============结束


<?php }} ?>