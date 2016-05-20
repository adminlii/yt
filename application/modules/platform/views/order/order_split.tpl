<link type="text/css" rel="stylesheet" href="/css/public/layout.css" />
<style>
<!--
#module-container .guild h2 {
	cursor: pointer;
	background: none repeat scroll 0% 0% transparent;
}

#module-container .guild h2.act {
	cursor: pointer;
	background: none repeat scroll 0% 0% #fff;
}

#search_more_div {
	display: none;
}

#search_more_div div {
	padding-top: 8px;
}

.input_text {
	width: 100px;
}

.fast_link {
	padding: 8px 10px;
}

.fast_link a {
	margin: 5px 8px 5px 0;
}

.order_detail {
	width: 100%;
	border-collapse: collapse;
}

.order_detail td {
	border: 1px solid #ccc;
}

#opDiv .baseBtn {
	margin-right: 10px;
}

.table-module th {
	border-bottom: 1px solid #FFFFFF;
	border-right: 1px solid #FFFFFF;
	line-height: 20px;
	padding: 5px;
}

.table-module {
	border-collapse: collapse;
}

.table-module-list-data td {
	text-align: left;
	border: 1px solid #ccc;
}

.table-module-list-data th {
	text-align: right;
	border: 1px solid #ccc;
}

.input_text {
	display: none;
}

#orderSubmitBtn {
	display: none
}

.btnRight {
	float: right;
	margin-right: 5px;
}
-->
</style>
<script type="text/javascript">
var num = 0;
var orderProductJson = <{$orderProductJson}>;

