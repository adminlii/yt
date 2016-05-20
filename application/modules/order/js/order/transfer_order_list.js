var quickId = '<{$quickId}>';
var statusArr = <{$statusArrJson}>;
var warehouseJson = <{$warehouseJson}>;
var warehouseShipping = {};
paginationPageSize = 50;
EZ.url = '/order/transfer-order-list/';

function getDetail(order_id_arr){
	//订单明细
	$.ajax({
		type: "POST",
		async: false,
		dataType: "json",
		url: '/order/transfer-order-list/get-list-detail-list',
		data: {'order_id_arr':order_id_arr},
		success: function (results) {
			$.each(results,function(k,json){
				if(json.ask){
					var html = '';
					$.each(json.data, function(kk, v) {
			            html +='';
			            html +='<div style="clear:both;">';
	    	            html += '<div class="sku">SKU: ' + v.product_sku+'</div>' ;
			            html += '<div class="qty">Qty: ' + (v.quantity)+'</div>' ;
			            html += '</div>';
			        });
					
					$('#order_detail_'+k).html(html);
				};
			});
		}
	});
}

function loadData(page, pageSize) {
    EZ.listDate.myLoading();
    $.ajax({
        type: "POST",
        async: false,
        dataType: "json",
        url: EZ.url + "list/a/a/page/" + page + "/pageSize/" + pageSize,
        data: EZ.searchObj.serializeArray(),
        error: function () {
            paginationTotal = 0;
            EZ.listDate.EzWmsSetSearchData({msg: 'The URL request error.'});
            return;
        },
        success: function (json) {
            paginationTotal = json.total;
            if (!isJson(json)) {
                paginationTotal = 0;
                EZ.listDate.EzWmsSetSearchData({msg: 'Returns the data type error.'});
                return;
            }
            if (json.state == '1') {
            	EZ.listDate.html(EZ.getListData(json));
            	
            	var order_id_arr = [];
            	$.each(json.data,function(k,v){
            		order_id_arr.push(v.to_id);
            	});
            	//获取明细
            	getDetail(order_id_arr);
            	//隐藏多行的SKU
            	hideExcessSku();
            } else {
                EZ.listDate.EzWmsSetSearchData({state: 1});
            }
        }
    });
}

