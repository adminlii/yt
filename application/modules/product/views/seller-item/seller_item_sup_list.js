var user_account_arr_json = <{$user_account_arr_json}>;
paginationPageSize =10;
EZ.url = '/product/seller-item/get-sup-log-';
EZ.getListData = function (json) {
	var html = '';
	var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
	var c = $('#listForm .table-module-title td').size();
	$.each(json.data, function (key, val) {
		var clazz = (key + 1) % 2 == 1 ? "table-module-b1" : "table-module-b2";
		clazz+=' item_'+val.item_id;
		html += "<tr class='"+clazz+"'>" ;
		html += "<td class='ec-center' >" + (i+key) + "</td>";
		html += "<td >" + (user_account_arr_json[val.user_account]?user_account_arr_json[val.user_account].platform_user_name:val.user_account) + "</td>";
		html += "<td class='' >" + val.item_id + "</td>";

		html += "<td >" +val.sku+ "</td>";
		//html += "<td >" +val.sell_qty+ "</td>";
		html += "<td >" +val.supply_qty+ "</td>";
		html += "<td >" +val.user_name+ "</td>";
		html += "<td class='' >" + val.create_time + "</td>";
	
		html += "</tr>";

	});
	//alert(html);
	return html;
}
$(function(){
	initData(0);
})