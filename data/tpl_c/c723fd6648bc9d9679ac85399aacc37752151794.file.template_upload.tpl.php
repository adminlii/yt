<?php /* Smarty version Smarty-3.1.13, created on 2016-05-12 13:32:44
         compiled from "D:\yt1\application\modules\order\views\order-template\template_upload.tpl" */ ?>
<?php /*%%SmartyHeaderCode:295915734157c3a1b96-52344767%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c723fd6648bc9d9679ac85399aacc37752151794' => 
    array (
      0 => 'D:\\yt1\\application\\modules\\order\\views\\order-template\\template_upload.tpl',
      1 => 1457516431,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '295915734157c3a1b96-52344767',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'file_name' => 0,
    'userTemplate' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_5734157c3ff7a7_83849923',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5734157c3ff7a7_83849923')) {function content_5734157c3ff7a7_83849923($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'D:\\yt1\\libs\\Smarty\\plugins\\block.t.php';
?><script type="text/javascript">
<?php echo $_smarty_tpl->getSubTemplate ('order/js/order-template/template_upload.js', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</script>
<style>
<!--
.fill_in {
	display: none;
}

.table-module tr {
	cursor: pointer;
}

.table-module .selected td {
	background: none repeat scroll 0 0 #cccccc;
}

.data_wrap,.data_wrap_title {
	width: 10000px;
}

.data_wrap {
	display: none;
}

.chooseTag {
	background: #2283c5;
	font-weight: bold;
}

.Tab ul li.mainStatus {
	margin: 0 0px 0 0;
	padding: 0 30px;
}

.mainStatus {
	cursor: pointer;
}

.data_wrap input {
	width: 80px;
}

.msg {
	color: red;
	padding: 0 5px;
}

#saveTemplateBtn {
	width: 80px;
	cursor: hand;
	float: right;
	margin: -40px 20px 0 0;
}
-->
</style>
<div id="module-container">
	<div class="Tab">
		<ul>
			<li id="normal" style="position: relative;" class="mainStatus chooseTag">
				<a href="/order/order-template/upload" class="statusTag ">
					<span class="order_title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
新增模板<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
				</a>
			</li>
			<li id="abnormal" style="position: relative;" class="mainStatus ">
				<a href="/order/order-template/edit" class="statusTag ">
					<span class="order_title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
修改模板<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
				</a>
			</li>
		</ul>
	</div>
	<div id="module-table" style='padding: 10px 20px;'>
		<h2 style='margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC;'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
选择文件上传<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
		<form enctype="multipart/form-data" method="POST" action="/order/order-template/upload" name="form" id='uploadForm'>
			<input type='file' class='input_text baseBtn' id='fileToUpload' name='fileToUpload'>
			<input type='button' class='submitBtn' id='uploadBtn' value='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
模板上传<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
'>
			<span style='padding: 0 20px;'><?php if ($_smarty_tpl->tpl_vars['file_name']->value){?><?php echo $_smarty_tpl->tpl_vars['file_name']->value;?>
<?php }?></span>
		</form>
	</div>
	<?php if ($_smarty_tpl->tpl_vars['userTemplate']->value){?> <?php echo $_smarty_tpl->getSubTemplate ('order/views/order-template/template_edit_form.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
 <?php }?>
</div><?php }} ?>