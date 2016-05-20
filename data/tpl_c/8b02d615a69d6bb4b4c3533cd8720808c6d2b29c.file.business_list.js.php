<?php /* Smarty version Smarty-3.1.13, created on 2016-05-06 17:06:51
         compiled from "D:\yt1\application\modules\shipment\js\business\business_list.js" */ ?>
<?php /*%%SmartyHeaderCode:17428572c5eab13a966-47942172%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8b02d615a69d6bb4b4c3533cd8720808c6d2b29c' => 
    array (
      0 => 'D:\\yt1\\application\\modules\\shipment\\js\\business\\business_list.js',
      1 => 1457516431,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17428572c5eab13a966-47942172',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_572c5eab14e1e5_27323421',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_572c5eab14e1e5_27323421')) {function content_572c5eab14e1e5_27323421($_smarty_tpl) {?>
function tongji(){	
	$.ajax({
		type: "POST",
//		async: false,
		dataType: "json",
		url: '/platform/order/get-statistics',
		data: {'platform':platform,'order_type':order_type},
		success: function (json) {

		}
	}); 
}
function autoInit(){
	$('.country_code').each(function(){
		var this_ = this;
		var tr = $(this).parent().parent();
		$(this).autocomplete({
			//source: "/product/product/get-by-keyword/limit/20",
			minLength: 2,
			delay:300,
			source:function(request, response) {
				//增加等待图标
				$(this_).addClass("autocomplete-loading");
				$(this_).autocomplete({delay: 100});
				var parameter = {};
				parameter["term"] = $(this_).val();
				$.ajax({
					type :"POST",
					url: "/common/country/get-by-keyword/limit/200",
					dataType: "json",
					data: parameter,
					success: function(json) {
						//关闭等待图标
						$(this_).removeClass("autocomplete-loading");
						var objItem = {};
						if(json != null){
							objItem = json;
						}
						response($.map(objItem, function(item) {
							/*
							 * 返回自定义的JSON对象
							 */
							return item;
						}));
					}
				});
			},
			select: function( event, ui ) {
				$('.country_code_select',tr).val(ui.item.country_code);		
				setTimeout(function(){
					$(this_).val('');
				},50)

			},
			search: function( event, ui ) {
			}
			,open: function() {
				$(this).removeClass("ui-corner-all").addClass("ui-corner-top");
			}
			,close: function() {
				$(this).removeClass("ui-corner-top").addClass("ui-corner-all");
			} 
		});
	})
}
$(autoInit);
$(function(){
	$(".datepicker").datepicker({ dateFormat: "yy-mm-dd"});
})
paginationPageSize = 20;
EZ.url = '/shipment/business/';
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
		html +=val.shipper_hawbcode;
		html += '</td>';
		html += '<td  valign="top">';
		html +=val.serve_hawbcode;
		html += '</td>';
		html += '<td  valign="top">';
		html += val.product_cnname;
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
		html += val.cargo_type_cnname;
		html += '</td>';
		
		html += '<td  valign="top">';
		html += val.tak_trackingbusiness?(val.tak_trackingbusiness.new_track_date+' / '+val.tak_trackingbusiness.new_track_location+' / '+val.tak_trackingbusiness.new_track_comment):'';
		html += '</td>';
		
		/**操作 结束**/
		html += "</tr>";

	});

	return html;
}

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
$(function(){
	
	$('.exportBtn').click(function(){
		var start = $('#searchForm .arrival_start').val();
		var end = $('#searchForm .arrival_end').val();
		var arrivalbatch_code = $('#searchForm .arrivalbatch_code').val();
		if(arrivalbatch_code==''){
			if(!start||start==''||end==''||!end){
				alertTip('请选择日期范围');
				return;
			}
		
			var days = GetDateDiff(start,end);
			if(days>7){
				alertTip('日期间隔不能大于7天哦');
				return;
			}
			if(days<0){
				alertTip('结束时间必须大于开始时间哦');
				return;
			}
		}
		
		$('#searchForm').attr('action','/shipment/business/list?ac=export');
		$('#searchForm').attr('target','_blank');
		$('#searchForm').attr('onsubmit','');
		$('#searchForm').submit();
		$('#searchForm').attr('target','_self');
		$('#searchForm').attr('onsubmit','return false;');
		
	});
	$('.clearDatepickerBtn').click(function(){
		$('#searchForm .datepicker').val('');
	});
})

$(function(){
	$('#search-module .input_select[name="country_code"]').chosen({width:'300px',search_contains:true});
	$('#search-module .input_select[name="product_code"]').chosen({width:'300px',search_contains:true});
}) 
$(function(){
	<?php if ($_GET['arrivalbatch_code']){?>
		$('.arrivalbatch_code').val('<?php echo $_GET['arrivalbatch_code'];?>
');
		initData(paginationCurrentPage-1);
	<?php }?>
})<?php }} ?>