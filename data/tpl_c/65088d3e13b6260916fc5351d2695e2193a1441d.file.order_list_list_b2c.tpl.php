<?php /* Smarty version Smarty-3.1.13, created on 2016-04-25 15:32:14
         compiled from "F:\yt20160425\application\modules\platform\views\order\order_list_list_b2c.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1864571dc7fe9c7382-35120016%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '65088d3e13b6260916fc5351d2695e2193a1441d' => 
    array (
      0 => 'F:\\yt20160425\\application\\modules\\platform\\views\\order\\order_list_list_b2c.tpl',
      1 => 1457516431,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1864571dc7fe9c7382-35120016',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'user_account_arr' => 0,
    'ob' => 0,
    'countryArr' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_571dc7fea0d895_32302432',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_571dc7fea0d895_32302432')) {function content_571dc7fea0d895_32302432($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'F:\\yt20160425\\libs\\Smarty\\plugins\\block.t.php';
?><style>
.sku,.warehouseSkuWrap {
	float: left;
	text-align: left;
}
.sku{
	width: 30%;
}
.warehouseSkuWrap{
	width: 35%;
}
.link_platform .count{color:red;padding:0 0 0 5px;}
</style>
<div id="search-module">
	<form class="submitReturnFalse" name="searchForm" id="searchForm">
		<table width="100%" cellspacing="0" cellpadding="0" border="0" id="searchfilterArea1" class="searchfilterArea">
			<tbody>
				<tr>
					<td>
						<div class="searchFilterText" style="width: 105px;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
平台<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</div><!-- 订单分类 -->
						<div class="pack_manager">
							<input type="hidden" class="input_text keyToSearch" id="platform" name="platform" value="">
							<a class="link_platform platform_" platform='' onclick="searchFilterSubmit('platform','',this)" href="javascript:void(0)"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
all<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<span class='count'>(0)</span></a><!-- 全部 -->
							<a class="link_platform platform_ebay" platform='ebay' onclick="searchFilterSubmit('platform','ebay',this)" href="javascript:void(0)"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
ebay<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<span class='count'>(0)</span></a>
							<a class="link_platform platform_amazon" platform='amazon' onclick="searchFilterSubmit('platform','amazon',this)" href="javascript:void(0)"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
amazon<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<span class='count'>(0)</span></a>
							<a class="link_platform platform_aliexpress" platform='aliexress' onclick="searchFilterSubmit('platform','aliexpress',this)" href="javascript:void(0)"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
aliexpress<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<span class='count'>(0)</span></a>
						    <a class="link_platform platform_mabang" platform='mabang' onclick="searchFilterSubmit('platform','mabang',this)" href="javascript:void(0)"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
mabang<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<span class='count'>(0)</span></a>
						</div>
					</td>
				</tr>
				
			</tbody>
		</table>
		<!--  
		<input type="hidden" value="" id="filterActionId" />
		 -->
		<div class="search-module-condition" style="padding-bottom:10px;">
			<span class="searchFilterText" style="width: 105px;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
标记发货<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</span><!-- 标记发货 -->	
			<div class="pack_manager">		
				<input type="hidden" class="input_text keyToSearch" id="platform_ship_status" name="platform_ship_status" value="">
				<a onclick="searchFilterSubmit('platform_ship_status','',this)" href="javascript:void(0)"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
all<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a><!-- 全部 -->
				<a onclick="searchFilterSubmit('platform_ship_status','1',this)" href="javascript:void(0)"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
是<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a><!-- 是 -->
				<a onclick="searchFilterSubmit('platform_ship_status','0',this)" href="javascript:void(0)"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
否<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a><!-- 否 -->
			</div>
		</div>
		<div class="search-module-condition">
			<span class="searchFilterText" style="width: 105px;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
单号<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</span>
			<input type="text" class="input_text keyToSearch" id="code" name="refrence_no" style='width: 400px;' placeholder='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
multi_split_space<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
' />
		</div>
		<div class="search-module-condition">
			<span class="searchFilterText" style="width: 105px;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
关键字<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</span><!-- 单号 -->			
			<input type="text" class="input_text keyToSearch" id="keyword" name="keyword" style='width: 800px;' placeholder='平台单号,参考号,运输方式,目的国家,买家ID,收件人名称,收件人邮箱,ItemID,eBay交易流水号,收件人手机,收件人邮编,收件人门牌号' />
		</div>
		<div class="search-module-condition">			
			<span class="searchFilterText" style="width: 105px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
shop_account<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</span><!-- 店铺帐号 -->
			<select id="user_account" name="user_account" class="input_text_select">
				<option value=''><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
all<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
				<?php  $_smarty_tpl->tpl_vars['ob'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['ob']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['user_account_arr']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['ob']->key => $_smarty_tpl->tpl_vars['ob']->value){
$_smarty_tpl->tpl_vars['ob']->_loop = true;
?>
				<option value='<?php echo $_smarty_tpl->tpl_vars['ob']->value['user_account'];?>
'><?php echo $_smarty_tpl->tpl_vars['ob']->value['platform_user_name'];?>
</option>
				<?php } ?>
			</select>
		</div>
		<div class="search-module-condition">			
			<span class="searchFilterText" style="width: 105px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_country<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</span><!-- 国家 -->
			<select id="country" name="country" style="width: 500px;">
				<option value=''><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
all<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
				<?php  $_smarty_tpl->tpl_vars['ob'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['ob']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['countryArr']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['ob']->key => $_smarty_tpl->tpl_vars['ob']->value){
$_smarty_tpl->tpl_vars['ob']->_loop = true;
?>
				<option value='<?php echo $_smarty_tpl->tpl_vars['ob']->value['country_code'];?>
'><?php echo $_smarty_tpl->tpl_vars['ob']->value['country_code'];?>
 (<?php echo $_smarty_tpl->tpl_vars['ob']->value['country_enname'];?>
 | <?php echo $_smarty_tpl->tpl_vars['ob']->value['country_cnname'];?>
)</option>
				<?php } ?>
			</select>
		</div>
		
		
		<div class="search-module-condition">
			<span class="searchFilterText" style="width: 105px;">&nbsp;</span>
			<input type="button" class="baseBtn submitToSearch" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
search<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"><!-- 搜索 -->
			<input type='hidden' name='status' id='status' value='2' />		
			<input type="button" style="float:right;" value="手动拉单" class="getOrderBtn baseBtn" status="5">	
		</div>
	</form>
</div><?php }} ?>