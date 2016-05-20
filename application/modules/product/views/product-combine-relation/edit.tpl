<div id='editWrap' title='<{t}>Editing_SKU_relations<{/t}>'><!-- 编辑SKU关系 -->
	<form onsubmit="return false;" id="editRelationForm">
		<table cellspacing="0" cellpadding="0" border="0" class="dialog-module">
			<tbody>
				<tr>
					<td class="dialog-module-title" width='120'><{t}>Platform_Sales_SKU<{/t}>:</td>
					<td>
						<input type="text" class="input_text" id="product_sku" name="product_sku" value='<{$product_sku}>' readonly style='background:#ddd;'>
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>platform_account<{/t}></td>
					<td>
						<input type="text" class="input_text" id="user_account" name="user_account" value='<{$acc}>' readonly style='background:#ddd;'>
						
					</td>
				</tr>
			</tbody>
		</table>
		<div id='pcr_wrap' style='border-top:1px solid #ccc;margin-top:5px;'>
		<{foreach from=$data name=o item=o key=k}>
		<table cellspacing="0" cellpadding="0" border="0" class="dialog-module" id='pcr_table_<{$k}>' style='padding:2px 10px 2px 0;margin-top:5px;'>
			<tbody>
				<tr>
					<td class="dialog-module-title"  width='120'><{t}>corresponding_sku<{/t}><{$k+1}>:</td>
					<td>
						<input type="text" class="input_text" name="pcr_product_sku[]" value='<{$o.pcr_product_sku}>'>
						<span class="msg">*</span>
						<a href='javascript:;' class="delPcrSku" key='<{$k}>' style='color:red;' title='<{t}><{/t}>SKU'>X</a>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>quantity<{/t}></td>
					<td>
						<input type="text" class="input_text" name="pcr_quantity[]" value='<{$o.pcr_quantity}>'>
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>purchase_price<{/t}><!-- 采购单价 --></td>
					<td>
						<input type="text" class="input_text" name="pcr_pu_price[]" value='<{$o.pcr_pu_price}>'>
						<span class="msg">*</span>
					</td>
				</tr>
			</tbody>
		</table>
		<{/foreach}>
		</div>
	</form>
</div>