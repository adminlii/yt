
<script type="text/javascript">
<{include file='fee/js/fee/fee_unpaid_list.js'}>
</script>
<div id="module-container">
	<{include file='fee/views/fee/common.tpl'}>
	<div id="search-module">
		<form id="searchForm" name="searchForm" method='POST' action='' onsubmit='return false;'>
			<div class="search-module-condition">			
			    <span class="">运输方式 : </span>
				<select style="" class="input_text_select input_select product_code_select" name="product_code" id="product_code">
					<option value=''><{t}>-all-<{/t}></option>
					<{foreach from=$productKind name=ob item=ob}>
					<option value='<{$ob.product_code}>'><{$ob.product_code}>[<{$ob.product_enname}> <{$ob.product_cnname}>]</option>
					<{/foreach}>
				</select>
				<span class=""> 日期 : </span>
				<input type="text" name="time_start" class="input_text datepicker time_start" readonly style='background-color: #eee;' value='<{$start}>' />
				~
				<input type="text" name="time_end" class="input_text datepicker time_end" readonly style='background-color: #eee;' value='<{$end}>' />
				&nbsp;&nbsp;
				<a href='javascript:;' class='clearDatepickerBtn'>清空时间</a>
				<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" />
			</div>
		</form>
	</div>
	<div id="module-table">
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td width="125"><{t}>费用发生日期<{/t}></td>
					<td width="80"><{t}>参考号<{/t}></td>
					<td width="80"><{t}>跟踪号<{/t}></td>
					<td width="80"><{t}>运输方式<{/t}></td>
					<td width="80"><{t}>目的地<{/t}></td>
					<td width="80"><{t}>计费重<{/t}></td>
					<td width="80"><{t}>总金额<{/t}></td>
					<td width="80"><{t}>未付金额<{/t}></td>
					<td width="80"><{t}>操作<{/t}></td>
				</tr>
			</tbody>
			<tbody id="table-module-list-data">
				<!-- 
				<tr class="table-module-b1">
					<td width="125"><{t}>费用发生日期<{/t}></td>
					<td width="80"><{t}>参考号<{/t}></td>
					<td width="80"><{t}>跟踪号<{/t}></td>
					<td width="80"><{t}>运输方式<{/t}></td>
					<td width="80"><{t}>目的地<{/t}></td>
					<td width="80"><{t}>计费重<{/t}></td>
					<td width="80"><{t}>总金额<{/t}></td>
					<td width="80"><{t}>未付金额<{/t}></td>
					<td width="80"><{t}>操作<{/t}></td>
				</tr>
				 -->
			</tbody>
		</table>
	</div>
	<div class="pagination"></div>
</div>