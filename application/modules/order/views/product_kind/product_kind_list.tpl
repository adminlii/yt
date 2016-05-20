<table cellspacing="0" cellpadding="0" border="0" width="100%" class="table-module">
	<tbody>
		<tr class="table-module-title">
			<td width="100">产品代码</td>
			<td width="100">中文名</td>
			<td width="100">英文名</td>
			<td>网站备注</td>
		</tr>
	</tbody>
	<tbody class="table-module-list-data" id="products">
	    <{foreach from=$productKind item=c name=c}>								
		<tr class="table-module-b1">
			<td><{$c.product_code}></td>
			<td><{$c.product_cnname}></td>
			<td><{$c.product_enname}></td>
			<td><{$c.product_note}></td>
		</tr>
		<{/foreach}>
	</tbody>
</table>