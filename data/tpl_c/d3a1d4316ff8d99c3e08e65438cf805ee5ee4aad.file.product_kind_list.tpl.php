<?php /* Smarty version Smarty-3.1.13, created on 2016-05-12 09:11:18
         compiled from "D:\yt1\application\modules\order\views\product_kind\product_kind_list.tpl" */ ?>
<?php /*%%SmartyHeaderCode:202115733d836da9542-55758890%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd3a1d4316ff8d99c3e08e65438cf805ee5ee4aad' => 
    array (
      0 => 'D:\\yt1\\application\\modules\\order\\views\\product_kind\\product_kind_list.tpl',
      1 => 1457516431,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '202115733d836da9542-55758890',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'productKind' => 0,
    'c' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_5733d836e2b047_91173461',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5733d836e2b047_91173461')) {function content_5733d836e2b047_91173461($_smarty_tpl) {?><table cellspacing="0" cellpadding="0" border="0" width="100%" class="table-module">
	<tbody>
		<tr class="table-module-title">
			<td width="100">产品代码</td>
			<td width="100">中文名</td>
			<td width="100">英文名</td>
			<td>网站备注</td>
		</tr>
	</tbody>
	<tbody class="table-module-list-data" id="products">
	    <?php  $_smarty_tpl->tpl_vars['c'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['c']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['productKind']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['c']->key => $_smarty_tpl->tpl_vars['c']->value){
$_smarty_tpl->tpl_vars['c']->_loop = true;
?>								
		<tr class="table-module-b1">
			<td><?php echo $_smarty_tpl->tpl_vars['c']->value['product_code'];?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['c']->value['product_cnname'];?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['c']->value['product_enname'];?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['c']->value['product_note'];?>
</td>
		</tr>
		<?php } ?>
	</tbody>
</table><?php }} ?>