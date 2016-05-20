
$(function(){
	$(".datepicker").datepicker({ dateFormat: "yy-mm-dd"});
})
paginationPageSize = 20;
EZ.url = '/shipment/arrival/';
EZ.getListData = function(json) {
	//设置订单条数
	var html = '';
	$(".checkAll").attr('checked', false);
	var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;

	//买家ID
	$.each(json.data, function(key, val) {	
		var orderId = val.order_id;
		var clazz = key%2==1?'table-module-b2':'table-module-b1';
		html += "<tr class='"+clazz+"' id='order_wrap_"+val.order_id+"'>";
		html += '<td  valign="top">';
		html +=val.arrival_date;
		html += '</td>';
		html += '<td  valign="top">';
		html +=val.csi_shipperchannel?val.csi_shipperchannel.customer_channelname:'';
		html += '</td>';
		html += '<td  valign="top">';
		html += val.arrivalbatch_labelcode;
		html += '</td>';
		html += '<td  valign="top">';
		html +=val.bsn_business_count;
		html += '</td>';
		
		html += '<td  valign="top">';
		html += '<a href="javascript:;" class="detailBtn" arrivalbatch_labelcode="'+val.arrivalbatch_labelcode+'">查看明细</a>';
		html += '&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/shipment/arrival/export-detail/?arrivalbatch_code='+val.arrivalbatch_labelcode+'" class="exportBtn" target="_blank" arrivalbatch_labelcode="'+val.arrivalbatch_labelcode+'">导出Excel</a>';
		html += '</td>';
		
		/**操作 结束**/
		html += "</tr>";

	});

	return html;
}
$(function(){
	$('.detailBtn').live('click',function(){
		var arrivalbatch_labelcode = $(this).attr('arrivalbatch_labelcode');
		leftMenu('yundan_'+arrivalbatch_labelcode,'运单查询'+arrivalbatch_labelcode,'/shipment/business/list?arrivalbatch_code='+arrivalbatch_labelcode);
	})
})