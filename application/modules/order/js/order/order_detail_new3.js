
paginationPageSize = 8;
var opId = 0;
var order_id = '<{$order.order_id}>';
var ref_id = '<{$order.refrence_no_platform}>';
EZ.url = '/order/order/product-';
EZ.getListData = function (json) {
	var html = '';
	var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
	$.each(json.data, function (key, val) {
		html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";	        
		html += "<td >" + val.product_sku + "</td>";
		html += "<td >" + val.product_title_en + "</td>";
		html += "<td >" + val.product_title + "</td>";
		var checked = '';

		//html += "<td><a  href='javascript:;'  id='"+val.product_id+"' sku='"+val.product_sku+"' title='"+val.product_title_en+"' title_en='"+val.product_title+"' class='productSelectBtn' >选择</a></td>";
		html += "<td><a href='javascript:;' style='color:#2266BB;'  id='"+val.product_id+"' sku='"+val.product_sku+"' title='"+val.product_title_en+"' title_en='"+val.product_title+"' class='productAddBtn' >选择</a></td>";

		html += "</tr>";
	});
	return html;
}
function clazzInit(){
	$('.table-module-list-data').each(function(){
		$("tr",this).removeClass('table-module-b1').removeClass('table-module-b2');
		$("tr:even",this).addClass('table-module-b1');
		$("tr:odd",this).addClass('table-module-b2');
	}); 
}

