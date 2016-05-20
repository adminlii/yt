<style>
#products .input_text {
	width: 75%;
}

#edit_products .input_text {
	width: 75%;
}
</style>
<script type="text/javascript">
<{include file='shipment/js/arrival/arrival_list.js'}>
</script>
<div id="module-container">
	<div id="search-module">
		<form id="searchForm" name="searchForm">			
			<!-- 产品代码 -->
			<div class="search-module-condition" name="purchase_AdvancedSearch">
				<span class=""> 到货总单 : </span>
				<input type="text" name="arrivalbatch_code" class="input_text keyToSearch" />
				<{$count = csi_shipperchannel|count}>
				<{if $csi_shipperchannel&&$count>1}>
				<span class=""> 子账号 : </span>
				<select style="" class="input_text_select input_select" name="customer_channelid">
					<option value=''><{t}>-all-<{/t}></option>
					<{foreach from=$csi_shipperchannel name=ob item=ob}>
					<option value='<{$ob.customer_channelid}>'><{$ob.customer_channelcode}>[<{$ob.customer_channelname}>]</option>
					<{/foreach}>
				</select>
				<{/if}>
				<span class=""> 到货日期 : </span>
				<input type="text" name="arrival_start" class="input_text datepicker"  value='<{$start}>'/>
				~
				<input type="text" name="arrival_end" class="input_text datepicker"  value='<{$end}>'/>
				<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" />
			</div>
		</form>
	</div>
	<div id="module-table">
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td width="152">到货日期</td>
					<td width="80"><{t}>子账号<{/t}></td>
					<td width="200"><{t}>到货总单<{/t}></td>
					<td width="80"><{t}>票数<{/t}></td>
					<td><{t}>操作<{/t}></td>
				</tr>
			</tbody>
			<tbody id="table-module-list-data">
				<!-- 
				<tr class="table-module-b1">
					<td width="80">到货日期</td>
					<td><{t}>子账号<{/t}></td>
					<td width="80"><{t}>到货总单<{/t}></td>
					<td width="80"><{t}>票数<{/t}>($)</td>
					<td width="60"><{t}>操作<{/t}></td>
				</tr>
				 -->
			</tbody>
		</table>
	</div>
	<div class="pagination"></div>
</div>