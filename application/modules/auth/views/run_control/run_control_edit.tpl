<script type="text/javascript">
EZ.url = '/auth/run-control/';
EZ.getListData = function (json) {
    var html = '';
    var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
    $.each(json.data, function (key, val) {
        html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
        html += "<td class='ec-center'>" + (i++) + "</td>";
        
        html += "<td >" + val.platform + "</td>";
        html += "<td >" + val.run_app + "</td>";
        html += "<td >" + val.company_code + "</td>";
        html += "<td >" + val.user_account + "</td>";
        html += "<td >" + val.start_time + "</td>";
        html += "<td >" + val.end_time + "</td>";
        html += "<td >" + val.run_interval_minute + "</td>";
        html += "<td >" + val.last_run_time + "</td>";
        html += "<td >" + val.status + "</td>";
        html += "<td>";
        html += "<a href=\"javascript:editById(" + val.E0 + ")\">" + EZ.edit + "</a>";
        html +="&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:deleteById(" + val.E0 + ")\">" + EZ.del + "</a>";
        html +="&nbsp;&nbsp;|&nbsp;&nbsp;<a href='javascript:;' class='reAuth' user_account='"+val.E2+"' short_name='"+val.E4+"' pu_id='"+val.E0+"'>禁用</a>";

        html +="</td>";
        html += "</tr>";
    });
    return html;
}
$(function(){
	initData(0);
})
</script>
<div id="module-container">
	<div id="search-module">
		<form id="searchForm" name="searchForm" class="submitReturnFalse">
			<input type="hidden" value="" id="filterActionId" />
			<div style="padding: 0">
				<!-- 账号名：
				<input type="text" name="E2" id="E2" class="input_text keyToSearch" />
				简称：
				<input type="text" name="E4" id="E4" class="input_text keyToSearch" />
				-->
				&nbsp;&nbsp;
				<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" />
			</div>
		</form>
	</div>
	<div id="module-table">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
			<tr class="table-module-title">
				<td width="3%" class="ec-center">NO.</td>
				<td>platform</td>
				<td>run_app</td>
				<td>company_code</td>
				<td>user_account</td>
				<td>start_time</td>
				<td>end_time</td>
				<td>run_interval_minute</td>
				<td>last_run_time</td>
				<td>status</td>
				<td><{t}>operate<{/t}></td>
			</tr>
			<tbody id="table-module-list-data"></tbody>
		</table>
	</div>
	<div class="pagination"></div>
</div>