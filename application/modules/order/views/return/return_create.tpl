<script type="text/javascript">
<!--
$(function(){
	$("#orderSubmitBtn").click(function(){
		var param = $("#orderForm").serialize();
		loadStart();
		$.ajax({
		   type: "POST",
		   url: "/order/return/create-return",
		   data: param,
		   dataType:'json',
		   success: function(json){
			   loadEnd('');
			   var html = json.message;
			   if(json.ask){
				   
			   }else{
				   
			   }
			   alertTip(html,600);
		   }
		});
	});
	$(".keyToSearch").keyup(function (e) {
        var key = e.which;
        if (key == 13) {
            try {
                submitSearch();
            } catch (s) {
                alertTip(s);
            }
        }
    });
	
})
//-->
</script>
<div id="module-container">
	<div id="module-table" style='clear: both; padding: 0 20px;'>
		<div class="feedback_tips">
			<div class="feedback_tips_text">
				<p id="create_feedback_div"></p>
			</div>
			<div style="clear: both;"></div>
		</div>
		<form method='POST' action='' onsubmit='return false;' id='orderForm'>
			<h2 style='padding: 20px 0 0 0; margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC;'>1、<{t}>return_info<{/t}></h2>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module" style='margin-top: 10px;'>
				<tbody class="table-module-list-data">
					<tr class=''>
						<td width="150" class="module-title"><{t}>order_code<{/t}>：</td>
						<td>
						    <{$order.refrence_no_platform}>
							<input type="hidden"   name="order_code" value="<{$order.refrence_no_platform}>" class="inputbox inputMinbox op_quantity">
						</td>
						<td width="150" class="module-title"><{t}>ref_no<{/t}>：</td>
						<td>
							<input type="text"  name="ref_no" value="" class="inputbox inputMinbox op_quantity">
							
						</td>
						<td width="150" class="module-title"><{t}>tracking_no<{/t}>：</td>
						<td>
							<input type="text" name="tracking_no" value="" class="inputbox inputMinbox op_quantity">
							
						</td>
					</tr>
					<tr class=''>
						<td width="150" class="module-title"><{t}>deal_method<{/t}>：</td>
						<td colspan='5'>
							<select style="width: 150px;" class="input_text2" id="ro_process_type" name="ro_process_type">
							    <{foreach from=$dealMethods name=o item=o key=k}>
								<option value="<{$k}>"><{$o}></option>
								<{/foreach}>
							</select>
							<span class="red">*</span>
						</td>
					</tr>
					<tr class=''>
						<td width="150" class="module-title"><{t}>return_reason<{/t}>：</td>
						<td colspan='5'>
							<textarea cols="80" rows="2" name="ro_desc" class='input_text' style='width: 450px; height: 55px;'></textarea>
							<span class="red">*</span>
						</td>
					</tr>
				</tbody>
			</table>
			<h2 style='padding: 20px 0 0 0; margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC;'>2、<{t}>product_info<{/t}></h2>
			<div style="width:100%">
				<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
					<tbody>
						<tr class="table-module-title">
							<td width="150"><{t}>SKU<{/t}></td>
							<td><{t}>product_title<{/t}></td>
							<td width="120"><{t}>quantity<{/t}></td>
							<td width="100"><{t}>return_quantity<{/t}></td>
							<td width="100"><{t}>value_added<{/t}></td>
						</tr>
					</tbody>
					<tbody class="table-module-list-data" id='products'>
						<{foreach from=$orderProduct item=product name=product}>
						<tr id="product<{$product.product_id}>" class="">
							<td><{$product.product_sku}></td>
							<td><{$product.product_title}></td>
							<td><{$product.op_quantity}></td>
							<td>
								<input type="text" size="8" value="" name="op_quantity[<{$product.product_id}>]" class="inputbox inputMinbox op_quantity">
								<span class="red">*</span>
							</td>
							<td>
								<select class="input_select package_type" title="" name="value_added_type[<{$product.product_id}>]" default='<{$product.value_added_type}>'>
								<option value=""><{t}>pleaseSelected<{/t}></option>
								<{foreach from=$valueAddedType key=k item=v}>
									<{if $v.vat_business_type == 0 || $v.vat_business_type == 3}>
									<option value="<{$k}>"><{$v.vat_name_cn}></option>
									<{/if}>
								<{/foreach}>
								</select>
							</td>
						</tr>
						<{/foreach}>
					</tbody>
				</table>
			</div>
			<div style="width: 100%; text-align: center; margin-top: 25px; padding-top: 5px; border-top: 1px dashed #CCCCCC;">
				<input type='button' value='<{t}>submit<{/t}>' id='orderSubmitBtn' class='baseBtn submitBtn' style="width: 80px;" />
			</div>
		</form>
	</div>
</div>