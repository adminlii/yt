<?php /* Smarty version Smarty-3.1.13, created on 2016-05-10 10:56:18
         compiled from "D:\yt1\application\modules\order\views\submiter\submiter_create_for_order.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2512457314dd296f018-39973827%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '20cb61cd3024d63e7a5ff50f1f9c38ca2958dc0a' => 
    array (
      0 => 'D:\\yt1\\application\\modules\\order\\views\\submiter\\submiter_create_for_order.tpl',
      1 => 1462426732,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2512457314dd296f018-39973827',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_57314dd2a70d52_23789515',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57314dd2a70d52_23789515')) {function content_57314dd2a70d52_23789515($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'D:\\yt1\\libs\\Smarty\\plugins\\block.t.php';
if (!is_callable('smarty_block_ec')) include 'D:\\yt1\\libs\\Smarty\\plugins\\block.ec.php';
?><script type="text/javascript">
//错误提示
function getTipTpl(){
	return $('<div class="info" style=""><span class="Validform_checktip Validform_wrong"></span><span class="dec"><s class="dec1">◆</s><s class="dec2">◆</s></span></div>');
}
// 通用错误提示
function err_tip(obj,reg,msg){
			
			var reg = reg; 
			var val = $(obj).val();
			if(val==''){
				return;
			}
			var tip = getTipTpl();
			var nodeid = $(obj).attr("nodeid");
			//去那带兄弟中的nodeid的info
			if(!nodeid){
				//插入id值
				nodeid =new Date().getTime()+''+parseInt(Math.random()*1000);
				tip.attr('nodeid',nodeid);
				$(obj).attr('nodeid',nodeid);
				$(obj).parent().prepend(tip);
			}
			var nodeTip = $(obj).siblings('.info[nodeid="'+nodeid+'"]');
			var left = $(obj).position().left-$(".module-title").width();
			nodeTip.css("margin-left",left+'px');
			
			if(!reg.test(val)){
				nodeTip.children('.Validform_checktip').text(msg);	
				nodeTip.show();			
			}else{
				nodeTip.hide();
			}
			
		
}
//公司名
$('.checkchar').live('keyup',function(){
	err_tip(this,/^[a-zA-Z0-9]{1,36}$/,'不允许出现非英文运行英文数字混合,长度最多36字符');
})
//城市
$('.checkchar1').live('keyup',function(){
	err_tip(this,/^[a-zA-Z]{1,36}$/,'不允许出现非英文，长度最多36字符');
})

//地址
$('.checkchar2').live('keyup',function(){
	err_tip(this,/^[\w\W]{0,36}$/,'长度最多36字符');
})

// 体积
$('.order_volume').live('keyup',function(){
	vol_tip(this,/^\d+(\.\d)?$/,'须为数字,且小数最多为1位');
})

// 电话
$('.order_phone').live('keyup',function(){
    //err_tip(this,/^\(\d+\)\d+-\d+$|^\d+\s\d+$/,'格式为(xxx)xxx-xxx 或xxx空格xxxxx');
	err_tip(this,/^(\d){4,25}$/,'格式为4-25位纯数字');
})
$('.checkchar3').live('keyup',function(){
	err_tip(this,/^[a-zA-Z]+$/,'不允许出现非英文');
})

$(function(){
	
	//绑定发件人的国家
	$("#E4").val("CN");
	$("#E4").attr('disabled','disabled');
	$("#orderSubmitBtn").click(function(){
		loadStart();
		var params = $("#submiterForm").serialize();
		$.ajax({
		    type: "post",
		    async: false,
		    dataType: "json",
		    url: '/order/submiter/edit',
		    data: params,
		    success: function (json) {
		    	loadEnd();
		    	switch (json.state) {
		            case 1:
			            
		            case 2:
			            parent.getSubmiter();
		                parent.alertTip(json.message);
		                parent.$('.dialogIframe').dialog('close');
		                break;
		            default:
		                var html = '';
		                if (json.errorMessage == null)return;
		                $.each(json.errorMessage, function (key, val) {
		                    html += '<span class="tip-error-message">' + val + '</span>';
		                });
		                alertTip(html);
		                break;
		        }
		    }
		});
	});
});

function editShipperAccount(shipper_account){	 
    $.ajax({
		   type: "POST",
		   url: '/order/submiter/get-by-json',
		   data: {'paramId':shipper_account},
		   async: true,
		   dataType: "json",
		   success: function(json){
			    if(json.state){
                    $.each(json.data,function(k,v){
                        v +='';
                        $("#submiterForm :input[name='"+k+"']").val(v); 			    	    
                    })
                    $("#submiterForm :checkbox[name='E17']").val('1');
                    if(json.data.E17==1||json.data.E17=='1'){
                    	$("#submiterForm :checkbox[name='E17']").attr('checked', true);
                    }else{
                    	$("#submiterForm :checkbox[name='E17']").attr('checked', false);
                    }                    
			    }else{
			       alertTip(json.message);
			    }
		   }
	});
}
<?php if ($_GET['shipper_account']){?>
editShipperAccount('<?php echo $_GET['shipper_account'];?>
');
<?php }?>
</script>
<style>
.module-title {
	text-align: right;
}

label {
	cursor: pointer;
}
.info {
	border: 1px solid #ccc;
	padding: 2px 20px 2px 5px;
	color: #666;
	position: absolute;
	margin-top: -24px;
	margin-left: 10px;
	display: none;
	line-height: 20px;
	background-color: #fff;
	float: left;
}
.info .Validform_wrong {
	background: url("/images/error.png") no-repeat scroll left center
		rgba(0, 0, 0, 0);
	color: red;
	padding-left: 20px;
	white-space: nowrap;
}
.dec {
	bottom: -8px;
	display: block;
	height: 8px;
	overflow: hidden;
	position: absolute;
	left: 10px;
	width: 17px;
}

.dec s {
	font-family: simsun;
	font-size: 16px;
	height: 19px;
	left: 0;
	line-height: 21px;
	position: absolute;
	text-decoration: none;
	top: -9px;
	width: 17px;
}

.dec .dec1 {
	color: #ccc;
}

.dec .dec2 {
	color: #fff;
	top: -10px;
}
</style>
<form id="submiterForm" onsubmit="return false;" action="" method="POST">
	<table cellspacing="0" cellpadding="0" width="100%" border="0" style="margin-top: 10px;" class="table-module">
		<tbody class="">
			<tr class="table-module-b1">
				<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
公司名<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
				<td>
					<input type="text" name="E3" value="" class="input_text checkchar">
					<span class="red">*</span>
				</td>
			</tr>
			<tr class="table-module-b2">
				<td class="module-title" width='100'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
发件人姓名<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
				<td>
					<input type="text" name="E2" value="" class="input_text checkchar2">
					<span class="red">*</span>
				</td>
			</tr>
			<tr class="table-module-b1">
				<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
发件人国家<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
				<td>
					<?php $_smarty_tpl->smarty->_tag_stack[] = array('ec', array('name'=>'E4','default'=>'Y','search'=>'N','class'=>'input_select')); $_block_repeat=true; echo smarty_block_ec(array('name'=>'E4','default'=>'Y','search'=>'N','class'=>'input_select'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
country<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_ec(array('name'=>'E4','default'=>'Y','search'=>'N','class'=>'input_select'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

					<span class="red">*</span>
					&nbsp;&nbsp;&nbsp;&nbsp;<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
省/州<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：
					<input type="text" name="E5" value="" class="input_text checkchar3">
					<span class="red">*</span>
					&nbsp;&nbsp;&nbsp;&nbsp;<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
城市<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：
					<input type="text" name="E6" value="" class="input_text checkchar3">
					<span class="red">*</span>
				</td>
			</tr>
			<tr class="table-module-b2">
				<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
联系地址<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
				<td>
					<input type="text" name="E7" value="" class="input_text checkchar2">
					<span class="red">*</span>
				</td>
			</tr>
			<tr class="table-module-b1">
				<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
邮编<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
				<td>
					<input type="text" name="E8" value="" class="input_text">
					<span class="red">*</span>
				</td>
			</tr>
			<tr class="table-module-b2">
				<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
联系电话<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
				<td>
					<input type="text" name="E10" value="" class="input_text order_phone">
					<span class="red">*</span>
				</td>
			</tr>
			<tr class="table-module-b1">
				<td class="module-title">&nbsp;</td>
				<td>
					<label><input type="checkbox" value="1" name="E17" class=" "><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
设为默认地址<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>
				</td>
			</tr>
			<tr class="table-module-b1">
				<td class="module-title">&nbsp;</td>
				<td>
					<input type="hidden" name="E0" value="" class="input_text">
					<input type="button" style="width: 80px;" class="baseBtn submitBtn" id="orderSubmitBtn" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
提交<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
">
				</td>
			</tr>
		</tbody>
	</table>
</form>
<?php }} ?>