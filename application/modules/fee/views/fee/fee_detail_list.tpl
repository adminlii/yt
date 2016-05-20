
<script type="text/javascript">
<{include file='fee/js/fee/fee_detail_list.js'}>
</script>
<div id="module-container">
	<{include file='fee/views/fee/common.tpl'}>
	<div id="search-module">
		<form id="searchForm" name="searchForm" method='POST' action='' onsubmit='return false;'>
			<div class="search-module-condition">
			    <span class="">运单 : </span>
				<input type="text" name="shipper_hawbcode" class="input_text keyToSearch shipper_hawbcode" />
				<span class="">到货总单 : </span>
				<input type="text" name="arrivalbatch_code" class="input_text keyToSearch arrivalbatch_code" />
				<span class=""> 日期 : </span>
				<input type="text" name="time_start" class="input_text datepicker time_start" readonly style='background-color: #eee;' value='<{$start}>' />
				~
				<input type="text" name="time_end" class="input_text datepicker time_end" readonly style='background-color: #eee;' value='<{$end}>' />
				&nbsp;&nbsp;
				<a href='javascript:;' class='clearDatepickerBtn'>清空时间</a>
				<input type="button" value="<{t}>search<{/t}>" class="baseBtn " id='submitToSearch' />
				&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="button" value="<{t}>export<{/t}>" class="baseBtn exportBtn"/>
			</div>
		</form>
	</div>
	<div id="module-table">
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td width="125"><{t}>到货日期<{/t}></td>
					<td width="180"><{t}>客户单号<{/t}></td>
					<td width="100"><{t}>跟踪单号<{/t}></td>
					<td width="100"><{t}>销售产品<{/t}></td>
					<td width="80"><{t}>货物类型<{/t}></td>
					<td width="80"><{t}>目的国家<{/t}></td>
					<td width="80"><{t}>计费重<{/t}></td>
					<td width="80"><{t}>运费<{/t}></td>
					<td width="80"><{t}>燃油费<{/t}></td>
					<td width="80"><{t}>挂号费<{/t}></td>
					<td width="80"><{t}>其他<{/t}></td>
					<td width="80"><{t}>总费用<{/t}></td>
				</tr>
			</tbody>
			<tbody id="table-module-list-data">
				<!-- 
				<tr class="table-module-b1">					
					<td width="125"><{t}>到货日期<{/t}></td>
					<td width="180"><{t}>客户单号<{/t}></td>
					<td width="100"><{t}>跟踪单号<{/t}></td>
					<td width="100"><{t}>销售产品<{/t}>(CNY)</td>
					<td width="80"><{t}>货物类型<{/t}></td>
					<td width="80"><{t}>目的国家<{/t}></td>
					<td width="80"><{t}>计费重<{/t}></td>
					<td width="80"><{t}>运费<{/t}></td>
					<td width="80"><{t}>燃油费<{/t}></td>
					<td width="80"><{t}>挂号费<{/t}></td>
					<td width="80"><{t}>其他<{/t}></td>
					<td width="80"><{t}>总费用<{/t}></td>
				</tr>
				 -->
			</tbody>
		</table>
	</div>
	<div class="pagination"></div>
</div>

<form id="exportForm" action="/fee/fee/fee-detail-list/ac/export" target='_blank' style='display: none;' method='POST'>
</form>