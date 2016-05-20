<?php /* Smarty version Smarty-3.1.13, created on 2016-04-29 20:05:38
         compiled from "F:\yt20160425\application\modules\order\js\order\order_list_list_b2c.js" */ ?>
<?php /*%%SmartyHeaderCode:1025571dc7f2772c19-79440848%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '08ed85e2552ee7636963067e9e367de3bfa343cd' => 
    array (
      0 => 'F:\\yt20160425\\application\\modules\\order\\js\\order\\order_list_list_b2c.js',
      1 => 1461931516,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1025571dc7f2772c19-79440848',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_571dc7f27d0827_96279264',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_571dc7f27d0827_96279264')) {function content_571dc7f27d0827_96279264($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'F:\\yt20160425\\libs\\Smarty\\plugins\\block.t.php';
?>EZ.getListData = function(json) {
	//设置订单条数
	setPageTotal(json.total);

	var html = '';

	$(".checkAll").attr('checked', false);
	var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;

	//买家ID
	$.each(json.data, function(key, val) {	
		var orderId = val.order_id;
		var clazz = key%2==1?'table-module-b2':'table-module-b1';
		if(val.orderWrongMsg!='') {
			html += "<tr title='"+ val.orderWrongMsg +"' class='" + clazz + "' id='order_wrap_" + val.order_id + "'>";
		}else{
			html += "<tr class='" + clazz + "' id='order_wrap_" + val.order_id + "'>";
		}
		html += '<td class="ec-center"><input type="checkbox" class="checkItem" name="orderId[]" ref_id="'+val.shipper_hawbcode+'" value="' + val.order_id + '"/></td>';

		html += '<td class="ec-center">';
		html += '<a url="/order/order/detail?order_id='+val.order_id+'" ref_id="'+val.order_id+'" class="orderDetail" href="javascript:;">'+val.shipper_hawbcode+'</a>';
		html += '</td>';
		html += '<td class="ec-center">'+(val.refer_hawbcode ? val.refer_hawbcode : '')+'</td>';
		html += '<td class="ec-center">'+val.product_code+'</td>';
		html += '<td class="ec-center">'+val.country_name+'</td>';
		html += '<td class="ec-center">'+val.consignee_name+'</td>';
		html += '<td class="ec-center">'+val.server_hawbcode+'</td>';
		html += '<td class="ec-center">'+val.create_date+'</td>';
		html += '<td class="ec-center">'+val.checkin_date+'</td>';
		html += '<td class="">';
		if(val.print_sign=='Y'){
			html += '<img src="/images/ico/preferences-desktop-printer.ico" class="ico"  title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
已打印<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>';
		}else{
			
		}
		if(val.InsuranceSign=='Y'){
			html += '<img src="/images/ico/services.ico" class="ico" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
已投保<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>';
		}else{
			
		}
		if(val.CustomerSign=='Y'){
			html += '<img src="/images/ico/task-due.ico" class="ico" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
需报关<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>';
		}else{
			
		}
		if(val.hold_sign=='Y'){
			html += '<img src="/images/ico/object-locked-2.ico" class="ico" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
拦截成功<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>';
		}else if(val.hold_sign=='N'){
			html += '<img src="/images/ico/object-unlocked-2.ico" class="ico" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
解除拦截<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>';
			
		}
		if(val.oda_sign=='Y'){
			html += '<img src="/images/ico/dialog-important-2.ico" class="ico" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
地址偏远<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>';
		}else{
			
		}
		if(val.order_create_code=='W'){
			html += '<img src="/images/ico/www.ico" class="ico" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
网站创建<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>';
		}

		if(val.order_create_code=='A'){
			html += '<img src="/images/ico/font-x-generic.ico" class="ico" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
API创建<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>';
		}

		if(val.order_create_code=='P'){
			html += '<img src="/images/ico/application-x-smb-workgroup.ico" class="ico" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
平台获取<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>';
		}

		if(val.return_sign=='Y'){
			html += '<img src="/images/ico/returnsign.gif" class="ico" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
海外退件需退回<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>';
		}
		html += '</td>';
		html += '<td class="ec-center">'+val.order_status_title+'</td>';


		/**操作 开始**/
		html += '<td  valign="top">';
		html += '<a href="javascript:;" class="cpyBtn" ref_id="'+val.order_id+'"  url="/order/order/create?order_id='+val.order_id+'&cpy=1" ><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
