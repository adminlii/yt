$(function() {
	$(".datepicker").datepicker({
		dateFormat : "yy-mm-dd"
	});
	
	// 导出
    $(".exportBtn").click(function(){
        
    	$('#exportForm').html('');
    	
    	// 获取查询 INPUT 条件
    	var searchTextParam = $('#searchForm input').clone();
    	var searchSelectParam = $('#searchForm select');

    	var html = '';
    	$.each(searchSelectParam, function() {
    		html += "<input type='hidden' name='" 
        		+ $(this).attr("name") 
        		+ "' value='" 
        		+ $(this).find("option:selected").val() 
        		+ "'/>";
        });

    	// 添加查询条件
    	$('#exportForm').append(searchTextParam);
    	$('#exportForm').append(html);
        $('#exportForm').append($(".code_check:checked").clone());

        // 提交
        setTimeout(function(){$('#exportForm').submit();},500);
    });
})
paginationPageSize = 20;
EZ.url = '/fee/fee/fee-detail-';
EZ.getListData = function(json) {
	// 设置订单条数
	var html = '';
	$(".checkAll").attr('checked', false);
	var i = paginationCurrentPage < 1 ? 1 : paginationPageSize
			* (paginationCurrentPage - 1) + 1;

	// 买家ID
	$.each(json.data, function(key, val) {
		var orderId = val.order_id;
		var clazz = key % 2 == 1 ? 'table-module-b2' : 'table-module-b1';
		html += "<tr class='" + clazz + "'>";
		html += '<td  valign="top">';
		html += val['到货时间'] ? val['到货时间'] : '';
		html += '</td>';
		html += '<td  valign="top">';
		html += val['客户单号'] ? val['客户单号'] : '';
		html += '</td>';
		html += '<td  valign="top">';
		html += val['跟踪单号'] ? val['跟踪单号'] : '';
		html += '</td>';
		html += '<td  valign="top">';
		html += val['销售产品'] ? val['销售产品'] : '';
		html += '</td>';
		html += '<td  valign="top">';
		html += val['货物类型'] ? val['货物类型'] : '';
		html += '</td>';

		html += '<td  valign="top">';
		html += val['目的国家'] ? val['目的国家'] : '';
		html += '</td>';

		html += '<td  valign="top">';
		html += val['计费重量'] ? val['计费重量'] : '';
		html += '</td>';
		html += '<td  valign="top">';
		html += val['运费'] ? val['运费'] : '';
		html += '</td>';
		html += '<td  valign="top">';
		html += val['燃油费'] ? val['燃油费'] : '';
		html += '</td>';

		html += '<td  valign="top">';
		html += val['挂号费'] ? val['挂号费'] : '';
		html += '</td>';

		html += '<td  valign="top">';
		html += val['其它杂费'] ? val['其它杂费'] : '';
		html += '</td>';

		html += '<td  valign="top">';
		html += val['总费用'] ? val['总费用'] : '';
		html += '</td>';

		/** 操作 结束* */
		html += "</tr>";

	});

	return html;
}

$(function() {
	$('#submitToSearch').click(function() {
		var start = $('#searchForm .time_start').val();
		var end = $('#searchForm .time_end').val();
		var shipper_hawbcode = $('#searchForm .shipper_hawbcode').val();
		var arrivalbatch_code = $('#searchForm .arrivalbatch_code').val();
		if ($.trim(shipper_hawbcode) == '' && $.trim(arrivalbatch_code) == '') {
			if (!start || start == '' || end == '' || !end) {
				alertTip('请选择日期范围');
				return;
			}
			var d = 30;
			var days = GetDateDiff(start, end);
			if (days > d) {
				alertTip('日期间隔不能大于' + d + '天哦');
				return;
			}
			if (days < 0) {
				alertTip('结束时间必须大于开始时间哦');
				return;
			}
		}

		initData(0);
	});
})