$(function(){
	clazzInit();		
	$('#search-module').dialog({       
		autoOpen: false,
		width: 800,
		maxHeight: 500,
		modal: true,
		show:"slide",
		position:'top',
		buttons: {            
			'Close': function() {
				$(this).dialog('close');
			}
		},
		close: function() {

		},
		open:function(){
			$(".submitToSearch").click();
		}
	});
	$("#selectProductBtn").click(function(){
		$('#search-module').dialog("open");
	});
	$(".changeSku").live('click',function(){
		opId = $(this).attr('op_id');
		$('#search-module').dialog('open');
	});
	$(".productSelectBtn").live('click',function(){
		var productId = $(this).attr("id");
		var productSku = $(this).attr("sku");
		var productName = $(this).attr("title");

		$('#product_sku_'+opId+"").val(productSku);
	});

	$('#addProductLine').live('click',function(){
		$('#search-module').dialog('open');
	})
	$(".productAddBtn").live('click',function(){
		var productId = $(this).attr("id");
		var productSku = $(this).attr("sku");
		var productName = $(this).attr("title");
		var exists = false;
		$('.product_sku').each(function(){
			if($(this).val()==productSku){
				exists = true;
			}
		})
		if(exists){
			alert('该产品已经存在,不可重复选择');
			return false;
		}

		var html = '';
		html+='<tr class="table-module-b1 add_tr">';
		html+='<td>';						   
		html+='<img width="75" height="75" src="/product/product/eb-img/item_id/0">';						   
		html+='</td>';
		html+='<td>';
		html+='<input type="text"  value="'+productSku+'" name="product_sku_add[]" class="input_text product_sku" style="display:inline;background:#eee;" readonly>';
		html+='</td>';

		html+='<td >';
		html+='<span>'+productName+'</span>';
		html+='<input type="hidden"  value="'+productName+'" name="product_title_add[]" class="input_text ">';
		html+='</td>';
		html+='<td>';
		html+='<input type="text"  value="1" name="op_quantity_add[]" class="input_text " style="display:inline;">';
		html+='</td>';
		html+='<td>';
		html+='0 ';
		html+='<{$order.currency}>';
		html+='</td>';

		html+='<td>';
		html+='<a class="delBtn" href="javascript:;" style="">删除</a>';
		html+='</td>';
		html+='</tr>';
		$('#table-module-list-data').append(html);
	});

	$(".deleteProductBtn").live('click',function(){
		$(this).parent().parent().remove();
		clazzInit();
	});

	$("#loading").dialog({
		autoOpen: false,
		width: 600,
		maxHeight: 300,
		modal: true,
		show: "slide",
		zIndex:3999,
		buttons: [	           
		          {
		        	  text: '重新编辑',
		        	  click: function () {
		        		  $(this).dialog("close");
		        		  window.location.href= '/order/order/detail-new2/orderId/<{$order.order_id}>';
		        	  }
		          },
		          {
		        	  text: '完成编辑返回订单列表',
		        	  click: function () {
		        		 $('.ui-dialog-titlebar-close',window.parent.document).click(); 
		        	  }
		          }
		          ],
		          close: function () {

		          }
	});
	
	$("#orderSubmitBtn").click(function(){
		var param = $("#order_form").serialize();
		alertTip('Loading...');;
		$.ajax({
			type: "POST",
			url: "/order/order/update-order-detail-address",
			data: param,
			dataType:'json',
			success: function(json){

				if(json.ask){
					$('.input_change').each(function(){
						var val = $(this).val();
						$(this).prev('span').text(val);
					});
					//$('#changeBtn').click();
					parent.reLoadWhenClose = 1;

					$('#dialog-auto-alert-tip').dialog('close');	
					$("#loading").html(json.message).dialog('open');

				}else{
					$('#dialog-auto-alert-tip').html(json.message);					
				}
			}
		});
	});
	$("#orderProductSubmitBtn").click(function(){
		var param = $("#order_product_form").serialize();
		alertTip('Loading...');;
		$.ajax({
			type: "POST",
			url: "/order/order/update-order-detail-product",
			data: param,
			dataType:'json',
			success: function(json){

				if(json.ask){
					$('.input_change').each(function(){
						var val = $(this).val();
						$(this).prev('span').text(val);
					});
					//$('#changeBtn').click();
					parent.reLoadWhenClose = 1;

					$('#dialog-auto-alert-tip').dialog('close');	
					$("#loading").html(json.message).dialog('open');

				}else{
					$('#dialog-auto-alert-tip').html(json.message);					
				}
			}
		});
	});
	
	$("#orderSubmitToVerifyBtn").click(function(){
		var param = $("#order_form").serialize();
		param+='&to_verify=1';
		alertTip('Loading...');;
		$.ajax({
			type: "POST",
			url: "/order/order/update-order-detail-address",
			data: param,
			dataType:'json',
			success: function(json){
				if(json.ask){
					$('.input_change').each(function(){
						var val = $(this).val();
						$(this).prev('span').text(val);
					});
					$('#changeBtn').click();
					parent.reLoadWhenClose = 1;

					$('#dialog-auto-alert-tip').dialog('close');	
					$("#loading").html(json.message).dialog('open');

				}else{
					$('#dialog-auto-alert-tip').html(json.message);					
				}
			}
		});
	});

	$("#orderProductSubmitToVerifyBtn").click(function(){
		var param = $("#order_product_form").serialize();
		param+='&to_verify=1';
		alertTip('Loading...');;
		$.ajax({
			type: "POST",
			url: "/order/order/update-order-detail-product",
			data: param,
			dataType:'json',
			success: function(json){
				if(json.ask){
					$('.input_change').each(function(){
						var val = $(this).val();
						$(this).prev('span').text(val);
					});
					$('#changeBtn').click();
					parent.reLoadWhenClose = 1;

					$('#dialog-auto-alert-tip').dialog('close');	
					$("#loading").html(json.message).dialog('open');

				}else{
					$('#dialog-auto-alert-tip').html(json.message);					
				}
			}
		});
	});

	$('#changeBtn').toggle(function(){
		$('#order_form .span_text').hide();
		$('#order_form .input_change').show();
		$(this).val('取消修改');
		$('.orderSubmitBtn').show();
		
		
	},function(){
		$('#order_form .span_text').show();
		$('#order_form .input_change').hide();
		$(this).val('修改地址');
		$('.orderSubmitBtn').hide();
	});

	$('#changeOrderProductBtn').toggle(function(){
		$('#order_product_form .span_text').hide();
		$('#order_product_form .input_change').show();
		$(this).val('取消修改');
		$('.orderProductSubmitBtn').show();
		
		$('#addProductLine').attr('disabled',false);
		$('.delBtn').show();
		$('.stopBtn').show();
		$('.add_tr').show();
		
	},function(){
		$('#order_product_form .span_text').show();
		$('#order_product_form .input_change').hide();
		$(this).val('修改SKU');
		$('.orderProductSubmitBtn').hide();
		$('#addProductLine').attr('disabled',true);
		$('.delBtn').hide();
		$('.stopBtn').hide();
		$('.add_tr').hide();
	});
	// initData(0);
	$('#close').click(function(){
		$(window.parent.document).find("#iframe-container-overlay").hide();    	 	
	});

	$('.delBtn').live('click',function(){
		if(!window.confirm('删除该产品?')){
			return false;
		}
		$(this).parent().parent().remove();
	});
	
	$('.stopBtn').live('click',function(){
		//alertTip('请使用拆单功能..');
		
		if(!window.confirm('确认该操作?')){
			return false;
		}
		
		var sku = $(this).attr('key');
		var count = 0;

		if(!$(this).attr('count')){
		    $(this).attr('count',1);
		}
		count = $(this).attr('count');
		count = parseInt(count);
		
		if(count%2==1){
			$(':input','.tr_'+sku).attr('disabled',false);	
			$(':input','.tr_'+sku).removeClass('disabled');
			$(this).text('不发');		
		}else{
			$(':input','.tr_'+sku).attr('disabled',true);
			$(':input','.tr_'+sku).addClass('disabled');
			$(this).text('取消不发');
		}
		count++;
	    $(this).attr('count',count);
	    

		//$(this).parent().parent().remove();
		
	})
	$('#logBtn').click(function(){
		$.ajax({
			type: "POST",
			url: "/order/order/get-order-log",
			data: {'ref_id':'<{$order.refrence_no_platform}>'},
			dataType:'json',
			success: function(json){
				var html = '';
				if(json.ask){
					html +='订单状态说明<br/>';
					$.each(json.statusArr,function(k,v){
						html+= k+' : '+v.name+';';
					})
					html +='<br/>';
					html +='日志信息:<br/>';
					html +='<ul class="log_list">';
					$.each(json.data,function(k,v){
						html+='<li>'+v.create_time+' : '+v.log_content+'操作人：'+v.op_username+'</li>';
					});
					html +='</ul>';
					alertTip(html,800,480);
				}else{
					var html = json.message;	
					alertTip(html,800,480);
					//alertTip(html);					   
				}
			}
		});
	});
})

