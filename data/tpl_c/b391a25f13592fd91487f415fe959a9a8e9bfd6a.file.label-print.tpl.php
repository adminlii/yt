<?php /* Smarty version Smarty-3.1.13, created on 2016-04-26 09:05:23
         compiled from "F:\yt20160425\application\modules\order\views\label-print\label-print.tpl" */ ?>
<?php /*%%SmartyHeaderCode:21193571ebed3449b05-08265715%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b391a25f13592fd91487f415fe959a9a8e9bfd6a' => 
    array (
      0 => 'F:\\yt20160425\\application\\modules\\order\\views\\label-print\\label-print.tpl',
      1 => 1457516431,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21193571ebed3449b05-08265715',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_571ebed34f1aa8_22700793',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_571ebed34f1aa8_22700793')) {function content_571ebed34f1aa8_22700793($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'F:\\yt20160425\\libs\\Smarty\\plugins\\block.t.php';
?><script language="javascript" src="/lodop/LodopFuncs.js?v=20140314"></script>
<script type="text/javascript" src="/lodop/lodop_print.js?v=20140314"></script>
<style>
<!--
#printForm div div{height:30px;}
-->
</style>
<div id='search-module' title='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
打印<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
' class='dialog_div'
	style='line-height: 22px;'>
	<form method="POST" id="printForm" action="/order/report/print-label"
		onsubmit='return false;'>
		<div class="">
			<div style='display: none;'>
				文件类型： <input type="radio" checked="checked" id="image" value="1"
					name="LableFileType"> <label class="fpx-label-input-label"
					for="image">图片</label>
				<!-- 
    			<input type="radio"id="pdf" value="2" name="LableFileType">
    			<label class="fpx-label-input-label" for="pdf">PDF</label>
    			 -->
			</div>
			<div>
				打印类型： <input type="radio" checked="checked" id="label" value="1"
					name="LablePaperType"> <label class="fpx-label-input-label"
					for="label">标签纸</label> <input type="radio" id="a4" value="2"
					name="LablePaperType"> <label class="fpx-label-input-label"
					for="a4">A4纸</label>
			</div>
			<div>
				打印选项： <select name='LableContentType' class='input_text_select input_select'
					style='padding: 0 3px;'>
					<option value='1'>标签</option>
					<option value='2'>报关单</option>
					<option value='3'>配货单</option>
					<option value='4'>标签+报关单</option>
					<option value='5'>标签+配货单</option>
					<option value='6'>标签+报关单+配货单</option>
				</select>
			</div>
			<div>
				打印机： <select name='printerNo' class='input_text_select input_select' id='printer'
					style='padding: 0 3px;'>
				</select>
			</div>
			<div>
				打印内容： <input type="checkbox" id="PrintDeclareInfoSign" value="Y"
					name="PrintDeclareInfoSign"> <label class="fpx-label-input-label"
					for="PrintDeclareInfoSign">在标签上打印配货信息</label> <input
					type="checkbox" id="printBuyerID" value="Y" name="printBuyerID"> <label
					class="fpx-label-input-label" for="printBuyerID">打印买家ID</label>
				<!-- <input type="checkbox" id="InsuranceSign" value="Y" name="InsuranceSign">
    			<label class="fpx-label-input-label" for="InsuranceSign">打印保险标记</label> -->
				<!-- <input type="checkbox" id="HighValueSign" value="Y" name="HighValueSign">
    			<label class="fpx-label-input-label" for="HighValueSign">打印高价值标记</label> -->
				<input type="checkbox" id="PrintTimeSign" value="Y"
					name="PrintTimeSign"> <label class="fpx-label-input-label"
					for="PrintTimeSign">打印时间</label>
				<!-- <input type="checkbox" id="ReturnSign" value="Y" name="ReturnSign">
    			<label class="fpx-label-input-label" for="ReturnSign">打印退货标记</label>  -->
				<input type="checkbox" id="printWeight" value="Y" name="printWeight">
				<label class="fpx-label-input-label" for="printWeight">打印实际重量（报关单）</label>
			</div>
			<div style='color: red;'>如果订单重量未填写,报关单重量默认为0.2KG</div>

			<div id='pring_order_id_wrap'>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;单号：<input
					name='order_code' type='text' class='input_text keyToSearch'
					value='' placeholder="客户单号或服务商单号" id='order_code'/> &nbsp;&nbsp;<input
					type="button" value="打印" class="baseBtn printBtn">
			</div>

		</div>
	</form>
</div>

<object id="LODOP_OB"
	classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0>
	<embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0
		pluginspage="/lodop/install_lodop32.exe"></embed>
</object>
<script>
function setPrinter(){

	/*
	 * 遍历打印机
	 */
	var print_count = LODOP.GET_PRINTER_COUNT();
	var printers = {};
	for (var i = 0; i < print_count; i++) {
		printers[i] = LODOP.GET_PRINTER_NAME(i);
	} 
	var html = "";
	for (var b in printers) {
		html += "<option value='" + b + "'>" + printers[b]+ "</option>";	        
	}
	$('#printer').html(html);
}

function myInitPrinter(){
	var bool = printerSetup();
	
	if(bool){
		setPrinter();
	}
}
$(function(){	
	myInitPrinter();
	$('#order_code').focus();
	$('.printBtn').click(function(){
		var param = $('#printForm').serialize();
		var order_code = $('#order_code').val();
		if($.trim(order_code)==''){
			alertTip("请输入单号");
			return;
		}
		alertTip("处理中，请稍候...");
    	$('.ui-button',$('#dialog-auto-alert-tip').parent()).hide();
    	$('#dialog-auto-alert-tip').dialog('option','closeOnEscape',false);  
		$.ajax({
			type: "POST",
//			async: false,
			dataType: "script",
			url: '/order/label-print/print-label',
			data: param,
			success: function () {
		       	$('.ui-button',$('#dialog-auto-alert-tip').parent()).show();
		    	$('#dialog-auto-alert-tip').dialog('option','closeOnEscape',true);
				$('#dialog-auto-alert-tip').dialog('close');
				lodop_print();
				$('#order_code').focus().select();
			}
		});
	});
	 $("#order_code").keyup(function (e) {
	        var key = e.which;
	        if (key == 13) {
	        	$('.printBtn').click();
	        }
	    }); 
});

</script><?php }} ?>