EZ.getListData = function(json) {
	//设置订单条数
	setPageTotal(json.total);

    var clone = $('#fix_header_content .clone').clone();
    $('#fix_header').html(clone);

	var html = '';
	if($('.chooseTag').size()<1){
		$('.statusTag1').addClass('chooseTag');
		var opHtml = '';
		$.each(statusArr['1']['actions'],function(k,v){
			opHtml += v;
		})
		$(".opDiv").html(opHtml);
	}
	$(".checkAll").attr('checked', false);
	var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;

    //买家ID
	$.each(json.data, function(key, val) {	
		var orderId = val.order_id;
		var clazz = key%2==1?'table-module-b2':'table-module-b1';
		html += "<tr class='"+clazz+"' id='order_wrap_"+val.order_id+"'>";
		html += '<td class="ec-center"><input type="checkbox" class="checkItem" name="orderId[]" ref_id="'+val.two_code+'" value="' + val.to_id + '"/></td>';

		/**单头信息 开始**/
    	html += '<td style="word-wrap:break-word;" class="order_line" valign="top">';
    	var order_code_title = "\"" + "转仓单:" + val.two_code + "\"";
    	var order_tag_a = "<a href='javascript:void(0)' " +
        "onclick='leftMenu(\"Order\"," + order_code_title + ",\"/order/transfer-order-list/detail/paramId/" + val.to_id + "\")'>" + 
        val.two_code + "</a>";
		html += '<p class="refrence_no_platform ellipsis"><{t}>order_code<{/t}>:'+ order_tag_a + '</p>';
		html += '<p style="" class="ellipsis"><{t}>源仓库<{/t}>:' +  val.warehouse_code +'</p>'; 
		html += '<p style="" class="ellipsis"><{t}>目的仓库<{/t}>: ' +val.to_warehouse_code+'</p>'; 
		if($('#status').val()==''){
			html += '<p style="" class="order_status_title ellipsis"><{t}>order_status<{/t}>:' + (statusArr[val.order_status].name) +'</p>';  			
		}

		html+='<p>';
        html += ' <span class="logIcon " ref_id="'+val.two_code+'" style="float: left;cursor: pointer;" title="View Log"></span>';   
		html+='</p>';
		html += '<p style="clear:both;"></p>';
		html += '</td>';
		/**单头信息 结束**/
		
		/**订单详情信息 开始**/
        html += '<td valign="top">';
        html += '<div  class="product_data order_detail" id="order_detail_'+val.to_id+'"><div style="text-align: center;margin-top:30px;"><img src="/images/base/loading.gif"/></div></div>'; 
        html += '</td>';
		/**订单详情信息 结束**/
		
		/**配送信息 开始**/
        html += '<td style="word-wrap: break-word;" valign="top">';
        html += '<p class="ellipsis"><{t}>shipping_method<{/t}>:'+(val.shipping_method?val.shipping_method:'')+'</p>';    
		html += '<p style="" class="ellipsis"><{t}>tracking_no<{/t}>:' + (val.shipping_method_no&&val.shipping_method_no!='null' ? val.shipping_method_no:'') +'</p>';     
        html += '</td>';
		/**配送信息 结束**/

		/**时间 开始**/
        html += '<td  valign="top">';
        html += '<{t}>order_date_create<{/t}>:' + (val.date_create?val.date_create:'' ) + '<br/>';
		html += '<{t}>order_date_release<{/t}>:' + (val.date_release?val.date_release:'') + '<br/>';
		html += '<{t}>order_date_ship<{/t}>:' + (val.date_warehouse_shipping ?val.date_warehouse_shipping:'') + '<br/>';;
        html += '</td>';
		/**时间 结束**/
        
        /**操作 开始**/
        html += '<td  valign="top">';
        html += '<a href="javascript:;" class="cpyBtn" ref_id="'+val.two_code +'" url="/order/transfer-order/create/cpy/1/refId/'+val.two_code+'" ><{t}>cpy<{/t}></a>';
        if(val.order_status=='1'){
            html += '&nbsp;&nbsp;<a href="javascript:;" class="orderDetail" ref_id="'+val.two_code+'" url="/order/transfer-order/create/refId/'+val.two_code+'" ><{t}>edit<{/t}></a>';        	
        }
        
        html += '</td>';
		/**操作 结束**/
		html += "</tr>";
	
	});
	
	return html;
}

/**
 * 检查订单是否超过3个SKU，超过则隐藏
 */
function hideExcessSku(e){
	var data = $(".product_data");
	if(typeof(e) != "undefined"){
		data = e;
	}
	data.each(function(i){
		var product_data_lines = $(this).find(".product_data_line");
		//如果SKU行数大于3行，将超过的进行隐藏
		var max_num = 3;
		if(product_data_lines.length > max_num){
			product_data_lines.each(function(j){
				if((j +1) > max_num){
					$(this).hide();
					$(this).next().hide();
				}
			});
			if(!e){
				var product_line_last = product_data_lines.next().last();
				var html = '<div class="product_data_option" style="float:left;width:99%;text-align: center;">'+
				'<a style="line-height: 10px;" class="product_data_hide" data_show="1" href="javascript:;"><span class="tips_title">'+$.getMessage("show_remaining_sku")+'</span> <b style="color:red;">'+ (product_data_lines.length - max_num) +'</b> SKU</a>'+
				'</div>';
				product_line_last.after(html);				
			}
		}
	});
}

