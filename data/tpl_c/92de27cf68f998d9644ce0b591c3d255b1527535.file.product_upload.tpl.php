<?php /* Smarty version Smarty-3.1.13, created on 2014-07-22 17:32:16
         compiled from "E:\Zend\workspaces\ruston_oms\application\modules\product\views\product\product_upload.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1720253ce2fa09aa917-07601755%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '92de27cf68f998d9644ce0b591c3d255b1527535' => 
    array (
      0 => 'E:\\Zend\\workspaces\\ruston_oms\\application\\modules\\product\\views\\product\\product_upload.tpl',
      1 => 1405996337,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1720253ce2fa09aa917-07601755',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'product' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_53ce2fa0ac6273_89576829',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ce2fa0ac6273_89576829')) {function content_53ce2fa0ac6273_89576829($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'E:\\Zend\\workspaces\\ruston_oms\\libs\\Smarty\\plugins\\block.t.php';
?><link type="text/css" rel="stylesheet" href="/css/public/ajaxfileupload.css" />
<script type="text/javascript" src="/js/ajaxfileupload.js"></script>
<script type="text/javascript">

function ajaxFileUpload(formId,btnId)
{	loadStart();
	var param = {};
	param.cat_lang = $('#cat_lang').val();
	param.cat_id0 = $('#c_level_0').val();
	param.cat_id1 = $('#c_level_1').val();
	param.cat_id2 = $('#c_level_2').val();
	$.ajaxFileUpload
	(
		{
			url:$('#'+formId).attr('action'), 
			secureuri:false,
			fileElementId:btnId,
			dataType: 'json',
			data:param,
			success: function (json, status)
			{		
				loadEnd('')		
				var html = json.message+"<br/>";
				
				$.each(json.err,function(k,v){
				    html+= v+'<br/>';
				})
				$('#dialog-auto-alert-tip p').html(html);
				if(json.ask){
					/*
					var count = 20;
					$('#dialog-auto-alert-tip').dialog('option',{'title':count+'秒后关闭'});
					interval = setInterval(function(){
						count--;
						$('#dialog-auto-alert-tip').dialog('option',{'title':count+'秒后关闭'});
						if(count<=0){
							$('#dialog-auto-alert-tip').dialog('close');
							clearInterval(interval);
						}
					},1000);
					*/
					var html = json.message;
					$.each(json.err,function(k,v){		
						html+='<p>'+(k+1)+':'+v+'</p>';
					});
					alertTip(html,500,500);
					/*var html = '';
				    $.each(json.data,function(sheet,v){				    	 
					        html+='<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">';
					        html+='<caption>'+sheet+'</caption>';
					        var header = '';
					        
					        $.each(v,function(kk,vv){
					        	header = '';					        
					        	header+='<tr class="table-module-title">';					       
						        $.each(vv,function(kkk,vvv){
						        	header+='<td style="word-wrap:break-word">'+kkk+'</td>';
						        })
						        header+='</tr>';
					        });
					        html +=header;
					        html+='<tbody id="table-module-list-data">';
					        $.each(v,function(kk,vv){
						        
						        html+='<tr class="table-module-b1">';					       
						        $.each(vv,function(kkk,vvv){
						            html+='<td style="word-wrap:break-word">'+vvv+'</td>';
						        })
						        html+='</tr>';
					        });
					        html+='</tbody>';
					        html+='</table>';
				    	 
				    })
				    parent.alertTip(html,1024,768);
				    */
					
				}else{
					alertTip(json.message,500);
				}
			},
			error: function (data, status, e)
			{
				//alert(e);
			}
		}
	)	
	return false;

}  

$(function(){


	
});

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
	<div id="module-table" style='clear: both;'>
		<div class="step_rowTitle"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
batch_upload_products<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</div>
		<form method='post' enctype="multipart/form-data" action='/product/product/upload' id='upload_form_new' onsubmit='return false;' style='float: left; width: 100%;'>
			<table cellspacing="0" cellpadding="0" border="0" class="table-module" style='width: 100%;'>
				<tbody class="table-module-list-data">
					<tr class="table-module-b2">
						<td class="dialog-module-title" width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_category<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</td>
						<td>
						    <input type='hidden' name='cat_lang' value='en' id='cat_lang'>
							<select id='c_level_0' name='cat_id0' class='c_level input_select' default='<?php if (isset($_smarty_tpl->tpl_vars['product']->value)){?><?php echo $_smarty_tpl->tpl_vars['product']->value['cat_id0'];?>
<?php }?>'></select>
							<select id='c_level_1' name='cat_id1' class='c_level input_select' default='<?php if (isset($_smarty_tpl->tpl_vars['product']->value)){?><?php echo $_smarty_tpl->tpl_vars['product']->value['cat_id1'];?>
<?php }?>'></select>
							<select id='c_level_2' name='cat_id2' class='c_level input_select' default='<?php if (isset($_smarty_tpl->tpl_vars['product']->value)){?><?php echo $_smarty_tpl->tpl_vars['product']->value['cat_id2'];?>
<?php }?>'></select>
							&nbsp;&nbsp;<a href='javascript:;' id='LangToggle'>中文/English</a>
						</td>
					</tr>
					<tr class="table-module-b1">
						<td class="dialog-module-title" width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
xls_file<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<input type="file" name="fileToUpload" id="fileToUpload_new" class="input_text baseBtn" />
							<input type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
upload<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="submitBtn" onclick='return ajaxFileUpload("upload_form_new","fileToUpload_new");'>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<a href="/file/product.xls" target="_blank">《<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
template<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
》</a>
						</td>
					</tr>
					<tr class="table-module-b2">
						<td class="dialog-module-title" width='150'></td>
						<td>
							<b style="color:#E06B26;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
please_note<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</b>
							<br>
							<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
only_xls<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
&nbsp;&nbsp;&nbsp;&nbsp;
							<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array('escape'=>1)); $_block_repeat=true; echo smarty_block_t(array('escape'=>1), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
p_note<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array('escape'=>1), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

						</td>
					</tr>
				</tbody>
			</table>
		</form>
		<div class="search-module-condition" style='clear: both;'>
			
		</div>
		
	</div>
</div>
<?php }} ?>