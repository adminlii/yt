<script type="text/javascript">
<{include file='fee/js/fee/fee_info_list.js'}>
</script>
<div id="module-container">
	<{include file='fee/views/fee/common.tpl'}>
	<div id="search-module">
		<form id="searchForm" name="searchForm" method='POST' action='' onsubmit='return false;'>
			<div class="search-module-condition">
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
					<td width="125">收款日期</td>
					<td width="80"><{t}>类型<{/t}></td>
					<td width="80"><{t}>收据号码<{/t}></td>
					<td width="80"><{t}>金额<{/t}>(CNY)</td>
					<td width="80"><{t}>收款前余额<{/t}></td>
					<td width="80"><{t}>收款后余额<{/t}></td>
					<td width="80"><{t}>摘要备注<{/t}></td>
				</tr>
			</tbody>
			<tbody id="table-module-list-data">
				<tr class="table-module-b1">
					<td width="125">收款日期</td>
					<td width="80"><{t}>类型<{/t}></td>
					<td width="80"><{t}>收据号码<{/t}></td>
					<td width="80"><{t}>金额<{/t}>(CNY)</td>
					<td width="80"><{t}>收款前余额<{/t}></td>
					<td width="80"><{t}>收款后余额<{/t}></td>
					<td width="80"><{t}>摘要备注<{/t}></td>
				</tr>
				<!--  -->
			</tbody>
		</table>
	</div>
	<div class="pagination"></div>
</div>