$(function(){
	$('#addProductLine').click(function(){
	    if($('.itemProduct:checked').size()==0){
	        alertTip('请选择需要拆分的产品');
	        return;
	    }
	    num++;
	    var html = '';
	    var arr = {};
	    $('.itemProduct:checked').each(function(k,v){
		    var item_id=$(this).val();
		    var sku=$.trim($(".sku",$(this).parent().parent()).text());
		    var tnx=$.trim($(".tnx",$(this).parent().parent()).text());
		    var title = $.trim($(".title",$(this).parent().parent()).text());

		    var key = item_id+'_'+tnx+'_'+sku;
		    if(arr[key]){
    	    	arr[key].qty++;
    	    }else{
    		    arr[key] = {'sku':sku,'title':title,'item_id':item_id,'qty':1,'tnx':tnx};
    	    }
		    /**/
		    //arr[k] = {'sku':sku,'title':title,'item_id':item_id,'qty':1};
	        $(this).parent().parent().remove();
	    });
	    
	    $.each(arr,function(k,v){
	    	html+='<tr class="table-module-b1">';
	    	html+='<td>';
    		html+='<img width="75" height="75" src="/product/product/eb-img/item_id/'+v.item_id+'">';
	    	html+='</td>';
	    	html+='<td class="sku">';
    		html+='<span class="span_text">'+v.sku+'</span>';
	    	html+='</td>';
	    	html+='<td class="title">';
    		html+='<span>'+v.title+'</span>';
	    	html+='</td>';
	    	html+='<td>';
    		html+='<span class="span_text">'+v.qty+'</span>';
    		html+='<input type="hidden" name="op['+num+'_'+v.item_id+'_'+v.tnx+'_'+v.sku+']" value="'+v.qty+'"/>';
	    	html+='</td>';
	        html+='</tr>';
	    }) 
	    
	    $('#template .inner .orderInfo').html(html);
	    $('#template .inner .num').html(num);
	    $('#order_product_div').append($('#template .inner').clone());
	});

	$('#splitProductLineFast').click(function(){
		$(this).attr('disabled',true);
		$('#order_product_div').html('');
	    var numm = 0;
	    $('#table-module-list-data').html('');
		$.each(orderProductJson,function(index,v){
			numm++;
			var html = '';
		    
		    	html+='<tr class="table-module-b1">';
		    	html+='<td>';
	    		html+='<img width="75" height="75" src="/product/product/eb-img/item_id/'+v.op_ref_item_id+'">';
		    	html+='</td>';
		    	html+='<td class="sku">';
	    		html+='<span class="span_text">'+v.product_sku+'</span>';
		    	html+='</td>';
		    	html+='<td class="title">';
	    		html+='<span>'+v.product_title+'</span>';
		    	html+='</td>';
		    	html+='<td>';
	    		html+='<span class="span_text">'+v.op_quantity+'</span>';
	    		html+='<input type="hidden" name="op['+numm+'_'+v.op_ref_item_id+'_'+v.op_ref_tnx+'_'+v.product_sku+']" value="'+v.op_quantity+'"/>';
		    	html+='</td>';
		        html+='</tr>';
		    
		    
		    $('#template .inner .orderInfo').html(html);
		    $('#template .inner .num').html(numm);
		    $('#order_product_div').append($('#template .inner').clone());
		})


	});
	
	$('.cancelBtn').click(function(){
		if(!window.confirm('重新拆分？')){
		    return;
		}		
	    window.location.href = window.location.href;
	});
	$('.submitBtn').click(function(){
		if($('.itemProduct').size()>0){
		    alertTip('必须将所有订单产品拆分');
		    return false;
		}
		if(!window.confirm('确认拆分订单？')){
		    return;
		}	
	    var param = $('#order_product_form').serialize();
	    alertTip('订单拆分中...');
	    $.ajax({
			type : "POST",
			url : "/order/order/split",
			data : param,
			dataType : 'json',
			success : function(json) {				
				$('#dialog-auto-alert-tip').html(json.message);
				if(json.ask){
				    parent.reLoadWhenClose  = 1;
				}
			}
		});	    
	});
});
</script>
<div id="module-container">
	<div id="module-table">
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td colspan='8'>
						订单基本信息
					</td>
				</tr>
			</tbody>
		</table>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
			<tbody class="table-module-list-data orderInfo">
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='100'>ebay订单编号</th>
					<td><{$order.refrence_no_platform}></td>
					<th width='100'>买家账号</th>
					<td><{$order.buyer_id}></td>
					<th width='100'>卖家账号</th>
					<td><{$order.user_account}></td>
					<th width='100'>目的国家</th>
					<td>
						<span class='span_text'><{$order.address.Country}>|<{$order.address.CountryName}></span>
					</td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th>订单金额</th>
					<td><{$order.amountpaid}></td>
					<th>运费</th>
					<td>
						<span class='span_text'><{$order.shippingservicecost}></span>
					</td>
					<th>paypal手续费</th>
					<td>
						<span class='span_text'><{$order.feeorcreditamount}></span>
					</td>
					<th>调整金额</th>
					<td>
						<span class='span_text'><{$order.adjustmentamount}></span>
					</td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th>省份/州</th>
					<td>
						<span class='span_text'><{$order.address.StateOrProvince}></span>
						<input type='text' class='input_text input_change' name='StateOrProvince' value='<{$order.address.StateOrProvince}>' />
						<input type='hidden' class='' name='ShippingAddress_Id' value='<{$order.address.ShippingAddress_Id}>' />
					</td>
					<th>城市</th>
					<td>
						<span class='span_text'><{$order.address.CityName}></span>
						<input type='text' class='input_text input_change' name='CityName' value='<{$order.address.CityName}>' />
					</td>
					<th>地址</th>
					<td>
						<span class='span_text'><{$order.address.Street1}></span>
						<input type='text' class='input_text input_change' name='Street1' value='<{$order.address.Street1}>' />
						<span class='span_text'><{$order.address.Street2}></span>
						<input type='text' class='input_text input_change' name='Street2' value='<{$order.address.Street2}>' />
					</td>
					<th>收件人</th>
					<td>
						<span class='span_text'><{$order.address.Name}></span>
						<input type='text' class='input_text input_change' name='Name' value='<{$order.address.Name}>' />
					</td>
				</tr>
				<tr class="table-module-b2" style='height: 35px;'>
					<th>邮编</th>
					<td>
						<span class='span_text'><{$order.address.PostalCode}></span>
						<input type='text' class='input_text input_change' name='PostalCode' value='<{$order.address.PostalCode}>' />
					</td>
					<th>站点</th>
					<td><{$order.site}></td>
					<th>发货仓库</th>
					<td><{if isset($order.warehouse_name)}><{$order.warehouse_name}><{else}>未分配发货仓库<{/if}></td>
					<th>重量</th>
					<td><{if isset($order.order_weight)}><{$order.order_weight}><{else}>未称重<{/if}></td>
				</tr>
				<tr class="table-module-b2" style='height: 35px;'>
					<th>eBay创建时间</th>
					<td><{$order.date_create_platform}></td>
					<th>eBay付款时间</th>
					<td><{$order.date_paid_platform}></td>
					<th>eBay运输时间</th>
					<td><{$order.shippedtime}></td>
					<th>订单eBay状态</th>
					<td><{if isset($order.OrderStatus)}><{$order.OrderStatus}><{else}>Completed<{/if}></td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th>订单备注</th>
					<td colspan='7'><{$order.order_desc}></td>
				</tr>
			</tbody>
		</table>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" style='margin-top: 10px;' class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td>图片</td>
					<td>SKU</td>
					<!-- 
						<td>仓库SKU</td>
						-->
					<td>产品Title</td>
					<td>数量</td>
					<td>
						<input type="button" id="addProductLine" value="将选择的产品作为一个单独的订单">
						<input type="button" id="splitProductLineFast" value="快速拆单">
					</td>
				</tr>
			</tbody>
			<tbody id="table-module-list-data" class='orderInfo'>
				<{foreach from=$order.product item=op name=op key=k}>
				<tr class='table-module-b1 item_<{$op.op_ref_item_id}>'>
					<td>
						<img width="75" height="75" src="/product/product/eb-img/item_id/<{$op.op_ref_item_id}>">
					</td>
					<td class='sku'>
						<span class='span_text'><{$op.product_sku}></span>
					</td>
					<td class='title'>
						<span><{$op.product_title}></span>
					</td>
					<td>
						<span class='span_text'><{$op.op_quantity}></span>
					</td>
					<td>
						<input type='checkbox' class='itemProduct' value='<{$op.op_ref_item_id}>'>
						<span class='tnx' style='display:none;'><{$op.op_ref_tnx}></span>
					</td>
					<!-- -->
				</tr>
				<{/foreach}>
			</tbody>
		</table>
		<form id='order_product_form'  action='/order/order/split' method='post'  onsubmit='return false;' style='padding:10px 5px;border:1px solid #ccc;'>
    		<div id='order_product_div'>
    		
    		</div>
    		
			 <input type='hidden' name='orderId' value='<{$order.order_id}>' />
    		 <input type="button" value="确定" class="submitBtn baseBtn">
    		 <input type="button" value="重新拆分" class="cancelBtn baseBtn">
		</form>
		
		<div id='template' style='display:none;'>
		    <div class='inner'>
    		    <h2>拆分后订单<span class='num'></span></h2>
        		<table width="100%" cellspacing="0" cellpadding="0" border="0" style='margin-top: 10px;' class="table-module">
        			<tbody>
        				<tr class="table-module-title">
        					<td>图片</td>
        					<td>SKU</td>
        					<td>产品Title</td>
        					<td>数量</td>
        				</tr>
        			</tbody>
        			<tbody class='orderInfo'>
        				
        			</tbody>
        		</table>		    
		    </div>
		</div>
	</div>
</div>