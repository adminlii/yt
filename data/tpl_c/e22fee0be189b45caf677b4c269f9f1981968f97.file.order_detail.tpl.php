<?php /* Smarty version Smarty-3.1.13, created on 2016-05-06 19:15:55
         compiled from "D:\yt1\application\modules\order\views\order\order_detail.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15484572c7cebe258c2-45234101%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e22fee0be189b45caf677b4c269f9f1981968f97' => 
    array (
      0 => 'D:\\yt1\\application\\modules\\order\\views\\order\\order_detail.tpl',
      1 => 1459158283,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15484572c7cebe258c2-45234101',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'order' => 0,
    'extservice' => 0,
    'shipperConsignee' => 0,
    'invoice' => 0,
    'i' => 0,
    'logArr' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_572c7cec0c7b35_36839578',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_572c7cec0c7b35_36839578')) {function content_572c7cec0c7b35_36839578($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'D:\\yt1\\libs\\Smarty\\plugins\\block.t.php';
?><link type="text/css" rel="stylesheet" href="/css/public/layout.css" />
<style>
<!--
#module-container .guild h2 {
	cursor: pointer;
	background: none repeat scroll 0% 0% transparent;
}

#module-container .guild h2.act {
	cursor: pointer;
	background: none repeat scroll 0% 0% #fff;
}

#search_more_div {
	display: none;
}

#search_more_div div {
	padding-top: 8px;
}

.input_text {
	width: 100px;
}

.fast_link {
	padding: 8px 10px;
}

.fast_link a {
	margin: 5px 8px 5px 0;
}

.order_detail {
	width: 100%;
	border-collapse: collapse;
}

.order_detail td {
	border: 1px solid #ccc;
}

#opDiv .baseBtn {
	margin-right: 10px;
}

.table-module th {
	border-bottom: 1px solid #FFFFFF;
	border-right: 1px solid #FFFFFF;
	line-height: 20px;
	padding: 5px;
}

.table-module {
	border-collapse: collapse;
}

.table-module-list-data td {
	text-align: left;
	border: 1px solid #ccc;
}

.table-module-list-data th {
	text-align: right;
	border: 1px solid #ccc;
}

.input_text {
	display: none;
}

.orderProductSubmitBtn,.orderProductSubmitToVerifyBtn,.orderSubmitBtn {
	display: none
}

.btnRight {
	float: right;
	margin-right: 5px;
}

.log_list {
	list-style-type: decimal;
	padding-left: 20px;
}

.disabled {
	background: #dddddd;
}

.depotState h2 {
	display: none;
}

.tabContent {
	padding: 5px 10px;
}

.table-module {
	background: none repeat scroll 0 0 #FFFFFF;
}

h3 {
	line-height: 35px;
}
-->
</style>
<script type="text/javascript"> 
</script>
<div id="module-container" style='padding: 0px 20px;'>
	<div id="module-table">
		<h3>1、<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
基本信息<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <?php echo $_smarty_tpl->tpl_vars['order']->value['shipper_hawbcode'];?>
</h3>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
			<tbody class="table-module-list-data orderInfo">
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
客户单号<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td width='150'><?php echo $_smarty_tpl->tpl_vars['order']->value['refer_hawbcode'];?>
</td>
					<th width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
服务商单号<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['order']->value['server_hawbcode'];?>
</td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
买家ID<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['order']->value['buyer_id'];?>
</td>
					<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
运输方式<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['order']->value['product_code'];?>
</td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
计费重<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['order']->value['order_weight'];?>
(KG)</td>
					<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
货物状态<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['order']->value['order_status'];?>
</td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
件数<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['order']->value['order_pieces'];?>
</td>
					<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
附加服务<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['extservice']->value;?>
</td>
				</tr>
				
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
体积<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['order']->value['length'];?>
CM*<?php echo $_smarty_tpl->tpl_vars['order']->value['width'];?>
CM*<?php echo $_smarty_tpl->tpl_vars['order']->value['height'];?>
CM</td>
					
					<td  colspan='2'></td>
				</tr>
				
			</tbody>
		</table>
		<h3>3、<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
收发件人信息<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
		<table width="45%" cellspacing="0" cellpadding="0" border="0" class="table-module" style='float:left;'>
		    <tbody>
				<tr class="table-module-title">
					<td colspan='2' align='center'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
发件人信息<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				</tr>
			</tbody>
			<tbody class="table-module-list-data orderInfo">
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
公司名称<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['shipper_company'];?>
</td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
姓名<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['shipper_name'];?>
</td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
国家<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['shipper_countrycode'];?>
</td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
省/州<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['shipper_province'];?>
</td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
城市<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['shipper_city'];?>
</td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
邮编<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['shipper_postcode'];?>
</td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
地址<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['shipper_street'];?>
</td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
联系电话<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['shipper_telephone'];?>
</td>
				</tr>
				<!-- 
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'>&nbsp;</th>
					<td style='text-align:right;'><a href='javascript:;' style='padding:0 10px;'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
展开更多<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></td>
				</tr>
				 -->
			</tbody>
		</table>		
		<table width="45%" cellspacing="0" cellpadding="0" border="0" class="table-module" style='float:left;margin-left:5%;'>
		    <tbody>
				<tr class="table-module-title">
					<td colspan='2' align='center'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
收件人信息<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				</tr>
			</tbody>
			<tbody class="table-module-list-data orderInfo">
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
公司名称<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['consignee_company'];?>
</td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
姓名<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['consignee_name'];?>
</td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
国家<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['consignee_countrycode'];?>
</td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
省/州<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['consignee_province'];?>
</td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
城市<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['consignee_city'];?>
</td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
邮编<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['consignee_postcode'];?>
</td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
地址1<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['consignee_street'];?>
</td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
地址2<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['consignee_street2'];?>
</td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
地址3<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['consignee_street3'];?>
</td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
联系电话<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['consignee_telephone'];?>
</td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
联系手机<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
					<td><?php echo $_smarty_tpl->tpl_vars['shipperConsignee']->value['consignee_mobile'];?>
</td>
				</tr>
				<!-- 
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'>&nbsp;</th>
					<td style='text-align:right;'><a href='javascript:;' style='padding:0 10px;'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
展开更多<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></td>
				</tr>
				 -->
			</tbody>
		</table>
		
		<h3 style='clear:both;'>3、<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
海关申报信息<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
		<table cellspacing="0" cellpadding="0" width="100%" border="0" class="table-module">
				<tbody>
					<tr class="table-module-title">
						<td width="180"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
英文品名<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width="180"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
中文品名<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width="40"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
数量<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width="60"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
单位<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width="60"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
单价<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
($)</td>
						<td width="80"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
总价<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width="80"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
重量<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
(kg)</td>
						<td width="80"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
总重<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width="120"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
SKU<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width="120"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
HSCODE<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width="120"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
配货信息<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
销售地址<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					</tr>
				</tbody>
				<tbody id="products" class="table-module-list-data">
					<?php  $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['i']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['invoice']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['i']->key => $_smarty_tpl->tpl_vars['i']->value){
$_smarty_tpl->tpl_vars['i']->_loop = true;
?>
					<tr class="table-module-b1">
						<td>
							<?php echo $_smarty_tpl->tpl_vars['i']->value['invoice_enname'];?>

						</td>
						<td>
							<?php echo $_smarty_tpl->tpl_vars['i']->value['invoice_cnname'];?>

						</td>
						<td>
							<?php echo $_smarty_tpl->tpl_vars['i']->value['invoice_quantity'];?>

						</td>
						<td>
						    <?php echo $_smarty_tpl->tpl_vars['i']->value['unit_code'];?>
							 
						</td>
						<td>
							<?php echo $_smarty_tpl->tpl_vars['i']->value['invoice_unitcharge'];?>

						</td>
						<td class='total'><?php echo $_smarty_tpl->tpl_vars['i']->value['invoice_totalcharge'];?>
</td>
						<td>
							<?php echo $_smarty_tpl->tpl_vars['i']->value['invoice_weight'];?>

						</td>
						<td class='total'><?php echo $_smarty_tpl->tpl_vars['i']->value['invoice_totalWeight'];?>
</td>
						
						<td>
							<?php echo $_smarty_tpl->tpl_vars['i']->value['sku'];?>

						</td>
						<td>
							<?php echo $_smarty_tpl->tpl_vars['i']->value['hs_code'];?>

						</td>
						<td>
							<?php echo $_smarty_tpl->tpl_vars['i']->value['invoice_note'];?>

						</td>
						<td><?php echo $_smarty_tpl->tpl_vars['i']->value['invoice_url'];?>

						</td>
						
					</tr>
					<?php } ?>
				</tbody>
			</table>
		<h3>4、<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
日志信息<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
		    <tbody>
				<tr class="table-module-title">
					<td width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
日期<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
日志<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
操作人<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				</tr>
			</tbody>
			<tbody class="table-module-list-data orderInfo">				
				<?php  $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['i']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['logArr']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['i']->key => $_smarty_tpl->tpl_vars['i']->value){
$_smarty_tpl->tpl_vars['i']->_loop = true;
?>
				<tr class="table-module-b1" style='height: 35px;'>
					<td><?php echo $_smarty_tpl->tpl_vars['i']->value['create_time'];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['i']->value['log_content'];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['i']->value['user_name'];?>
</td>
				</tr>
				<?php } ?>
				
				
			</tbody>
		</table>
	</div>
</div>
<?php }} ?>