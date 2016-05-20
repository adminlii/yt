<?php /* Smarty version Smarty-3.1.13, created on 2016-04-25 15:31:56
         compiled from "F:\yt20160425\application\modules\order\views\order\order_import.tpl" */ ?>
<?php /*%%SmartyHeaderCode:20063571dc7ec40a457-40652872%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8fe971044370b7b9c00b94f4564ce8566ddd0dbe' => 
    array (
      0 => 'F:\\yt20160425\\application\\modules\\order\\views\\order\\order_import.tpl',
      1 => 1459158283,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20063571dc7ec40a457-40652872',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'baseTemplate' => 0,
    'o' => 0,
    'productKind' => 0,
    'productCount' => 0,
    'c' => 0,
    'order' => 0,
    'country' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_571dc7ec936908_85130134',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_571dc7ec936908_85130134')) {function content_571dc7ec936908_85130134($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'F:\\yt20160425\\libs\\Smarty\\plugins\\block.t.php';
?><link type="text/css" rel="stylesheet" href="/css/public/ajaxfileupload.css" />
<script type="text/javascript" src="/js/ajaxfileupload.js"></script>
<script type="text/javascript">
<?php echo $_smarty_tpl->getSubTemplate ('order/js/order/order_import.js', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</script>
<style>
<!--
.fill_in {
	display: none;
}
.table-module tr{cursor:pointer;}
.table-module .selected td{background:none repeat scroll 0 0 #cccccc;}
.data_wrap,.data_wrap_title {
	width: 10000px;
}
.data_wrap{
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
.data_wrap input{width:80px;}
#file_name{padding:0 8px;display:none;}
.dialog_div{display:none;}
.module-title{text-align:right;}
-->
</style>
<div id="module-container">
	<div class="Tab">
		<ul>
			<li id="normal" style="position: relative;" class="mainStatus0 ">
				<a href="javascript:;" class="statusTag " onclick="leftMenu('order_create','<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
单票录入<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
','/order/order/create?quick=52')">
					<span class="order_title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
单票录入<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
				</a>
			</li>
			<li id="abnormal" style="position: relative;" class="mainStatus0 chooseTag">
				<a href="javascript:;" class="statusTag " onclick="leftMenu('order_import','<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
批量上传<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
','/order/order/import?quick=24')">
					<span class="order_title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
批量上传<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
				</a>
			</li>
			<li id="abnormal" style="position: relative;" class="mainStatus0">
				<a href="javascript:;" class="statusTag " onclick="leftMenu('order_import_batch','<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
上传记录<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
','/order/order/get-import-batch?quick=0')">
					<span class="order_title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
上传记录<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
				</a>
			</li>
		</ul>
	</div>
	<div id="module-table" style='padding: 10px 20px;'>
		<h2 style='margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC;'>
			1、<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
下载模板并填写<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			<span style="font-weight: normal; font-size: 12px; padding: 0 15px;">(若不需要，请略过)</span>
		</h2>
		<div style='padding: 10px;'>
			<?php  $_smarty_tpl->tpl_vars['o'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['o']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['baseTemplate']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['o']->key => $_smarty_tpl->tpl_vars['o']->value){
$_smarty_tpl->tpl_vars['o']->_loop = true;
?>
			<a class="baseBtn submitToSearch" href='<?php echo $_smarty_tpl->tpl_vars['o']->value['report_file_path'];?>
' target='_blank'><?php echo $_smarty_tpl->tpl_vars['o']->value['report_filename'];?>
</a>
			<?php } ?>
			 
			<!--
			<a class="baseBtn submitToSearch" href='/file/小包模板.xls' target='_blank'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
小包模板<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
			<a class="baseBtn submitToSearch" href='/file/标准模板.xls' target='_blank'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
标准模板<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
			
			<a class="baseBtn submitToSearch" href='/file/normal_order3.xls' target='_blank'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
标准模板3<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a> 
			-->
			<a class="  viewProductKindBtn" href='javascript:;' style='margin-left:10px;'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
产品信息<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
			<a class="  viewRelationBtn" href='javascript:;' style='margin-left:10px;'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
运输方式&目的国家&附加服务关系<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
			
		</div>
		<h2 style='margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC;'>
			2、<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
选择发件人资料<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			<span style="font-weight: normal; font-size: 12px; padding: 0 15px;">(如果上传的资料中存在发件人信息，则按上传资料中保存，如果文件中没有，则按勾选的保存)</span>
			<a href='javascript:;' style='font-weight: normal; font-size: 12px; padding: 0 10px;' onclick='getSubmiter();'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
刷新<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
		</h2>
		<div style='line-height: 22px;padding: 10px;' id='submiter_wrap'></div>
		<h2 style='margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC;'>
			3、<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
选择填好的文件上传<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			<span style="font-weight: normal; font-size: 12px; padding: 0 15px;"><input type="checkbox" name="ansych" id="ansych" value="1"/> 只提交文件(内容超过1000条时勾选)</span>
    	</h2>	    
		<form enctype="multipart/form-data" method="POST" action="" name="form" id='uploadForm' onsubmit='return false;'>
			<label for='fileToUpload' style='padding:0;margin:0;display:none;'><input  type='button' class='baseBtn' value='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
选择文件<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
' style='margin-top:-4px;'/></label>
			<span id='file_name'></span>
			<input type='file' class='input_text fileToUpload' id='fileToUpload' name='fileToUpload' style=''>
			<input type='button' class='submitBtn' value='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
确认上传(数据提交)<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
' id='submitBtn'>
			<div id='file_upload_content_wrap'>
				<?php echo $_smarty_tpl->getSubTemplate ('order/views/order/order_import_file_content.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
				
			</div>
		</form>
	</div>
</div>
<div class='dialog_div' title='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
批量设置运输方式<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
' id='set_product_code_div'>
<!-- 运输方式种类 -->
<?php $_smarty_tpl->tpl_vars['productCount'] = new Smarty_variable(count($_smarty_tpl->tpl_vars['productKind']->value), null, 0);?>
<select id='product_code_set'  class="input_select ">
    <!-- 多个运输方式的时候，提供请选择选项 -->
    <?php if ($_smarty_tpl->tpl_vars['productCount']->value>1){?>
	<option value='' class='ALL'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
-select-<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
	<?php }?>
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
</select>
</div>

<div class='dialog_div' title='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
批量添加单号前缀<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
' id='add_prefix_div'>
    <input value="" id='prefix_set' class="input_text">
</div>
<div class='dialog_div' title='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
运输方式&目的国家&附加服务关系<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
' id='shipping_method_country_extra_service_div'>
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module" style='margin-top: 10px;'>
	<tbody class="table-module-list-data">
		<tr class=''>
			<td width="150" class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
运输方式<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
			<td>
				<select class='input_select product_code' name='order[product_code]' default='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['product_code'];?>
<?php }?>' id='product_code'>
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
				</select>
				<span class="msg">*</span>
			</td> 
		</tr>		
		<tr class=''>
			<td width="150" class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_country<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
			<td>
				<select class='input_select country_code' name='order[country_code]' default='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['country_code'];?>
<?php }?>' id='country_code' style='width:400px;max-width:400px;'>
					<option value=''  class='ALL'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
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
				</select>
				
				<span class="msg">*</span>
			</td>
		</tr>
		<tr class=''>
			<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
附加服务<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
			<td>
				<span class='product_extraservice_wrap' id='product_extraservice_wrap'>
 							<!-- 
 							<label><input type='checkbox' class='' value='A1' name='extraservice[]' /><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
购买保险<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label> 
 							<label><input type='checkbox' class='' value='11' name='extraservice[]' /><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
支持退件<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>
 							 -->
				</span>
				
				 <!-- -->
				 <span class="msg"></span>
			</td>
		</tr>		
	</tbody>
</table>
</div><?php }} ?>