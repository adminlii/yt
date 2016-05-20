$(function(){
	$(".datepicker").datepicker({ dateFormat: "yy-mm-dd"});
})
paginationPageSize = 20;
EZ.url = '/fee/fee/fee-detail-';
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
		html +=val.arrival_date;
		html += '</td>';
		html += '<td  valign="top">';
		html +=val.shipper_hawbcode;
		html += '</td>';
		html += '<td  valign="top">';
		html +=val.serve_hawbcode;
		html += '</td>';
		html += '<td  valign="top">';
		html +=val.productKind?val.productKind.product_cnname:'';
		html += '</td>';
		html += '<td  valign="top">';
		html += val.country?val.country.country_cnname:val.destination_countrycode;
		html += '</td>';

		html += '<td  valign="top">';
		html +=val.checkin_grossweight;
		html += '</td>';

		html += '<td  valign="top">';
		html +=val.checkin_volumeweight;
		html += '</td>';
		html += '<td  valign="top">';
		html +=val.shipper_chargeweight;
		html += '</td>';
		html += '<td  valign="top">';
		html +=val.shipper_pieces;
		html += '</td>';
		html += '<td  valign="top">';
		html +=val.atd_cargo_type?val.atd_cargo_type.cargo_type_cnname:'';
		html += '</td>';

		html += '<td  valign="top">';
		html += val.tak_trackingbusiness?(val.tak_trackingbusiness.new_track_date+' / '+val.tak_trackingbusiness.new_track_location+' / '+val.tak_trackingbusiness.new_track_comment):'';
		html += '</td>';

		/**操作 结束**/
		html += "</tr>";

	});

	return html;
}
