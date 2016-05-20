<?php /* Smarty version Smarty-3.1.13, created on 2016-05-18 10:15:52
         compiled from "D:\yt1\application\modules\order\views\order\order_list_list_b2c.tpl" */ ?>
<?php /*%%SmartyHeaderCode:21476572c2580c184c5-55887576%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3d076edfb3611ceafdd67d886825145b1b4fe17a' => 
    array (
      0 => 'D:\\yt1\\application\\modules\\order\\views\\order\\order_list_list_b2c.tpl',
      1 => 1463537749,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21476572c2580c184c5-55887576',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_572c2580c954e4_67646562',
  'variables' => 
  array (
    'countryArr' => 0,
    'ob' => 0,
    'productKind' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_572c2580c954e4_67646562')) {function content_572c2580c954e4_67646562($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'D:\\yt1\\libs\\Smarty\\plugins\\block.t.php';
?><div id="search-module">
	<form class="submitReturnFalse" name="searchForm" id="searchForm">
		<input type="hidden" value="" id="filterActionId" />
		<input class="order_by" type="hidden" value="create_date desc" name='orderBy' id='orderBy' /> 
		<div class="search-module-condition">
			<span class="searchFilterText" style="width: 90px;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
单号<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</span>
			<select name="code_type" class='input_text_select input_select' id="code_type">
				<option value="shipper">运单号</option>
				<option value="refer">客户单号</option>
				<option value="server">跟踪单号</option>
			</select>
			<input type="text" class="input_text keyToSearch" id="code" name="shipper_hawbcode" style='width: 700px;' placeholder='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
multi_split_space<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
' />
		</div>
		<div id="search-module-baseSearch">
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;">&nbsp;</span>
				<input type="button" class="baseBtn submitToSearch" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
search<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
">
				&nbsp;&nbsp;
				<input type="button" class="baseBtn clearBtn" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
reset<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
">
				&nbsp;
				<a href="javascript:void(0)" class="toAdvancedSearch" onclick="toAdvancedSearch()"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
showAdvancedSearch<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
			</div>
			<div class="search-module-condition">
				<!-- 隐藏的提交属性 -->
				<input type="hidden" name='hold_sign' id='process_again'>
				<input type="hidden" name='ot_id' id='ot_id'>
				<input type="hidden" name='sub_status' id='sub_status'>			
			</div>
		</div>
		<div id="search-module-advancedSearch">
			
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_country<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</span>
				<select name="country_code" class='input_text_select input_select' style="float: left;">
					<option value=''><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
-all-<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
					<?php  $_smarty_tpl->tpl_vars['ob'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['ob']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['countryArr']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['ob']->key => $_smarty_tpl->tpl_vars['ob']->value){
$_smarty_tpl->tpl_vars['ob']->_loop = true;
?>
					<option value='<?php echo $_smarty_tpl->tpl_vars['ob']->value['country_code'];?>
'><?php echo $_smarty_tpl->tpl_vars['ob']->value['country_code'];?>
[<?php echo $_smarty_tpl->tpl_vars['ob']->value['country_cnname'];?>
 <?php echo $_smarty_tpl->tpl_vars['ob']->value['country_enname'];?>
]</option>
					<?php } ?>
				</select>
				<span class="searchFilterText" style="width: 90px;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
收件人名<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</span>
				
				<input type="text" class="input_text keyToSearch" name="consignee_name_like" style="width: 200px;" />
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
shipping_method<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</span>
				<select id="country" name="product_code" class='input_text_select input_select' style="float: left;">
					<option value=''><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
-all-<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
					<?php  $_smarty_tpl->tpl_vars['ob'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['ob']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['productKind']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['ob']->key => $_smarty_tpl->tpl_vars['ob']->value){
$_smarty_tpl->tpl_vars['ob']->_loop = true;
?>
					<option value='<?php echo $_smarty_tpl->tpl_vars['ob']->value['product_code'];?>
'><?php echo $_smarty_tpl->tpl_vars['ob']->value['product_code'];?>
[<?php echo $_smarty_tpl->tpl_vars['ob']->value['product_enname'];?>
 <?php echo $_smarty_tpl->tpl_vars['ob']->value['product_cnname'];?>
]</option>
					<?php } ?>
				</select>
				<span class="searchFilterText" style="width: 90px;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
create_time<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</span>
				<input type="text" class="datepicker input_text  " name="create_date_from" id='createDateFrom'/>
				~
				<input type="text" class="datepicker input_text  " name="create_date_to" id='createDateEnd'/>
			</div>
			
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
打印标志<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</span>
				<select name="print_sign" class='input_text_select input_select'>
					<option value=''><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
-all-<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
					<option value='0'>否</option>
					<option value='1'>是</option>
				</select>
			</div>
			<div class="search-module-condition" id='div_for_status_3_4_'></div>
			
			<div id='search_more_div'></div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;">&nbsp;</span>
				<input type="button" class="baseBtn submitToSearch" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
search<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
">
				&nbsp;&nbsp;
				<input type="button" class="baseBtn clearBtn" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
reset<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
">
				<input type='hidden' name='keyword' id='keyword' value='' />
				<input type='hidden' name='status' id='status' value='' />
				<input type='hidden' name='is_more' id='is_more' value='0' />
				&nbsp;
				<a href="javascript:void(0)" class="toAdvancedSearch" onclick="toBaseSearch()"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
hideAdvancedSearch<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
			</div>
		</div>
	</form>
</div><?php }} ?>