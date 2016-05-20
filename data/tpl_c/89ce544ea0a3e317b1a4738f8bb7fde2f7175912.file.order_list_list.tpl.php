<?php /* Smarty version Smarty-3.1.13, created on 2016-04-25 15:32:14
         compiled from "F:\yt20160425\application\modules\platform\views\order\order_list_list.tpl" */ ?>
<?php /*%%SmartyHeaderCode:10271571dc7fe88eb48-04122550%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '89ce544ea0a3e317b1a4738f8bb7fde2f7175912' => 
    array (
      0 => 'F:\\yt20160425\\application\\modules\\platform\\views\\order\\order_list_list.tpl',
      1 => 1457516431,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10271571dc7fe88eb48-04122550',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'user_account_arr' => 0,
    'ob' => 0,
    'load_platform_order_day' => 0,
    'statusArr' => 0,
    'k' => 0,
    'status' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_571dc7fe90f9d7_64499708',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_571dc7fe90f9d7_64499708')) {function content_571dc7fe90f9d7_64499708($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'F:\\yt20160425\\libs\\Smarty\\plugins\\block.t.php';
?>
<script src="/js/jquery-ui-timepicker-addon.js" type="text/javascript"></script>
<script src="/js/jquery.overlay.min.js" type="text/javascript"></script>
<script type="text/javascript">
<?php echo $_smarty_tpl->getSubTemplate ('platform/js/order/order_list_list_common.js', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ('platform/js/order/order_list_list.js', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</script>
<style>
<!--
.opDiv .baseBtn {
	margin: 3px 5px 5px;
}

.statusTag .count {
	color: red;
	padding: 0 3px;
}

.dialog_div {
	display: none;
}

.chooseTag {
	font-weight: bold;
	font-size: 14px;
	color: #000;
}

.datepicker {
	background-color: #eee;
}
.logIcon,.loadLogIcon {
	background: url(/images/sidebar/bg_mailSch.gif) center center;
	display: none;
	height: 16px;
	vertical-align: middle;
	white-space: nowrap;
	width: 18px;
}
.ellipsis {
	display: block;
	width: 100%; /*对宽度的定义,根据情况修改*/
	overflow: hidden;
	white-space: nowrap;
	text-overflow: ellipsis;
}

.ellipsis:after {
	/*content: "...";*/
}
.load_order_list_msg{list-style-type: decimal;padding-left:30px;}
 
-->
</style>
<!-- 订单审核 -->
<div id='verify_div_verify' title='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
生成标准订单<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
' class='dialog_div'>
	<form action="" id='verify_div_form' method='POST' onsubmit='return false;'>
		<h2 style="padding: 0px 0 0 0; margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC; line-height: 30px;">
			<span class=" web_require">1、<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
运输方式<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
			<span class="msg">*</span>
			<span style="font-weight: normal; font-size: 12px;">未分配运输方式的订单,将设置为该运输方式</span>
		</h2>
		<select id="product_code" name="product_code" class="input_select product_code web_require" style=''>
		</select>
		<div>
			<h2 style="padding: 15px 0 0 0; margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC;">
				<span class="submiter_wrap web_require">2、<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
发件人信息<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
				<span class="msg">*</span>
				<a onclick="getSubmiter();" style="font-weight: normal; font-size: 12px; padding: 0 10px;" href="javascript:;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
刷新<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
			</h2>
			<div id="submiter_wrap" style="line-height: 22px;">
				<div class="shipper_account"></div>
				<div>
					<a shipper_account="" class="submiterOpBtn" style="font-weight: normal; font-size: 12px; line-height: 35px; margin: 0 0px;" href="javascript:;" onclick="submiterOp(this);"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
点击添加<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
				</div>
			</div>
		</div>
	</form>
</div>

<!-- 平台SKU申报信息映射 -->
<div id='product_relation_div' title='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
平台SKU申报信息映射<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
' class='dialog_div'>
	
</div>
<!-- 拉单 -->
<div id='load_order_div' title='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
手工拉单<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
' class='dialog_div'>
	<form action="" id='load_order_form' method='POST' onsubmit='return false;'>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="table-module">
			<tbody>
				<tr class="table-module-b1">
					<td class="ec-center">账号</td>
					<td class="">
						<select name="user_account" class="input_text_select">
							<option value=''><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
