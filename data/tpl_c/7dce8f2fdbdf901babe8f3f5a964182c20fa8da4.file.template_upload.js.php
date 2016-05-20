<?php /* Smarty version Smarty-3.1.13, created on 2016-05-12 13:32:44
         compiled from "D:\yt1\application\modules\order\js\order-template\template_upload.js" */ ?>
<?php /*%%SmartyHeaderCode:270125734157c40f1a4-04057919%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7dce8f2fdbdf901babe8f3f5a964182c20fa8da4' => 
    array (
      0 => 'D:\\yt1\\application\\modules\\order\\js\\order-template\\template_upload.js',
      1 => 1457516431,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '270125734157c40f1a4-04057919',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_5734157c432435_87623050',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5734157c432435_87623050')) {function content_5734157c432435_87623050($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'D:\\yt1\\libs\\Smarty\\plugins\\block.t.php';
?>
var customer_checkbox_arr = {};
$(function(){

	$('#uploadBtn').live('click',function(){
		jConfirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
are_you_sure<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
模板上传<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', function(r) {
			if(r){
				$('#uploadBtn').attr('disabled',true);
				$('#uploadForm').submit();
			}
		});	
	});

	$('#saveTemplateBtn').live('click',function(){
		var template_op = $('#template_op').val();
		jConfirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
are_you_sure<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
保存模板<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', function(r) {
			if(r){
				//$('#saveTemplateBtn').attr('disabled',true);
				var param = $('#dataForm').serialize();
				$.ajax({
					type : "POST",
					url : "/order/order-template/save",
					data : param,
					dataType : 'json',
					success : function(json) {
						var html = json.message;
						if(json.ask){
							$('#report_id').val(json.report_id);
							alertTip(html);
							if(template_op=='edit'){//编辑
								$('#customerReport').change();
							}else{//新增
								
							}

						}else{
							if(json.err){
								html+="<ul style='padding:0 25px;list-style-type:decimal;'>";
								$.each(json.err,function(k,v){
									html+="<li>"+v+"</li>";
								})
								html+="</ul>";
							}
							alertTip(html);
						}
					}
				});
			}
		});	
	});
	$('#param').attr('readonly',true);
//	$('.customer_checkbox').live('click',function(){
		//customer_checkbox(this);
//	})

	$('#operationBtn').live('click',function(){
		if($('.standard_checkbox:checked').size()!=1){
			alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
请在标准模板中选择一行进行操作<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
			return;
		}
		if($('.customer_checkbox:checked').size()<1){
			alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
请在标客户模板中选择至少一行进行操作<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
			return;
		}
		var index = $('.standard_checkbox:checked').eq(0).attr('key');
		var val =  $('.standard_checkbox:checked').eq(0).val();
		var tr = 'standardTR_'+index;

		var param = $('#param').val();
		var keys = param.split(',');
		var type = 'C';
		if(keys.length>1){
			type = 'M';
		}
		$('#cporct').val(type);
		var type_text = $('#cporct :selected').text();
		//alert(type_text);
		$.each(keys,function(k,v){
			$('#td_mapNameValue'+v).text(val);
		})
		//alert(param);

		$('#td_mappingType'+index).text(type_text);
		$('#td_mappingValue'+index).text(param);
		$('#mappingType'+index).val(type);
		$('#mappingValue'+index).val(param);

		$('.standard_checkbox').attr('checked',false);
		$('.customer_checkbox').attr('checked',false);
		$('#param').val('');
		customer_checkbox_arr = {};

	});

	$('#revokedTemplateBtn').live('click',function(){
		if($('.standard_checkbox:checked').size()<1){
			alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
请在标准模板中选择一行进行操作<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
			return;
		}
		$('.standard_checkbox:checked').each(function(){
			var index = $(this).attr('key');
			var map = $('#mappingValue'+index).val();
			var keys = map.split(',');
			$.each(keys,function(k,v){
				$('#td_mapNameValue'+v).text('');
			})
			$('#td_mappingType'+index).text('');
			$('#td_mappingValue'+index).text('');
			$('#mappingType'+index).val('');
			$('#mappingValue'+index).val('');
		});

		$('.standard_checkbox').attr('checked',false);		
	})
});

function customer_checkbox(obj,event){
	var index = $(obj).attr('index');	
	//alert(key);
	var element = event.target;
	var tagName = element.tagName;
	tagName = tagName.toLowerCase();
	if(tagName!='tr'){
		switch(tagName){
			case 'input':
				
				break;
			default:
				$('#customer_checkbox'+index).attr('checked',!$('#customer_checkbox'+index).is(':checked'));
				
			
		}
	}
	var obj = $('#customer_checkbox'+index);
	var index = $(obj).attr('key');
	var key = 'key'+index;
	if($(obj).is(':checked')){
		customer_checkbox_arr[key]=index;
	}else{
		//alert(key);
		delete customer_checkbox_arr[key];
	}
	var arr = [];
	$.each(customer_checkbox_arr,function(k,v){
		arr.push(v);
	});
//	alert(arr.join(';'));
	$('#param').val(arr.join(','));
}

function standard_checkbox(obj,event){
	var index = $(obj).attr('index');	
	//alert(key);
	var element = event.target;
	var tagName = element.tagName;
	tagName = tagName.toLowerCase();
	if(tagName!='tr'){
		switch(tagName){
			case 'input':
				
				break;
			default:
				$('#standard_checkbox'+index).attr('checked',!$('#standard_checkbox'+index).is(':checked'));
		}
	}
}

<?php }} ?>