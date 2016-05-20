<?php /* Smarty version Smarty-3.1.13, created on 2014-07-22 11:10:32
         compiled from "E:\Zend\workspaces\ruston_oms\application\modules\order\views\order\order_list_list_common.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1917353cdd628e7c396-62258140%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fd790803abbf679f1c5ace8552922222eb702740' => 
    array (
      0 => 'E:\\Zend\\workspaces\\ruston_oms\\application\\modules\\order\\views\\order\\order_list_list_common.tpl',
      1 => 1405996339,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1917353cdd628e7c396-62258140',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'jsfile' => 0,
    'tplfile' => 0,
    'statusArr' => 0,
    'k' => 0,
    'status' => 0,
    'process_againArr' => 0,
    'paK' => 0,
    'abnormal' => 0,
    'abK' => 0,
    'tag' => 0,
    'user_tag' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_53cdd629148595_02889627',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53cdd629148595_02889627')) {function content_53cdd629148595_02889627($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'E:\\Zend\\workspaces\\ruston_oms\\libs\\Smarty\\plugins\\block.t.php';
?><script type="text/javascript" src="/js/xheditor/xheditor-1.1.13-en.min.js"></script>
<script type="text/javascript" src="/js/ajaxfileupload.js"></script>
<script src="/js/jquery-ui-timepicker-addon.js" type="text/javascript"></script>
<script src="/js/jquery.overlay.min.js" type="text/javascript"></script>
<script type="text/javascript">
</script>
<script type="text/javascript">
<?php echo $_smarty_tpl->getSubTemplate ('order/js/order/order_list_list_common.js', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ('order/js/order/order_process_tag.js', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ($_smarty_tpl->tpl_vars['jsfile']->value, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</script>
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
	width: 150px;
}
#priceFrom,#priceEnd{width:135px;}
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

.opDiv .baseBtn {
	margin-right: 10px;
}

#tag_form_div {
	display: none;
}

#tag_form {
	position: relative;
}

#biaojifahuo_div {
	display: none;
}

.baseExport {
	float: right;
}

#tag_select ul {
	line-height: 22px;
}

.deleteTag {
	float: right;
}
.Tab ul li {
    padding: 0 0px;
}
.Tab ul li li {
	background: none repeat scroll 0 0 #FFFFFF;
	border: 0 none;
	float: none;
	height: 26px;
	line-height: 26px;
}

.define_item {
	display: none;
}

.dialog_div {
	display: none;
}

.set_it_for_allot {
	display: none;
}

#allot_condition_div p {
	line-height: 22px;
	width: 300px;
}

.del_oac_btn {
	
}

.pagination {
	text-align: right;
	height: auto;
}

#aaaaaaaaaaa td {
	height: 1px;
	line-height: 1px;
	overflow: hidden;
	padding-top: 0;
	padding-bottom: 0px;
}

.orderSetWarehouseShipBtn,.orderSetWarehouseShipAutoBtn ,.biaojifahuo{
	float: right;
}

.order_line span {
	padding-left: 0px;
}

.status_3 {
	
}

.status_6 {
	color: #0090E1;
}

.status_7 {
	color: red;
}

.checked_count {
	padding: 0 5px;
	font-weight: bold;
	font-size: 14px;
	color: red;
}

.opration_area {
	height: auto;
}

.opDiv {
	padding: 0px 0 4px 0;
}

.logIcon,.loadLogIcon {
	background: url(/images/sidebar/bg_mailSch.gif) center center;
	display: block;
	height: 16px;
	vertical-align: middle;
	white-space: nowrap;
	width: 18px;
}

.hideIcon{
	display: none;
}

.order_line .logIcon {
	padding: 0px;
}

.operator_note_span {
	
}

.recordNo,.qty,.sku,.unitPrice,.warehouseSkuWrap {
	width: 20%;
	float: left;
}

.recordNo{width:30%;}

.qty{
	width: 50px;
}

.sku {
	width: 20%;
}
.warehouseSkuWrap {
	width: 20%;
}
.unitPrice{
	width: 50px;
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
#searchForm .input_text_select{width:100px;}

.order_status_tag_container{
	position:absolute;
	z-index:10;
	background:#FFFFFF;
	text-align: center;
    width: 100%;
	left:0;
	display: none;
}
.order_status_tag_container dt{
	border-left: 1px solid #CCCCCC;
	border-right: 1px solid #CCCCCC;
	border-top: 1px solid #CCCCCC;
	border-bottom: 1px solid #CCCCCC;
}
.zidingyibiaoji{}
.completeSale{display:none;}
/* 更多的搜索条件--样式开始 */
.table-module-seach{
	background: none repeat scroll 0 0 #D1E7FC;
    border: 2px solid #D1E7FC;
    table-layout: fixed;
    width: 80%;
}			

