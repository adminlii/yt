<?php /* Smarty version Smarty-3.1.13, created on 2016-04-20 17:53:14
         compiled from "D:\phpStudy\toms\application\modules\order\views\order\order_create.tpl" */ ?>
<?php /*%%SmartyHeaderCode:125295717518a058671-76859553%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b1c56c58c461970d469a3eb6d8a8e7909c37c1d1' => 
    array (
      0 => 'D:\\phpStudy\\toms\\application\\modules\\order\\views\\order\\order_create.tpl',
      1 => 1459158283,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '125295717518a058671-76859553',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'order' => 0,
    'productKind' => 0,
    'c' => 0,
    'country' => 0,
    'shipperConsignee' => 0,
    'certificates' => 0,
    's' => 0,
    'mailCargoTypes' => 0,
    'invoice' => 0,
    'i' => 0,
    'units' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_5717518c1fec01_15883712',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5717518c1fec01_15883712')) {function content_5717518c1fec01_15883712($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'D:\\phpStudy\\toms\\libs\\Smarty\\plugins\\block.t.php';
?><link type="text/css" rel="stylesheet" href="/css/public/layout.css" />
<style>

<!--
.input_text {
	
}

.hide {
	display: none;
}

#loading {
	display: none;
}

#search-module {
	overflow: auto;
}

.msg {
	color: red;
	padding: 0 5px;
}

.module-title {
	text-align: right;
}

.info .Validform_wrong {
	background: url("/images/error.png") no-repeat scroll left center
		rgba(0, 0, 0, 0);
	color: red;
	padding-left: 20px;
	white-space: nowrap;
}

.Validform_checktip {
	color: #999;
	font-size: 12px;
	height: 20px;
	line-height: 20px;
	margin-left: 8px;
	overflow: hidden;
}


.Validform_checktip {
	margin-left: 0;
}

.info {
	border: 1px solid #ccc;
	padding: 2px 20px 2px 5px;
	color: #666;
	position: absolute;
	margin-top: -32px;
	margin-left: 10px;
	display: none;
	line-height: 20px;
	background-color: #fff;
	float: left;
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

.input_text {
	width: 135px;
}

.quantity {
	width: 26px;
}

.invoice_unitcharge {
	width: 35px;
}
.weight{
     width:30px;
}
-->
</style>
<script>
$(function(){
	//alert(0);
	//$('#country_code').chosen();
});

</script>

<script type="text/javascript">
<?php echo $_smarty_tpl->getSubTemplate ('order/js/order/order_create.js', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</script>
<div id='loading'>Loading...</div>
<div id="module-container">
	<div class="Tab">
		<ul>
			<li id="normal" style="position: relative;"
				class="mainStatus chooseTag"><a href="javascript:;"
				class="statusTag "
				onclick="leftMenu('order_create','<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
单票录入<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
','/order/order/create?quick=52')">
					<span class="order_title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
单票录入<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
			</a></li>
			<li id="abnormal" style="position: relative;" class="mainStatus "><a
				href="javascript:;" class="statusTag "
				onclick="leftMenu('order_import','<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
批量上传<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
','/order/order/import?quick=24')">
					<span class="order_title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
批量上传<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
			</a></li>
		</ul>
	</div>
	<div id="module-table" style='clear: both; padding: 10px 20px;'>
		<div class="feedback_tips">
			<div class="feedback_tips_text">
				<p id="create_feedback_div"></p>
			</div>
			<div style="clear: both;"></div>
		</div>
		<form method='POST' action='' onsubmit='return false;' id='orderForm'>
			<h2 style='margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC;'>1、<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
基本信息<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
			<table width="100%" cellspacing="0" cellpadding="0" border="0"
				class="table-module" style='margin-top: 10px;'>
				<tbody class="table-module-list-data">
					<tr class=''>
						<td width="150" class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
运输方式<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td width="480"><select class='input_select product_code'
							name='order[product_code]'
							default='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['product_code'];?>
<?php }?>'
							id='product_code'>
								<option value='' class='ALL'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
-select-<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
								<?php  $_smarty_tpl->tpl_vars['c'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['c']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['productKind']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['c']->key => $_smarty_tpl->tpl_vars['c']->value){
$_smarty_tpl->tpl_vars['c']->_loop = true;
?>
								<option value='<?php echo $_smarty_tpl->tpl_vars['c']->value['product_code'];?>
'><?php echo $_smarty_tpl->tpl_vars['c']->value['product_code'];?>

									[<?php echo $_smarty_tpl->tpl_vars['c']->value['product_cnname'];?>
 <?php echo $_smarty_tpl->tpl_vars['c']->value['product_enname'];?>
]</option>
								<?php } ?>
						</select> <span class="msg">*</span></td>
						<td width="150" class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_country<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td><select class='input_select country_code'
							name='order[country_code]'
							default='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['country_code'];?>
<?php }?>'
							id='country_code' style='width: 400px; max-width: 400px;'>
								<option value='' class='ALL'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
-select-<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
								<!-- 
								<?php  $_smarty_tpl->tpl_vars['c'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['c']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['country']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['c']->key => $_smarty_tpl->tpl_vars['c']->value){
$_smarty_tpl->tpl_vars['c']->_loop = true;
?>
								<option value='<?php echo $_smarty_tpl->tpl_vars['c']->value['country_code'];?>
' country_id='<?php echo $_smarty_tpl->tpl_vars['c']->value['country_id'];?>
' class='<?php echo $_smarty_tpl->tpl_vars['c']->value['country_code'];?>
'><?php echo $_smarty_tpl->tpl_vars['c']->value['country_code'];?>
 [<?php echo $_smarty_tpl->tpl_vars['c']->value['country_name'];?>
  <?php echo $_smarty_tpl->tpl_vars['c']->value['country_name_en'];?>
]</option>
								<?php } ?>
								 -->
						</select> <span class="msg">*</span></td>
					</tr>
					<tr class=''>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
客户单号<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td><input type='text' style='width: 220px;'
							class='input_text refer_hawbcode'
							value='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['refer_hawbcode'];?>
<?php }?>'
							name='order[refer_hawbcode]' id='refer_hawbcode' /> <span
							class="msg"></span></td>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
货物重量<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<div class="info"
								style="display: block; color: red; display: none;">
								<span class="Validform_checktip Validform_wrong">须为整数</span> <span
									class="dec"> <s class="dec1">◆</s> <s class="dec2">◆</s>
								</span>
							</div> <input type='text' class='input_text weight'
							value='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['order_weight'];?>
<?php }?>'
							name='order[order_weight]' id='order_weight' /> (KG) <span
							class="msg"></span>

						</td>
					</tr>
					<tr class=''>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
外包装件数<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td><input type='text' class='input_text quantity'
							value='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['order_pieces'];?>
<?php }else{ ?>1<?php }?>'
							name='order[order_pieces]' id='order_pieces' /> <span class="msg"></span>
							
							
						</td>
						
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
附加服务<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td><span class='product_extraservice_wrap'
							id='product_extraservice_wrap'> <!-- 
    							<label><input type='checkbox' class='' value='A1' name='extraservice[]' /><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
购买保险<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label> 
    							<label><input type='checkbox' class='' value='11' name='extraservice[]' /><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
支持退件<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>
    							 -->
						</span> <!-- --> <span class="msg"></span> <span
							id='insurance_value_div' style='display: none;'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
投保金额<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：<input
								type='text' class='input_text insurance_value'
								value='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['insurance_value'];?>
<?php }else{ ?><?php }?>'
								name='order[insurance_value]' id='insurance_value' disabled
								style='width: 30px;' /> USD
						</span></td>
					</tr>
					<tr>
					<td class="module-title">体积：</td>
						<td colspan="3">
						<span class=""> 长：</span>
							<input type='text' name='order[order_length]' id='order_length' class='input_text order_volume' 
							value='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['length'];?>
<?php }?>' style="width:30px;"/>CM&nbsp;
							
							<span class="">宽：</span>
							<input type='text' name='order[order_width]' id='order_width'class='input_text order_volume' 
							value='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['width'];?>
<?php }?>'style="width:30px;"/>CM&nbsp;
							
							<span class="">高：</span>
							<input type='text' name='order[order_height]'id='order_height'class='input_text order_volume'
							value='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['height'];?>
<?php }?>'style="width:30px;"/>CM&nbsp;
						</td>
						
					</tr>
					
				</tbody>
			</table>
			<h2
				style='padding: 35px 0 0 0; margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC;'>2、<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_info<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
			<table width="100%" cellspacing="0" cellpadding="0" border="0"
				class="table-module" style='margin-top: 10px;'>
				<tbody class="table-module-list-data">
					<tr class=''>
						<td width="150" class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_company<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td colspan='3'><input type='text'
							class='input_text consignee_company'
							value="<?php if (isset($_smarty_tpl->tpl_vars['shipperConsignee']->value)){?><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['consignee_company'];?>
<?php }?>"
							name='consignee[consignee_company]' id='consignee_company' /> <span
							class="msg"></span></td>
					</tr>
					<tr class=''>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_state<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td width='480'><input type='text'
							class='input_text consignee_state consignee_province'
							value="<?php if (isset($_smarty_tpl->tpl_vars['shipperConsignee']->value)){?><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['consignee_province'];?>
<?php }?>"
							name='consignee[consignee_province]' id='consignee_province' /> <span
							class="msg"></span></td>
						<td width="150" class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td><input type='text' class='input_text consignee_name'
							value="<?php if (isset($_smarty_tpl->tpl_vars['shipperConsignee']->value)){?><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['consignee_name'];?>
<?php }?>"
							name='consignee[consignee_name]' id='consignee_name' /> <span
							class="msg">*</span></td>
					</tr>
					<tr class=''>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_city<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td><input type='text' class='input_text consignee_city'
							value="<?php if (isset($_smarty_tpl->tpl_vars['shipperConsignee']->value)){?><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['consignee_city'];?>
<?php }?>"
							name='consignee[consignee_city]' id='consignee_city' /> <span
							class="msg"></span></td>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_phone<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td><input type='text' class='input_text consignee_telephone order_phone'
							value='<?php if (isset($_smarty_tpl->tpl_vars['shipperConsignee']->value)){?><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['consignee_telephone'];?>
<?php }?>'
							name='consignee[consignee_telephone]' id=consignee_telephone /> <span
							class="msg">*</span></td>
					</tr>
					<tr class=''>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_zip<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td><input type='text' class='input_text consignee_postcode'
							value='<?php if (isset($_smarty_tpl->tpl_vars['shipperConsignee']->value)){?><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['consignee_postcode'];?>
<?php }?>'
							name='consignee[consignee_postcode]' id='consignee_postcode' /> <span
							class="msg"></span></td>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_email<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td><input type='text' class='input_text consignee_email'
							value='<?php if (isset($_smarty_tpl->tpl_vars['shipperConsignee']->value)){?><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['consignee_email'];?>
<?php }?>'
							name='consignee[consignee_email]' id='consignee_email' /> <span
							class="msg"></span></td>
					</tr>
					<tr>
					    <td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
收件人手机<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td><input type='text' class='input_text order_phone'
							value='<?php if (isset($_smarty_tpl->tpl_vars['shipperConsignee']->value)){?><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['consignee_mobile'];?>
<?php }?>'
							name='consignee[consignee_mobile]' id='consignee_mobile' /> <span
							class="msg"></span></td>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
地址1<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td colspan="1"><input type='text'
							class='input_text consignee_street' style='width: 66%;'
							value="<?php if (isset($_smarty_tpl->tpl_vars['shipperConsignee']->value)){?><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['consignee_street'];?>
<?php }?>"
							name='consignee[consignee_street]' id='consignee_street' /> <span
							class="msg">*</span>
						    <span class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
收件人门牌号<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</span>
						    <input type='text' class='input_text doorplate'
							value='<?php if (isset($_smarty_tpl->tpl_vars['shipperConsignee']->value)){?><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['consignee_doorplate'];?>
<?php }?>'
							name='consignee[consignee_doorplate]' id='consignee_doorplate' />
						</td>
						  
							
					</tr>
					<tr>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
地址2<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td colspan="1"><input type='text'
							class='input_text consignee_street2' style='width: 90%;'
							value="<?php if (isset($_smarty_tpl->tpl_vars['shipperConsignee']->value)){?><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['consignee_street2'];?>
<?php }?>"
							name='consignee[consignee_street2]' id='consignee_street2' />  <span
							class="msg"></span> </td>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
地址3<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td><input type='text' class='input_text consignee_street3'
							style='width: 90%;'
							value="<?php if (isset($_smarty_tpl->tpl_vars['shipperConsignee']->value)){?><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['consignee_street3'];?>
<?php }?>"
							name='consignee[consignee_street3]' id='consignee_street3' /> <span
							class="msg"></span></td>
					</tr>
					<tr class=''>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
证件类型<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td colspan="3"><select
							name="consignee[consignee_certificatetype]"
							class="input_select consignee_certificatetype"
							id='consignee_certificatetype'
							default='<?php if (isset($_smarty_tpl->tpl_vars['shipperConsignee']->value)){?><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['consignee_certificatetype'];?>
<?php }?>'>
								<?php  $_smarty_tpl->tpl_vars['s'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['s']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['certificates']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['s']->key => $_smarty_tpl->tpl_vars['s']->value){
$_smarty_tpl->tpl_vars['s']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['s']->key;
?>
								<option value="<?php echo $_smarty_tpl->tpl_vars['s']->value['certificate_type'];?>
"><?php echo $_smarty_tpl->tpl_vars['s']->value['certificate_type_enname'];?>
(<?php echo $_smarty_tpl->tpl_vars['s']->value['certificate_type_cnname'];?>
)</option>
								<?php } ?>
						</select> <span class="msg"></span> &nbsp;&nbsp;&nbsp;&nbsp;
							<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
号码<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
： <input type='text'
							class='input_text consignee_certificatecode'
							value='<?php if (isset($_smarty_tpl->tpl_vars['shipperConsignee']->value)){?><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['consignee_certificatecode'];?>
<?php }?>'
							name='consignee[consignee_certificatecode]'
							id='consignee_certificatecode' /> <span class="msg"></span>
							&nbsp;&nbsp;&nbsp;&nbsp; <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
有效期<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
： <input type='text'
							class='input_text datepicker consignee_credentials_period'
							value='<?php if (isset($_smarty_tpl->tpl_vars['shipperConsignee']->value)){?><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['consignee_credentials_period'];?>
<?php }?>'
							name='consignee[consignee_credentials_period]'
							id='consignee_credentials_period' /> <span class="msg"></span></td>
					</tr>
					<tr class=''>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
买家ID<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td colspan="3"><input type='text' class='input_text buyer_id'
							value='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['buyer_id'];?>
<?php }?>'
							name='order[buyer_id]' id='buyer_id' /> <span class="msg"></span>
						</td>
					</tr>
					<tr class=''>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
包裹申报种类<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td colspan="3"><select
							default="<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['mail_cargo_type'];?>
<?php }?>"
							name="order[mail_cargo_type]"
							class="input_select mail_cargo_type" id='mail_cargo_type'>
								<option value='' class='ALL'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
-select-<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
								<?php  $_smarty_tpl->tpl_vars['s'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['s']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['mailCargoTypes']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['s']->key => $_smarty_tpl->tpl_vars['s']->value){
$_smarty_tpl->tpl_vars['s']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['s']->key;
?>
								<option value="<?php echo $_smarty_tpl->tpl_vars['s']->value['mail_cargo_code'];?>
"><?php echo $_smarty_tpl->tpl_vars['s']->value['mail_cargo_enname'];?>
(<?php echo $_smarty_tpl->tpl_vars['s']->value['mail_cargo_cnname'];?>
)</option>
								<?php } ?>
						</select> <span class="msg"></span></td>
					</tr>
				</tbody>
			</table>
			<h2
				style='padding: 35px 0 0 0; margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC; line-height: 30px;'>
				3、<span class='invoice_wrap'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
海关申报信息<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span><span
					class="msg">*</span> <a href='javascript:;'
					style='font-weight: normal; font-size: 12px; padding: 0 10px; display: none;'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
选择品名<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
				<span style='font-weight: normal; font-size: 12px;'>
					<!-- 注意：如果是Fedex联邦产品，请在备注中注明‘材质’和‘用途’，以便相关 -->
				</span>
			</h2>
			<table cellspacing="0" cellpadding="0" width="100%" border="0"
				class="table-module">
				<tbody>
					<tr class="table-module-title">
						<td width="165"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
英文品名<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width="165"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
中文品名<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width="60"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
数量<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width="60"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
单位<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width="60"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
单价<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
($)</td>
						<td width="50"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
总价<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width="70"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
重量<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
(kg)</td>
						<td width="40"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
总重<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width="70"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
SKU<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width="70"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
海关协制编号<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width="70"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
配货信息<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width="70"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
销售地址<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
操作<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					</tr>
				</tbody>
				<tbody id="products" class="table-module-list-data">
					<?php if ($_smarty_tpl->tpl_vars['invoice']->value){?> <?php  $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['i']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['invoice']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['i']->key => $_smarty_tpl->tpl_vars['i']->value){
$_smarty_tpl->tpl_vars['i']->_loop = true;
?>
					<tr class="table-module-b1">
						<td><input type='text' class='input_text invoice_enname'
							name='invoice[invoice_enname][]' value='<?php echo $_smarty_tpl->tpl_vars['i']->value['invoice_enname'];?>
'> <span
							class="msg">*</span></td>
						<td><input type='text' class='input_text invoice_cnname'
							name='invoice[invoice_cnname][]' value='<?php echo $_smarty_tpl->tpl_vars['i']->value['invoice_cnname'];?>
'> <span
							class="msg">*</span></td>
						<td><input type='text' class='input_text quantity'
							name='invoice[invoice_quantity][]'
							value='<?php echo $_smarty_tpl->tpl_vars['i']->value['invoice_quantity'];?>
'> <span class="msg">*</span></td>
						<td><select default="<?php if ($_smarty_tpl->tpl_vars['i']->value['unit_code']){?><?php echo $_smarty_tpl->tpl_vars['i']->value['unit_code'];?>
<?php }else{ ?>PCS<?php }?>" name="invoice[unit_code][]" class="input_select unit_code">
								<?php  $_smarty_tpl->tpl_vars['s'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['s']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['units']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['s']->key => $_smarty_tpl->tpl_vars['s']->value){
$_smarty_tpl->tpl_vars['s']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['s']->key;
?>
								<option value="<?php echo $_smarty_tpl->tpl_vars['s']->value['unit_code'];?>
"><?php echo $_smarty_tpl->tpl_vars['s']->value['unit_enname'];?>
(<?php echo $_smarty_tpl->tpl_vars['s']->value['unit_cnname'];?>
)</option>
								<?php } ?>
							</select><span class="msg"></span></td>
						<td><input type='text' class='input_text invoice_unitcharge'
							name='invoice[invoice_unitcharge][]'
							value='<?php echo $_smarty_tpl->tpl_vars['i']->value['invoice_unitcharge'];?>
'> <span class="msg">*</span></td>
						<td class=''><span class='total invoice_totalcharge'><?php echo $_smarty_tpl->tpl_vars['i']->value['invoice_totalcharge'];?>
</span><span
							class="msg"></span></td>
							
							<!--  -->
							<td><input type='text' class='input_text weight'
							name='invoice[invoice_weight][]' value='<?php echo $_smarty_tpl->tpl_vars['i']->value['invoice_weight'];?>
'><span style="display:inline;color:red"> * </span>
						    </td>
						    <td class='totalWeight'><span class='totalWeight invoice_totalWeight'><?php echo $_smarty_tpl->tpl_vars['i']->value['invoice_totalWeight'];?>
</span><span class="msg"></span>
						    </td>
							<!--  -->
							
						<td><input type='text' class='input_text sku'
							name='invoice[sku][]' value='<?php echo $_smarty_tpl->tpl_vars['i']->value['sku'];?>
'> <span class="msg"></span>
						</td>
						<td><input type='text' class='input_text hs_code'
							name='invoice[hs_code][]' value='<?php echo $_smarty_tpl->tpl_vars['i']->value['hs_code'];?>
'> <span
							class="msg"></span></td>
						<td><input type='text' class='input_text invoice_note'
							name='invoice[invoice_note][]' value='<?php echo $_smarty_tpl->tpl_vars['i']->value['invoice_note'];?>
'> <span
							class="msg"></span></td>
						<td><input type='text' class='input_text invoice_url'
							name='invoice[invoice_url][]' value='<?php echo $_smarty_tpl->tpl_vars['i']->value['invoice_url'];?>
'> <span
							class="msg"></span></td>
						<td><a href='javascript:;' class='delInvoiceBtn'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
delete<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
						</td>
					</tr>
					<?php } ?> <?php }else{ ?>
					<tr class="table-module-b1">
						<td><input type='text' class='input_text invoice_enname'
							name='invoice[invoice_enname][]'> <span class="msg">*</span></td>
						<td><input type='text' class='input_text invoice_cnname'
							name='invoice[invoice_cnname][]' value=''> <span class="msg">*</span>
						</td>
						<td><input type='text' class='input_text quantity'
							name='invoice[invoice_quantity][]'> <span class="msg">*</span></td>
						<td><select default="PCE" name="invoice[unit_code][]"
							class="input_select unit_code"> <?php  $_smarty_tpl->tpl_vars['s'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['s']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['units']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['s']->key => $_smarty_tpl->tpl_vars['s']->value){
$_smarty_tpl->tpl_vars['s']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['s']->key;
?>
								<option value="<?php echo $_smarty_tpl->tpl_vars['s']->value['unit_code'];?>
"><?php echo $_smarty_tpl->tpl_vars['s']->value['unit_enname'];?>
(<?php echo $_smarty_tpl->tpl_vars['s']->value['unit_cnname'];?>
)</option>
								<?php } ?>
						</select> <span class="msg"></span></td>
						<td><input type='text' class='input_text invoice_unitcharge'
							name='invoice[invoice_unitcharge][]'> <span class="msg">*</span>
						</td>
						<td class='total'><span class='total invoice_totalcharge'>0</span><span
							class="msg"></span></td>
							
							<!--  -->
							<td><input type='text' class='input_text weight'
							name='invoice[invoice_weight][]'><span class="msg">*</span>
						    </td>
						    <td class='totalWeight'><span class='totalWeight invoice_totalWeight' >0</span><span class="msg"></span>
						    </td>
							<!--  -->
						
						<td><input type='text' class='input_text sku'
							name='invoice[invoice_sku][]' value='<?php echo $_smarty_tpl->tpl_vars['i']->value['sku'];?>
'> <span class="msg"></span>
						</td>
						<td><input type='text' class='input_text hs_code'
							name='invoice[hs_code][]'> <span class="msg"></span></td>
						<td><input type='text' class='input_text invoice_note'
							name='invoice[invoice_note][]' value=''> <span class="msg"></span>
						</td>
						<td><input type='text' class='input_text invoice_url'
							name='invoice[invoice_url][]' value=''> <span class="msg"></span>
						</td>
						<td><a href='javascript:;' class='delInvoiceBtn'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
delete<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
						</td>
					</tr>
					<?php }?>
				</tbody>
			</table>
			<div>
				<a href='javascript:;'
					style='font-weight: normal; font-size: 12px; line-height: 35px;'
					class='addInvoiceBtn'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
点击添加<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
			</div>
			<h2
				style='padding: 35px 0 0 0; margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC;'>
				4、<span class='submiter_wrap'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
发件人信息<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span><span
					class="msg">*</span> <a href='javascript:;'
					style='font-weight: normal; font-size: 12px; padding: 0 10px;'
					onclick='getSubmiter();'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
刷新<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
			</h2>
			<div style='line-height: 22px;' id='submiter_wrap'></div>
			<input type="hidden" name='order[order_id]'
				value='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)&&$_smarty_tpl->tpl_vars['order']->value['order_id']){?><?php echo $_smarty_tpl->tpl_vars['order']->value['order_id'];?>
<?php }?>'
				id='order_id'>
			<div
				style="width: 100%; text-align: center; margin-top: 15px; padding-top: 5px; border-top: 1px dashed #CCCCCC;">

				<input type='button' value='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
提交预报<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
'
					class='baseBtn submitBtn orderSubmitVerifyBtn orderSubmitBtn'
					status='P' style="padding: 5px 30px; margin: 5px;" /> <?php if (!$_GET['cpy']){?> <?php }?> <input type='button'
					value='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
保存草稿<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
'
					class='baseBtn  orderSubmitDraftBtn orderSubmitBtn' status='D'
					style="padding: 5px 30px; margin: 5px;" />
			</div>
		</form>
	</div>
</div>


<?php }} ?>