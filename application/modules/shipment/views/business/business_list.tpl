<style>
#products .input_text {
	width: 75%;
}

#edit_products .input_text {
	width: 75%;
}
</style>
<script type="text/javascript">
<{include file='shipment/js/business/business_list.js'}>
</script>
<div id="module-container">
	<div id="search-module">
		<form id="searchForm" name="searchForm" method='POST' action='' onsubmit='return false;'>			
			<div class="search-module-condition">
				<span class=""> 单号 : </span>
				<input type="text" name="shipper_hawbcode" class="input_text keyToSearch" />
				<span class=""> 到货总单 : </span>
				<input type="text" name="arrivalbatch_code" class="input_text keyToSearch arrivalbatch_code" />
				<span class=""> 到货日期 : </span>
				<input type="text" name="arrival_start" class="input_text datepicker arrival_start"  readonly style='background-color:#eee;' value='<{$start}>'/>
				~
				<input type="text" name="arrival_end" class="input_text datepicker arrival_end" readonly style='background-color:#eee;' value='<{$end}>'/>				
				<a href='javascript:;' class='clearDatepickerBtn'>&nbsp;&nbsp;清空时间</a>
			</div>
			<div class="search-module-condition">
				<span class=""> 目的地 : </span>
				<select style="" class="input_text_select input_select country_code_select" name="country_code">
					<option value=''><{t}>-all-<{/t}></option>
					<{foreach from=$countryArr name=ob item=ob}>
					<option value='<{$ob.country_code}>'><{$ob.country_code}>[<{$ob.country_cnname}> <{$ob.country_enname}>]</option>
					<{/foreach}>					
				</select>
				<!-- <input type="text" class="input_text country_code" style='width: 50px;' placeholder='简码或名称' /> -->
				<span class=""> 运输方式 : </span>
				<select style="" class="input_text_select input_select product_code_select" name="product_code" id="product_code">
					<option value=''><{t}>-all-<{/t}></option>
					<{foreach from=$productKind name=ob item=ob}>
					<option value='<{$ob.product_code}>'><{$ob.product_code}>[<{$ob.product_enname}> <{$ob.product_cnname}>]</option>
					<{/foreach}>
				</select>
				<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" />
				<span style='line-height:35px;margin-left:15px;'>温馨提示：日期间隔不能大于7天哦</span>
				<a href='javascript:;' class='exportBtn' style='line-height:35px;margin-left:15px;'>导出Excel</a>
			</div>
		</form>
	</div>
	<div id="module-table">
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td width="125">到货日期</td>
					<td width="80"><{t}>参考号<{/t}></td>
					<td width="80"><{t}>跟踪号<{/t}></td>
					<td width="80"><{t}>运输方式<{/t}></td>
					<td width="80"><{t}>目的地<{/t}></td>
					<td width="80"><{t}>实重<{/t}></td>
					<td width="80"><{t}>体积重<{/t}></td>
					<td width="80"><{t}>计费重<{/t}></td>
					<td width="80"><{t}>件数<{/t}></td>
					<td width="80"><{t}>类型<{/t}></td>
					<td ><{t}>状态<{/t}></td>
				</tr>
			</tbody>
			<tbody id="table-module-list-data">
				<!-- 
				<tr class="table-module-b1">
					<td>到货日期</td>
					<td><{t}>参考号<{/t}></td>
					<td><{t}>跟踪号<{/t}></td>
					<td><{t}>运输方式<{/t}>($)</td>
					<td><{t}>目的地<{/t}></td>
					<td><{t}>实重<{/t}></td>
					<td><{t}>体积重<{/t}></td>
					<td><{t}>计费重<{/t}></td>
					<td><{t}>件数<{/t}></td>
					<td><{t}>类型<{/t}></td>
					<td><{t}>状态<{/t}></td>
				</tr>
				 -->
			</tbody>
		</table>
	</div>
	<div class="pagination"></div>
</div>