.table-module-seach td{
	border-bottom: 1px solid #FFFFFF;
	border-right: 1px solid #FFFFFF;
	line-height: 25px;
}
	
.table-module-seach-title{
	width: 89px;
	text-align: right;
}
.table-module-seach-float{
	-moz-border-bottom-colors: none;
    -moz-border-left-colors: none;
    -moz-border-right-colors: none;
    -moz-border-top-colors: none;
	background-color: #FFFFFF;
    border-color: #D9D9D9;
    border-image: none;
    border-style: none solid solid;
    border-width: medium 1px 1px;
    right:5px;
    width: 35%;
	min-width: 500px;
    position: fixed;
    top: -450px;
    z-index: 105;
	border-top: 1px solid #CCCCCC;
    border-left: 1px solid #CCCCCC;
    border-right: 1px solid #CCCCCC;
    border-bottom: 1px solid #CCCCCC;
}
.table-module-seach-float-content{
	max-height: 300px;
	overflow: auto;
}
#search_more_div .table-module-seach-float{
	padding-top: 0px;
}
#search_more_div .table-module-seach-float div{
	padding-top: 1px;
}
.table-module-seach-float h2 {
    border-bottom: 1px solid #E3E3E3;
    font-size: 12px;
    font-weight: bold;
    height: 30px;
    line-height: 30px;
    padding: 0 10px;
}
.give_up_1{color:red;}
.give_up_0{color:green;}

