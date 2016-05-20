<?php /* Smarty version Smarty-3.1.13, created on 2014-07-22 16:18:55
         compiled from "E:\Zend\workspaces\ruston_oms\application\modules\product\views\product\product-detail-log.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3266853ce1e6fd6ab19-26972372%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a80fecdf8ff47c26306b4f32e3de572fcb1da30a' => 
    array (
      0 => 'E:\\Zend\\workspaces\\ruston_oms\\application\\modules\\product\\views\\product\\product-detail-log.tpl',
      1 => 1405996337,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3266853ce1e6fd6ab19-26972372',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'product' => 0,
    'logs' => 0,
    'row' => 0,
    'productInventory' => 0,
    'orderProduct' => 0,
    'productAsn' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_53ce1e700dc952_32531115',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ce1e700dc952_32531115')) {function content_53ce1e700dc952_32531115($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'E:\\Zend\\workspaces\\ruston_oms\\libs\\Smarty\\plugins\\block.t.php';
?><style>
.ellipsis {
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
	cursor: pointer;
}
</style>
<div id="content" xmlns="http://www.w3.org/1999/html">
	<div class="depotState_title">
		<img align="absmiddle" style="margin-right: 5px;" src="/images/pack/icon_depot01.gif">
		<span style="font-size: 14px; font-weight: bold;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_detail<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>&nbsp;&nbsp;&gt;&nbsp;&nbsp;
		<b><?php echo $_smarty_tpl->tpl_vars['product']->value['product_sku'];?>
</b>
	</div>
	<div class="depotState" style="">
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="depotForm">
			<tbody>
				<tr>
					<td width="12%" class="depot_path"><b style="font-size: 14px;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_status<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</b>：</td>
					<td width="20%"><b style="font-size: 14px;"><?php if ($_smarty_tpl->tpl_vars['product']->value['product_status']=="1"){?><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
confirm<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }else{ ?><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
delete<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }?></b></td>
					<td width="10%" class="depot_path"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
					<td width="20%"><?php echo $_smarty_tpl->tpl_vars['product']->value['product_title'];?>
</td>
					<td width="10%" class="depot_path"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_title_en<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
					<td width="28%"><?php echo $_smarty_tpl->tpl_vars['product']->value['product_title_en'];?>
</td>
				</tr>
				<tr>
					<td class="depot_path"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
SKU<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
					<td><?php echo $_smarty_tpl->tpl_vars['product']->value['product_sku'];?>
</td>
					<td width="8%" class="depot_path"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_barcode<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
					<td width="22%"><?php echo $_smarty_tpl->tpl_vars['product']->value['product_barcode'];?>
</td>
					<td class="depot_path"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
ref_no<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
					<td><?php echo $_smarty_tpl->tpl_vars['product']->value['reference_no'];?>
</td>
				</tr>
				<tr>
					<td class="depot_path"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_category<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
					<td><?php echo $_smarty_tpl->tpl_vars['product']->value['product_category_name'];?>
</td>
					<td class="depot_path"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_declared_name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
					<td><?php echo $_smarty_tpl->tpl_vars['product']->value['product_declared_name'];?>
</td>
					<td class="depot_path"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_declared_value<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
(USD)：</td>
					<td><?php echo $_smarty_tpl->tpl_vars['product']->value['product_declared_value'];?>
</td>
				</tr>
				<tr>
					<td class="depot_path"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_weight<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
(KG)：</td>
					<td><?php echo $_smarty_tpl->tpl_vars['product']->value['product_weight'];?>
</td>
					<td class="depot_path"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_length<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 * <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_width<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 * <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_height<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
(CM)：</td>
					<td><?php echo $_smarty_tpl->tpl_vars['product']->value['product_length'];?>
 * <?php echo $_smarty_tpl->tpl_vars['product']->value['product_width'];?>
 * <?php echo $_smarty_tpl->tpl_vars['product']->value['product_height'];?>
</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td style="vertical-align: top;" class="depot_path"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_desc<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
					<td colspan="5"><?php echo $_smarty_tpl->tpl_vars['product']->value['product_desc'];?>
</td>
				</tr>
			</tbody>
		</table>
		<div class="clr"></div>
	</div>
	<div class="depotTab2">
		<ul>
			<li>
				<a href="javascript:void(0);" id='tab_log' class='tab' style="cursor: pointer"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_log<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
			</li>
			<li class="chooseTag">
				<a href="javascript:void(0);" id='tab_inventory' class='tab' style="cursor: pointer"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_inventory<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
			</li>
			<li class="chooseTag">
				<a href="javascript:void(0);" id='tab_order' class='tab' style="cursor: pointer"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_orders<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
			</li>
			<li class="chooseTag">
				<a hhref="javascript:void(0);" id='tab_asn' class='tab' style="cursor: pointer"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_asn<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
			</li>
		</ul>
	</div>
	<div class="depotContent" style="">
		<div class="tabContent" id="log">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
				<tr class="table-module-title">
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
log_content<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
log_user<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
log_time<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
ip<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				</tr>
				<tbody id="table-module-list-data">
					<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['row']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['logs']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
$_smarty_tpl->tpl_vars['row']->_loop = true;
?>
					<tr class='table-module-b1'>
						<td class="ellipsis" title="<?php echo $_smarty_tpl->tpl_vars['row']->value['pl_note'];?>
"><?php echo $_smarty_tpl->tpl_vars['row']->value['pl_note'];?>
</td>
						<td><?php if ($_smarty_tpl->tpl_vars['row']->value['user_id']=="0"){?>系统<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['row']->value['user_name'];?>
<?php }?></td>
						<td><?php echo $_smarty_tpl->tpl_vars['row']->value['pl_add_time'];?>
</td>
						<td><?php echo $_smarty_tpl->tpl_vars['row']->value['pl_ip'];?>
</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<div class="tabContent" id="inventory" style="display: none;">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
				<tr class="table-module-title">
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
warehouse_name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pi_onway<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pi_pending<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pi_sellable<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pi_unsellable<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pi_reserved<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pi_shipped<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pi_hold<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td width="120"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
create_time<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td width="120"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
update_time<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				</tr>
				<tbody>
					<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['row']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['productInventory']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
$_smarty_tpl->tpl_vars['row']->_loop = true;
?>
					<tr class='table-module-b1'>
						<td><?php echo $_smarty_tpl->tpl_vars['row']->value['warehouse_code'];?>
</td>
						<td><?php echo $_smarty_tpl->tpl_vars['row']->value['pi_onway'];?>
</td>
						<td><?php echo $_smarty_tpl->tpl_vars['row']->value['pi_pending'];?>
</td>
						<td><?php echo $_smarty_tpl->tpl_vars['row']->value['pi_sellable'];?>
</td>
						<td><?php echo $_smarty_tpl->tpl_vars['row']->value['pi_unsellable'];?>
</td>
						<td><?php echo $_smarty_tpl->tpl_vars['row']->value['pi_reserved'];?>
</td>
						<td><?php echo $_smarty_tpl->tpl_vars['row']->value['pi_shipped'];?>
</td>
						<td><?php echo $_smarty_tpl->tpl_vars['row']->value['pi_hold'];?>
</td>
						<td><?php echo $_smarty_tpl->tpl_vars['row']->value['pi_add_time'];?>
</td>
						<td><?php echo $_smarty_tpl->tpl_vars['row']->value['pi_update_time'];?>
</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		
		<div class="tabContent" id="order" style="display: none;">
			<div style="padding: 5px; font-weight: bold"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array('k'=>'50')); $_block_repeat=true; echo smarty_block_t(array('k'=>'50'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
lastest_num_record<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array('k'=>'50'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</div>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
				<tr class="table-module-title">
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
order_code<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
quantity<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
warehouse_name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
add_time<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
update_time<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				</tr>
				<tbody>
					<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['row']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['orderProduct']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
$_smarty_tpl->tpl_vars['row']->_loop = true;
?>
					<tr class='table-module-b1'>
						<td><?php echo $_smarty_tpl->tpl_vars['row']->value['order_code'];?>
</td>
						<td><?php echo $_smarty_tpl->tpl_vars['row']->value['op_quantity'];?>
</td>
						<td><?php echo $_smarty_tpl->tpl_vars['row']->value['warehouse_code'];?>
</td>
						<td><?php echo $_smarty_tpl->tpl_vars['row']->value['op_add_time'];?>
</td>
						<td><?php echo $_smarty_tpl->tpl_vars['row']->value['op_update_time'];?>
</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<div class="tabContent" id="asn" style="display: none;">
			<div style="padding: 5px; font-weight: bold"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array('k'=>'20')); $_block_repeat=true; echo smarty_block_t(array('k'=>'20'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
lastest_num_record<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array('k'=>'20'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</div>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
				<tr class="table-module-title">
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
receiving_code<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
receiving_status<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
receiving_quantity<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
putaway_qty<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
received_quantity<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
is_qc<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
is_priority<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				</tr>
				<tbody>
					<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['row']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['productAsn']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
$_smarty_tpl->tpl_vars['row']->_loop = true;
?>
					<tr class='table-module-b1'>
						<td><?php echo $_smarty_tpl->tpl_vars['row']->value['receiving_code'];?>
</td>
						<td><?php if ($_smarty_tpl->tpl_vars['row']->value['rd_status']=="2"){?>处理完成<?php }elseif($_smarty_tpl->tpl_vars['row']->value['rd_status']=="1"){?>收货中<?php }else{ ?>在途<?php }?></td>
						<td><?php echo $_smarty_tpl->tpl_vars['row']->value['rd_receiving_qty'];?>
</td>
						<td><?php echo $_smarty_tpl->tpl_vars['row']->value['rd_putaway_qty'];?>
</td>
						<td><?php echo $_smarty_tpl->tpl_vars['row']->value['rd_received_qty'];?>
</td>
						<td><?php if ($_smarty_tpl->tpl_vars['row']->value['is_qc']=="1"){?>是<?php }else{ ?>否<?php }?></td>
						<td><?php if ($_smarty_tpl->tpl_vars['row']->value['is_priority']=="1"){?>是<?php }else{ ?>否<?php }?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
	<script type="text/javascript">
    $(function(){
        $(".tab").click(function(){
            $(".tabContent").hide();

            $(this).parent().removeClass("chooseTag");
            $(this).parent().siblings().addClass("chooseTag");
            $("#"+$(this).attr("id").replace("tab_","")).show();
        });
    })
</script><?php }} ?>