cpy<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>';
		if(val.order_status=='D'||val.order_status=='Q'){
			html += '&nbsp;&nbsp;<a href="javascript:;" class="orderEdit" ref_id="'+val.order_id+'" url="/order/order/create?order_id='+val.order_id+'" ><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
edit<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>';        	
		}

		html += '</td>';
		/**操作 结束**/
		html += "</tr>";

	});

	return html;
}
$(function(){
		$('#print_dialog').dialog({
			autoOpen: false,
			width: 800,
			maxHeight: 400,
			modal: true,
			show: "slide",
			buttons: [
	//{
	//text: '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
货物标签<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
',
	//click: function () {
	//$(this).dialog("close");
	//var type = 'label';
	//$('#listForm').attr('action','/order/order/print?type='+type).attr('target','_blank').submit();
	//}
	//},
	{
		text: '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
确定<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
',
		click: function () {
			$('#pring_order_id_wrap').html('');	
			$('.checkItem:checked').each(function(){
				var order_id =$(this).val();
				$('#pring_order_id_wrap').append('<input type="hidden" name="order_id[]" value="'+order_id+'"/>');
			})
			var params = $('#printForm').serialize();
			$('#printForm').attr('onsubmit','return true;').submit();
			//$(this).dialog("close");
		}
	},
	{
		text: 'Close(<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
关闭<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
)',
		click: function () {
			$(this).dialog("close");
		}
	}
	],
	close: function () {
		//$(this).remove();
		$('#printForm').attr('onsubmit','return false;')
	}
		});

	$('#editinvoice_dialog').dialog({
		autoOpen: false,
		width: 1200,
		maxHeight: 600,
		modal: true,
		show: "slide",
		buttons: [
		          {
		        	  text: '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
确定<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
',
		        	  click: function () {
		        		  $.ajax({
		        				type : "POST",
		        				url : "/order/order/edit-invoice",
		        				data : $('#invoiceForm').serialize(),
		        				dataType : 'json',
		        				success : function(json) {
		        					if (!isJson(json)) {
		        		                alertTip('error 500');
		        		            }
		        					var html = '';
		        					if(json.ask){
		        						html+='<p>操作成功</p>';
		        						$("#editinvoice_dialog").dialog("close");
		        					}

		        					if(json.err){
		        						html+="<ul style='list-style-type:decimal;padding:5px 0 5px 30px;'>";
		        						$.each(json.err,function(k,v){
		        							html+="<li>"+v+"</li>";
		        						})
		        						html+="</ul>";
		        					}
		        					alertTip(html,600,500);
		        				}
		        		  });
		        	  }
		          },
		          {
		        	  text: 'Close(<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
关闭<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
)',
		        	  click: function () {
		        		  $(this).dialog("close");
		        	  }
		          }
		          ],
	      close: function () {
	    	  $('#invoiceForm').attr('onsubmit','return false;')
	      }
	});
	
}
)
function printDialog() {
	$('#print_dialog').dialog('open');
}
var op = '';
var opTitle = '';

