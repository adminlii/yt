<link type="text/css" rel="stylesheet" href="/css/public/layout.css" />
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
.msg{color:red;padding:0 5px;}
.module-title{
	text-align: right;
}
-->
</style>
<script type="text/javascript">
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
			   url: "/order/transfer-order/create",
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
</script>
<script type="text/javascript">
	$(function(){
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
        	},
        	search: function( event, ui ) {
        		$('#auto_product_id').val('');
        	    $('#auto_product_title').val('');
        	    $('#auto_product_sku').val('');
        	    $('#auto_product_weight').val('');
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
			    html+='<td>'+productSku+'</td>';
			    html+='<td>'+productName+'</td>';
			    html+='<td><input class="inputbox inputMinbox op_quantity" type="text" name="op_quantity['+productId+']" value="'+quantity+'" size="8"><span class="red">*</span></td>';
			    html+='<td><a href="javascript:;" class="deleteProductBtn"><{t}>delete<{/t}></a></td>';			
			    html+='</tr>';	    	    			
    		    $("#products").append(html);
    		    clazzInit();

			}else{
				if(window.confirm('SKU'+productSku+'已经存在，是否进行数量累加')){
					var qty_exist = $('#product'+productId+' .op_quantity').val();
					$('#product'+productId+' .op_quantity').val(parseInt(qty_exist)+parseInt(quantity));
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
</script>
<div id='loading'>Loading...</div>
<div id="module-container">
	<div id='search-module' style='display: none;' title='Product List&nbsp;&nbsp;[each SKU separate with space or ","]'>
		<form class="submitReturnFalse" name="searchForm" id="searchForm">
			<div class="block ">
				<table cellspacing="3" cellpadding="3">
					<tbody>
						<tr>
							<th>SKUs:</th>
							<td>
								<input type="text" class="input_text" value="" name="product_sku" id='product_sku' size='35' placeholder='<{t}>multi_split_space<{/t}>'>
								&nbsp;
								<input type="checkbox" name="borrowType" value="borrow"><{t}>borrow<{/t}>
								<input type="submit" class="baseBtn searchProductBtn submitToSearch" value="Search">
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</form>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td width="100"><{t}>SKU<{/t}></td>
					<td><{t}>product_title<{/t}></td>
					<td width="65" class="ec-center">
						<input type="checkbox" class="checkAll">
					</td>
				</tr>
			</tbody>
			<tbody id="table-module-list-data" class='table-module-list-data'>
			</tbody>
		</table>
		<div class="pagination"></div>
	</div>
	<div id="module-table" style='clear: both;padding:0 45px;'>
		<div class="feedback_tips" >
			<div class="feedback_tips_text" >
				<p id="create_feedback_div">
					
				</p>
			</div>
			<div style="clear: both;"></div>
		</div>		
		<form method='POST' action='' onsubmit='return false;' id='orderForm'>
		    <h1 style="text-align:right;padding: 10px 0 0 0;"><span style='color:#E06B26;'><{t}>orders<{/t}></span></h1>
			<h2 style='margin-bottom:10px; border-bottom: 1px dashed #CCCCCC;'>1、源<{t}>warehouse_name<{/t}>
			</h2>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module" style='margin-top: 10px;'>
				<tbody class="table-module-list-data">
					<tr>
						<td style="padding-left: 30px;">
							<{foreach from=$warehouse item=w name=w}>
								<{if $w.warehouse_type==0||$w.warehouse_type=='0'}>
								<label style="cursor: pointer;margin: 0 5px;">
									<input type="radio" name='warehouse_code' warehouse_id='<{$w.warehouse_id}>' value="<{$w.warehouse_code}>" class='warehouse_code' <{if isset($order) && $order.warehouse_code eq $w.warehouse_code}>checked="checked"<{/if}> >
									<{$w.warehouse_code}> [<{$w.warehouse_desc}>]
								</label>
								<{/if}>
							<{/foreach}>
						</td>
					</tr>
				</tbody>
			</table>
			
			<h2 style='margin-bottom:10px; border-bottom: 1px dashed #CCCCCC;'>2、<{t}>目的仓库<{/t}>
			</h2>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module" style='margin-top: 10px;'>
				<tbody class="table-module-list-data">
					<tr>
						<td style="padding-left: 30px;">
							<{foreach from=$warehouse item=w name=w}>
								<{if $w.warehouse_type==0||$w.warehouse_type=='0'}>
								<label style="cursor: pointer;margin: 0 5px;">
									<input type="radio" name='to_warehouse_code' warehouse_id='<{$w.warehouse_id}>' value="<{$w.warehouse_code}>" <{if isset($order) && $order.to_warehouse_code eq $w.warehouse_code}>checked="checked"<{/if}> >
									<{$w.warehouse_code}> [<{$w.warehouse_desc}>]
								</label>
								<{/if}>
							<{/foreach}>
						</td>
					</tr>
				</tbody>
			</table>
			
			<h2 style='padding: 35px 0 0 0;margin-bottom:10px; border-bottom: 1px dashed #CCCCCC;'>3、<{t}>order_select_sku<{/t}></h2>
			<div style="width: 800px;min-width: 750px;">
			<div style='margin: 5px 0 5px 0;' id="fillInItem">
				<{t}>SKU<{/t}>：<input type="text" class="input_text" id="imCodeSearch" style="width:150px;margin-right: 10px;text-transform: uppercase;" placeholder="<{t}>like_search_before&after<{/t}>">
				<{t}>quantity<{/t}>：<input type="text" class="input_text" id="imQuantity" style="width:80px;margin-right: 10px;">
				<input type="button" value="<{t}>add<{/t}>" class="baseBtn" id="addItem" style="margin-right: 10px;">
				
				<input type="hidden" id="auto_product_id">
				<input type="hidden" id="auto_product_title">
				<input type="hidden" id="auto_product_sku">
				<input type="hidden" id="auto_product_weight">
				<a class="selectProductBtn" href="javascript:;"><{t}>select_product<{/t}></a>
			</div>
			<div style='clear: both;'></div>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
				<tbody>
					<tr class="table-module-title">
						<td width="150"><{t}>SKU<{/t}></td>
						<td><{t}>product_title<{/t}></td>
						<td width="120"><{t}>quantity<{/t}></td>
						<td width="100"><{t}>operation<{/t}></td>
					</tr>
				</tbody>
				<tbody class="table-module-list-data" id='products'>
					<{foreach from=$orderProduct item=product name=product}>
					<tr id="product<{$product.product_id}>" class="">
						<td><{$product.product_sku}></td>
						<td><{$product.product_title}></td>
						<td>
							<input type="text" size="8" value="<{$product.quantity}>" name="op_quantity[<{$product.product_id}>]" class="inputbox inputMinbox op_quantity">
							<span class="red">*</span>
						</td>
						<td>
							<a class="deleteProductBtn" href="javascript:;"><{t}>delete<{/t}></a>
						</td>
					</tr>
					<{/foreach}>
				</tbody>
			</table>
			</div>
			
			<h2 style='padding: 35px 0 0 0;margin-bottom:10px; border-bottom: 1px dashed #CCCCCC;'>4、<{t}>else_info<{/t}></h2>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module" style='margin-top: 10px;'>
				<tbody class="table-module-list-data">
					<tr class=''>
						<td class="module-title"><{t}>shipping_method<{/t}>：</td>
						<td colspan="1">
							<select class='input_select' name='courier' default='<{if isset($order)}><{$order.shipping_method}><{/if}>'>					
								<option value=''><{t}>-select-<{/t}></option>
								<{foreach from=$shippingMethods item=c name=c}>
								<option value='<{$c.sm_code}>'><{$c.sm_code}> [<{$c.sm_name_cn}><!--  -- <{$c.sm_name}> -->]</option>
								<{/foreach}>
							</select>
							<span class="msg">*</span>
						</td>
					</tr>
					<tr class=''>
						<td width="150" class="module-title"><{t}>order_instructions<{/t}>：</td>
						<td >
							<textarea cols="80" rows="2" name="order_desc" class='input_text' style='width: 450px; height: 55px;'><{if isset($order)}><{$order.order_desc}><{/if}></textarea>
							<br/><{t}>order_remark_tip<{/t}>
						</td>
					</tr>
				</tbody>
			</table>
			
			<{if isset($order)&&$order.two_code}>
				<input type="hidden" name='refId' value='<{$order.two_code}>' id='ref_id'>
			<{/if}>
			
			<div style="width: 100%;text-align: center;margin-top: 25px;padding-top:5px; border-top: 1px dashed #CCCCCC;">
				<input type='button' value='<{t}>submit<{/t}>' id='orderSubmitBtn' class='baseBtn submitBtn' style="width: 80px;"/>
			</div>
		</form>
	</div>
</div>