<?php /* Smarty version Smarty-3.1.13, created on 2014-07-22 14:07:06
         compiled from "E:\Zend\workspaces\ruston_oms\application\modules\product\views\product\product_create.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3162153cdd2504ee7a2-54423386%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '44f877bec2e96b7d4d2a57c0b3f235a788b7f12c' => 
    array (
      0 => 'E:\\Zend\\workspaces\\ruston_oms\\application\\modules\\product\\views\\product\\product_create.tpl',
      1 => 1406009225,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3162153cdd2504ee7a2-54423386',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_53cdd25076d357_05044350',
  'variables' => 
  array (
    'product' => 0,
    'maxUpload' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53cdd25076d357_05044350')) {function content_53cdd25076d357_05044350($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'E:\\Zend\\workspaces\\ruston_oms\\libs\\Smarty\\plugins\\block.t.php';
?><link type="text/css" rel="stylesheet" href="/css/public/layout.css" />
<style>
<!--
.input_text {
	width: 250px;
}

.hide {
	display: none;
}

#loading {
	display: none;
}

#search-module {
	overflow: auto;
}
.msg{
	color: red;
}
.warmTips{
	color: #F79304;
	padding-left: 10px;
}
.c_level{width:120px;}
.module-title{
	text-align: right;
}
-->
</style>
<script type="text/javascript">

function clazzInit(){
	$('.table-module-list-data').each(function(){
		$("tr",this).removeClass('table-module-b1').removeClass('table-module-b2');
		$("tr:even",this).addClass('table-module-b1');
		$("tr:odd",this).addClass('table-module-b2');
	}); 
}
	$(function(){
		clazzInit();
	   
	    $("#productSubmitBtn").click(function(){
			var param = $("#productForm").serialize();	
			loadStart();		
			$.ajax({
			   type: "POST",
			   url: "/product/product/edit",
			   data: param,
			   dataType:'json',
			   success: function(json){
				   loadEnd('');
				   var html = json.message;
				   if(json.ask){
					   if($.trim($('#product_id').val())==''){
							$('#product_sku').val('');
							$('#reference_no').val('');
					   }
				   }else{
					   
				   }
				   $('#create_feedback_div').html(html);
				   $('.feedback_tips').show();
				   setTimeout(function(){scrollTo(0,0);},500);
				   //alertTip(html);
				   
			   }
			});
	    });

	    $('select').each(function(){
	        var defVal = $(this).attr('default')||'';
	        $(this).val(defVal);
	    });
	    
	    
	})
	function initCat(){
		var pid = '0';
		var lang = $('#cat_lang').val();
    	var param = {'pid':pid,'lang':lang};
		$.ajax({
			   type: "POST",
			   url: "/product/category-oms/get-category",
			   data: param,
			   dataType:'json',
			   success: function(json){
			   		var html = '';
	    			var def = $('#c_level_0').attr('default');
					$.each(json,function(k,v){
						var select = '';
						if(def==v.ig_id){
							select = 'selected';
						}
						html+='<option value="'+v.ig_id+'" '+select+'>'+v.ig_name+'</option>';		
					});
					//alert(html);
					$('#c_level_0').html(html);
					$('#c_level_1').html('');
					$('#c_level_2').html('');
					//if(def!=''){
						$('#c_level_0').change();
					//}
			   }
			});	
	}
	$(function(){	
		initCat();	
	    $('#c_level_0').change(function(){
	    	var pid = $(this).val();
	    	if(pid==''){
		   		var html = '';
				html+='<option value="">'+$.getMessage('sys_common_select');+'</option>';
				$('#c_level_1').html(html);
	    	    return;
	    	}
			var lang = $('#cat_lang').val();
	    	var param = {'pid':pid,'lang':lang};
	    	$.ajax({
			   type: "POST",
			   url: "/product/category-oms/get-category",
			   data: param,
			   dataType:'json',
			   success: function(json){
			   		var html = '';
					html+='<option value="">'+$.getMessage('sys_common_select');+'</option>';
				    var def = $('#c_level_1').attr('default');
					$.each(json,function(k,v){
						var select = '';
						if(def==v.ig_id){
							select = 'selected';
						}						
						html+='<option value="'+v.ig_id+'" '+select+'>'+v.ig_name+'</option>';		
						//html+='<option value="'+v.ig_id+'">'+v.ig_name+'</option>';					
					});
					$('#c_level_1').html(html);
					$('#c_level_2').html('');
					//if(def!=''){
						$('#c_level_1').change();
					//}
			   }
			});	
	    });
	    $('#c_level_1').change(function(){
	    	var pid = $(this).val();
	    	if(pid==''){
		   		var html = '';
				html+='<option value="">'+$.getMessage('sys_common_select');+'</option>';
				$('#c_level_2').html(html);
	    	    return;
	    	}
			var lang = $('#cat_lang').val();
	    	var param = {'pid':pid,'lang':lang};
	    	$.ajax({
			   type: "POST",
			   url: "/product/category-oms/get-category",
			   data: param,
			   dataType:'json',
			   success: function(json){
			   		var html = '';
					html+='<option value="">'+$.getMessage('sys_common_select');+'</option>';
			   	    var def = $('#c_level_2').attr('default');
					$.each(json,function(k,v){
						var select = '';
						if(def==v.ig_id){
							select = 'selected';
						}						
						html+='<option value="'+v.ig_id+'" '+select+'>'+v.ig_name+'</option>';					
					});
					$('#c_level_2').html(html);
			   }
			});	
	    });
	    $('#LangToggle').click(function(){
			var lang = $('#cat_lang').val();
			if(lang=='en'){
				$('#cat_lang').val('zh');
			}else{
				$('#cat_lang').val('en');
			}	
			initCat();		
	    });
	})
</script>
<div id="module-container">
	<div id="module-table" style='clear: both;padding: 0 45px;'>
		<div class="feedback_tips">
			<div class="feedback_tips_text" >
				<p id="create_feedback_div">
					
				</p>
			</div>
			<div style="clear: both;"></div>
		</div>
		<form method='POST' action='' onsubmit='return false;' id='productForm'>
			<h2 style="border-bottom: 1px dashed #CCCCCC;margin-bottom:10px;">1、<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_category<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
			<table cellspacing="0" cellpadding="0" border="0" class="table-module" style='margin-top: 10px;width: 100%;'>
				<tbody class="table-module-list-data">
					<tr>
						<td style="padding-left: 30px;">
							<input type='hidden' name='cat_lang' value='<?php if (isset($_smarty_tpl->tpl_vars['product']->value)){?><?php echo $_smarty_tpl->tpl_vars['product']->value['cat_lang'];?>
<?php }else{ ?>en<?php }?>' id='cat_lang'>
							<select id='c_level_0' name='cat_id0' class='c_level input_select' style="width:180px;" default='<?php if (isset($_smarty_tpl->tpl_vars['product']->value)){?><?php echo $_smarty_tpl->tpl_vars['product']->value['cat_id0'];?>
<?php }?>'></select>
							<select id='c_level_1' name='cat_id1' class='c_level input_select' style="width:180px;" default='<?php if (isset($_smarty_tpl->tpl_vars['product']->value)){?><?php echo $_smarty_tpl->tpl_vars['product']->value['cat_id1'];?>
<?php }?>'></select>
							<select id='c_level_2' name='cat_id2' class='c_level input_select' style="width:180px;" default='<?php if (isset($_smarty_tpl->tpl_vars['product']->value)){?><?php echo $_smarty_tpl->tpl_vars['product']->value['cat_id2'];?>
<?php }?>'></select>
							&nbsp;&nbsp;<a href='javascript:;' id='LangToggle'>中文/English</a>
						</td>
					</tr>
				</tbody>
			</table>
			<h2 style='padding: 35px 0 0 0;margin-bottom:10px; border-bottom: 1px dashed #CCCCCC;'>2、<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_info<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
			<table cellspacing="0" cellpadding="0" border="0" class="table-module" style='margin-top: 10px;width: 100%;'>
				<tbody class="table-module-list-data">
					<tr>
						<td width="150" class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
p_product_title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<input type="text" name="product_title" id="product_title" value="<?php if (isset($_smarty_tpl->tpl_vars['product']->value)){?><?php echo $_smarty_tpl->tpl_vars['product']->value['product_title'];?>
<?php }?>" class="input_text" validator="required" err-msg="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
require<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" />
							<span class="msg">*</span>
							<span class="warmTips"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_title_tip<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
						</td>
					</tr>
					<tr>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
SKU<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
						    <?php if (isset($_smarty_tpl->tpl_vars['product']->value)&&$_smarty_tpl->tpl_vars['product']->value['have_asn']!=0){?>
						    <input type="text" name="product_sku" id="product_sku" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['product_sku'];?>
" class="input_text" readonly/>							
							<?php }else{ ?>
							<input type="text" name="product_sku" id="product_sku" value="<?php if (isset($_smarty_tpl->tpl_vars['product']->value)){?><?php echo $_smarty_tpl->tpl_vars['product']->value['product_sku'];?>
<?php }?>" class="input_text" validator="required" err-msg="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
require<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" />		
							<span class="msg">*</span>
							<span class="warmTips"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
sku_tip<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>			
							<?php }?>							
						</td>
					</tr>					
					<tr>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
ref_no<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<input type="text" name="reference_no" id="reference_no" value='<?php if (isset($_smarty_tpl->tpl_vars['product']->value)){?><?php echo $_smarty_tpl->tpl_vars['product']->value['reference_no'];?>
<?php }?>' class="input_text" />
							
						</td>
					</tr>
					
					<!--
					<tr style="">
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_sales_value<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
(RMB)：</td>
						<td>
							<input type="text" name="product_sales_value" id="product_sales_value" value="<?php if (isset($_smarty_tpl->tpl_vars['product']->value)){?><?php echo $_smarty_tpl->tpl_vars['product']->value['product_sales_value'];?>
<?php }?>" class="input_text" />
							<span class="msg">&nbsp;</span>
							<span class="warmTips"></span>
						</td>
					</tr>
					<tr style="">
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_purchase_value<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
(RMB)：</td>
						<td>
							<input type="text" name="product_purchase_value" id="product_purchase_value" value="<?php if (isset($_smarty_tpl->tpl_vars['product']->value)){?><?php echo $_smarty_tpl->tpl_vars['product']->value['product_purchase_value'];?>
<?php }?>" class="input_text" />
							<span class="msg">&nbsp;</span>
							<span class="warmTips"></span>
						</td>
					</tr>
					-->
					<tr>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_declared_value<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
(USD)：</td>
						<td>
							<input type="text" name="product_declared_value" id="product_declared_value" value='<?php if (isset($_smarty_tpl->tpl_vars['product']->value)){?><?php echo $_smarty_tpl->tpl_vars['product']->value['product_declared_value'];?>
<?php }?>' validator="required" err-msg="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
require<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="input_text" />
							<span class="msg">*</span>
							<span class="warmTips"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_declared_value_tip<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
						</td>
					</tr>
					<tr>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_declared_name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<input type="text" name="product_declared_name" id="product_declared_name" value='<?php if (isset($_smarty_tpl->tpl_vars['product']->value)){?><?php echo $_smarty_tpl->tpl_vars['product']->value['product_declared_name'];?>
<?php }?>' validator="required" err-msg="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
require<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="input_text" />
							<span class="msg">*</span>
							<span class="warmTips"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_declared_name_tip<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
						</td>
					</tr>
				</tbody>
			</table>
			<h2 style='padding: 35px 0 0 0;margin-bottom:10px; border-bottom: 1px dashed #CCCCCC;'>3、<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_size_info<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
			<table cellspacing="0" cellpadding="0" border="0" class="table-module" style='margin-top: 10px;width: 100%;'>
				<tbody class="table-module-list-data">
					<tr>
						<td width="150" class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_weight<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
(KG)：</td>
						<td>
							<input type="text" name="product_weight" id="product_weight" value='<?php if (isset($_smarty_tpl->tpl_vars['product']->value)){?><?php echo $_smarty_tpl->tpl_vars['product']->value['product_weight'];?>
<?php }?>' validator="required" err-msg="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
require<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="input_text" />
							<span class="msg">*</span>
							<span class="warmTips"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_weight_tip<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
						</td>
					</tr>
					<tr>
						<td class="module-title" ><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_size<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<div style="background-color: #FFFFFF;border: 1px solid #DDDDDD;padding: 5px;width: 650px;min-width:580px; float: left;">
								<div style=" padding: 5px 0;width: 100%;">
									<div style="width:65px;float: left;text-align: right;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_length<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
(CM)：</div>&nbsp;&nbsp;
									<div style="width:85%;float: left;">
										<input type="text" style="width: 65px;" name="product_length" id="product_length" value='<?php if (isset($_smarty_tpl->tpl_vars['product']->value)){?><?php echo $_smarty_tpl->tpl_vars['product']->value['product_length'];?>
<?php }?>' validator="required" err-msg="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
require<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="input_text" />
										<span class="red">&nbsp;*</span>
										<span class="warmTips"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_size_tip<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
									</div>
								</div>
								<div style=" padding: 5px 0;width: 100%;">
									<div style="width:65px;float: left;text-align: right;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_width<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
(CM)：</div>&nbsp;&nbsp;
									<div style="width:85%;float: left;">
										<input type="text" style="width: 65px;" name="product_width" id="product_width" value='<?php if (isset($_smarty_tpl->tpl_vars['product']->value)){?><?php echo $_smarty_tpl->tpl_vars['product']->value['product_width'];?>
<?php }?>' validator="required" err-msg="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
require<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="input_text" />
										<span class="msg">*</span>
										<span class="warmTips"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_size_tip<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
									</div>
								</div>
								<div style=" padding: 5px 0;width: 100%;">
									<div style="width:65px;float: left;text-align: right;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_height<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
(CM)：</div>&nbsp;&nbsp;
									<div style="width:85%;float: left;">
										<input type="text" style="width: 65px;" name="product_height" id="product_height" value='<?php if (isset($_smarty_tpl->tpl_vars['product']->value)){?><?php echo $_smarty_tpl->tpl_vars['product']->value['product_height'];?>
<?php }?>' validator="required" err-msg="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
require<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="input_text" />
										<span class="msg">*</span>
										<span class="warmTips"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_size_tip<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
									</div>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
contain_battery<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<select name="contain_battery" id="contain_battery" default='<?php if (isset($_smarty_tpl->tpl_vars['product']->value)){?><?php echo $_smarty_tpl->tpl_vars['product']->value['contain_battery'];?>
<?php }?>' class="input_select">
								<option value="0"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
not_contain_battery<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
								<option value="1"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
contain_battery<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
			<h2 style='padding: 35px 0 0 0;margin-bottom:10px; border-bottom: 1px dashed #CCCCCC;'>4、<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_images<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
&nbsp;&nbsp;&nbsp;&nbsp;(Max <?php echo $_smarty_tpl->tpl_vars['maxUpload']->value;?>
)</h2>
			<?php echo $_smarty_tpl->getSubTemplate ('product/views/product/product-picture-upload.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

			<!-- 
			<h2 style='padding: 35px 0 0 0;margin-bottom:10px; border-bottom: 1px dashed #CCCCCC;'>4、<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_inventory_info<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
			<table cellspacing="0" cellpadding="0" border="0" class="table-module" style='margin-top: 10px;width: 100%;'>
				<tbody class="table-module-list-data">
					<tr>
						<td width="150" class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
warning_qty<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<input type="text" name="warning_qty" value='<?php if (isset($_smarty_tpl->tpl_vars['product']->value)){?><?php echo $_smarty_tpl->tpl_vars['product']->value['warning_qty'];?>
<?php }?>' class="input_text" />
							<span class="msg"></span>
							<span class="warmTips"></span>
						</td>
					</tr>
				</tbody>
			</table>
			 -->
			<?php if (isset($_smarty_tpl->tpl_vars['product']->value)&&$_smarty_tpl->tpl_vars['product']->value['product_id']){?>
				<input type="hidden" name='product_id' value='<?php echo $_smarty_tpl->tpl_vars['product']->value['product_id'];?>
' id='product_id'>
			<?php }?>
			<div style="width: 100%;text-align: center;margin-top: 25px;padding-top:5px; border-top: 1px dashed #CCCCCC;">
				<input type='button' value='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
submit<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
' id='productSubmitBtn' class='baseBtn submitBtn' style="width: 80px;"/>
			</div>
		</form>
	</div>
</div><?php }} ?>