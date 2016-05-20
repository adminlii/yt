var user_account_arr_json = <{$user_account_arr_json}>;
var warehouseJson = <{$warehouseJson}>;
paginationPageSize =15;
EZ.url = '/product/seller-item/get-black-';
EZ.getListData = function (json) {
	var html = '';
	var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
	var c = $('#listForm .table-module-title td').size();
	$.each(json.data, function (key, val) {
		var clazz = (key + 1) % 2 == 1 ? "table-module-b1" : "table-module-b2";
		clazz+=' item_'+val.item_id;
		html += "<tr class='"+clazz+"'>" ;

		html += "<td class='ec-center' >" +'<input type="checkbox" class="checkItem" name="sibl_id[]" ref_id="'+val.sibl_id+'" value="' + val.sibl_id + '"/>' + "</td>";
		html += "<td >" + (user_account_arr_json[val.user_account]?user_account_arr_json[val.user_account].platform_user_name:val.user_account) + "</td>";

		html += "<td >" +val.sku+ "</td>";
		html += "<td >";
		html += '<p><a href="javascript:;" class="releaseBlackListBtn" sku="'+val.sku+'" account="'+val.user_account+'"><{t}>lifting_replenishment_blacklist<{/t}></a></p>';//解除补货黑名单
		html += "</td>";
		html += "</tr>";

	});
	return html;
}
function checkItem(){
	$('.checkItem').each(function(){
		var checked = $(this).is(':checked');
		var item_id = $(this).val();
		if(checked){
			$('.item_'+item_id+' .supply_qty').attr('disabled',false);
		}else{
			$('.item_'+item_id+' .supply_qty').attr('disabled',true);
		}
	})
}

function blackListOp(url,sku,account){
	//'数据处理中...'
	alertTip($.getMessage('sys_common_processing'));
	$.ajax({
		type: "post",
		dataType: "json",
		url: url,
		data: {'sku':sku,'user_account':account},
		success: function (json) {
			var html = '';
			if(json.ask){
				html+=(json.message);
				$.each(json.result,function(k,v){
					html+='<p>'+v.message+'</p>';
				})
				initData(paginationCurrentPage-1);
			}else{
				html+=(json.message);
			}
			if($('#dialog-auto-alert-tip').size()>0){
				$('#dialog-auto-alert-tip').html(html);
			}else{
				alertTip(html);
			}
		}
	});
}
$(function(){
	initData(0);
	$(".checkAll").live('click', function() {
		$(".checkItem").attr('checked', $(this).is(':checked'));

		var size = $('.checkItem:checked').size();
		$('.checked_count').text(size);
	});

	$('.releaseBlackListBtn').live('click',function(){
		var sku = $(this).attr('sku');
		var account = $(this).attr('account');
		//'将SKU:'+sku+'从黑名单解除？'
		if(!window.confirm($.getMessage('the_sku_released_from_the_blacklist',[sku]))){
			return false;
		}
		blackListOp('/product/seller-item/release-black-list',sku,account);
	});
	
	$('#batch_op_div').dialog({
		autoOpen: false,
		width: 500,
		modal: true,
		show: "slide",
		buttons: [
		          {
		        	  text: '加入黑名单(' + $.getMessage('join_replenishment_blacklist') + ')',
		        	  click: function () {
		        		  var sku = $('#sku',this).val();
		        		  var account = $('#user_account',this).val();
		        		  blackListOp('/product/seller-item/add-to-black-list',sku,account);
		        		  $(this).dialog("close");
		        	  }
		          },
		          {
		        	  text: '解除黑名单(' + $.getMessage('lifting_replenishment_blacklist') + ')',
		        	  click: function () {
		        		  var sku = $('#sku',this).val();
		        		  var account = $('#user_account',this).val();
		        		  
		        		  blackListOp('/product/seller-item/release-black-list',sku,account);
		        		  $(this).dialog("close");
		        	  }
		          },
		          {
		        	  text: '关闭(Close)',
		        	  click: function () {
		        		  $(this).dialog("close");
		        	  }
		          }
		          ],
		          close: function () {

		          }
	});

	$('.batchSetBtn').click(function(){
		$('#batch_op_div').dialog('open');
	})
})