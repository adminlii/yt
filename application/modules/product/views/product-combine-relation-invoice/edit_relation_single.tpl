<style>
<!--
#invoice_div .invoice_code {
	width: 80px;
}

#invoice_div .qty {
	width: 40px;
}
-->
</style>
<div id='invoice_div' style='' title='批量添加'>
	<form action="" id='invoice_form' onsubmit='return false;'>
	    <div style='height:30px;'>
	    <{if $product_sku}>
		<input type='hidden' name='product_sku' value='<{$product_sku}>' />
		<input type='hidden' name='user_account' value='<{$user_account}>' />
		平台SKU:<{$product_sku}> 账号:<{$pUser.platform_user_name}>
		<{else}>		
		平台SKU:<input type='text' name='product_sku' value='<{$product_sku}>' class='input_text'/>
		账号:
		<select name='user_account'>
		    <option value=''>所有账户</option>		
		    <{foreach from=$pUsers name=o item=o}>
		    <option value='<{$o.user_account}>'><{$o.platform}> [<{$o.platform_user_name}>] [<{$o.user_account}>]</option>
		    <{/foreach}>
		</select>
		<{/if}>
	    </div>
		<table cellspacing="0" cellpadding="0" width="100%" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td width="140">品名代码</td>
					<td width="80">数量</td>
					<td><{t}>品名<{/t}></td>
					<td width="80"><{t}>单价<{/t}></td>
					<td width="80"><{t}>海关协制编号<{/t}></td>
					<td width="80"><{t}>配货信息<{/t}></td>
					<td width="80"><{t}>销售地址<{/t}></td>
					<td width="60"><{t}>操作<{/t}></td>
				</tr>
			</tbody>
			<tbody id="products" class="table-module-list-data">
				<{if $relations}> <{foreach from=$relations name=o item=o}>
				<tr class="table-module-b1">
					<td>
						<input type="text" value="<{$o.pcr_product_sku}>" name="invoice[pcr_product_sku][]" class="input_text invoice_code web_require">
						<span class="msg">*</span>
					</td>
					<td>
						<input type="text" value="<{$o.pcr_quantity}>" name="invoice[pcr_quantity][]" class="input_text qty web_require">
						<span class="msg">*</span>
					</td>
					<td class='invoice_enname invoice_text'><{$o.invoice_info.invoice_enname}></td>
					<td class='invoice_unitcharge invoice_text'><{$o.invoice_info.invoice_unitcharge}></td>
					<td class='hs_code invoice_text'><{$o.invoice_info.hs_code}></td>
					<td class='invoice_note invoice_text'><{$o.invoice_info.invoice_note}></td>
					<td class='invoice_url invoice_text'><{$o.invoice_info.invoice_url}></td>
					<td>
						<a href='javascript:;' class='delInvoiceBtn'><{t}>delete<{/t}></a>
					</td>
				</tr>
				<{/foreach}> <{else}>
				<tr class="table-module-b1">
					<td>
						<input type="text" value="" name="invoice[pcr_product_sku][]" class="input_text invoice_code web_require">
						<span class="msg">*</span>
					</td>
					<td>
						<input type="text" value="1" name="invoice[pcr_quantity][]" class="input_text qty web_require">
						<span class="msg">*</span>
					</td>
					<td class='invoice_enname invoice_text'></td>
					<td class='invoice_unitcharge invoice_text'></td>
					<td class='hs_code invoice_text'></td>
					<td class='invoice_note invoice_text'></td>
					<td class='invoice_url invoice_text'></td>
					<td>
						<a href='javascript:;' class='delInvoiceBtn'><{t}>delete<{/t}></a>
					</td>
				</tr>
				<{/if}>
			</tbody>
		</table>
	</form>
	<div>
		<a href='javascript:;' style='font-weight: normal; font-size: 12px; line-height: 35px;' class='addInvoiceBtn'><{t}>点击添加<{/t}></a>
	</div>
</div>