function opProcess(){

	alertTip('Loading...',300); 
	var refIds = [];
	$("#listForm .checkItem:checked").each(function(){
		refIds.push($(this).val());
	});
	var param = {'order_id':refIds,'op':op};				

	$.ajax({
		type : "POST",
		url : "/order/order/verify",
		data : param,
		dataType : 'json',
		success : function(json) {
			$('#dialog-auto-alert-tip').dialog('close');						 
			var html = json.message;
			if(json.ask){
				if(json.rs){	
					html+='<p>已处理订单</p>';
					html+="<ul style='list-style-type:decimal;padding:5px 0 5px 30px;'>";
					$.each(json.rs,function(k,v){
						html+="<li>"+v.shipper_hawbcode+"</li>";
					})
					html+="</ul>";
				}
			}

			if(json.err){
				html+="<ul style='list-style-type:decimal;padding:5px 0 5px 30px;'>";
				$.each(json.err,function(k,v){
					html+="<li>"+v+"</li>";
				})
				html+="</ul>";
			}

			if(json.rs == ""){
				html+="<ul style='list-style-type:decimal;padding:5px 0 5px 30px;'>";
                var noprint = "没有订单可打印，请检查订单是否已获取跟踪号";
				html+="<li>"+ noprint +"</li>";
				html+="</ul>";
				json.ask = 0;
			}

			if(json.ask){
				switch(op){
					case 'export':
						jConfirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
are_you_sure<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', opTitle+'<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
订单<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', function(r) {
							if(r){
								$('#listForm').attr('action','/order/order/export/').attr('target','_blank').submit();
							}
						});					

						break;
					case 'print':
						printDialog();

						break;
					case 'printAsn':
						jConfirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
are_you_sure<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', opTitle+'<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
订单<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', function(r) {
							if(r){
								$('#listForm').attr('action','/order/asn-print/index/').attr('target','_blank').submit();
							}
						});	
						break;
					case 'printInvoice':
						jConfirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
are_you_sure<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', opTitle+'<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
订单<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', function(r) {
							if(r){
								$('#listForm').attr('action','/order/invoice-print/invoice-label/').attr('target','_blank').submit();
							}
						});	
						break;
					default:
						initData(paginationCurrentPage - 1);
					tongji();
					alertTip(html,600,500);
				}
			}else{
				alertTip(html,600,500);
			}

		}
	});	
}

