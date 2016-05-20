$(function(){
	$(".datepicker").datepicker({ dateFormat: "yy-mm-dd"});
})
paginationPageSize = 20;
EZ.url = '/fee/fee/fee-flow-';
EZ.getListData = function(json) {
	//设置订单条数
	var html = '';
	$(".checkAll").attr('checked', false);
	var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;

	//买家ID
	$.each(json.data, function(key, val) {	
		var orderId = val.order_id;
		var clazz = key%2==1?'table-module-b2':'table-module-b1';
		html += "<tr class='"+clazz+"'>";
		html += '<td  valign="top">';
		html += val['发生日期'] ? val['发生日期'] : '';
		html += '</td>';
		html += '<td  valign="top">';
		html += val['结算模式'] ? val['结算模式'] : '';
		html += '</td>';
		html += '<td  valign="top">';
		html += val['业务单号'] ? val['业务单号'] : '';
		html += '</td>';
		html += '<td  valign="top">';
		html += val['账户收入'] ? val['账户收入'] : '';
		html += '</td>';
		html += '<td  valign="top">';
		html += val['账户支出'] ? val['账户支出'] : '';
		html += '</td>';

		html += '<td  valign="top">';
		html += val['当时余额'] ? val['当时余额'] : '';
		html += '</td>';

		html += '<td  valign="top">';
		html += val['财务备注'] ? val['财务备注'] : '';
		html += '</td>';
		/**操作 结束**/
		html += "</tr>";

	});

	return html;
}

$(function(){
	$('#submitToSearch').click(function(){
		var start = $('#searchForm .time_start').val();
		var end = $('#searchForm .time_end').val();  
		var sc_business_hawbcode = $('#searchForm .sc_business_hawbcode').val();  
		if($.trim(sc_business_hawbcode)==''){
			if(!start||start==''||end==''||!end){
				alertTip('请选择日期范围');
				return;
			}
			var d = 30;
			var days = GetDateDiff(start,end);
			if(days>d){
				alertTip('日期间隔不能大于'+d+'天哦');
				return;
			}
			if(days<0){
				alertTip('结束时间必须大于开始时间哦');
				return;
			}	
		}
			 
		
		initData(0);
	});
})