function tongji(){	
	
	$('.statusTag').each(function(k,v){
    	$('.count',this).html('(0)');
    })
    var platform=$('#platform').val();
    var order_type=$('#order_type').val();
	$.ajax({
		type: "POST",
//		async: false,
		dataType: "json",
		url: '/order/transfer-order-list/get-statistics',
		success: function (tongji) {
			$.each(tongji,function(k,v){
		    	$('.statusTag'+v.order_status+' .count').html('('+v.count+')').css('color','red');
		    })
		}
	});
	/**/    
}
$(function (){
	tongji();
})

/**
 * 设置订单条数
 */
function setPageTotal(total){
	var pageTotalHtml="<span>当前共</span>";
	pageTotalHtml+="<b style='color:red;font-weight:bold;padding:0 2px;'>";
	pageTotalHtml+=total;
	pageTotalHtml+="</b>条订单"+'，已选择了<span class="checked_count">0</span>条';
	
	$("#pageTotal").html(pageTotalHtml);
};
	
$(function(){
	$('.ellipsis').live('mouseover',function(){
		var content = $(this).text();
		if($(this).hasClass('order_no_copy')){
			content += " (在“订单”上双击可复制单号)";
		}
		$(this).attr('title',content);
	});
	
	$('#loading').click(function(evt) {
		evt.preventDefault();
		$(this).overlay({
			effect: 'fade',
			opacity: 0.5,
			closeOnClick: true,
			onShow: function() {
				
			},
			onHide: function() {
				
			},
		})
	});
	
	/**
	 * 查看订单操作日志
	 */
	$('.logIcon').live('click',function(){
		var ref_id = $(this).attr('ref_id');
		$.ajax({
			type: "POST",
			url: "/order/transfer-order/get-order-log",
			data: {'ref_id':ref_id},
			dataType:'json',
			success: function(json){
				var html = '';
				if(json.ask){
					html +='<b>订单: '+ref_id+'</b><br/> 状态说明：<br/>';
					html += '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table-module"><tbody><tr class="table-module-b1">';
					$.each(json.statusArr,function(k,v){
						html+= '<td>' + k +' : '+v.name+'</td>';
					})
					html += '</tr></tbody></table>';
					html +='日志信息:<br/>';
					html += '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table-module"><tbody>';
					$.each(json.data,function(k,v){
						html += (k + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
						html += '<td style="width:70px;">'+v.op_username+'</td>';
						html += '<td style="width:140px;">'+v.create_time+'</td>';
						html += '<td>'+v.log_content+'</td>';
						html += '</tr>';
					});
					html += '</tbody></table>';
					alertTip(html,800,480);
				}else{
					var html = json.message;	
					alertTip(html,800,480);
				}
			}
		});
	});
	
	$('.submitToSearch').click(function(){
		//每次查询请，清空订单条数
		setPageTotal(0);
	});
});

$(function() {
	$('.orderDetail').live('click',function(){
		var url = $(this).attr('url')+"?t="+Math.random();
		var ref_id = $(this).attr('ref_id');
        leftMenu('order-'+ref_id,'<{t}>edit_order<{/t}>',url);
	})
	
	$('.cpyBtn').live('click',function(){
		var url = $(this).attr('url')+"?t="+Math.random();
		var ref_id = $(this).attr('ref_id');
        leftMenu('order-'+ref_id,'<{t}>cpy_order<{/t}>',url);
	})
	
	/**
	 * 是否取消订单
	 */
	var _rmaCancelOrderBol = false;
	$('.rmaCancelOrder').live('change',function(){
		if($(this).is(":checked")){
			$(this).next().val('1');
			_rmaCancelOrderBol = true;
		}else{
			$(this).next().val('0');
			_rmaCancelOrderBol = false;
		}
	});
	
	$('#inventory_div').dialog({
        autoOpen: false,
        width: 800,
        modal: true,
        show: "slide",
        buttons: [            
            {
                text: "取消(Cancel)",
                click: function () {
                    $(this).dialog("close");
                    adjustFormatForRma(0);
                }
            }
        ], close: function () {
        }
    });
	
    var editSuccess = function (json) {
    	$("#dialog-auto-alert-tip").dialog("close");
        switch (json.state) {
            case 1:
                if(json.rma_resend){
                	var tipObj = $("#validateTips");
                	var orderId = json.order_id;
                	var rmaId = json.rma_id;
                	tipObj.html(json.successMessage);
                }else{	                	
                	alertTip(json.message);
                	$("#dialog-edit-alert-tip").dialog("close");
                }
                break;
            case 2:
                $("#dialog-edit-alert-tip").dialog("close");
                loadData(paginationCurrentPage, paginationPageSize);
                alertTip(json.message);
                break;
            default:
                var html = '';
                var tipObj = $("#validateTips");
                if (json.errorMessage == null)return;
                $.each(json.errorMessage, function (key, val) {
                    html += '<span class="tip-error-message">' + val + '</span>';
                });
                if(_rmaCancelOrderBol){
                	html += '<span class="tip-warning-message">若提示截单失败，可尝试去掉“同时取消订单”的勾选</span>';	                	
                }
                tipObj.html(html);
                break;
        }
    };

	$('#module-container .mainStatus').live('mouseover',function(){
		$('.sub_status_container',this).show();
	}).live('mouseout',function(){
		$('.sub_status_container',this).hide();		
	});
	
	/**
	 * 状态点击处理
	 * @param obj
	 * @returns
	 */
	function statusBtnClick(obj){
		var status = obj.attr('status');
		$(".statusTag").removeClass('chooseTag');
		if(status==''){
			$(".statusTag-1").addClass('chooseTag');			
		}else{
			$(".statusTag"+status).addClass('chooseTag');			
		}
		var opHtml = '';
		opHtml += '';
		if(statusArr[status]){
			$.each(statusArr[status]['actions'],function(k,v){
				opHtml += v;
			})
		}	

		$(".opDiv").html(opHtml);
		$('#status').val(obj.attr('status'));	
		$('.submitToSearch:visible').click();
		
		//控制按钮隐藏和显示
		hideOperateBt();
		// loadData(1, paginationPageSize);
	}
	
	/**
	 * 订单状态切换
	 */
	$("#module-container .statusTag").live('click', function() {
		var obj = $(this);
		statusBtnClick(obj);
	});

	/**
	 * 订单导出按钮
	 */
	$(".exportBtn").live('click', function() {
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<{t}>pls_select_orders<{/t}>');
			return false;
		}
		$("#export_wrap_div").dialog('open');
	});
	
	/**
	 * 截单按钮
	 */
	$('.cancelOrder').live('click', function() {
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<{t}>pls_select_orders<{/t}>');
			return false;
		}
		var refIds = [];
		$(".checkItem:checked").each(function(){
			refIds.push($(this).attr('ref_id'));
		})
		var refIds = [];
		$(".checkItem:checked").each(function(){
			refIds.push($(this).attr('ref_id'));
		})
		var status = $(this).attr('status');
		$('#cancel_order_status').val(status);
		$('#cancel_order_ref_ids').val(refIds.join(';'));
		$('#cancel_order_div').dialog('open');
	});
	
	$('#reason_type option').click(function(){
		if($(this).val()==''){
			$('#reason').val('');
			return;
		}
		$('#reason').val($(this).text());
	});
	
	/**
	 * 截单
	 */
	$('#cancel_order_div').dialog({
		autoOpen: false,
		width: 400,
		maxHeight: 480,
		modal: true,
		show: "slide",
		buttons: [
			{
				text: '<{t}>cancel_order<{/t}>',
				click: function () {
					var reason = $('#reason').val();
					if($.trim(reason)==''){
						alertTip('<{t}>pls_enter_or_select_some_message<{/t}>');
						return false;
					}
					var this_ = $(this);
					this_.dialog("close");	
					alertTip('Loading...',300);	
					var param = $("#cancel_order_form").serialize();
					$.ajax({
						type : "POST",
						url : "/order/transfer-order/cancel-order",
						data : param,
						dataType : 'json',
						success : function(json) {	
							$('#dialog-auto-alert-tip').dialog('close');		
							var html = '';							
							$.each(json, function(k, v) {
								html+= "<p><{t}>order_code<{/t}>: "+v.ref_id+" :"+v.message+"</p>";
								
							});
							alertTip(html,600,400);	
							initData(paginationCurrentPage-1);
							
							setTimeout(function(){tongji();},500);
						}
					});
					
				}
			},
			{
				text: '<{t}>cancel<{/t}>',
				click: function () {
					var this_ = $(this);
					this_.dialog("close");	
				}
			}],
		close: function () {},
		open:function(){
			$('#reason').val('');
			$('#reason_type').val('');
		}
	});
	
	/**
	 * 订单审核按钮
	 */
	$('.orderVerify').live('click', function() {
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<{t}>pls_select_orders<{/t}>');
			return false;
		}
		
		var refIds = [];
		$("#listForm .checkItem:checked").each(function(){
			refIds.push($(this).attr('ref_id'));
		});
		var param = {'refIds':refIds};	
		
		$.ajax({
			type : "POST",
			url : "/order/transfer-order/order-verify",
			data : param,
			dataType : 'json',
			success : function(json) {
				var html = '';							
				html+="Result:<br/>";
				html+="<{t}>fail<{/t}> <span class='status_7'>"+json.fail_count+"</span>，";
				html+="<{t}>success<{/t}> <span class='status_3'>"+json.success_count+"</span>，";
				html+="<{t}>stock_insufficient<{/t}> <span class='status_6'>"+json.quehuo_count+"</span>，";
				html+="<{t}>fund_insufficient<{/t}> <span class='status_6'>"+json.fund_count+"</span><br/>";
				
				$.each(json.result,function(kk,vv){
					html+='<p class="result_'+vv.ask+'">';
					html +=(kk+1)+":";
					html+=vv.ref_id+":"+vv.message+"";
					html+='</p>';
				});
				
				setTimeout(function(){
					initData(paginationCurrentPage - 1);
				},500);
				
				setTimeout(function(){tongji();},500);
				
				alertTip(html, 800,500);
			}
		});	
	});

	/**
	 * 订单审核，弹出框事件 开始
	 */
	$('#warehouse').live('change',function(){
		var warehouseId = $(this).val();
		var shipping = warehouseShipping[warehouseId].shipping;
		var html = '';
		$.each(shipping,function(k,v){
			html += '<option value="' + v.code + '">' + v.code+'['+v.sm_name_cn+']' + '</option>';			
		});
		$('#shipping_method_arr').html(html);
	});

	$('#warehouse_verify').live('change',function(){
		var warehouseId = $(this).val();
		var shipping = warehouseShipping[warehouseId].shipping;
		var html = '';
		$.each(shipping,function(k,v){
			//html += '<option value="' + v.code + '">' + v.code+'['+v.sm_name_cn+']'+'['+v.sm_carrier_number+']' + '</option>';	
			html += '<option value="' + v.code + '">' + v.code+' ['+v.sm_name_cn+']' + '</option>';			
		});
		$('#shipping_method_arr_verify').html(html);
	});
	/**
	 * 订单审核，弹出框事件 结束
	 */
	$("#tag_form_div").dialog({
		autoOpen : false,
		width : 300,
		maxHeight : 500,
		modal : true,
		show : "slide",
		buttons : [{
			text : 'Ok',
			click : function() {
				var this_ = $(this);
				var param = $("#listForm").serialize();
				var tag_input = $('#tag_input').val();
				if ($.trim(tag_input) == '') {
					alertTip('请输入自定义标记名称');
					return false;
				}
				if ($(".checkItem:checked").size() == 0) {
					alertTip('<{t}>pls_select_orders<{/t}>');
					return false;
				}
				var param2 = $("#tag_form").serialize();
				param += "&"+param2;
				this_.dialog("close");
				$.ajax({
					type : "POST",
					url : "/order/order/user-defined-tag",
					data : param,
					dataType : 'json',
					success : function(json) {
						if (json.ask) {
							var html = '';
							$.each(json.result, function(k, v) {
								html += "<p>" +v.ref_id+':'+ v.message + "</p>";
							})
							alertTip(html, 600,400);
							initData(paginationCurrentPage - 1);
							getUserDefinedTag();
						} else {
							alertTip(json.message);
						}
					}
				});
			}
		}, {
			text : 'Close',
			click : function() {
				$(this).dialog("close");
			}
		}],
		close : function() {

		},
		open : function() {
			$.ajax({
				type : "get",
				url : "/order/order/user-defined-tag/order_status/"+$('#ot_id_order_status').val(),
				data : {},
				dataType : 'json',
				success : function(json) {
					var html = '<ul>';
					$.each(json, function(k, v) {
						html += '<li><a href="javascript:;" class="selectTag">' + v.tag_name + '</a><a href="javascript:;" class="deleteTag" val="' + v.ot_id + '">删除</a></li>';
					})
					html += "</ul>";
					$("#tag_select").html(html);
				}
			});
		}
	});
	$('.selectTag').live('click', function() {
		var val = $(this).text();
		$('#tag_input').val(val);
	});

	$('#export_template').live('change',function(){
		var file = $('option:selected',this).attr('file');
		if(file!=''){
			$(this).siblings('a').attr('href','/file/'+file).show();			
		}else{
			$(this).siblings('a').attr('href','/file/'+file).hide();			
		}
	});
	$("#export_wrap_div").dialog({
		autoOpen : false,
		width : 600,
		maxHeight : 480,
		modal : true,
		show : "slide",
		buttons : [{
			text : 'Ok',
			click : function() {
				var excel_tpl_id = $('#export_template').val();
				$("#listForm").attr('action', '/order/order/export/tpl_id/' + excel_tpl_id).attr('target', '_blank').submit();
				$(this).dialog("close");
			}
		}, {
			text : 'Close',
			click : function() {
				$(this).dialog("close");
			}
		}],
		close : function() {

		},
		open : function() {
			$.ajax({
				type : "POST",
				url : "/order/order/get-export-template",
				data : {
					'type' : '1'
				},
				dataType : 'json',
				success : function(json) {
					if (json.ask) {
						var html = '';
						$.each(json.result, function(i, v) {
							html += '<option value="' + v.excel_defined_id + '" file="'+(v.tpl_name?v.tpl_name:'')+'">' + v.excel_defined_name + '</option>';
						});
						$("#export_template").html(html);
						$('#export_template').change();
					} else {
						alert(json.message);
					}
				}
			});
		}
	});

	$('.baseExport').live('click', function() {
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<{t}>pls_select_orders<{/t}>');
			return false;
		}
		$("#listForm").attr('action', '/order/order/base-export/').attr('target', '_blank').submit();
	})
});