$(function(){
	$('.opBtn').live('click',function(){
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pls_select_orders<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
			return false;
		}
		var this_ = $(this);
		op = $(this).attr('op');
		opTitle = $(this).val();
		jConfirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
are_you_sure<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', opTitle+'<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
订单<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', function(r) {
			if(r){
				opProcess();
			}
		});
	})


	$('.printBtn').live('click',function(){
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pls_select_orders<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
			return false;
		}
		var this_ = $(this);
		op = $(this).attr('op');
		opTitle = $(this).val();

		opProcess();
	})

	$('.printAsnBtn').live('click',function(){
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pls_select_orders<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
			return false;
		}
		var this_ = $(this);
		op = $(this).attr('op');
		opTitle = $(this).val();

		opProcess();
	})
	
	$('.printInvoiceBtn').live('click',function(){
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pls_select_orders<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
			return false;
		}
		var this_ = $(this);
		op = $(this).attr('op');
		opTitle = $(this).val();

		opProcess();
	})
	$('.exportBtn').live('click',function(){
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pls_select_orders<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
			return false;
		}
		var this_ = $(this);
		op = $(this).attr('op');
		opTitle = $(this).val();

		opProcess();
	})
	
	// 
	$(".editInvoiceBtn").live('click', function() {
		
		$(".invoice-table-module-list-data").html("");
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pls_select_orders<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
			return false;
		}
		
		var uri = '/order/order/get-invoice';
		var refIds = [];
		$("#listForm .checkItem:checked").each(function(){
			refIds.push($(this).val());
		});
		var param = {'order_id':refIds};	
		
		$.post(uri, 
				{
					order_id: refIds
		        }, 
		        function (json) {
		            if (!isJson(json)) {
		                alertTip('error 500');
		            }
		            
		            var html = '';
		            $.each(json.data, function(k, v){
		            	html += '<tr class="table-module-b1">';
		            	html += "<td>" + v.shipper_hawbcode + "111</td>";
		            	html += "<td>";
		            	html += "<input type='text' class='input_text invoice_enname' name='invoice[" + v.order_id + "][invoice_enname][]' value='" + v.invoice_enname +  "'>";
		            	html += "</td>";
		            	html += "<td>";
		            	html += "<input type='text' class='input_text invoice_cnname' name='invoice[" + v.order_id + "][invoice_cnname][]' value='" + (v.invoice_cnname ? v.invoice_cnname : '') +  "'>";
						html += "</td>";
						html += "<td>";
						html += "<input type='text' style='width:50px;' class='input_text invoice_quantity' name='invoice[" + v.order_id + "][invoice_quantity][]'  value='" + v.invoice_quantity +  "'>";
						html += "</td>";
						html += "<td>";
						html += "<select name='invoice[" + v.order_id + "][unit_code][]' class='input_select unit_code'>";
						$.each(json.units, function(k1, v1){
							html += "<option value='" +v1.unit_code + "'"
							if(v.unit_code == v1.unit_code) { 
								html += " selected ";
							}
							html += ">" + v1.unit_enname + "(" + v1.unit_cnname + ")</option>";
						});
						html += "</select>";
						html += "</td>";
						html += "<td>";
						html += "<input type='text' style='width:50px;' class='input_text invoice_unitcharge' name='invoice[" + v.order_id + "][invoice_unitcharge][]'  value='" + v.invoice_unitcharge +  "'>";
						html += "</td>";
						html += "<td>";
						html += "<input type='text' style='width:80px;' class='input_text hs_code' name='invoice[" + v.order_id + "][hs_code][]'  value='" + v.hs_code +  "'>";
						html += "</td>";
						html += "<td>";
						html += "<input type='text' class='input_text invoice_note' name='invoice[" + v.order_id + "][invoice_note][]'  value='" + v.invoice_note +  "'>";
						html += "</td>";
						html += "<td>";
						html += "<input type='text' class='input_text invoice_url' name='invoice[" + v.order_id + "][invoice_url][]'  value='" + v.invoice_url +  "'>";
						html += "</td>";
						html += "</tr>";
		            });
		            
		            $(".invoice-table-module-list-data").html(html);
		        }, 
		        'json');
		
		$('#editinvoice_dialog').dialog('open');
	});
	
	// 导入申报信息
	$(".importInvoiceBtn").live('click', function(){
		$("#table-module-list-data-batchAdd").find("tr").remove();
    	$("#import-invoice-dialog").dialog("open");
	});
	
	/** 导入申报信息 **/
    $("#import-invoice-dialog").dialog({
        autoOpen: false,
        modal: true,
        width: 620,
        //height: 500,
        show: "slide",
        close: function () {
        	$("#import-invoice-dialog").dialog('close');
        }
    });
    
    // 导入重量
    $(".importWeightBtn").live('click', function(){
    	$("#table-module-weight-list-data-batchAdd").find("tr").remove();
    	$("#import-weight-dialog").dialog("open");
    });
    
    /** 导入重量信息 **/
    $("#import-weight-dialog").dialog({
    	autoOpen: false,
    	modal: true,
    	width: 620,
    	//height: 500,
    	show: "slide",
    	close: function () {
    		$("#import-weight-dialog").dialog('close');
    	}
    });
    
    $('.sort').click(function(){	

    	$(".sort").css("color","black");
		
	    var name= $(this).attr('name');
	    var sort= $(this).attr('sort');
	    var nowSort = '';
	    var nowSortTitle = '';
	    if(sort==''||sort=='asc'){
	    	nowSort = 'desc';
	    	nowSortTitle = "↓";
	    } else {
	    	nowSort = 'asc';
	    	nowSortTitle = "↑";
	    }
	    
		$(this).attr('sort',nowSort);
		$(this).find("span").html(nowSortTitle);
		$(this).css("color","red");
		
	    $('#orderBy').val(name+' '+nowSort);
	    
	    initData(paginationCurrentPage-1);
	});
})

