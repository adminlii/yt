<?php /* Smarty version Smarty-3.1.13, created on 2016-05-12 15:40:09
         compiled from "D:\yt1\application\modules\order\js\order\order_create_dhl.js" */ ?>
<?php /*%%SmartyHeaderCode:28368572c6d7eed3ee9-50840507%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8297f83760131bfbe4004facdc57efec814513f0' => 
    array (
      0 => 'D:\\yt1\\application\\modules\\order\\js\\order\\order_create_dhl.js',
      1 => 1463029920,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '28368572c6d7eed3ee9-50840507',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_572c6d7f00ac74_51565494',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_572c6d7f00ac74_51565494')) {function content_572c6d7f00ac74_51565494($_smarty_tpl) {?>

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
	
		function err_tip1(obj,reg,msg){
			
			var reg = reg; 
			var val = $(obj).val();
			if(val==''){
				return;
			}
			var nodeid = $(obj).attr("nodeid");
			var tip = $(obj).siblings('.info[nodeid="'+nodeid+'"]');
			if(!nodeid){
				var random_num = new Date().getTime()+''+parseInt(Math.random()*1000);
				tip = getTipTpl();
				tip.attr("nodeid",random_num);
				$(obj).attr("nodeid",random_num);
				$(obj).parent().prepend(tip);
			}
			var left = $(obj).position().left;
			if(!reg.test(val)){
				$('.Validform_checktip',tip).text(msg);	
				tip.show();			
			}else{
				tip.hide();
			}
			
		
	}
	
//校验规则		

function rule_check_length(data,minlen,maxlen){
	var datalen	= data.length;
	if(minlen>0){
		return datalen>=minlen;
	}
	if(maxlen>0){
		return datalen<=maxlen;
	}
	return true;
}
function rule_check_zm(data){
	return /^[A-Za-z]+$/.test(data);
}
function rule_check_hanzi(data){
	return /[\u4e00-\u9fa5]+/.test(data);
}
function rule_check_num(data){
	return /^[0-9]+$/.test(data);
}		

//公司
$('.checkchar').live('keyup',function(){
	err_tip(this,/^[a-zA-Z0-9\s]{1,36}$/,'不允许出现非英文允许英文数字混合,长度最多36字符');
})
//收件人
$('.checkchar1').live('keyup',function(){
	err_tip(this,/^[a-zA-Z\s]{1,36}$/,'不允许出现非英文，长度最多36字符');
})
//城市 
$('.checkchar3').live('keyup',function(){
	err_tip(this,/^[a-zA-Z\s]+$/,'不允许出现非英文');
})

//地址
$('.checkchar2').live('keyup',function(){
	err_tip(this,/^[\w\W]{0,36}$/,'长度最多36字符');
})

//发件人参考信息：商户订单号
$('.checkchar4').live('keyup',function(){
	err_tip(this,/^[\w\W]{0,35}$/,'长度最多35字符');
})

// 体积
$('.order_volume').live('keyup',function(){
	vol_tip(this,/^\d+(\.\d)?$/,'须为数字,且小数最多为1位');
})

$('.order_volume1').live('keyup',function(){
	err_tip(this,/^\d+(\.\d)?$/,'须为数字,且小数最多为1位');
})

// 电话
$('.order_phone').live('keyup',function(){
    //err_tip(this,/^\(\d+\)\d+-\d+$|^\d+\s\d+$/,'格式为(xxx)xxx-xxx 或xxx空格xxxxx');
	err_tip(this,/^(\d){4,25}$/,'格式为4-25位纯数字');
})

// 申报价值
   $('.invoice_unitcharge').live('keyup',function(){
	var reg = /^\d+(\.\d{1,2})?$/;
	err_tip(this,reg,'须为数字,且小数最多为2位');		
})

// 重量
$('.weight').live('keyup',function(){
	var reg = /(^0\.[5-9]$)|(^[1-9]+(\.?\d?)$)/;
	err_tip1(this,reg,'须为数字,且小数最多为1位,范围为0.5-999999.9');	
})

// 数量
$('.quantity').live('keyup',function(){
	var reg = /^[1-9][0-9]?$/;
	err_tip(this,reg,'须为正整数，范围为1-99');
})

// 海关商品名
$('.invoename').live('keyup',function(){
	var reg = /^[\w\s]+?$/;
	err_tip(this,reg,'须为英文');
})
// ==============结束


<?php }} ?>