$(function() {
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

	$('#payDateFrom,#payDateEnd,#createDateFrom,#createDateEnd,#shipDateFrom,#shipDateEnd,#verifyDateFrom,#verifyDateEnd,.baseDateFrom,.baseDateEnd').datetimepicker({
		dayNamesMin : dayNamesMin,
		monthNamesShort : monthNamesShort,
		changeMonth : true,
		changeYear : true,
		dateFormat : 'yy-mm-dd'
	});
	$(".checkAll").live('click', function() {
		$(".checkItem").attr('checked', $(this).is(':checked'));

		var size = $('.checkItem:checked').size();
		$('.checked_count').text(size);
	});

	$('.userDefinedSelect').hover(function() {
		$('.define_item').show();
	}, function() {
		$('.define_item').hide();
	});
	$('.define_item').live('click', function() {
		// $('#define_item').text($(this).text());
	});
	area = $('<textarea rows="14" name="content" id="content" cols="80" style="width: 770px;border:1px solid #ccc;"></textarea>');
	$('#title').live('change', function() {
		var item = $('option:selected', this).attr('item');
		$('#item').val(item);
	});
	
	$('#select_module').live('change', function() {
		var key = $('option:selected', this).attr('index');
		if (key !== '') {
			area.val(templateList[key].content);
//			$("#messageSubject").val(templateList[key].subject + ',物品名称:' + $('#title').val() + ' 物品编号:' + $('#item').val());
			$("#messageSubject").val(templateList[key].subject + ',Item Title:[item_title]  ItemID:[item_id]');
		}
	});

	/**
	 * 订单审核按钮点击事件
	 */
	$('.orderSetWarehouseShipBtn').live('click',function(){
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<{t}>pls_select_orders<{/t}>');
			return false;
		}
		$('#verify_div').dialog('open');
	});

});
$(function(){
	/**
	 * 滚动保持头部固定
	 */
	var clone = $('#fix_header_content .clone').clone();
	$('#fix_header').append(clone);
	$(window).scroll(function(){
		var off = $('#fix_header_content').offset();
		var top = off.top;
		var scrollTop = $(this).scrollTop();
		if(scrollTop>top){
			$('#fix_header').show();
			$('.sub_status_container').hide();
		}else{
			$('#fix_header').hide();			
		}
		
	});
	
	/**
	 * 选择订单checkbox
	 */
	$('.checkItem').live('click',function(){
		var size = $('.checkItem:checked').size();
		$('.checked_count').text(size);
	});
	
	
})

