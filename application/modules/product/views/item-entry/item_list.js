
var user_account_arr_json = <{$user_account_arr_json}>;
//paginationPageSize =1;
EZ.url = '/product/item-entry/';
var data = {};
EZ.getListData = function (json) {
	$('.checkAll').attr('checked',false);
	var html = '';
	var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
	var c = $('#listForm .table-module-title td').size();
	data = json.data;
	var step = 0;
	$.each(json.data, function (key, val) {
		var clazz = (step + 1) % 2 == 1 ? "table-module-b1" : "table-module-b2";
		step++;
		clazz+=' item_'+val.item_id;
		html += "<tr class='"+clazz+"'>" ;
		//html += "<td class='ec-center' >" +'<input type="checkbox" class="checkItem" name="item_id[]" ref_id="'+val.item_id+'" value="' + val.item_id + '"/>' + "</td>";
		html += "<td >" + val.user_account+"</td>";
		html += "<td  class='word-break'>" + val.account_details_entry_type+"  <br/> "+val.description+"  <br/> "+(val.net_detail_amount+" "+val.net_detail_amount_currency)+"</td>";
		html += "<td  class='word-break'>" + val.item_id+" <br/> "+val.title+"</td>";
		html += "<td  class='word-break'>" +"【"+val.date+"】" +(val.order_line_item_id==''?'':'【'+val.order_line_item_id+'】')+""+val.memo+"</td>";
		html += "<td ><a href='javascript:;' class='syncBtn' item_id='"+val.item_id+"'>数据更新</a></td>";
		html += "</tr>";
		
	});
	return html;
}
$(function(){
	$('.syncBtn').live('click',function(){
		if(!window.confirm('Are You Sure?')){
			return;
		}
		var param = {};
		var item_id = $(this).attr('item_id');
		param.item_id = item_id;
		//alertTip('数据处理中,同步时间可能会需要较长时间，请耐心等待....',800,600);
		$.ajax({
			type: "post",
			dataType: "json",
			url: '/product/item-entry/reload',
			data: param,
			success: function (json) {
				alertTip(json.response_str,800,600);
			}
		});

	});

})


$(function(){
	
	$('#type').change(function(){
		if($(this).val()=='sku'){
			$('#code').attr('placeholder','模糊搜索');
		}else if($(this).val()=='item_id'){
			$('#code').attr('placeholder','请输入ItemID');			
		}
	}).change();
})