.Tab .sub_status_container{font-weight:normal;
    background: none repeat scroll 0 0 #FFFFFF;
    display: none;
    left: 0;
    position: absolute;
    text-align: center;
    border: 1px solid #CCCCCC;
    border-bottom: 0 none;
    margin-left:-1px;
    width: 100%;
    z-index: 10;
}
.statusTag{display:block;padding:0 15px;}
.Tab a:hover{color:#000; border-bottom: 2px solid #87B87F;box-shadow: 0 0 1px #87B87F;}
.Tab .chooseTag{font-weight:bold;color:#393939;border-bottom: 2px solid #87B87F}
#module-container .Tab  .sub_status_container dt{	
    border-bottom: 1px solid #CCCCCC;	
}
#module-container .Tab  .sub_status_container a{font-weight: normal;padding:0;}
#module-container .Tab  .sub_status_container a:hover{color:#393939;}

#module-container .Tab  .sub_status_container .selected{font-weight:bold;color:#000;}


/* 更多的搜索条件--样式结束 */
-->
</style>
<link type="text/css" rel="stylesheet" href="/css/public/layout.css" />
<div id='tag_form_div' title='自定义标记' class='dialog_div'>
	<form id='tag_form' onsubmit='return false;'>
		<input type='text' id='tag_input' name='tag_input' value='' placeholder='输入文字或者从下方选择'/>
		<input type='hidden' id='ot_id_order_status' name='order_status' value='' />
		<br />
		<div id='tag_select'></div>
	</form>
</div>
<div id='inventory_div' title='产品库存' class='dialog_div'>
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
		<tbody>
			<tr class="table-module-title">
				<td>产品代码</td>
				<td width='200'>仓库</td>
				<td>在途数量</td>
				<td>待上架数量</td>
				<td>可用数量</td>
				<td>冻结库存</td>
			</tr>
		</tbody>
		<tbody id='inventory_detail'>
		</tbody>
	</table>
</div>
<div id='verify_div' title='订单手工分仓' class='dialog_div'>
	<select id='warehouse' name='warehouse' class='input_text_select'>
	</select>
	<select id='shipping_method_arr' name='shipping_method' class='input_text_select'>
	</select>
</div>
<div id='verify_div_new' title='订单审核' class='dialog_div'>
	<p>订单未分仓，则无法通过审核，请使用右侧的手工分仓或者自动分仓功能对订单进行分仓，</p>
	<p>注意：自动分仓功能需要预先设定好分仓规则，（系统管理→分仓规则设置）</p>
</div>
<div id='verify_div_verify' title='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
order_verify<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
' class='dialog_div'>
	<div>
		<label><input type='radio' name='verify_type' value='0' class='verify_type verify_type0' checked='checked' /><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
order_verify_default<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>
	</div>
	<div>
		<label><input type='radio' name='verify_type' value='1' class='verify_type verify_type1' /><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
order_verify_re_assign_warehouse_shipping<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>
	</div>
	<div class='verify_warehouse_sel' style='display: none;'>
		<select id='warehouse_verify' name='warehouse' class='input_text_select'>
		</select>
		<select id='shipping_method_arr_verify' name='shipping_method' class='input_text_select'>
		</select>
	</div>
</div>
<div id='allot_div' title='保存搜索条件作为自动匹配仓库和运输方式规则' class='dialog_div'>
	<div style='display: none;'>
		匹配仓库：
		<select id='warehouse_allot' class='input_text_select'>
		</select>
		匹配运输方式：
		<select id='shipping_method_allot' class='input_text_select'>
		</select>
	</div>
	<div style='padding: 8px 0 0 0;'>
		保存名称：
		<input type='text' id='save_allot_name' class='input_text' />
	</div>
</div>
<div id='export_wrap_div' title='订单导出' class='dialog_div'>
	请选择模板：
	<select id='export_template' name='export_template'>
	</select>
	<a href='' target='_blank'>下载模板</a>
	<table width="100%" cellspacing="1" cellpadding="0" border="0" style="line-height: 150%">
		<tbody>
			<tr>
				<td height="40" colspan="2">
					<table border="0">
						<tbody>
							<tr>
								<td>
									<font color="#808080"><b><img border="0" src="/images/base/bg_tips01.png"></b></font>
								</td>
								<td>
									<font color="#808080"><b>小提示</b></font>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td align="left">
					<table border="0">
						<tbody>
							<tr>
								<td>
									<ul class="list_style_1">
										<li><font color="#FF3399">该导出功能，只可导出第三方仓库订单，自营仓库订单，无法导出，系统会自动忽略系类订单</font></li>
										<li>如何将导出的报表，直接映射到第三方仓库的运输方式？</li>
										<li>
											<font color="#FF3399">配置第三方运输代码！登陆仓配管理系统-》物流管理-》运输方式管理-》搜索，选择对应的渠道-》编辑-》填写第三方运输方式代码-》保存，导出的文件当中将是您配置的第三方运输代码!</font>
										</li>
									</ul>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div id='biaojifahuo_div' title='标记发货' class='dialog_div'>
	<form enctype="multipart/form-data" method="POST" action="">
		<div style='display: none;'>
			选择模板：
			<select id='import_template' name='import_template'>
			</select>
			<br />
		</div>
		文件:
		<input type='file' id='fileToUpload' name='fileToUpload' value='' class='input_text' style='width: 300px;' />
		<a href="/file/input-4px.xls" target="_blank" style='color: red;'>下载示例模板</a>
		<input type='button' id='fileUploadBtn' onclick='return ajaxFileUpload();' value='Upload' style='display: none;'>
	</form>
</div>
<div id='message_feedback_div' title='消息回复' class='dialog_div'>
	<form enctype="multipart/form-data" method="POST" action="" onsubmit='return false;' id='feedBackForm' style=''>
		<table cellspacing="0" cellpadding="0" border="0" style='width: 770px; border-collapse: collapse;' id='feedback_table'>
			<tr style='display: none;'>
				<td width='80'>Message Ids</td>
				<td>
					<input type='text' class='input_text' id='orderIds' name='orderIds' style='width: 450px;' />
				</td>
			</tr>
			<tr style='height: 35px;'>
				<td>主题</td>
				<td>
					<input type='text' class='input_text' id='messageSubject' name='subject' value='' style='width: 650px;' />
				</td>
			</tr>
			<tr style='height: 35px; display: none;'>
				<td>物品名称</td>
				<td>
					<select id='title' name='title' style='width: 500px;'>
					</select>
				</td>
			</tr>
			<tr style='height: 35px; display: none;'>
				<td>物品编号</td>
				<td>
					<input type='text' class='input_text' id='item' name='item' value='' style='width: 450px; background: #ddd;' readonly='readonly' />
				</td>
			</tr>
			<tr>
				<td width='80'>选择模板</td>
				<td>
					<select id='select_module' name='language'>
					</select>
				</td>
			</tr>
		</table>
		<div id='editor_wrap'></div>
	</form>
</div>
<div id='cancel_order_div' title='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
why_cancel_order<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
' class='dialog_div'>
	<form action="" id='cancel_order_form' onsubmit='return false;'>
		<p>
			<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
why_cancel_order<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：
			<select id='reason_type' name='reason_type'>
				<option value=''><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
-select-<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
				<option value='1'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
abnormal_change_address<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
				<option value='2'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
abnormal_cancel_order<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
				<option value='3'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
abnormal_change_sku<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
				<option value='4'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
abnormal_other<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
			</select>
		</p>
		<textarea id='reason' style='width: 350px; height: 60px;' name='reason'></textarea>
		<input name='refIds' value='' type='hidden' id='cancel_order_ref_ids' />		
		<input name='status' value='2' type='hidden' id='cancel_order_status' />
	</form>
</div>
<div id='operator_note_wrap_div' title='客服留言' class='dialog_div'>
	<form action="" id='operator_note_wrap_form' onsubmit='return false;'>
		<textarea id='note_content' style='width: 350px; height: 60px;' name='note_content'></textarea>
		<input name='ref_id' value='' type='hidden' id='operator_note_ref_id' />
	</form>
</div>
<div id='allot_condition_div' class='dialog_div' title='已保存搜索条件'></div>
<div id="module-container" style=''>
    <?php echo $_smarty_tpl->getSubTemplate ($_smarty_tpl->tpl_vars['tplfile']->value, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<div id="fix_header_content" style="z-index:9;">
		<div class='clone' >
			<div class="Tab">
				<ul>
				    <li class="">
						<a href="javascript:void (0)" class='order_title_container statusTag statusTag-1'  status=''  data-id='' is_original='0'  abnormal_type='' process_again=''>
							<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
all_orders<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

							<span class='count' style='display: none;'></span>
						</a>
					</li>
					<?php  $_smarty_tpl->tpl_vars['status'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['status']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['statusArr']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['status']->key => $_smarty_tpl->tpl_vars['status']->value){
$_smarty_tpl->tpl_vars['status']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['status']->key;
?>
					<li style='position: relative;' class='mainStatus'>
						<!-- 订单状态存在处理等级，is_original为1，需要控制处理状态，否则为0（同时data-id也不做控制，设置为空）-->
						<a href="javascript:void (0)" class="statusTag statusTag<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
"  status='<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
' abnormal_type='' process_again=''>
							<span class='order_title<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
' title='<?php echo $_smarty_tpl->tpl_vars['status']->value['name'];?>
'><?php echo $_smarty_tpl->tpl_vars['status']->value['name'];?>
</span>
							<span class='count'></span>
						</a>
						<dl class='sub_status_container'>	
						<?php if (isset($_smarty_tpl->tpl_vars['status']->value['process_again'])||isset($_smarty_tpl->tpl_vars['status']->value['abnormal_type'])||isset($_smarty_tpl->tpl_vars['status']->value['insufficient'])||isset($_smarty_tpl->tpl_vars['status']->value['user_tag'])){?>
						    <!-- 
						    <?php if (isset($_smarty_tpl->tpl_vars['status']->value['process_again'])){?>							
								<?php  $_smarty_tpl->tpl_vars['process_againArr'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['process_againArr']->_loop = false;
 $_smarty_tpl->tpl_vars['paK'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['status']->value['process_again']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['process_againArr']->key => $_smarty_tpl->tpl_vars['process_againArr']->value){
$_smarty_tpl->tpl_vars['process_againArr']->_loop = true;
 $_smarty_tpl->tpl_vars['paK']->value = $_smarty_tpl->tpl_vars['process_againArr']->key;
?>
						        <dt class='order_status_tag_parent<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
'>
						            <a style="display:block;" href="javascript:void (0)" title='<?php echo $_smarty_tpl->tpl_vars['process_againArr']->value;?>
' class='process_again' sub_status='<?php echo $_smarty_tpl->tpl_vars['paK']->value;?>
' status='<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
'><?php echo $_smarty_tpl->tpl_vars['process_againArr']->value;?>
</a>
						        </dt>
								<?php } ?>
					        <?php }?>
					       
					        <?php if (isset($_smarty_tpl->tpl_vars['status']->value['abnormal_type'])){?>							
								<?php  $_smarty_tpl->tpl_vars['abnormal'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['abnormal']->_loop = false;
 $_smarty_tpl->tpl_vars['abK'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['status']->value['abnormal_type']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['abnormal']->key => $_smarty_tpl->tpl_vars['abnormal']->value){
$_smarty_tpl->tpl_vars['abnormal']->_loop = true;
 $_smarty_tpl->tpl_vars['abK']->value = $_smarty_tpl->tpl_vars['abnormal']->key;
?>
						        <dt>
						            <a style="display:block;" href="javascript:void (0)" title='<?php echo $_smarty_tpl->tpl_vars['abnormal']->value;?>
' class='abnormal_type' sub_status ='<?php echo $_smarty_tpl->tpl_vars['abK']->value;?>
'  status='<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
'><?php echo $_smarty_tpl->tpl_vars['abnormal']->value;?>
</a>
						        </dt>
								<?php } ?>
					        <?php }?>
						     -->
					        <?php if (isset($_smarty_tpl->tpl_vars['status']->value['insufficient'])){?>							
								<?php  $_smarty_tpl->tpl_vars['abnormal'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['abnormal']->_loop = false;
 $_smarty_tpl->tpl_vars['abK'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['status']->value['insufficient']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['abnormal']->key => $_smarty_tpl->tpl_vars['abnormal']->value){
$_smarty_tpl->tpl_vars['abnormal']->_loop = true;
 $_smarty_tpl->tpl_vars['abK']->value = $_smarty_tpl->tpl_vars['abnormal']->key;
?>
						        <dt>
						            <a style="display:block;" href="javascript:void (0)" title='<?php echo $_smarty_tpl->tpl_vars['abnormal']->value;?>
' class='sub_status' sub_status ='<?php echo $_smarty_tpl->tpl_vars['abK']->value;?>
' status='<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
'><?php echo $_smarty_tpl->tpl_vars['abnormal']->value;?>
</a>
						        </dt>
								<?php } ?>
					        <?php }?>
					        <?php if (isset($_smarty_tpl->tpl_vars['status']->value['user_tag'])){?>							
								<?php  $_smarty_tpl->tpl_vars['tag'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['tag']->_loop = false;
 $_smarty_tpl->tpl_vars['tk'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['status']->value['user_tag']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['tag']->key => $_smarty_tpl->tpl_vars['tag']->value){
$_smarty_tpl->tpl_vars['tag']->_loop = true;
 $_smarty_tpl->tpl_vars['tk']->value = $_smarty_tpl->tpl_vars['tag']->key;
?>
						        <dt>
						            <a style="display:block;" href="javascript:void (0)" title='<?php echo $_smarty_tpl->tpl_vars['tag']->value['text'];?>
' class='ot_id' ot_id ='<?php echo $_smarty_tpl->tpl_vars['tag']->value['ot_id'];?>
' abnormal_type ='' process_again='' status='<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
'><?php echo $_smarty_tpl->tpl_vars['tag']->value['text'];?>
</a>
						        </dt>
								<?php } ?>
					        <?php }?>
					       
					    <?php }?>
					    </dl>
					</li>
					<?php } ?>
					
					<li style='padding: 0px; border: 0 none; position: relative; display: none;' class='userDefinedSelect'>
						<ul style='border: 1px solid #ccc;'>
							<li style='float: none; height: 25px;' id='define_item'>*自定义标记</li>
							<?php  $_smarty_tpl->tpl_vars['tag'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['tag']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['user_tag']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['tag']->key => $_smarty_tpl->tpl_vars['tag']->value){
$_smarty_tpl->tpl_vars['tag']->_loop = true;
?>
							<li status='<?php echo $_smarty_tpl->tpl_vars['tag']->value['k'];?>
' style='float: none;' class='define_item statusTag' id='define_item<?php echo $_smarty_tpl->tpl_vars['tag']->value['ot_id'];?>
'>
								<a href="javascript:void (0)">*<?php echo $_smarty_tpl->tpl_vars['tag']->value['text'];?>
</a>
							</li>
							<?php } ?>
						</ul>
					</li>
					
				</ul>
				<input type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
order_export_base_btn<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="baseExport baseBtn" style="margin-right: 12px;">
				<div id="pageTotal" style="text-align: right; float: right; line-height: 25px; padding: 0 5px;"></div>
				<!--
               <a href="javascript:void (0)" style='line-height:30px;padding:0 10px;font-size:25px;' title='添加其它状态'>+</a>
                -->
			</div>
			<div class="opration_area0">
				<!--<div class="opration_area_lft"><input type="button" value="审核"  class="baseBtn" /></div>-->
				<div class="opration_area" style="margin-left: 10px">
					<div class='opDiv'></div>
				</div>
			</div>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
				<tbody>
					<tr class="table-module-title">
						<td width="15px" class="ec-center">
							<input type="checkbox" class="checkAll">
						</td>
						<td width='190px' class="ec-center"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
order_info<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td class="ec-center"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
order_detail<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width='185px' class="ec-center"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
order_ship_info<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width='170px' class="ec-center"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
order_time_info<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width='80px' class="ec-center"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
operation<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div id='fix_header' style='position: fixed; top: 0px; display: none; background: #fff;z-index:9;width: 99.2%'></div>
	<div id="module-table" style='overflow: auto;'>
		<form action="" id='listForm' method='POST'>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
				<tr class="table-module-title" id='aaaaaaaaaaa' style=''>
					<td width="15px" class="ec-center">&nbsp;</td>
					<td width='190px' class="ec-center">&nbsp;</td>
					<td class="ec-center">&nbsp;</td>
					<td width='185px' class="ec-center">&nbsp;</td>
					<td width='170px' class="ec-center">&nbsp;</td>
					<td width='80px' class="ec-center">&nbsp;</td>
				</tr>
				<tbody id="table-module-list-data">
					<!-- 
					<tr valign="top" class="table-module-b2">
						<td valign="top" align="center">
							<input type="checkbox" class="checkItem">
						</td>
						<td valign="top">
							<p>
								订单：
								<a href="javascript:;">202-1237838-9717907</a>
							</p>
							<p>买家：Jacky</p>
							<p>卖家：UserAccount001</p>
							<p>
								<span style="float: left;">状态：</span>
								<span title="付款处理中" class="iconpedding00 iconmoney2" style="float: left; cursor: pointer;" id="eBayPaymentStatus_394225"></span>
								<span title="已标记发货并同步到EBAY" class="iconshipebay" style="float: left; cursor: pointer;"></span>
							</p>
						</td>
						<td>
							<div style="">
								<a href='javascript:;' style='line-height: 25px;'>12V 5M 300 Leds Waterproof 5050Led Strip String Xmas Party Light Cool White IP65</a>
								<div style="width: 100px; float: right;">
									<ul class="dropdown_nav">
										<li>
											<a href="javascript:;">
												RMA管理&nbsp;<strong style=""></strong>
											</a>
										</li>
									</ul>
								</div>
							</div>
							<div style="width: 100%;">
								<div>
									<div style='padding-left: 0px; float: left; width: 200px;'>
										recordNo：<b>03591109184632</b>
									</div>
									<div style='padding-left: 0px; float: left; width: 200px;'>
										SKU：<b>L13 [<a href='javascript:;' title='点击查看库存'>ABC</a>*2+<a href='javascript:;' title='点击查看库存'>DEF</a>*3]
										</b>
									</div>
									<div style='padding-left: 0px; float: left; width: 70px;'>
										QTY：<b>2</b>
									</div>
									<div style='padding-left: 0px; float: left; width: 200px;'>
										单价：<b>6.99 EUR</b>
									</div>
									<div class='clear' style='line-height: 0px; height: 0px; clear: both;'></div>
								</div>
							</div>
							<div style="">
								<a href='javascript:;' style='line-height: 25px;'>12V 5M 300 Leds Waterproof 5050Led Strip String Xmas Party Light Cool White IP65</a>
								<div style="width: 100px; float: right;">
									<ul class="dropdown_nav">
										<li>
											<a href="javascript:;">
												RMA管理&nbsp;<strong style=""></strong>
											</a>
										</li>
									</ul>
								</div>
							</div>
							<div style="width: 100%;">
								<div>
									<div style='padding-left: 0px; float: left; width: 200px;'>
										recordNo：<b>03591109184632</b>
									</div>
									<div style='padding-left: 0px; float: left; width: 200px;'>
										SKU：<b>L13 [<a href='javascript:;' title='点击查看库存'>ABC</a>*2+<a href='javascript:;' title='点击查看库存'>DEF</a>*3]
										</b>
									</div>
									<div style='padding-left: 0px; float: left; width: 70px;'>
										QTY：<b>2</b>
									</div>
									<div style='padding-left: 0px; float: left; width: 200px;'>
										单价：<b>6.99 EUR</b>
									</div>
									<div class='clear' style='line-height: 0px; height: 0px; clear: both;'></div>
								</div>
							</div>
							<div style="">
								<a href='javascript:;' style='line-height: 25px;'>12V 5M 300 Leds Waterproof 5050Led Strip String Xmas Party Light Cool White IP65</a>
								<div style="width: 100px; float: right;">
									<ul class="dropdown_nav">
										<li>
											<a href="javascript:;">
												RMA管理&nbsp;<strong style=""></strong>
											</a>
										</li>
									</ul>
								</div>
							</div>
							<div style="width: 100%;">
								<div>
									<div style='padding-left: 0px; float: left; width: 200px;'>
										recordNo：<b>03591109184632</b>
									</div>
									<div style='padding-left: 0px; float: left; width: 200px;'>
										SKU：<b>L13 [<a href='javascript:;' title='点击查看库存'>ABC</a>*2+<a href='javascript:;' title='点击查看库存'>DEF</a>*3]
										</b>
									</div>
									<div style='padding-left: 0px; float: left; width: 70px;'>
										QTY：<b>2</b>
									</div>
									<div style='padding-left: 0px; float: left; width: 200px;'>
										单价：<b>6.99 EUR</b>
									</div>
									<div class='clear' style='line-height: 0px; height: 0px; clear: both;'></div>
								</div>
							</div>
						</td>
						<td valign="top" align="center">15.20 EUR</td>
						<td>
							<p>目的国家：DE</p>
							<p>平台方式：Royal Mail</p>
							<p>仓库方式：RM1</p>
							<p style="cursor: pointer;" title="单击复制跟踪号">跟踪号：003404338364...</p>
						</td>
						<td>
							<p>创建：2013-11-18 23:14:42</p>
							<p>付款：2013-11-18 23:14:42</p>
							<p>审核：2013-11-20 17:22:54</p>
							<p>发货：2013-11-20 17:22:58</p>
						</td>
					</tr>
					 -->
				</tbody>
			</table>
		</form>
		<div class="pagination"></div>
	</div>
	<div id='loading'></div>
	<div id='t' style='position: fixed; top: 0px;'></div>
</div>
<?php }} ?>