/**
 * 控制订单状态下的特殊标记标签 / 悬浮“过滤条件”窗口
 * @author Frank
 * @date 2013-12-3 19:40:24
 */
//需要隐藏的操作按钮
var _OperateBtHide = new Array();
/**
 * 弹出等等对话框
 */
function alertLoadTip(str){
	var tips = "<span class='tip-load-message'>" + str + "</span>";
	alertTip(tips);
}

/**
 * 隐藏指定操作按钮
 */
function hideOperateBt(){
	if(typeof(_OperateBtHide) == 'unfinished'){
		return;
	}
	//隐藏对应的操作啊按钮
	if(_OperateBtHide.length > 0){
		$('.opDiv').find('.baseBtn').show();
		for ( var int = 0; int < _OperateBtHide.length; int++) {
			$('.opDiv').find('.' + _OperateBtHide[int]).hide();
		};
	};
}

/**
 * 设置需要隐藏的按钮
 * @returns
 */
function setHideOpteratBtArr(classNameArr){
	_OperateBtHide = new Array();
	for ( var int = 0; int < classNameArr.length; int++) {
		_OperateBtHide[int] = classNameArr[int];
	};
}

$(function(){
	/*
     * 控制订单状态的，处理标记显示和隐藏--开始
     */
	$('.order_title_container').click(function(){
		var is_original = $(this).attr('is_original');
		if(is_original == '1'){
			$('#process_again').val('1');
		}else{
			var process_again_id = $(this).attr('data-id');
			$('#process_again').val(process_again_id);
		}
	});
	
	$('.order_status_tag').click(function(){
		//隐藏域赋值
		var process_again_id = $(this).attr('data-id');
		$('#process_again').val(process_again_id);
		

		//更改文本显示内容
		$(this).parent().hide();
		var key = $(this).attr('data-status');
		$('.order_title' + key).html($(this).attr('title'));
		$('.order_title' + key).attr('title',$(this).attr('title'));
		$('.order_title' + key).parent().attr('is_original','0');
		$('.order_title' + key).parent().attr('data-id',process_again_id);
		//显示原始状态
		$('.order_status_text_parent' + key).show();

		//显示和隐藏操作按钮
		setHideOpteratBtArr(['ordersMark']);
	});

	$('.order_status_text').click(function(){
		var process_again_id = $(this).attr('data-id');
		$('#process_again').val(process_again_id);

		//更改文本显示内容
		$(this).parent().hide();
		var key = $(this).attr('data-status');
		$('.order_title' + key).html($(this).attr('title'));
		$('.order_title' + key).attr('title',$(this).attr('title'));
		$('.order_title' + key).parent().attr('is_original','1');
		$('.order_title' + key).parent().attr('data-id',process_again_id);

		//隐藏原始状态
		$('.order_status_text_parent' + key).hide();
		$('.order_status_tag_parent' + key).show();
		//显示和隐藏操作按钮
		setHideOpteratBtArr(['unOrdersMark']);
	});

	$(".statusTag").live({
		mouseout: function() {
			$('.Tab').find('.order_status_tag_container').hide();
		},
		mouseover: function() {
			
			$(this).find('.order_status_tag_container').show();
		}
	});
	
	/*
     * 控制订单状态的，处理标记显示和隐藏--结束
     */
	
	/*
	 * 控制“过滤条件”窗口，显示、隐藏，拖动--开始
	 */
	var _show_more_float_container_handle = $('.table-module-seach-float-handle');
	var _show_more_float_container = $('.table-module-seach-float');
	$("#show_more_float").click(function(){
		_show_more_float_container.animate({top:'0px'},'slow');
	});

	$("#hide_more_float").click(function(e){
		e.stopPropagation();
		_show_more_float_container.animate({top:'-450px'},'slow');
	});
	
	//鼠标拖动的监听
	_show_more_float_container_handle.mousedown(function(e){
		//点击"收起"，鼠标事件return
		var element = e.target;
		if(element.tagName == 'A' && (element.getAttribute("name") == 'hide_more_float' ||  element.getAttribute("class") == 'link_order_source')){
			return;
		}
		loadMasks();
		$(this).css("cursor","move");  
		var disX = e.clientX - _show_more_float_container[0].offsetLeft;
		var disY = e.clientY - _show_more_float_container[0].offsetTop;
		
		$(document).mousemove(function(e){
			xx=e.clientX - disX;
			yy=e.clientY - disY;
			_show_more_float_container.css({"left":xx,"top":yy});
		});
		
		$(document).mouseup(function(){
			closeMasks();
			_show_more_float_container.css("cursor","default");  
			$(document).unbind('mousemove');
			$(document).unbind('mouseup');
		});
		
		return false;
	});
	
	//显示过滤条件的选中项，在“高级搜索”中
	_show_more_float_container.find(".link_option").click(function(){
		var selected_val =  _show_more_float_container.find(".current");
		var content = '';
		selected_val.each(function(i){
			if(!$(this).hasClass('link_option_title')){
				var title_name =  $(this).parent().prev().html();//过滤条件的名称
				var title_vale =  $(this).html();				 //选中的具体类型
				content += "["+ title_name + title_vale +"]";
			}
		});
		$('.float_selected_title').html(content);
	});
	/*
	 * 控制“过滤条件”窗口，显示、隐藏，拖动--解释
	 */
});

/**
 * 加载遮罩  function loadMasks(top){ 
 */
var _masks_i = null;
var _masks_d = null;
function loadMasks(){                
	var height = $(window).height();
	if(height < $("body").height()){
		height = $("body").height();
	}
	_masks_i = $('<iframe></iframe>').appendTo($('body'));			
	_masks_i.css({"display":"block","width":"100%","height":height,"background":"gray","position":"absolute","top":"0","left":"0","z-index":"100","opacity":"0.1"});   //z-index控制层次用的，相当于一个垂直页面的的轴，
	_masks_d = $('<div></div>').appendTo($('body'));
	_masks_d.css({"display":"block","width":"100%","height":height,"background":"gray","position":"absolute","top":"0","left":"0","z-index":"101","opacity":"0.7"});
}
/**
 * 关闭遮罩 
 */
function closeMasks(){
	try{
		_masks_i.remove();
		_masks_d.remove();
		_masks_i = null;
		_masks_d = null;
	}catch (e) {
	}         
}