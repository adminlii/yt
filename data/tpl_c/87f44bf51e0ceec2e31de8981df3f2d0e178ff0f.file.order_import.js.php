<?php /* Smarty version Smarty-3.1.13, created on 2016-04-25 15:31:56
         compiled from "F:\yt20160425\application\modules\order\js\order\order_import.js" */ ?>
<?php /*%%SmartyHeaderCode:15975571dc7ec94a192-54178288%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '87f44bf51e0ceec2e31de8981df3f2d0e178ff0f' => 
    array (
      0 => 'F:\\yt20160425\\application\\modules\\order\\js\\order\\order_import.js',
      1 => 1457516431,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15975571dc7ec94a192-54178288',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_571dc7ec984b21_51189718',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_571dc7ec984b21_51189718')) {function content_571dc7ec984b21_51189718($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'F:\\yt20160425\\libs\\Smarty\\plugins\\block.t.php';
?>function getSubmiter() {
	var order_id = $('#order_id').val();
	if(order_id==''){//避免复制订单的时候,order_id置空不默认选中发件人的情况
		order_id = '<?php echo $_GET['order_id'];?>
';
	}
	var param = {};
	param.order_id = order_id;
	var tip_id = 'ttttttt';
	////loadStart(tip_id);
	$.ajax({
		type : "POST",
		url : "/order/order/get-submiter",
		data : param,
		dataType : 'html',
		success : function(html) {
			////loadEnd('',tip_id);
			$('#submiter_wrap').html(html);
		}
	});
}
$(function(){
	setTimeout(getSubmiter,100);
});

$(function(){
	$('#set_product_code_div').dialog({
        autoOpen: false,
        width: 300,
        maxHeight: 200,
        modal: true,
        show: "slide",
        buttons: [
            {
                text: 'Ok',
                click: function () {
                	var product_code_set = $('#product_code_set').val();
                	if($.trim(product_code_set)!=''){
                		$('#abnormal_wrap .product_code').val(product_code_set);
                        $(this).dialog("close");
                	}else{
                		alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
请选择运输方式<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
                	}
                	
                }
            },
            {
                text: 'Close',
                click: function () {
                    $(this).dialog("close");
                }
            }
        ],
        close: function () {
            
        }
    });

	$('#add_prefix_div').dialog({
        autoOpen: false,
        width: 300,
        maxHeight: 200,
        modal: true,
        show: "slide",
        buttons: [
            {
                text: 'Ok',
                click: function () {
                	var prefix_set = $('#prefix_set').val();
                	prefix_set = $.trim(prefix_set);

            		var reg = /^[a-zA-Z0-9\-_]+$/;
                	if($.trim(prefix_set)!=''){
                		if(reg.test(prefix_set)){
                			$('#abnormal_wrap .shipper_hawbcode').each(function(){
                    			var input = $(this).val();
                    			$(this).val(prefix_set+input);
                    		})
                            $(this).dialog("close");
                		}else{
                			alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
只能输入字母,数字 , _ , -<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
                		}
                		
                	}else{
                		alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
请输入文字<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
                	}
                }
            },
            {
                text: 'Close',
                click: function () {
                    $(this).dialog("close");
                }
            }
        ],
        close: function () {
            
        }
    });
	$('.addPrefixBtn').live('click',function(){
		$('#add_prefix_div').dialog('open');
	});
	$('.setProductCodeBtn').live('click',function(){
		$('#set_product_code_div').dialog('open');
	});
});


function ajaxFileUpload()
{
	var shipper_account = $('.shipper_account:checked').val();
	if(!shipper_account){
		shipper_account = 0;
	}
	
	var ansych = 0;
	if($('#ansych').is(':checked')) {
		ansych = $('#ansych:checked').val();
	}
	
	var fileToUpload = $('#fileToUpload').val();
	if(fileToUpload){//文件上传
		jConfirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
are_you_sure<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
文件上传<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 / <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
数据上传<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', function(r) {
			if(r){
				loadStart();
				$('#file_upload_content_wrap').html('');
				$.ajaxFileUpload
				(
						{
							url:'/order/order/import?shipper_account='+shipper_account+'&ansych=' + ansych, 
							secureuri:false,
							fileElementId:'fileToUpload',
							//data : `,
							dataType: 'content',
							success: function (html, status)
							{
								loadEnd('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
数据验证结束<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
								$('#fileToUpload').val('');
								$('#file_upload_content_wrap').html(html);
								$('.mainStatus[id="abnormal"]').click();

								var result_message_h = $('#result_message').height();
								if(result_message_h>150){
									$('#result_message').height(150);
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
		});
		
	}else{
		if($('#file_upload_content_wrap input').size()==0){
			alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
请选择文件<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
		}else{

			jConfirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
are_you_sure<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
文件上传<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 / <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
数据上传<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', function(r) {
				if(r){
					var param = $('#uploadForm').serializeArray();
					loadStart();
					$('#file_upload_content_wrap').html('');
					$.ajax({
						type : "POST",
						url:'/order/order/import?shipper_account='+shipper_account, 
						data : param,
						dataType : 'html',
						success : function(html) {
							loadEnd('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
数据验证结束<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
							$('#fileToUpload').val('');
							$('#file_upload_content_wrap').html(html);
							$('.mainStatus[id="abnormal"]').click();

							var result_message_h = $('#result_message').height();
							if(result_message_h>150){
								$('#result_message').height(150);
							}
						}
					});
				}
			});	
			
		}
	}

}  
function clazzInit(){
	$('#table-module-list-data').each(function(){
		$("tr",this).removeClass('table-module-b1').removeClass('table-module-b2');
		$("tr:even",this).addClass('table-module-b1');
		$("tr:odd",this).addClass('table-module-b2');
	}); 
}
$(function(){
	$('.mainStatus').live('click',function(){
		var id = $(this).attr('id');
		$('.data_wrap').hide();
		$('#'+id+'_wrap').show();
		$('.mainStatus').removeClass('chooseTag');
		$(this).addClass('chooseTag');

		var data_wrapper_h = 'auto';
		var h = $('#'+id+'_wrap').height();
		if(h>250){
			data_wrapper_h = '250px';
		}	
		$('#data_wrapper').css('height',data_wrapper_h).css('overflow','auto');
		$('#'+id+'_wrap .table-module-title td').each(function(k,v){
			var td_w = $(this).width();
			$('#data_wrapper .table-module-title td').eq(k).width(td_w); 
		});


	});


	$('.delBtn').live('click',function(){ 
		var this_ = $(this);
		jConfirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
are_you_sure<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
删除订单<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', function(r) {
			if(r){
				this_.parent().parent().remove();
			}
		});	    
	});
	$('.submitBtn').live('click',function(){
		ajaxFileUpload();
	});

	$('.data_wrap .table-module-list-data tr').live('click',function(){
		$('.data_wrap .table-module-list-data tr').removeClass('selected');
		$(this).addClass('selected');
	});
	$('#data_wrapper').scroll(function(){
		var scrollTop = $(this).scrollTop();
		$('.data_wrap_title').css('top',scrollTop+'px')
	});

	$('#fileToUpload').change(function(){
		var file_name = $(this).val();
		//$('#file_name').text(file_name).show();
		$('#file_upload_content_wrap').html('');
		//alert(file_name);
	});
	
	$('.viewProductKindBtn').live('click',function(){
		var url = '/order/product-kind/list?type=dialog';
		$.ajax({
			type : "POST",
			url: url,
			dataType : 'html',
			success : function(html) {
				alertTip(html,600,500);
			}
		});

//		var url = '/order/product-kind/list';
//		var width = 500;
//		var height=500;
//		var title='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
产品信息<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
';
//		openIframeDialog(url, width, height,title);
	})
});

$(function(){
	$('#product_code').chosen({width:'300px',search_contains:true});
	$('#country_code').chosen({search_contains:true});
	$('#country_code').live('change',function(){
		var product_code = $('#product_code').val();
		var country_code = $('#country_code').val();
		var order_id = $('#order_id').val();
		if($.trim(order_id)==''){
			order_id='<?php echo $_GET['order_id'];?>
';
		}
		$('#product_extraservice_wrap').html('');
		if($.trim(product_code)==''){
			return;
		}
		if($.trim(country_code)==''){
			return;
		}
		var tip_id='dddddd';
		//loadStart(tip_id);
		//附加服务
		$.ajax({
			type: "POST",
			url: "/order/product-rule/optional-serve-type",
			data: {'product_code':product_code,'country_code':country_code,'order_id':order_id},
			dataType:'json',
			success: function(json){
				var html = json.message;
				//loadEnd(html,tip_id);
				if(json.ask){
					var product_extraservice_wrap = '';
					//alert(json.data.length); 
					var has_data = false;
					$.each(json.data,function(k,v){
						has_data = true;
						var checked = v.checked?'checked':'';
						product_extraservice_wrap+="<p><label title='"+v.extra_service_note+"'>"+'【'+k+'】'+v.extra_service_cnname+"</label></p>";
						//$('#product_extraservice_wrap').append(product_extraservice_wrap);
					});
					$('#product_extraservice_wrap').html(product_extraservice_wrap);
					if(!has_data){
						product_extraservice_wrap = '无附加服务';
						$('#product_extraservice_wrap').html(product_extraservice_wrap);
					}
					
					 
					
				}else{
					 
				}

			}
		});		
	});

	$('#product_code').live('change',function(){
		var product_code = $(this).val();
		var options = "<option value=''  class='ALL'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
-select-<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>";
		$('#country_code').html(options);
		if($.trim(product_code)==''){
			return;
		}
		var tip_id='bbbbbbb';
		//loadStart(tip_id);
		//获取支持国家
		$.ajax({
			type: "POST",
			url: "/order/product-rule/get-country",
			data: {'product_code':product_code},
			dataType:'json',
			success: function(json){
				//loadEnd('',tip_id);
				var html = json.message;
				if(json.ask){
					 var default_v = $('#country_code').attr('default');
					 options = '';
					 if(json.data.length>1){
						 options = "<option value=''  class='ALL'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
-select-<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>";						 
					 }
					 //alert(json.data.length);
					 $.each(json.data,function(k,v){
						 options+="<option value='"+v.country_code+"' class='"+v.country_code+"'>"+v.country_code+" ["+v.country_cnname+"  "+v.country_enname+"]</option>";
					 });
					 $('#country_code').html(options);
					 
					 setTimeout( function(){
						 $('#country_code').chosen('destroy');
						 $('#country_code').chosen({search_contains:true});
					 },20);
					 setTimeout(function(){
						 $('#country_code').val(default_v);
						 $('#country_code').change();
					 },10)
					 
				}else{
					 
				}

			}
		});		
	});
	

	$('#shipping_method_country_extra_service_div').dialog({
        autoOpen: false,
        width: 800,
        height: 600,
        modal: true,
        show: "slide",
        buttons: [
            {
                text: 'Ok',
                click: function () {
                	var prefix_set = $('#prefix_set').val();
                	prefix_set = $.trim(prefix_set);

            		var reg = /^[a-zA-Z0-9\-_]+$/;
                	if($.trim(prefix_set)!=''){
                		if(reg.test(prefix_set)){
                			$('#abnormal_wrap .shipper_hawbcode').each(function(){
                    			var input = $(this).val();
                    			$(this).val(prefix_set+input);
                    		})
                            $(this).dialog("close");
                		}else{
                			alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
只能输入字母,数字 , _ , -<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
                		}
                		
                	}else{
                		alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
请输入文字<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
                	}
                }
            },
            {
                text: 'Close',
                click: function () {
                    $(this).dialog("close");
                }
            }
        ],
        open:function(){
			 $('#country_code').chosen('destroy');
			 $('#country_code').chosen({search_contains:true});
        },
        close: function () {
            
        }
    });
	$('.viewRelationBtn').live('click',function(){
		$('#shipping_method_country_extra_service_div').dialog('open');
	});
})<?php }} ?>