<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
	<tbody>
		<tr class="table-module-title">
			<td width="125"><{t}>参考号<{/t}></td>
			<td width="125"><{t}>运单号<{/t}></td>
			<td width="80"><{t}>费用类型<{/t}></td>
			<td width="80"><{t}>总金额<{/t}></td>
			<td width="80"><{t}>欠费金额<{/t}></td>
		</tr>
	</tbody>
	<tbody class="table-module-list-data">
	    <{if $data}>
	    <{foreach from=$data item=o name=o key=k}>
		<tr class="table-module-b1">
			<td><{$o.shipper_hawbcode}></td>
			<td><{$o.serve_hawbcode}></td>
			<td><{$o.fk_name}></td>
			<td><{$o.ic_amount}></td>
			<td><{$o.ic_amounts}></td> 
		</tr>
		<{/foreach}>
		<{else}>
		<tr class="table-module-b1">
			<td colspan='6'>没有数据</td>
			
		</tr>
		<{/if}>
	</tbody>
</table>