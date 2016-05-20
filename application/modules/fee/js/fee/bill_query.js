$(function(){
	$(".datepicker").datepicker({ dateFormat:"yy-mm-dd"});
})

$(function(){
	$("#submitToSearch").click(function(){
		var start=$("#searchForm .time_start").val();
		var end=$("#searchForm .time_end").val();
		if(!start || start==''||!end || end==''){
			alertTip('请选择日期范围');
			return;
		}
		var d=365;
		var days=GetDateDiff(start,end);
		if(days>d){
			alertTip('日期间隔不能大于'+d+'天');
			return;
		}
		if(days<0){
			alertTip('结束时间必须大于开始时间');
			return;
		}
		initData(0);
	});
})

paginationPageSize =20;
EZ.url='/fee/fee/bill-query/';
EZ.getListData = function(json){
	var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
	var html = '';
	$.each(json.data,function(key,val){
		var style=key%2==1?'table-module-b2':'table-module-b1';
		html +="<tr class='"+style+"'>";
		html +='<td valign="top">';
		html +=i++;
		html += '</td>';
		html +='<td valign="top">';
		html +=val['sb_billdate']?val['sb_billdate']:'';
		html += '</td>';
		html +='<td valign="top">';
		html +=val['sb_labelcode']?val['sb_labelcode']:'';
		html += '</td>';
		html +='<td valign="top">';
		html +=val['sb_amount']?val['sb_amount']:'';
		html += '</td>';
		html +='<td valign="top">';
		html +=val['sb_note']?val['sb_note']:'无备注信息';
		html += '</td>';
		html +='<td valign="top">';
		html +="<a href='JavaScript:void();'>下载</a>";
		html += '</td>';
		html += '</tr>';		
	});
	return html;
}







