$(function() {
	$(".datepicker").datepicker({
		dateFormat : "yy-mm-dd"
	});
})
paginationPageSize = 20;
EZ.url = '/fee/fee/fee-unpaid-';
EZ.getListData = function(json) {
	// 设置订单条数
	var html = '';
	$(".checkAll").attr('checked', false);
	var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;

	// 买家ID
	$.each(json.data, function(key, val) {
		var orderId = val.order_id;
		var clazz = key % 2 == 1 ? 'table-module-b2' : 'table-module-b1';
		html += "<tr class='" + clazz + "'>";
		html += '<td  valign="top">';
		html += val.arrival_date;
		html += '</td>';
		html += '<td  valign="top">';
		html += val.shipper_hawbcode;
		html += '</td>';
		html += '<td  valign="top">';
		html += val.serve_hawbcode;
		html += '</td>';
		html += '<td  valign="top">';
		html += val.product_cnname ? val.product_cnname : '';
		html += '</td>';
		html += '<td  valign="top">';
		html += val.destination_countrycode ? val.destination_countrycode : '';
		html += '</td>';

		html += '<td  valign="top">';
		html += val.shipper_chargeweight ? val.shipper_chargeweight : 0;
		html += '</td>';

		html += '<td  valign="top">';
		html += val.A ? val.A : '0';
		html += '</td>';
		html += '<td  valign="top">';
		html += (val.B ? parseFloat(val.B) : 0);
		html += '</td>';

		html += '<td  valign="top">';
		html +='<a href="javascript:;" class="viewDetailBtn" bs_id="'+val.bs_id+'">查看明细</a>';
		html += '</td>';

		/** 操作 结束* */
		html += "</tr>";

	});

	return html;
}

$(function(){
	$('#submitToSearch').click(function(){
		var start = $('#searchForm .time_start').val();
		var end = $('#searchForm .time_end').val();  
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
		
		initData(0);
	});
})
$(function(){
	$('.viewDetailBtn').live('click',function(){
		var bs_id = $(this).attr('bs_id'); 
		$.ajax({
			type: "post",
			async: false,
			dataType: "html",
            url: '/fee/fee/fee-unpaid-detail',
			data: {'bs_id':bs_id},
			success: function (html) {
				alertTip(html,800,400);
			}
		});
	})
})