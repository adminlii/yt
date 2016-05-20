<?php /* Smarty version Smarty-3.1.13, created on 2016-05-10 13:37:09
         compiled from "D:\yt1\application\modules\order\views\order\order_import_batch.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3152857317385e50614-60913748%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '561ea7308fc4bad67e07ffaeda7d999e21eaca43' => 
    array (
      0 => 'D:\\yt1\\application\\modules\\order\\views\\order\\order_import_batch.tpl',
      1 => 1457516431,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3152857317385e50614-60913748',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'batch' => 0,
    'k' => 0,
    'o' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_57317385f13b47_25417755',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57317385f13b47_25417755')) {function content_57317385f13b47_25417755($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'D:\\yt1\\libs\\Smarty\\plugins\\block.t.php';
?><link type="text/css" rel="stylesheet" href="/css/public/ajaxfileupload.css" />
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
<script type="text/javascript">
<!--
function detail(id) {
	$('#detail-table-module-list-data').html("");
	$.ajax({
		  type : "POST",
		  url : "/order/order/get-import-batch-detail",
		  data : {'id':id},
		  dataType : 'json',
		  success : function(json) {
			  if(!isJson(json)) {
				  alertTip("Internal error");
			  }
			  if(json.data  && json.data.length > 0) {
				  var html = '';
				  $.each(json.data,function(k,v){
			          html += (k + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
					  html+='<td class="ec-center">'+v.line_row+'</td>';
					  html+='<td>'+v.shipper_hawbcode+'</td>';
					  html+='<td>'+v.note+'</td>';
					  html+='</tr>';
				  });
			  } else {
				  html+="<tr><td colspan='3'>无明细数据</td></tr>";
			  }
			  
			  $('#detail-table-module-list-data').html(html);
		  }
	});
}

function getBatch() {
	$('#table-module-list-data').html("");
	$('#detail-table-module-list-data').html("");
	
	$.ajax({
		  type : "POST",
		  url : "/order/order/get-import-batch",
		  dataType : 'json',
		  success : function(json) {
			  if(!isJson(json)) {
				  alertTip("Internal error");
			  }
			  if(json.data){
				  var html = '';
				  $.each(json.data,function(k,v){
			          html += (k + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
					  html+='<td class="ec-center">'+(k+1)+'</td>';
					  html+='<td class="ec-center">'+v.filename+'</td>';
					  html+='<td class="ec-center">'+v.success_count+'</td>';
					  html+='<td class="ec-center">'+v.fail_count+'</td>';
					  html+='<td class="ec-center">'+v.createdate+'</td>';
					  html+='<td class="ec-center">'+v.modifydate+'</td>';
					  html+='<td class="ec-center"><a href="javascript:void(0)" onclick="detail(' + v.ccib_id + ')"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
detail<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></td>';
					  html+='</tr>';
				  });
			  } else {
				  html+="<tr><td colspan='3'>无批准数据</td></tr>";
			  }
			  
			  $('#table-module-list-data').html(html);
		  }
	});
}
//-->
</script>
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
			<li id="abnormal" style="position: relative;" class="mainStatus0">
				<a href="javascript:;" class="statusTag " onclick="leftMenu('order_import','<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
批量上传<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
','/order/order/import?quick=24')">
					<span class="order_title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
批量上传<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
				</a>
			</li>
			<li id="abnormal" style="position: relative;" class="mainStatus0 chooseTag">
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
	
	<h2 style='margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC;'>
		1、<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
近10次上传记录<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

		<a href='javascript:;' style='font-weight: normal; font-size: 12px; padding: 0 10px;' onclick='getBatch();'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
刷新<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
	</h2>
	<div id="module-table" style='padding: 10px 20px;'>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
				<tbody id='table-module-title'>
					<tr class="table-module-b1">
    					<td class="ec-center">序号</td>
    					<td class="ec-center"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
文件名<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
    					<td class="ec-center"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
成功数<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
    					<td class="ec-center"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
失败数<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
    					<td class="ec-center"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
创建时间<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
    					<td class="ec-center"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
完成时间<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>    					
    					<td class="ec-center"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
operation<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
    				</tr>
				</tbody>
				<tbody id="table-module-list-data">
					<?php  $_smarty_tpl->tpl_vars['o'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['o']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['batch']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['o']->key => $_smarty_tpl->tpl_vars['o']->value){
$_smarty_tpl->tpl_vars['o']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['o']->key;
?>
						<tr class="<?php if ($_smarty_tpl->tpl_vars['k']->value%2==1){?>table-module-b2<?php }else{ ?>table-module-b1<?php }?>">
	    					<td class="ec-center"><?php echo $_smarty_tpl->tpl_vars['k']->value+1;?>
</td>
	    					<td class="ec-center"><?php echo $_smarty_tpl->tpl_vars['o']->value['filename'];?>
</td>
	    					<td class="ec-center"><?php echo $_smarty_tpl->tpl_vars['o']->value['success_count'];?>
</td>
	    					<td class="ec-center"><?php echo $_smarty_tpl->tpl_vars['o']->value['fail_count'];?>
</td>
	    					<td class="ec-center"><?php echo $_smarty_tpl->tpl_vars['o']->value['createdate'];?>
</td>
	    					<td class="ec-center"><?php echo $_smarty_tpl->tpl_vars['o']->value['modifydate'];?>
</td>
	    					<td class="ec-center"><a href="javascript:void(0)" onclick="detail(<?php echo $_smarty_tpl->tpl_vars['o']->value['ccib_id'];?>
)"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
detail<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></td>
	    				</tr>
    				<?php } ?>
				</tbody>
			</table>
	</div>
	
	<h2 style='margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC;'>
		2、<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
明细<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

	</h2>
	<div id="module-table" style='padding: 10px 20px;'>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
			<tbody id='table-module-title'>
				<tr class="table-module-b1">
    				<td class="ec-center" width="5%">行号</td>
    				<td class="ec-center" width="20%"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
单号<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
    				<td class="ec-center" width="75%"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
失败原因<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
    			</tr>
			</tbody>
			<tbody id="detail-table-module-list-data">
			</tbody>
		</table>
	</div>
</div><?php }} ?>