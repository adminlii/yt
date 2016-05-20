<table width="500" cellspacing="0" cellpadding="0" border="0" class="table-module">
	<caption>账户总览</caption>
	<tbody>
		<tr class="table-module-title">
			<td width="125">运费余额</td>
			<td width="80"><{t}>押金<{/t}></td>
			<td width="80"><{t}>总信用额度<{/t}></td>
			<td width="80"><{t}>剩余信用额度<{/t}></td>
		</tr>
	</tbody>
	<tbody>
		<tr class="table-module-b1">
			<td><{$zonglan.a}></td>
			<td><{$zonglan.b}></td>
			<td><{$zonglan.c}></td>
			<td><{$zonglan.d}></td>
		</tr>
	</tbody>
</table>
<ul class='types'>
	<li>
		<a href='/fee/fee/fee-list' class="fee-list">交款记录</a>
	</li>
	<li>
		<a href='/fee/fee/fee-detail-list' class="fee-detail-list">运费明细</a>
	</li>
	<li>
		<a href='/fee/fee/fee-flow-list' class="fee-flow-list">账户流水</a>
	</li>
	<!-- 
	<li>
		<a href='/fee/fee/fee-info-list' class="fee-info-list">充值说明</a>
	</li> 
	-->
	<li>
		<a href='/fee/fee/fee-unpaid-list' class="fee-unpaid-list">未付款</a>
	</li>
	<li>
		<a href='/fee/fee/fee-tongji-list' class="fee-tongji-list">成本统计</a>
	</li>
	<li>
		<a href='/fee/fee/fee-income' class="fee-income">账户充值</a>
	</li>
	<li>
		<a href='/fee/fee/bill-query' class="bill-query">账单查询</a>
	</li>
	<li>
		<a href='/fee/fee/fee-statistics-list' class="fee-statistics-list">日费用统计</a>
	</li>
</ul>
<style>
.types .current {
	color: #000;
	text-decoration: none;
}

.types {;
	
}

.types li {
	float: left;
	margin: 0 10px 0 0px;
	padding: 12px 5px;
	font-size: 16px;
}

.types li .current {
	font-weight: bold;
}

#search-module {
	clear: both;
}

caption {
	line-height: 35px;
	font-size: 15px;
	font-weight: bold;
}
</style>
<script>
$(function(){
    //
    $('.<{$action}>').attr('href','javascript:;').addClass('current');
	$('.clearDatepickerBtn').click(function(){
	    $('.datepicker').val('');
	});
})

/**
 * 时间间隔
 * @param startDate
 * @param endDate
 * @returns {Number}
 */
function GetDateDiff(startDate,endDate)
{
	var startTime = new Date(Date.parse(startDate.replace(/-/g, "/"))).getTime();
	var endTime = new Date(Date.parse(endDate.replace(/-/g, "/"))).getTime();
	//var dates = Math.abs((endTime - startTime))/(1000*60*60*24);
	var dates = (endTime - startTime)/(1000*60*60*24);
	return dates;
} 
</script>