
paginationPageSize = 8;
EZ.url = '/order/order/product-';
EZ.getListData = function (json) {
	var html = '';
	var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
	$.each(json.data, function (key, val) {
		html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";	        
		html += "<td >" + val.product_sku + "</td>";
		html += "<td >" + val.product_title + "</td>";
		var checked = '';
		if($("#product"+val.product_id).size()>0){
			checked = 'checked';
		}
		html += "<td class='ec-center'><input type='checkbox' id='"+val.product_id+"' sku='"+val.product_sku+"' title='"+val.product_title_en+"' title_en='"+val.product_title+"' class='productAddBtn' "+checked+"/></td>";
		html += "</tr>";
	});
	setTimeout('productSelectedCheck()',50);
	return html;
}
function clazzInit(){
	$('.table-module-list-data').each(function(){
		$("tr",this).removeClass('table-module-b1').removeClass('table-module-b2');
		$("tr:even",this).addClass('table-module-b1');
		$("tr:odd",this).addClass('table-module-b2');
	}); 
}
function productSelectedCheck(){
	var size = $('.productAddBtn').size();
	var checkedSize = $('.productAddBtn:checked').size();
	if(size==checkedSize){
		$('.checkAll').attr('checked',true);
	}else{
		$('.checkAll').attr('checked',false);
	}
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
	$(".selectProductBtn").click(function(){
		$('#search-module').dialog("open");
	});
	$(".productAddBtn").live('click',function(){
		var productId = $(this).attr("id");
		var productSku = $(this).attr("sku");
		var productName = $(this).attr("title");
		if($(this).is(":checked")){
			if($('#product'+productId).size()==0){
				var html = '';
				html+='<tr id="product'+productId+'">';
				html+='<td>'+productSku+'</td>';
				html+='<td>'+productName+'</td>';
				html+='<td class=""><input class="inputbox inputMinbox op_quantity" type="text" name="op_quantity['+productId+']" value="1" size="8"><span class="red">*</span></td>';
				html+='<td><a href="javascript:;" class="deleteProductBtn"><{t}>delete<{/t}></a></td>';			
				html+='</tr>';

				$("#products").append(html);
			}				
		}else{
			$("#product"+productId).remove();
		}
		clazzInit();
		setTimeout('productSelectedCheck()',50);
	});
	$(".deleteProductBtn").live('click',function(){
		$(this).parent().parent().remove();
		clazzInit();
		countSku();//ruston0903
		/*$('.inputbox').each(function(i,v){
		    	math=math+parseInt(v.value)
	   	    });*/
	});
	$(".checkAll").live('click',function() {
		if ($(this).is(':checked')) {
			$(".productAddBtn").each(function(){
				$(this).attr('checked', true);
				var productId = $(this).attr("id");
				var productSku = $(this).attr("sku");
				var productName = $(this).attr("title");
				if($("#product"+productId).size()==0){
					var html = '';
					html+='<tr id="product'+productId+'">';
					html+='<td>'+productSku+'</td>';
					html+='<td>'+productName+'</td>';
					html+='<td><input class="inputbox inputMinbox" type="text" name="op_quantity['+productId+']" value="1" size="8"><span class="red">*</span></td>';
					html+='<td><a href="javascript:;" class="deleteProductBtn"><{t}>delete<{/t}></a></td>';			
					html+='</tr>';

					$("#products").append(html);
				}

			});
		} else {
			$(".productAddBtn").each(function(){
				$(this).attr('checked', false);
				var productId = $(this).attr("id");
				var productSku = $(this).attr("sku");
				var productName = $(this).attr("title");
				if($("#product"+productId).size()>0){
					$("#product"+productId).remove();
				}

			});
		}
		clazzInit();
	});

	$("#orderSubmitBtn").click(function(){
		var param = $("#orderForm").serialize();
		loadStart();
		$.ajax({
			type: "POST",
			url: "/order/order/create",
			data: param,
			dataType:'json',
			success: function(json){
				loadEnd('');
				var html = json.message;
				if(json.ask){
					//html+='<{t}>order_code<{/t}>:'+json.ref_id;
					if($.trim($('#ref_id').val())==''){
						//$('#refrence_no').val('');
						$('#products').html('');
					}
				}else{
					if(json.err){
						$.each(json.err,function(k,v){
							html+="<p>"+v+"</p>";
						})
					}
				}
				//alertTip(html);

				$('#create_feedback_div').html(html);
				$(".feedback_tips").show();
				setTimeout(function(){scrollTo(0,0);},500);
			}
		});
	});

	$('select').each(function(){
		var defVal = $(this).attr('default')||'';
		$(this).val(defVal);
	});

	//只有一个仓库，默认选择
	if($('.warehouse_code').size()==1){
		$('.warehouse_code').attr('checked',true);
	}
}) 
$(function(){
	countSku();//ruston0903
	$('#imCodeSearch').autocomplete({
		//source: "/product/product/get-by-keyword/limit/20",
		minLength: 2,
		delay:100,
		source:function(request, response) {
			//增加等待图标
			$("#imCodeSearch").addClass("autocomplete-loading");
			$("#imCodeSearch").autocomplete({delay: 100});
			var parameter = {};
			parameter["term"] = $("#imCodeSearch").val();
			$.ajax({
				type :"POST",
				url: "/product/product/get-by-keyword/limit/20",
				dataType: "json",
				data: parameter,
				success: function(json) {
					//关闭等待图标
					$("#imCodeSearch").removeClass("autocomplete-loading");
					var objItem = {};
					if(json != null){
						objItem = json;
					}
					response($.map(objItem, function(item) {
						/*
						 * 返回自定义的JSON对象
						 */
						 return item;
					}));
				}
			});
		},
		select: function( event, ui ) {
			$('#auto_product_id').val(ui.item.product_id);
			$('#auto_product_title').val(ui.item.product_title);
			$('#auto_product_sku').val(ui.item.product_sku);
			$('#auto_product_weight').val(ui.item.product_weight);
			productDeclaredValue=ui.item.product_declared_value ? ui.item.product_declared_value : 0;//ruston0924申报价值
			$('#productDeclaredValue').val(productDeclaredValue);//ruston0924申报价值
		},
		search: function( event, ui ) {
			$('#auto_product_id').val('');
			$('#auto_product_title').val('');
			$('#auto_product_sku').val('');
			$('#auto_product_weight').val('');
			$('#productDeclaredValue').val('');//ruston0924申报价值
		}
		,open: function() {
			$(this).removeClass("ui-corner-all").addClass("ui-corner-top");
		}
		,close: function() {
			$(this).removeClass("ui-corner-top").addClass("ui-corner-all");
		} 
	});

	$('#addItem').click(function(){
		var productId = $('#auto_product_id').val();
		var productSku = $('#auto_product_sku').val();
		var productName = $('#auto_product_title').val();
		var quantity = $('#imQuantity').val();
		if($('#imCodeSearch').val()==''){
			$('#imCodeSearch').focus();
			return;
		}
		if(productId==''){

			//根据sku搜索产品信息
			$.ajax({
				type: "POST",
				url: "/product/product/get-product-by-sku",
				data: {'sku':$('#imCodeSearch').val()},
				dataType:'json',
				async: false,
				success: function(json){			 				   
					if(json.ask){
						var product = json.product;
						productId = product['product_id'];
						productSku = product['product_sku'];
						productName = product['product_title'];
						productDeclaredValue = product['product_declared_value']?product['product_declared_value']:0;//ruston0924申报价值
					}else{

					}
				}
			});	


		}
		if(productId==''){
			alert('SKU 不存在');
			$('#imCodeSearch').focus();
			return;
		}
		if(quantity==''){
			$('#imQuantity').focus();
			return;
		}
		//已经选择
		if($('#product'+productId).size()==0){
			var html = '';
			html+='<tr id="product'+productId+'">';
			html+='<td>0</td>';
			html+='<td>'+productSku+'</td>';
			html+='<td>'+productName+'</td>';
			html+='<td><input id="op_quantity_'+productId+'" onblur="countSku('+productId+')" class="inputbox inputMinbox op_quantity" type="text" name="op_quantity['+productId+']" value="'+quantity+'" size="8"><span class="red">*</span></td>';
			html+='<td><input onblur="sumPrice('+productId+')" class="inputMinbox productDeclaredValue" type="text" name="productDeclaredPrice['+productId+']" value="'+productDeclaredValue+'" size="8" id="productDeclaredPrice_'+productId+'"><span class="red">*</span></td>';//ruston0924申报价值
			html+='<td><input class="inputMinbox" type="hidden" id="productDeclaredValue_'+productId+'" name="productDeclaredValue['+productId+']" value="'+(productDeclaredValue*quantity).toFixed(2)+'" size="8"><span id="inputMinbox_'+productId+'">'+(productDeclaredValue*quantity).toFixed(2)+'</span></td>';//ruston0924申报价值
			html+='<td><a href="javascript:;" class="deleteProductBtn"><{t}>delete<{/t}></a></td>';			
			html+='</tr>';	    	    			
			$("#products").append(html);
			countSku(productId);//ruston0903
			clazzInit();

		}else{
			if(window.confirm('SKU'+productSku+'已经存在，是否进行数量累加')){
				var qty_exist = $('#product'+productId+' .op_quantity').val();
				$('#product'+productId+' .op_quantity').val(parseInt(qty_exist)+parseInt(quantity));
				countSku(productId);//ruston0903
			}						
		}
		//初始化
		$('#fillInItem .input_text').val('');

	});
	function getSm(){
		var warehouse_id = $(':input[name="warehouse_code"]:checked').attr('warehouse_id');
		var country_id = $(':input[name="country"] option:selected').attr('country_id');
		if(!warehouse_id||country_id!=''){
			//return;
		}
		$(':input[name="courier"]').html('<option value=""><{t}>-select-<{/t}></option>');
		var param = {};
		param.warehouse_id = warehouse_id;
		param.country_id = country_id;
		$.ajax({
			type: "POST",
			url: "/order/order/get-courier",
			data: param,
			dataType:'json',
			success: function(json){
				var def = $(':input[name="courier"]').attr('default');
				var html = '';
				$.each(json,function(k,v){
					var selected = def==v.sm_code?'selected':'';
					var option = '<option value="'+v.sm_code+'" '+selected+'>'+v.sm_code+'</option>'
					$(':input[name="courier"]').append(option);
				})
			}
		});
	}
	$(':input[name="country"]').change(function(){
		getSm();
	});

	$(':input[name="warehouse_code"]').click(function(){
		getSm();
	});

});
function countSku(id){
	math=0;
	$('.inputbox').each(function(i,v){
		math=math+parseInt(v.value)
		$(this).parent().prev().prev().prev().html(i+1)
	});
	$('#quantity').html(math);
	sumPrice(id);
}
function sumPrice(id){
	if(id){
		var op_quantitys=$('#op_quantity_'+id).val()
		var productDeclaredPrices=$('#productDeclaredPrice_'+id).val()
		var re = /^[0-9]+\.?[0-9]{2}$/;
		if (!re.test(productDeclaredPrices)){
			alertTip('<span style="color:red">申报单价格式不正确,必须为数字或小数</span>')
			return;
		}
		$('#productDeclaredValue_'+id).val((op_quantitys*productDeclaredPrices).toFixed(2))
		$('#inputMinbox_'+id).html((op_quantitys*productDeclaredPrices).toFixed(2))
		$('#productDeclaredValue').val((op_quantitys*productDeclaredPrices).toFixed(2))
	}
}