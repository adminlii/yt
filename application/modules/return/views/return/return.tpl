<script type="text/javascript" src="/js/jquery-ui-timepicker-addon.js"></script>
<script>
EZ.url = '/return/return/';
EZ.getListData = function (json) {
    var html = '';
    var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1; 
    $.each(json.data, function (key, val) {
        html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
        html += "<td class='ec-center'>" + (i++) + "</td>";
                html += "<td>";
                html += val.shipper_hawbcode?val.shipper_hawbcode:' ';
                html += "</td>";
                html += "<td>" + val.serve_hawbcode + "</td>";
                html += "<td>" + val.destination_countrycode + "</td>";
                html += "<td>";
                html += val.return_time?val.return_time:' '; 
                html +="</td>";
                /* html += "<td >" + val.returnstatus_code + "</td>"; */
                html += "<td>" + val.transferstatus_code + "</td>";
                html += "<td>";
                html += val.return_note ?val.return_note : ' ';
                html +="</td>";
               
        /* html += "<td><a href=\"javascript:getReponseMessage(" + val.issue_id + ")\">查看</a></td>"; *///EZ.view
        html += "</tr>";
    });
    $("#count_").html(json.total);
    return html;
}

$(function(){
	$(".datepicker").datepicker({ dateFormat: "yy-mm-dd"});
	$('.country_code').chosen({'width':'260px','search_contains':true});
})

//=====导出数据======
$(".exportBtn").live("click",function(json){
	var data=$("#count_").html();
	if(data === '0'){
		alert('没有数据');
		return;
	}
	$("#exportForm").html('');
	var textParam=$("#searchForm input").clone();
	var selectParam=$("#searchForm select");
	var html='';
	 $.each(selectParam,function(){
		html += "<input type='hidden' name='"+$(this).attr("name")+"' value='"+$(this).find("option:selected").val()+"'/>";
	}) 
	
	$("#exportForm").append(textParam);
	$("#exportForm").append(html);
	setTimeout(function(){$("#exportForm").submit();},500);
})

//===============

$(function(){
	
	$('.submitReturnSearch').click(function(){	
	 	var start = $('#searchForm .time_start').val();
		var end = $('#searchForm .time_end').val();  
		var return_hawbcode = $('#searchForm .return_hawbcode').val();
		var shipper_hawbcode = $('#searchForm .shipper_hawbcode').val();
		var hascode= ($.trim(return_hawbcode)=='' && $.trim(shipper_hawbcode)=='' )?1:0;
		if(hascode && (!start||start==''||end==''||!end)){
			alertTip('请选择日期范围');
			return false;
		}
		var d = 30;
		var days = GetDateDiff(start,end);
		if(days>d){
			alertTip('日期间隔不能大于'+d+'天');	
			return false;
		}
		if(days<0){
			alertTip('结束时间必须大于开始时间');
			return false;
		}
		initData(0);		
	});
	
	$('.clearDatepickerBtn').click(function(){
		$('#searchForm .datepicker').val('');
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
<div id="module-container">
   <div id="search-module">
       <!-- <form id="searchForm" name="searchForm" class="submitReturnFalse" onsubmit="return false" > -->
       <form id="searchForm" name="searchForm" method='POST' action='' onsubmit='return false;'>
            <div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;">跟踪号：</span>
				<input type="text" name="return_hawbcode" id="return_hawbcode" class="input_text keyToSearch return_hawbcode"  style = "width:250px;" placeholder=""/>
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;">客户单号：</span>
				<input type="text" name="shipper_hawbcode" id="shipper_hawbcode" class="input_text keyToSearch shipper_hawbcode"  style = "width:250px;" placeholder=""/>
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;">退件类型：</span>
				<select name="transferstatus_code" class="input_select">
					<option value=''>全部</option>
					<{foreach from=$status item=stat name=stat key=k}>
						<option value='<{$k}>'><{$stat}></option>
					<{/foreach}>
				</select>	
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;">国家：</span>
				<select name="country_code" class="country_code" class="input_select">
					<option value=''>全部</option>
					<{foreach from=$countryArr name=o item=o}>
						<option value='<{$o.country_code}>' title='<{$o.country_code}>/<{$o.country_cnname}>/<{$o.country_enname}>' name='<{$o.country_cnname}>'><{$o.country_code}> <{$o.country_cnname}></option>
					<{/foreach}>
				</select>	
			</div>
			<div class="search-module-condition">
			     <span class="searchFilterText" style="width: 90px;"><{t}>退件时间<{/t}>：</span>
				<input type="text" class="datepicker input_text time_start" name="create_date_from" id='DateFrom' style="width:100px;"readonly style='background-color: #eee;' value='<{$start}>'/>
				~
				<input type="text" class="datepicker input_text time_end" name="create_date_end" id='DateEnd' style="width:100px;"/>
				<!-- <a href='javascript:;' class='clearDatepickerBtn'>清空时间</a> -->
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;"></span>
				<input type="button" name = "code" value="<{t}>search<{/t}>" class="baseBtn submitReturnSearch" />				
			</div>
			<input name='issue_status' value='N' type='hidden'  id = "issue_status" />
        </form>
   </div>
   
</div>

 <div id="module-table" style="margin-top:0px;">
    	<div>
				<div class="" style="text-align: right; float: right; line-height: 25px; padding: 0px 10px;color:red;margin-top:-20px;"><b>共&nbsp;<span id = "count_">0</span>&nbsp;条</b>
				&nbsp;&nbsp;
				<input type="button" name = "export" value="<{t}>export<{/t}>" class="baseBtn exportBtn" /></div>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
            <tr class="table-module-title">
               <td width="3%" class="ec-center">NO.</td>
		       <td width="10%">客户单号</td>
		       <td width="10%">跟踪号</td>
		       <td width="8%">国家</td>
		       <td width="10%"">退件时间</td>
		       <!-- <td width="80">退件状态</td> -->
		       <td width="20%">退件类型</td>
		       <td width="20%">退件备注</td>
            </tr>
            <tbody id="table-module-list-data"></tbody>
            </table> 
        </div>
    <div class="pagination"></div>
</div>
<form id="exportForm" action="/return/return/list/ac/export" method="post" target="_blank" style="display:none;">

</form>