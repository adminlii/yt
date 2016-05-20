
var customer_checkbox_arr = {};
$(function(){

	$('#uploadBtn').live('click',function(){
		jConfirm('<{t}>are_you_sure<{/t}>', '<{t}>模板上传<{/t}>', function(r) {
			if(r){
				$('#uploadBtn').attr('disabled',true);
				$('#uploadForm').submit();
			}
		});	
	});

	$('#saveTemplateBtn').live('click',function(){
		var template_op = $('#template_op').val();
		jConfirm('<{t}>are_you_sure<{/t}>', '<{t}>保存模板<{/t}>', function(r) {
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
			alertTip('<{t}>请在标准模板中选择一行进行操作<{/t}>');
			return;
		}
		if($('.customer_checkbox:checked').size()<1){
			alertTip('<{t}>请在标客户模板中选择至少一行进行操作<{/t}>');
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
			alertTip('<{t}>请在标准模板中选择一行进行操作<{/t}>');
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

