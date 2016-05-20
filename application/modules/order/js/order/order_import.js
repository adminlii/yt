function getSubmiter() {
	var order_id = $('#order_id').val();
	if(order_id==''){//避免复制订单的时候,order_id置空不默认选中发件人的情况
		order_id = '<{$smarty.get.order_id}>';
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
                		alertTip('<{t}>请选择运输方式<{/t}>');
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
                			alertTip('<{t}>只能输入字母,数字 , _ , -<{/t}>');
                		}
                		
                	}else{
                		alertTip('<{t}>请输入文字<{/t}>');
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
		jConfirm('<{t}>are_you_sure<{/t}>', '<{t}>文件上传<{/t}> / <{t}>数据上传<{/t}>', function(r) {
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
								loadEnd('<{t}>数据验证结束<{/t}>');
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
			alertTip('<{t}>请选择文件<{/t}>');
		}else{

			jConfirm('<{t}>are_you_sure<{/t}>', '<{t}>文件上传<{/t}> / <{t}>数据上传<{/t}>', function(r) {
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
							loadEnd('<{t}>数据验证结束<{/t}>');
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
		jConfirm('<{t}>are_you_sure<{/t}>', '<{t}>删除订单<{/t}>', function(r) {
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
//		var title='<{t}>产品信息<{/t}>';
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
			order_id='<{$smarty.get.order_id}>';
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
		var options = "<option value=''  class='ALL'><{t}>-select-<{/t}></option>";
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
						 options = "<option value=''  class='ALL'><{t}>-select-<{/t}></option>";						 
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
                			alertTip('<{t}>只能输入字母,数字 , _ , -<{/t}>');
                		}
                		
                	}else{
                		alertTip('<{t}>请输入文字<{/t}>');
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
})