-select-<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
							<?php  $_smarty_tpl->tpl_vars['ob'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['ob']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['user_account_arr']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['ob']->key => $_smarty_tpl->tpl_vars['ob']->value){
$_smarty_tpl->tpl_vars['ob']->_loop = true;
?>
							<option value='<?php echo $_smarty_tpl->tpl_vars['ob']->value['user_account'];?>
'><?php echo $_smarty_tpl->tpl_vars['ob']->value['platform'];?>
 [<?php echo $_smarty_tpl->tpl_vars['ob']->value['user_account'];?>
] [<?php echo $_smarty_tpl->tpl_vars['ob']->value['platform_user_name'];?>
]</option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr class="table-module-b1">
					<td class="ec-center">平台订单更新时间</td>
					<td class="">
						<input type="text" class="input_text datepicker" value="" name="start_time" style='width: 120px;' readonly>
						~
						<input type="text" class="input_text datepicker" value="" name="end_time" style='width: 120px;' readonly>
					</td>
				</tr>
			</tbody>
		</table>
		<p style='color: red;line-height:30px;'>时间间隔需小于<?php echo $_smarty_tpl->tpl_vars['load_platform_order_day']->value;?>
天</p>
	</form>
</div>
<div id='biaojifahuo_div' title='标记发货' class='dialog_div'>
	<form enctype="multipart/form-data" method="POST" action="" onsubmig='return false;' id='biaojifahuo_form'>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td class="ec-center">订单号</td>
					<td class="ec-center">目的国家</td>
					<td class="ec-center">承运商(标记运输方式)</td>
					<td class="ec-center">跟踪号</td>
				</tr>
			</tbody>
			<tbody id='order_list_wrap'>
			</tbody>
		</table>
	</form>
</div>

<div id='allot_div' title='分配运输方式' class='dialog_div'>
	<form enctype="multipart/form-data" method="POST" action="" onsubmig='return false;' id='allot_form'>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td class="ec-center">订单号</td>
					<td class="ec-center">目的国家</td>
					<td class="ec-center">平台运输方式</td>
					<td class="ec-center">运输方式</td>
				</tr>
			</tbody>
			<tbody id='allot_list_wrap'>
			</tbody>
		</table>
	</form>
</div>
<div id="module-container" style=''>
	 <?php echo $_smarty_tpl->getSubTemplate ('platform/views/order/order_list_list_b2c.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<div id="module-table" style='overflow: auto;'>
		<div class="Tab">
			<ul>
				<?php  $_smarty_tpl->tpl_vars['status'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['status']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['statusArr']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['status']->key => $_smarty_tpl->tpl_vars['status']->value){
$_smarty_tpl->tpl_vars['status']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['status']->key;
?>
				<li style='position: relative;' class='mainStatus'>
					<a href="javascript:void (0)" class="statusTag <?php if ($_smarty_tpl->tpl_vars['k']->value!=''){?>statusTag<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
<?php }else{ ?>statusTagAll<?php }?>" status='<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
'>
						<span class='order_title<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
' title='<?php echo $_smarty_tpl->tpl_vars['status']->value['name'];?>
'><?php echo $_smarty_tpl->tpl_vars['status']->value['name'];?>
</span>
						<span class='count'></span>
					</a>
				</li>
				<?php } ?>
			</ul>
			<div id="" class="pageTotal" style="text-align: right; float: right; line-height: 25px; padding: 0 5px;"></div>
		</div>
		<div class="opration_area" style="margin-left: 0px; clear: both; height: auto;">
			<div class='opDiv'></div>
		</div>
		<form action="" id='listForm' method='POST'>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
				<tbody id='table-module-list-header'>
					<!-- 通过js复制表头到此处 -->
					<tr class="table-module-title">
						<td width="15px" class="ec-center">
							<input type="checkbox" class="checkAll">
						</td>
						<td width='185px' class="ec-center order_info_td"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
order_info<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<!-- 订单信息 -->
						<td class="ec-center"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
order_detail<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<!-- 订单明细 -->
						<td width='125px' class="ec-center"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
order_amount<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<!-- 订单金额 -->
						<td width='165px' class="ec-center delivery_info_td"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
delivery_information<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<!-- 配送信息 -->
						<td width='130px' class="ec-center date_info_td"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
date<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<!-- 时间 -->
					</tr>
				</tbody>
				<tbody id="table-module-list-data">
					<!-- 
					
					 -->
				</tbody>
			</table>
		</form>
		<div class="pagination"></div>
	</div>
</div><?php }} ?>