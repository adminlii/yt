<?php /* Smarty version Smarty-3.1.13, created on 2016-05-10 10:56:38
         compiled from "D:\yt1\application\modules\order\views\submiter\submiter_create.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2906957314de656c1b2-00254546%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '901993c0f62d29894bfc68bdd62779b45224df50' => 
    array (
      0 => 'D:\\yt1\\application\\modules\\order\\views\\submiter\\submiter_create.tpl',
      1 => 1457516431,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2906957314de656c1b2-00254546',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_57314de65e91d8_69524380',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57314de65e91d8_69524380')) {function content_57314de65e91d8_69524380($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'D:\\yt1\\libs\\Smarty\\plugins\\block.t.php';
if (!is_callable('smarty_block_ec')) include 'D:\\yt1\\libs\\Smarty\\plugins\\block.ec.php';
?><script type="text/javascript">
$(function(){
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
			            parent.getData();
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
					//setTimeout(function(){
	                	$('.input_select').chosen({width:'350px',search_contains:true});      
					//},50);
                	            
			    }else{
			       alertTip(json.message);
			    }
		   }
	});
}
<?php if ($_GET['shipper_account']){?>
editShipperAccount('<?php echo $_GET['shipper_account'];?>
');
<?php }else{ ?>
$(function(){
	$('.input_select').chosen({width:'350px',search_contains:true});
});
<?php }?>
</script>
<style>
.module-title {
	text-align: right;
}

label {
	cursor: pointer;
}
</style>
<form id="submiterForm" onsubmit="return false;" action="" method="POST">
	<h2 style='margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC; line-height: 40px;'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
新增发件人信息<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
	<table cellspacing="0" cellpadding="0" width="100%" border="0" style="margin-top: 10px;" class="table-module">
		<tbody class="">
			<tr class="table-module-b1">
				<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
公司名<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
				<td>
					<input type="text" name="E3" value="" class="input_text">
					<span class="red">*</span>
				</td>
			</tr>
			<tr class="table-module-b2">
				<td class="module-title" width='100'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
发件人姓名<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
				<td>
					<input type="text" name="E2" value="" class="input_text">
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
				</td>
			</tr>
			<tr class="table-module-b2">
				<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
省/州<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
				<td>					 
					<input type="text" name="E5" value="" class="input_text">
					<span class="red">*</span>
				</td>
			</tr>
			<tr class="table-module-b1">
				<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
城市<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
				<td>					 
					<input type="text" name="E6" value="" class="input_text">
					<span class="red">*</span>
				</td>
			</tr>
			<tr class="table-module-b2">
				<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
联系地址<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
				<td>
					<input type="text" name="E7" value="" class="input_text">
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
					<input type="text" name="E10" value="" class="input_text">
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
</form><?php }} ?>