function getUserAddress() {
	var pickup_order_id = $('#pickup_order_id').val();
	if(pickup_order_id==''){//避免复制提货单的时候,order_id置空不默认选中发件人的情况
		pickup_order_id = '<{$smarty.get.pickup_order_id}>';
	}
	var param = {};
	param.pickup_order_id = pickup_order_id;
	var tip_id = 'ttttttt';
	////loadStart(tip_id);
	$.ajax({
		type : "POST",
		url : "/pickup/pickup/get-user-address",
		data : param,
		dataType : 'html',
		success : function(html) {
			////loadEnd('',tip_id);
			$('#submiter_wrap').html(html);
			//alert($('.address_id:checked').size());
			setTimeout(function(){
				$('.address_id_radio:checked').click();				
			},50);
		}
	});
}

function successTip(tip, pickup_order_id) {
		$('<div title="操作提示 (Esc)" id="success-tip"><p align="">' + tip + '</p></div>').dialog({
	        autoOpen: true,
	        closeOnEscape:false,
	        width: 600,
	        maxHeight: 400,
	        modal: true,
	        show: "slide",
	        buttons: [
	            {
	                text: '<{t}>录入下一条提货单<{/t}>',
	                click: function () {
	                    $(this).dialog("close");
	                    $('#pickup_order_id').val('');
	    				$('#products').html('');
	    				$('#bags').val('');
	    				$('#pieces').val('');
	    				$('#weight').val('');
	                }
	            },
	            {
	                text: '<{t}>查看提货单<{/t}>',
	                click: function () {
	                    $(this).dialog("close");
	                    leftMenu('pick_order_list','<{t}>提货单管理<{/t}>','/pickup/pickup/list?pickup_order_id='+pickup_order_id);
	                }
	            }
	            /*,
	            {
	                text: '<{t}>编辑提货单<{/t}>',
	                click: function () {
	                    $(this).dialog("close");
	                    $('#pickup_order_id').val(pickup_order_id);
	                }
	            }*/
	            
	        ],
	        close: function () {
	            $(this).remove();
	        },
	        open:function(){
	        	$('.ui-dialog-titlebar-close',$(this).parent()).remove();
	        }
	    });
}

function formSubmit(){
	$("#orderForm :input").each(function(){
		var val = $(this).val();
		val = $.trim(val)
		$(this).val(val);
	});
	var param = $("#orderForm").serialize();
	
	loadStart(null,'正在向提货服务商申请跟踪面单号，请稍等……');
	$.ajax({
		type: "POST",
		url: "/pickup/pickup/create",
		data: param,
		dataType:'json',
		success: function(json){
			loadEnd('');
			var html = json.message;
			if(json.ask){				           
				//alertTip(html);
				successTip(html,json.pickup_order_id);
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
$(function() {
	//setTimeout(getUserAddress,100)
	getUserAddress();
	$('.address_id_radio').live('click',function(){
		var pickup_og_id = $(this).attr('pickup_og_id');
		$.ajax({
			type: "POST",
			url: "/pickup/pickup/get-org-time",
			data: {'pickup_og_id':pickup_og_id},
			dataType:'json',
			success: function(json){
				var html = '';
				$.each(json,function(k,v){
					html+='<option value="'+v.pickup_server_id+'">'+v.pickup_server_note+'</option>';
				})
				$('#pickup_server_id').html(html);
				$('#pickup_server_id').val($('#pickup_server_id').attr('default'));
			}
		});
	});
    $(".datepicker").datepicker({ dateFormat: "yy-mm-dd"});
	$('.addInvoiceBtn').live('click',function(){
		var clone = $('#tr_tpl table tr').clone();
		 //alert(clone);
		$('#products').append(clone);
	});
	

	$('.delInvoiceBtn').live('click',function(){
		if($('.delInvoiceBtn').size()<=1){			
			//alertTip('<{t}>最后一个申报信息不可删除<{/t}>');
			//return;
		}
		var this_ = $(this);
		jConfirm('<{t}>are_you_sure<{/t}>', '<{t}>删除明细<{/t}>', function(r) {
    		if(r){
    			this_.parent().parent().remove();
    		}
    	});
       
	});
 
	

	$(".orderSubmitBtn").click(function(){
		var tip = '';
		var title = $(this).val()+"?";
		tip+='<{t}>are_you_sure<{/t}>'; 
		
		jConfirm(tip, title, function(r) {
			if(r){
				formSubmit();
			}
		});			
	});
	
	//$('.input_text').val('1');
});
$(function(){
	$('select').each(function(){
		$(this).val($(this).attr('default'));
	});
	//$('.msg').html('*');
});

function getTipTpl(){
	return $('<div class="info" style=""><span class="Validform_checktip Validform_wrong"></span><span class="dec"><s class="dec1">◆</s><s class="dec2">◆</s></span></div>');
}
$(function(){

	$('.pieces,.bags').live('keyup',function(){
		var reg = /^\d+$/;

		 //var reg = new RegExp("^[0-9]*$");
		var val = $(this).val();
		if(val==''){
			return;
		}
		var tip = getTipTpl();
		if($(this).siblings('.info').size()==0){
			$(this).parent().prepend(tip);
		}else{
			
		}

		var tip = $(this).siblings('.info');
		if(!reg.test(val)){
			$('.Validform_checktip',tip).text('<{t}>须为整数<{/t}>');	
			tip.show();			
		}else{
			tip.hide();
		}
		
	})
	
	$('.weight').live('keyup',function(){
		var reg = /^\d+(\.\d+)?$/;
		 //var reg = new RegExp("^[0-9]*$");
		var val = $(this).val();
		if(val==''){
			return;
		}
		var tip = getTipTpl();
		if($(this).siblings('.info').size()==0){
			$(this).parent().prepend(tip);
		}else{
			
		}
		var tip = $(this).siblings('.info');
		if(!reg.test(val)){
			$('.Validform_checktip',tip).text('<{t}>须为数字<{/t}>');	
			tip.show();			
		}else{
			tip.hide();
		}
		
	})
	
});
 