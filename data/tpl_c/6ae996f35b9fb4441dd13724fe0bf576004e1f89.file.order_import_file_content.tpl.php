<?php /* Smarty version Smarty-3.1.13, created on 2016-05-06 17:03:50
         compiled from "D:\yt1\application\modules\order\views\order\order_import_file_content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3669572c5df6381dd5-53507211%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6ae996f35b9fb4441dd13724fe0bf576004e1f89' => 
    array (
      0 => 'D:\\yt1\\application\\modules\\order\\views\\order\\order_import_file_content.tpl',
      1 => 1457516432,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3669572c5df6381dd5-53507211',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'result' => 0,
    'o' => 0,
    'k' => 0,
    'kk' => 0,
    'oo' => 0,
    'productKind' => 0,
    'clazz' => 0,
    'productCount' => 0,
    'c' => 0,
    'notExistCountryArr' => 0,
    'countrys' => 0,
    'result_str' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_572c5df6547048_19611555',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_572c5df6547048_19611555')) {function content_572c5df6547048_19611555($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'D:\\yt1\\libs\\Smarty\\plugins\\block.t.php';
?>
<?php if (isset($_smarty_tpl->tpl_vars['result']->value)){?> 
<?php if ($_smarty_tpl->tpl_vars['result']->value['ask']==0){?>
<div class="Tab">
	<ul>
		<li class="mainStatus" style="position: relative;" id='normal'>
			<a class="statusTag " href="javascript:;">
				<span class="order_title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
正常订单<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
			</a>
		</li>
		<li class="mainStatus" style="position: relative;" id='abnormal'>
			<a class="statusTag " href="javascript:;">
				<span class="order_title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
异常订单<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
			</a>
		</li>
		
		<li class="" style="position: relative;border:0 none;">
			<a class="addPrefixBtn " href="javascript:;" style='color:#2283c5;'>
				<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
批量添加单号前缀<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			</a>
		</li>
		
		<li class="" style="position: relative;border:0 none;">
			<a class="setProductCodeBtn " href="javascript:;" style='color:#2283c5;'>
				<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
批量设置运输方式<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			</a>
		</li>
	</ul>
</div>
<div style='width: 100%; overflow: auto;position:relative;' id='data_wrapper'>
    <div class='data_wrap_title' style='position:absolute;top:0;left:0;'>
		<table cellspacing="0" cellpadding="0" width="100%" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
				    <td width='60'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Excel行<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<?php  $_smarty_tpl->tpl_vars['o'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['o']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['result']->value['excel_column']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['o']->key => $_smarty_tpl->tpl_vars['o']->value){
$_smarty_tpl->tpl_vars['o']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['o']->key;
?>
					<td><?php echo $_smarty_tpl->tpl_vars['o']->value;?>
</td>
					<?php } ?>
					<td width="100"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
operation<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				</tr>
			</tbody>							
		</table>
	</div>
	<div id='normal_wrap' class='data_wrap'>
		<table cellspacing="0" cellpadding="0" width="100%" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
				    <td width='60'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Excel行<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<?php  $_smarty_tpl->tpl_vars['o'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['o']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['result']->value['excel_column']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['o']->key => $_smarty_tpl->tpl_vars['o']->value){
$_smarty_tpl->tpl_vars['o']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['o']->key;
?>
					<td><?php echo $_smarty_tpl->tpl_vars['o']->value;?>
</td>
					<?php } ?>
					<td width="100"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
operation<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				</tr>
			</tbody>
			<tbody class="table-module-list-data">
				<?php  $_smarty_tpl->tpl_vars['o'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['o']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['result']->value['success_arr']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['o']->key => $_smarty_tpl->tpl_vars['o']->value){
$_smarty_tpl->tpl_vars['o']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['o']->key;
?>
				<tr class="table-module-b1">
				    <td class='ec-center'><?php echo $_smarty_tpl->tpl_vars['k']->value;?>
</td>
					<?php  $_smarty_tpl->tpl_vars['oo'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['oo']->_loop = false;
 $_smarty_tpl->tpl_vars['kk'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['o']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['oo']->key => $_smarty_tpl->tpl_vars['oo']->value){
$_smarty_tpl->tpl_vars['oo']->_loop = true;
 $_smarty_tpl->tpl_vars['kk']->value = $_smarty_tpl->tpl_vars['oo']->key;
?>
					<td>					   
						<input class='input_text' name='fileData[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][<?php echo $_smarty_tpl->tpl_vars['kk']->value;?>
]' value='<?php echo $_smarty_tpl->tpl_vars['oo']->value;?>
' />	
					</td>
					<?php } ?>
					<td>
						<a href='javascript:;' class='delBtn'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
删除<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<div id='abnormal_wrap' class='data_wrap'>
		<table cellspacing="0" cellpadding="0" width="100%" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
				    <td width='60'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Excel行<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<?php  $_smarty_tpl->tpl_vars['o'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['o']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['result']->value['excel_column']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['o']->key => $_smarty_tpl->tpl_vars['o']->value){
$_smarty_tpl->tpl_vars['o']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['o']->key;
?>
					<td><?php echo $_smarty_tpl->tpl_vars['o']->value;?>
</td>
					<?php } ?>
					<td width="100"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
operation<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				</tr>
			</tbody>
			<tbody class="table-module-list-data">
			    <!-- 运输方式种类 -->
			    <?php $_smarty_tpl->tpl_vars['productCount'] = new Smarty_variable(count($_smarty_tpl->tpl_vars['productKind']->value), null, 0);?>
			    
				<?php  $_smarty_tpl->tpl_vars['o'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['o']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['result']->value['fail_arr']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['o']->key => $_smarty_tpl->tpl_vars['o']->value){
$_smarty_tpl->tpl_vars['o']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['o']->key;
?>
				<tr class="table-module-b1" title='<?php if (isset($_smarty_tpl->tpl_vars['result']->value['errTips'][$_smarty_tpl->tpl_vars['k']->value])){?><?php echo $_smarty_tpl->tpl_vars['result']->value['errTips'][$_smarty_tpl->tpl_vars['k']->value];?>
<?php }?>'>
				    <td class='ec-center'><?php echo $_smarty_tpl->tpl_vars['k']->value;?>
</td>
					<?php  $_smarty_tpl->tpl_vars['oo'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['oo']->_loop = false;
 $_smarty_tpl->tpl_vars['kk'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['o']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['oo']->key => $_smarty_tpl->tpl_vars['oo']->value){
$_smarty_tpl->tpl_vars['oo']->_loop = true;
 $_smarty_tpl->tpl_vars['kk']->value = $_smarty_tpl->tpl_vars['oo']->key;
?>
					<td>
					    <?php $_smarty_tpl->tpl_vars['clazz'] = new Smarty_variable('', null, 0);?>
					    <?php if ($_smarty_tpl->tpl_vars['kk']->value=='运输方式'){?>
					    <?php $_smarty_tpl->tpl_vars['clazz'] = new Smarty_variable('product_code', null, 0);?>
					    <?php }elseif($_smarty_tpl->tpl_vars['kk']->value=='客户单号'){?>
					    <?php $_smarty_tpl->tpl_vars['clazz'] = new Smarty_variable('shipper_hawbcode', null, 0);?>
					    <?php }?>
					    <?php if ($_smarty_tpl->tpl_vars['kk']->value=='运输方式'){?>
					    <select name="fileData[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][<?php echo $_smarty_tpl->tpl_vars['kk']->value;?>
]" class="input_select <?php echo $_smarty_tpl->tpl_vars['clazz']->value;?>
 product_code" style='width:200px;'>
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
' <?php if ($_smarty_tpl->tpl_vars['oo']->value==$_smarty_tpl->tpl_vars['c']->value['product_code']||$_smarty_tpl->tpl_vars['oo']->value==$_smarty_tpl->tpl_vars['c']->value['product_cnname']||$_smarty_tpl->tpl_vars['oo']->value==$_smarty_tpl->tpl_vars['c']->value['product_enname']){?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['c']->value['product_code'];?>
 [<?php echo $_smarty_tpl->tpl_vars['c']->value['product_cnname'];?>
  <?php echo $_smarty_tpl->tpl_vars['c']->value['product_enname'];?>
]</option>
							<?php } ?>
                        </select>
						<!-- <input class='input_text input_text01' name='fileData[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][<?php echo $_smarty_tpl->tpl_vars['kk']->value;?>
]' value='<?php echo $_smarty_tpl->tpl_vars['oo']->value;?>
' />	 -->				    
					    <?php }else{ ?>
						<input class='input_text <?php echo $_smarty_tpl->tpl_vars['clazz']->value;?>
' name='fileData[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][<?php echo $_smarty_tpl->tpl_vars['kk']->value;?>
]' value='<?php echo $_smarty_tpl->tpl_vars['oo']->value;?>
' />
						<?php }?>
					</td>
					<?php } ?>
					<td>
						<a href='javascript:;' class='delBtn'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
删除<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>	
<?php }else{ ?> 
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['result']->value['template_type']==''){?>
<!-- 没有找到匹配的模板  -->
<script type="text/javascript">
$(function(){
    <?php if ($_smarty_tpl->tpl_vars['result']->value['error_code']==1001){?>
    alertTip('<?php echo $_smarty_tpl->tpl_vars['result']->value['message'];?>
');
	<?php }else{ ?>
	openIframeDialog('/order/order-template/upload',1000,600,'<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
新增模板<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
	
	<?php }?>
});
</script>			
<?php }?>
<?php }?>		

<?php if ($_smarty_tpl->tpl_vars['notExistCountryArr']->value){?>
    <div id='notExistCountryMapWrap' style='width:800px;' title='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
配对失败国家<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
'>
       <table cellspacing="0" cellpadding="0" width="100%" border="0" class="table-module">
           <caption style='line-height:35px;font-weight:bold;font-size:14px;'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
配对失败国家<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</caption>
			<tbody>
				<tr class="table-module-title">
				    <td width='60'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
序号<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>				    
					<td width='120'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
上传国家<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
标准国家<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				</tr>
			</tbody>
			<tbody class="table-module-list-data">
				<?php  $_smarty_tpl->tpl_vars['o'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['o']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['notExistCountryArr']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['o']->key => $_smarty_tpl->tpl_vars['o']->value){
$_smarty_tpl->tpl_vars['o']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['o']->key;
?>
				<tr class="table-module-b1">
				    <td class='ec-center'><?php echo $_smarty_tpl->tpl_vars['k']->value+1;?>
</td>			    
					<td><?php echo $_smarty_tpl->tpl_vars['o']->value;?>
</td>
					<td>
					    <select class='notExistCountryMap' name='country_map[<?php echo $_smarty_tpl->tpl_vars['o']->value;?>
]' style=''>	
						    <option value=''><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
请选择<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
    					<?php  $_smarty_tpl->tpl_vars['oo'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['oo']->_loop = false;
 $_smarty_tpl->tpl_vars['kk'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['countrys']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['oo']->key => $_smarty_tpl->tpl_vars['oo']->value){
$_smarty_tpl->tpl_vars['oo']->_loop = true;
 $_smarty_tpl->tpl_vars['kk']->value = $_smarty_tpl->tpl_vars['oo']->key;
?>					
    						<option value='<?php echo $_smarty_tpl->tpl_vars['oo']->value['country_code'];?>
'><?php echo $_smarty_tpl->tpl_vars['oo']->value['country_code'];?>
[<?php echo $_smarty_tpl->tpl_vars['oo']->value['country_cnname'];?>
 <?php echo $_smarty_tpl->tpl_vars['oo']->value['country_enname'];?>
]</option>					
    					<?php } ?>					    
					    </select>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table> 
    </div>
        
    <!-- 国家不存在  -->
    <script type="text/javascript">
    $(function(){
    	$('#notExistCountryMapWrap0').dialog({
            autoOpen: true,
            width: 500,
            maxHeight: 500,
            modal: true,
            show: "slide",
            buttons: [
                {
                    text: 'Close',
                    click: function () {
                        $(this).dialog("close");
                    }
                }
            ],
            close: function () {
                $(this).remove();
            }
        });
    });

    $(function(){
    	$('.notExistCountryMap').chosen('destroy');
    	$('.notExistCountryMap').chosen({width:'300px',search_contains:true});


    	//$('.table-module-list-data .product_code').chosen('destroy');
    	//$('.table-module-list-data .product_code').chosen({width:'300px',search_contains:true});
    	
    }) 
        
    </script>	
<?php }?>	
<?php if ($_smarty_tpl->tpl_vars['result']->value){?>
<div style='padding: 15px; line-height: 22px;max-height:250px;overflow:auto;border:1px solid #ccc;margin-top:10px;position:relative;' id='result_message'>
	<p style='font-weight: bold; font-size: 25px; line-height: 30px; color: red;'><?php echo $_smarty_tpl->tpl_vars['result']->value['message'];?>

	<?php if ($_smarty_tpl->tpl_vars['result']->value['ask']==0){?>
	<input type='button' class='submitBtn' value='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
验证并上传<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
' style='margin-left:15px;margin-top:-5px;'>
	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['result']->value['ask']==1){?>
		<?php if ($_smarty_tpl->tpl_vars['result']->value['ansych']==0){?>
			<a href='javascript:;' onclick="leftMenu('order_manage','<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
订单管理<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
','/order/order-list/list?quick=39')" style='padding-left:10px;color:#0090e1;font-size:12px;'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
订单管理<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
		<?php }else{ ?>
			<a href='javascript:;' onclick="leftMenu('order_import_batch','<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
上传记录<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
','/order/order/get-import-batch?quick=0')" style='padding-left:10px;color:#0090e1;font-size:12px;'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
上传记录<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
		<?php }?>
	<?php }?>
	</p>
		
	<?php  $_smarty_tpl->tpl_vars['o'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['o']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['result']->value['errs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['o']->key => $_smarty_tpl->tpl_vars['o']->value){
$_smarty_tpl->tpl_vars['o']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['o']->key;
?>
	<p><?php echo $_smarty_tpl->tpl_vars['o']->value;?>
</p>
	<?php } ?>
</div>
<?php }?>
<div style='display:none;'><?php echo $_smarty_tpl->tpl_vars['result_str']->value;?>
</div><?php }} ?>