// 导入重量
function importWeight() {
	var pFile = $("#WeightFileName").val();
    if (pFile == '') {
        $("#uploadWeightTip").text('请选择文件.');
        return false;
    }
    var postfix = pFile.substr(pFile.length - 3, 3).toLowerCase();
    if (postfix != 'xls') {
        $("#uploadWeightTip").text('仅支持 xls 文件.');
        return false;
    }
    $("#uploadWeightTip").html("");
    //数据提交
    alertTip('数据上传中....',450);
    $.ajaxFileUpload
    (
            {
                url: $('#importWeightForm').attr('action'),
                secureuri: false,
                fileElementId: 'WeightFileName',
                dataType: 'json',
                //data:{batchAddProduct_supplier:$("#batchAddProduct_supplier").val()},
                success: function (json, status) {
                    $('#dialog-auto-alert-tip').dialog('close');
                    var html = "";
					if(!isJson(json)) {
						html = "Internal error";
					} else if(json.ask=='1'){
						html += "<tr>";
                        html += "<td colspan='2'><span style='color:green;'>成功("+json.successCount+")条</span>, <span style='color:red;'>失败("+ json.failCount +")条 </span></td>";
                        html += "</tr>";
                        $.each(json.errorMsg, function (k, v) {
                            html += "<tr style='color:red;'>";
                            html += "<td>"+(k*1+1)+"</td>";
                            html += "<td>"+v+"</td>";
                            html += "</tr>";
                        });
                    } else {
                        $.each(json.errorMsg, function (k, v) {
                            html += "<tr style='color:red;'>";
                            html += "<td>"+(k*1+1)+"</td>";
                            html += "<td>"+v+"</td>";
                            html += "</tr>";
                        });
                    }

                    $("#table-module-weight-list-data-batchAdd").find("tr").remove();
                    $("#table-module-weight-list-data-batchAdd").append(html);
                    $("#WeightFileName").val('');
                },
                error: function (data, status, e) {
                    alert(e);
                }
            }
    )
}

// 导入发票数据
function importInvoice() {
	var pFile = $("#FileName").val();
	if (pFile == '') {
		$("#uploadTip").text('请选择文件.');
		return false;
	}
	var postfix = pFile.substr(pFile.length - 3, 3).toLowerCase();
	if (postfix != 'xls') {
		$("#uploadTip").text('仅支持 xls 文件.');
		return false;
	}
	$("#uploadTip").html("");
	//数据提交
	alertTip('数据上传中....',450);
	$.ajaxFileUpload
	(
			{
				url: $('#importInvoiceForm').attr('action'),
				secureuri: false,
				fileElementId: 'FileName',
				dataType: 'json',
				//data:{batchAddProduct_supplier:$("#batchAddProduct_supplier").val()},
				success: function (json, status) {
					$('#dialog-auto-alert-tip').dialog('close');
					var html = "";
					if(!isJson(json)) {
						html = "Internal error";
					} else if(json.ask=='1'){
						html += "<tr>";
						html += "<td colspan='2'><span style='color:green;'>" + json.message +"</td>";
						html += "</tr>";
					} else {
						$.each(json.errorMsg, function (k, v) {
							html += "<tr style='color:red;'>";
							html += "<td>"+(k*1+1)+"</td>";
							html += "<td>"+v+"</td>";
							html += "</tr>";
						});
					}
					
					$("#table-module-list-data-batchAdd").find("tr").remove();
					$("#table-module-list-data-batchAdd").append(html);
					$("#FileName").val('');
				},
				error: function (data, status, e) {
					alert(e);
				}
			}
	)
}

function toBaseSearch(){
    $("#search-module-baseSearch").show();
    $("#search-module-advancedSearch").hide();
    $(".input_text","#search-module-advancedSearch").val('');
    $(".input_select","#search-module-advancedSearch").val('');
    $('#search-module .input_select[name="country_code"]').trigger("chosen:updated"); 
	$('#search-module .input_select[name="product_code"]').trigger("chosen:updated"); 
}

$(function(){
<?php if ($_GET['shipper_hawbcode']){?>
//订单创建成功，链接到订单列表界面,默认查询客户订单号

<?php if ($_GET['code_type']){?>
$('#code_type').val('<?php echo $_GET['code_type'];?>
');
<?php }?>
$('#code').val('<?php echo $_GET['shipper_hawbcode'];?>
');
setTimeout(function(){ $('.statusTagAll:visible').click(); },5);
<?php }?>


<?php if ($_GET['ac']=='upload'){?>
//批量上传成功,链接到订单列表界面,默认查询草稿状态订单
setTimeout(function(){ $('.statusTagD:visible').click(); },5);
<?php }?>
})

<?php }} ?>