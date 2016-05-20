
EZ.url = '/pickup/pickup/';
var data = {};
var country_code = '';
EZ.getListData = function (json) {
    var html = '';
    if(json.state){
    	 data = json.data;
    	 country_code = json.country_code;
       	 var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
         $.each(json.data, function (key, val) {
             html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
             html += "<td class='ec-center'>" + (i++) + "</td>";
             html += "<td >" + val['提货编号'] + "</td>";
             html += "<td >" + val['申请状态'] + "</td>";
             html += "<td >" + val['申请时间']+" " + "</td>";
             html += "<td ><p pickup_id='"+val['提货编号']+"'>" + val['取件方式'] + "</p></td>";
             html += "<td >" + val['期望时间'] + "</td>";
             html += "<td >" + val['到货总单']+ "</td>"; 
            
             html += "<td >";
             switch(val.status_type){
	             case 'C':
		             html += "<a href='javascript:;' class='cancelPickupBtnC' tip='取消提货申请?' id='"+val['提货编号']+"'>取消申请</a>";	
		             //html += "&nbsp;|&nbsp;<a href='javascript:;' class='editPickupBtn'  id='"+val['提货编号']+"'>编辑</a>";
		             html += "&nbsp;|&nbsp;<a href='/pickup/pickup/print?pickup_order_id="+val['提货编号']+"' class='printPickupBtn'  id='"+val['提货编号']+"' target='_blank'>打印标签</a>";	
		             break;
	             case 'N':
		             html += "<a href='javascript:;' class='cancelPickupBtnN' tip='"+val.tip+"' id='"+val['提货编号']+"'>取消申请</a>";	
		             //html += "&nbsp;|&nbsp;<a href='javascript:;' class='editPickupBtn'  id='"+val['提货编号']+"'>编辑</a>";	
		             html += "&nbsp;|&nbsp;<a href='/pickup/pickup/print?pickup_order_id="+val['提货编号']+"' class='printPickupBtn'  id='"+val['提货编号']+"' target='_blank'>打印标签</a>";		
		             break;
	             default:
		             html += "<a href='/pickup/pickup/print?pickup_order_id="+val['提货编号']+"' class='printPickupBtn'  id='"+val['提货编号']+"' target='_blank'>打印标签</a>";	
		             
             }
             html += "</td>";
             html += "</tr>";
         });
    }else{
        var colspan = $('.table-module-title td').size();
         html +=  "<tr class='table-module-b1'>";
         html += "<td colspan='"+colspan+"' style='color:red;'>" + json.message + "</td>";
         html += "</tr>";
    }
    
    return html;
};

$(function(){
	$('.cancelPickupBtnC').live('click',function(){ 	
		var tip = $(this).attr('tip');
	 
//		if(!window.confirm(tip)){
//			return false;
//		}	
		var id = $(this).attr('id');
		jConfirm( tip,'<{t}>are_you_sure<{/t}>', function(r) {
    		if(r){
    			alertTip('处理中...');
    			$.ajax({
    				type : "POST",
    				url : "/pickup/pickup/cancel",
    				data : {'pickup_order_id':id},
    				dataType : 'json',
    				success : function(json) { 
    					$('#dialog-auto-alert-tip').dialog('close');
    	 				var html = json.message;
    	 				if(json.ask){
    						initData(paginationCurrentPage -1);
    	 				}else{
    	 	 				//
    	 				}

    					alertTip(html);
    				}
    			});	
    		}
    	});
		
		
 	});

	$('.cancelPickupBtnN').live('click',function(){ 	
		var tip = $(this).attr('tip');
		alertTip(tip);
 	});

	$('.editPickupBtn').live('click',function(){ 	
		var id = $(this).attr('id');
		var val = "提货单编辑"+id;
		leftMenu(val,val,'/pickup/pickup/create?pickup_order_id='+id);		 
 	});

	$('.createPickupBtn').live('click',function(){ 
		var val = $(this).val();
		leftMenu(val,val,'/pickup/pickup/create'); 			 
 	});
	$('#query_div').dialog({
        autoOpen: false,
        width: 800,
        maxHeight: 600,
        modal: true,
        show: "slide",
        buttons: [
            {
                text: 'Close',
                click: function () {
                    $(this).dialog("close");
                }
            }
        ],
        close: function () {
             
        }
    });
	$('.trackBtn').live('click',function(){
		var pickup_id = $(this).parent().attr('pickup_id');
		var track_number = $(this).text();
		$('#track_number').text(track_number);
		$('#query_div .table-module-list-data').html('<tr><td colspan="3" style="text-align:center; "><img src="/images/base/loading.gif"/></td></tr>');
		$('#query_div').dialog('open');
		//return;
		$.ajax({
			type : "POST",
			url : "/pickup/pickup/query",
			data : {'pickup_order_id':pickup_id},
			dataType : 'json',
			success : function(json) { 
 				var html = json.message;
 				if(json.ask){
 					html = '';
 					if(json.steps){
 						$.each(json.steps,function(k,v){
 							html+="<tr>";
 							html+="<td>";
 							html+=(k+1);
 							html+="</td>";
 							html+="<td>";
 							html+=v.acceptTime;
 							html+="</td>";
 							html+="<td>";
 							html+=v.acceptAddress;
 							html+="</td>";
 							html+="</tr>";
 						})
 					}
 					$('#query_div .table-module-list-data').html(html);					 
 				}else{
 	 				//
 					$('#query_div .table-module-list-data').html('<tr><td colspan="3" style="">'+html+'</td></tr>');
 				}
 
			}
		});		
	})
	
});

$(function(){
	var dayNamesMin = ['日', '一', '二', '三', '四', '五', '六'];
	var monthNamesShort = ['01月', '02月', '03月', '04月', '05月', '06月', '07月', '08月', '09月', '10月', '11月', '12月'];
	$.timepicker.regional['ru'] = {
		timeText : '选择时间',
		hourText : '小时',
		minuteText : '分钟',
		secondText : '秒',
		millisecText : '毫秒',
		currentText : '当前时间',
		closeText : '确定',
		ampm : false
	};
	$.timepicker.setDefaults($.timepicker.regional['ru']);
	$('#createDateFrom,#createDateEnd').datetimepicker({
		dayNamesMin : dayNamesMin,
		monthNamesShort : monthNamesShort,
		changeMonth : true,
		changeYear : true,
		dateFormat : 'yy-mm-dd'
	});
});
<{if $smarty.get.pickup_order_id}>
$(function(){
	 $('#pickup_order_id').val('<{$smarty.get.pickup_order_id}>');
	 initData(0);
});
<{/if}> 