$(function(){
	$(".tab").click(function(){
        $(".tabContent").hide();
        $(".tab").removeClass("chooseTag");
        $(this).addClass("chooseTag");
        var selectTabContent = "tabContent_"+$(this).attr("id").replace("tab_","");

        $("#"+selectTabContent).show();
		var colspan = $('#'+selectTabContent+' .table-module-title td').size();
		
		var html = '';
		html+='<tr>';
		html+='<td colspan="'+colspan+'"><img src="/images/base/loading.gif"/></td>';
		html+='</tr>';
		$('#'+selectTabContent+' .table-module-list-data').html(html);
        
        var fun = $(this).attr('fun');
        $.ajax({
			type: "POST",
			url: "/order/order/"+fun,
			data: {'ref_id':'<{$order.refrence_no_platform}>'},
			dataType:'json',
			success: function(json){
				html = '';
				if(json.ask){
					switch(fun){
						case 'get-customer-order':
							$.each(json.data,function(k,v){
								html+='<tr>';
								html+='<td>'+v.refrence_no_platform+'</td>';
								html+='<td>'+v.subtotal+'</td>';
								html+='<td>'+v.ship_fee+'</td>';
								html+='<td>'+v.platform_fee+'</td>';
								html+='<td>'+v.finalvaluefee+'</td>';
								html+='<td>'+v.order_status_title+'</td>';
								html+='<td>'+v.date_create_platform+'</td>';
								html+='</tr>';
							});
							$('#'+selectTabContent+' .table-module-list-data').html(html);
							
							break;
						case 'get-customer-message':
							$.each(json.data,function(k,v){
								html+='<tr>';
								html+='<td>'+v.user_account+'</td>';
								html+='<td>'+v.message_title+'</td>';
								html+='<td>'+v.message_type+'</td>';
								html+='<td>'+v.send_time+'</td>';
								html+='</tr>';
							});
							
							$('#'+selectTabContent+' .table-module-list-data').html(html);
							break;
						case 'get-customer-case':
							$.each(json.data,function(k,v){
								html+='<tr>';
								html+='<td>'+v.case_type+'</td>';
								html+='<td>'+v.item_title+'</td>';
								html+='<td>'+v.case_status+'</td>';
								html+='<td>'+v.case_creation_date+'</td>';
								html+='</tr>';
							});
							$('#'+selectTabContent+' .table-module-list-data').html(html);
							break;
						case 'get-customer-feedback':
							$.each(json.data,function(k,v){
								html+='<tr>';
								html+='<td>'+v.ecf_item_title+'</td>';
								html+='<td>'+v.ecf_comment_text+'</td>';
								html+='<td>'+v.ecf_comment_type+'</td>';
								html+='<td>'+v.ecf_comment_time+'</td>';
								html+='</tr>';
							});
							$('#'+selectTabContent+' .table-module-list-data').html(html);
							break;
					}
				}else{	
					var html = '';
					html+='<tr>';
					html+='<td colspan="'+colspan+'">'+json.message+'</td>';
					html+='</tr>';
					$('#'+selectTabContent+' .table-module-list-data').html(html);
				}
			}
		});
        
    });
	$(".tab").eq(0).click();

})