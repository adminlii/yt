
<script type="text/javascript"> 
EZ.url = '/product/amazon-report/';
var data = {};
EZ.getListData = function (json) {
	var html = '';
	var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
	var c = $('#listForm .table-module-title td').size();
	data = json.data;
	var step = 0;
	$.each(json.data, function (key, val) {
		var clazz = (step + 1) % 2 == 1 ? "table-module-b1" : "table-module-b2";
		step++;
		clazz+=' item_'+val.product_id;
		html += "<tr class='"+clazz+"'>" ;

		html+='<td>'+val.user_account+'</td>';
		html+='<td>'+val.ReportRequestId+'</td>';
		
		html+='<td>';
		html+='<b>'+val.ReportType+'</b><br/>';
		html+='</td>';

	    html+='<td>'+val.ReportProcessingStatus+'</td>';
	    html+='<td>'+val.GeneratedReportId+'</td>';
	    html+='<td>'+val.download+'</td>';
	    

		html+='<td>';
		html+='<b>SubmittedDate:</b> '+val.SubmittedDate+'<br/>';
		html+='<b>StartedProcessingDate:</b> '+val.StartedProcessingDate+'<br/>';
		html+='<b>CompletedDate:</b> '+val.CompletedDate+'<br/>';
		html+='<b>StartDate:</b> '+val.StartDate+'<br/>';
		html+='<b>EndDate:</b> '+val.EndDate+'<br/>';
		html+='<b>create_time:</b> '+val.create_time+'';
		
		html+='</td>';
		
		html+='<td>';
		html+='<a href="javascript:;" class="getReportResultBtn" report_request_id="'+val.ReportRequestId+'">获取数据</a><br/>';;
		html+='<a href="javascript:;" class="viewReportDataBtn" report_id="'+val.GeneratedReportId+'">查看结果</a><br/>';
		html+='</td>';
		html += "</tr>";
		
	});
	return html;
}
$(function(){
	$('.getReportResultBtn').live('click',function(){
		if(!window.confirm('<{t}>are_you_sure<{/t}>')){
		    return;
		}
		var text = $(this).text();
	    var report_request_id = $(this).attr('report_request_id');
		var url = "/product/amazon-report/get-report-result?report_request_id="+report_request_id;
		openIframeDialog(url, 800, 600,text);
	});

	$('.viewReportDataBtn').live('click',function(){
		if(!window.confirm('可能会造成浏览器假死,<{t}>are_you_sure<{/t}>')){
		    return;
		}
		var text = $(this).text();
	    var report_id = $(this).attr('report_id');
		var url = "/product/amazon-report/view-report-data?report_id="+report_id;
		openIframeDialog(url, 800, 600,text);
	    
	})
})
</script>
<div id="module-container" style=''>
	<div id="search-module">
		<form class="submitReturnFalse" name="searchForm" id="searchForm">			
			<div class="search-module-condition">
				<span style="width: 125px;" class="searchFilterText"><{t}>user_account<{/t}>：</span>
				<select id="user_account" name="user_account">
					<option value=''><{t}>all<{/t}></option>
					<{foreach from=$user_account_arr name=ob item=ob}>
					<option value='<{$ob.user_account}>'><{$ob.platform_user_name}></option>
					<{/foreach}>
				</select>
			</div>
			<div class="search-module-condition">
				<span style="width: 125px;" class="searchFilterText"><{t}>FeedType<{/t}>：</span>
				<select name="ReportType">
					<option value=''><{t}>all<{/t}></option>
					<{foreach from=$ReportTypeArr name=ob item=ob}>
					<option value='<{$ob.ReportType}>'><{$ob.ReportType}></option>
					<{/foreach}>
				</select>
			</div>
			<div class="search-module-condition">
				<span style="width: 125px;" class="searchFilterText"><{t}>FeedProcessingStatus<{/t}>：</span>
				<select name="ReportProcessingStatus">
					<option value=''><{t}>all<{/t}></option>
					<{foreach from=$ReportProcessingStatusArr name=ob item=ob}>
					<option value='<{$ob.ReportProcessingStatus}>'><{$ob.ReportProcessingStatus}></option>
					<{/foreach}>
				</select>
			</div>
			<div class="search-module-condition">
				<span style="width: 125px;" class="searchFilterText"><{t}>ReportRequestId<{/t}>：</span>				
				<input type="text" name="ReportRequestId" class="input_text keyToSearch">				
			</div>
			<div class="search-module-condition">
				<span style="width: 125px;" class="searchFilterText"><{t}>GeneratedReportId<{/t}>：</span>				
				<input type="text" name="GeneratedReportId" class="input_text keyToSearch">				
			</div>
			<div class="search-module-condition">
				<span style="width: 125px;" class="searchFilterText">&nbsp;</span>
				<input type="button" class="baseBtn submitToSearch" value="<{t}>search<{/t}>">
			</div>
		</form>
	</div>
	<div id="module-table" style='overflow: auto;'>		
		<form action="" id='listForm' method='POST'>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
				<tbody>
					<tr class="table-module-title">
						<td width='100'><{t}>user_account<{/t}></td>
						<td><{t}>ReportRequestId<{/t}></td>
						<td><{t}>ReportType<{/t}></td>
						<td><{t}>ReportProcessingStatus<{/t}></td>
						<td><{t}>GeneratedReportId<{/t}></td>	
						<td><{t}>download<{/t}></td>					
						<td><{t}>date<{/t}></td>
						<td width='80'><{t}>operate<{/t}></td>
					</tr>
				</tbody>
				<tbody id="table-module-list-data">
				</tbody>
			</table>
		</form>
		<div class="pagination"></div>
	</div>
	<div id='loading'></div>
</div>