
<script type="text/javascript"> 
EZ.url = '/product/amazon-feed/';
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
		html+='<td>'+val.FeedSubmissionId+'</td>';
		
		html+='<td>';
		html+='<b>'+val.FeedType+'</b><br/>';
		html+='</td>';

	    html+='<td>'+val.SubmittedDate+'</td>';
	    html+='<td>'+val.FeedProcessingStatus+'</td>';
	    //html+='<td>'+val.RequestId+'</td>';
	    html+='<td>'+val.download+'</td>';

		html+='<td>';
		html+='<b>MessagesProcessed:</b> '+val.MessagesProcessed+'<br/>';
		html+='<b>MessagesSuccessful:</b> '+val.MessagesSuccessful+'<br/>';
		html+='<b>MessagesWithError:</b> '+val.MessagesWithError+'<br/>';
		html+='<b>MessagesWithWarning:</b> '+val.MessagesWithWarning+'<br/>';
		html+='</td>';
		
	    html+='<td>'+val.create_time+'</td>';
		html+='<td>';
		html+='<a href="javascript:;" class="getFeedResultBtn" feed_id="'+val.FeedSubmissionId+'">getAmazonFeedResult</a><br/>';
		html+='<a href="javascript:;" class="viewFeedContentBtn" feed_id="'+val.FeedSubmissionId+'">FeedContent</a><br/>';
		html+='<a href="javascript:;" class="viewFeedResultBtn" feed_id="'+val.FeedSubmissionId+'">FeedResult</a><br/>';
		html+='</td>';
		html += "</tr>";
		
	});
	return html;
}
$(function(){
	$('.getFeedResultBtn').live('click',function(){
		if(!window.confirm('<{t}>are_you_sure<{/t}>')){
		    return;
		}
	    var feed_id = $(this).attr('feed_id');
		var url = "/product/amazon-feed/get-feed-result?feed_id="+feed_id;
		openIframeDialog(url, 800, 600,'FeedContent');
	});

	$('.viewFeedContentBtn').live('click',function(){
	    var feed_id = $(this).attr('feed_id');
		var url = "/product/amazon-feed/view-feed-content?feed_id="+feed_id;
		openIframeDialog(url, 800, 600,'FeedContent');
	});

	$('.viewFeedResultBtn').live('click',function(){
	    var feed_id = $(this).attr('feed_id');
		var url = "/product/amazon-feed/view-feed-result?feed_id="+feed_id;
		openIframeDialog(url, 800, 600,'FeedResult');
	    
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
				<select name="FeedType">
					<option value=''><{t}>all<{/t}></option>
					<{foreach from=$FeedTypeArr name=ob item=ob}>
					<option value='<{$ob.FeedType}>'><{$ob.FeedType}></option>
					<{/foreach}>
				</select>
			</div>
			<div class="search-module-condition">
				<span style="width: 125px;" class="searchFilterText"><{t}>FeedProcessingStatus<{/t}>：</span>
				<select name="FeedProcessingStatus">
					<option value=''><{t}>all<{/t}></option>
					<{foreach from=$FeedProcessingStatusArr name=ob item=ob}>
					<option value='<{$ob.FeedProcessingStatus}>'><{$ob.FeedProcessingStatus}></option>
					<{/foreach}>
				</select>
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
						<td><{t}>FeedSubmissionId<{/t}></td>
						<td><{t}>FeedType<{/t}></td>
						<td><{t}>SubmittedDate<{/t}></td>
						<td><{t}>FeedProcessingStatus<{/t}></td>
						<td><{t}>download<{/t}></td>
						<td><{t}>Result<{/t}></td>
						<td><{t}>date<{/t}></td>
						<td><{t}>operate<{/t}></td>
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