<{if isset($templateList)}>
var templateList =<{$templateList}>;
<{/if}>
var quickId = '<{$quickId}>';
var statusArr = <{$statusArrJson}>;
var warehouseJson = [];
var user_account_arr_json = [];
var warehouseShipping = {};
paginationPageSize = 20;
EZ.url = '/order/order-list/';

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
		url: '/order/order-list/get-statistics',
		data: {'platform':platform,'order_type':order_type},
		success: function (tongji) {
			var total = 0;
			$.each(tongji,function(k,v){
		    	$('.statusTag'+v.order_status+' .count').html('('+v.count+')');
		    	
		    	// 总数量不统计废弃订单
		    	if(v.order_status != 'E') {
		    		total+=parseInt(v.count);
		    	}
		    });
	    	$('.statusTagAll .count').html('('+total+')');
			
		}
	});
	/**/    
}
$(function (){
	tongji();
	/**
	 * 设置发货仓库
	 */
	$("#ship_warehouse").attr('name','warehouse_id');
	
	/**
	 * 鼠标悬停在SKU Title上，显示图片
	 */
	var _ImgPreviewMaxWidth = 165;// 预览图片最大宽度
	var _ImgPreviewTimeSort = null;
    $('body').append('<div id="orderPreviewDiv" style="display:none;"></div>');
    $('.imgPreview').live('mousemove',function(e){
    	clearTimeout(_ImgPreviewTimeSort);
		var _this = $(this);
		_ImgPreviewTimeSort = setTimeout(function(){
			var off = _this.offset();
	    	var x = off.left+20;
	    	var y = off.top + 20;
	    	var tempIMG = new Image();
//	        tempIMG.src = _this.attr('url');
//	    	tempIMG.onload = function(){
//	    		var w = tempIMG.width;
//    	    	w = w>maxW?maxW:w;
//	    		var img = '<img src="'+_this.attr('url')+'" width="'+w+'"/>';
//        		$('#orderPreviewDiv:hidden').html(img).css({'position':'absolute','top':y+'px','left':x+'px','border':'1px solid #ccc'}).slideDown(100); 
//	    	};
	        var img = '<img src="'+_this.attr('url')+'" width="'+_ImgPreviewMaxWidth+'"/>';
	        $('#orderPreviewDiv:hidden').html(img).css({'position':'absolute','top':y+'px','left':x+'px','border':'1px solid #ccc'}).slideDown(100);
        },250);
    	
    }).live('mouseout',function(e){
    	clearTimeout(_ImgPreviewTimeSort);
    	$('#orderPreviewDiv').html("");
    	$('#orderPreviewDiv').hide();
    });
	
    
    
    //移动鼠标时，图片也需要隐藏
    $(window).scroll(function(){
    	clearTimeout(_ImgPreviewTimeSort);
    	$('#orderPreviewDiv:visible').html("");
    	$('#orderPreviewDiv:visible').hide();
    });
})

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

/**
 * 监听显示、隐藏多余sku的事件
 */
$(".product_data_hide").live("click",function(){
	var data_show = $(this).attr("data_show");
	var _this = $(this);
	var product_data = _this.parent().parent();
	if(data_show == 1){
		//显示隐藏的sku
		var produc_data_lines = product_data.find(".product_data_line");
		produc_data_lines.each(function(){
			$(this).show();
			$(this).next().show();
		});
		_this.attr("data_show",0);
		_this.find("span").html($.getMessage("hide_excessive_sku"));
	}else{
		//隐藏显示的过多sku
		hideExcessSku(product_data);
		_this.attr("data_show",1);
		_this.find("span").html($.getMessage("show_remaining_sku"));
	}
});

/**
 * 设置订单条数
 */
function setPageTotal(total){
	var pageTotalHtml="<span>当前共</span>";
	pageTotalHtml+="<b style='color:red;font-weight:bold;padding:0 2px;'>";
	pageTotalHtml+=total;
	pageTotalHtml+="</b>条订单"+'，已选择了<span class="checked_count">0</span>条';
	
	$(".pageTotal").html(pageTotalHtml);
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
	
	$('.submitToSearch').click(function(){
		//每次查询请，清空订单条数
		setPageTotal(0);
	});
});

function ajaxFileUpload() {
	alertTip('数据处理中。。。', 650);
	$.ajaxFileUpload({
		url : '/order/order/upload-to-confirm-order-dispatch/tpl_id/' + $("#import_template").val(),
		secureuri : false,
		fileElementId : 'fileToUpload',
		dataType : 'json',
		success : function(json, status) {
			$('#biaojifahuo_div').dialog('close');
			if (json.ask) {
				var html = '';
				html += '成功:' + json.success_count + "条&nbsp;&nbsp;&nbsp;&nbsp;失败:" + json.fail_count + '<br/>';
				$.each(json.result, function(k, v) {
					html += (k + 1) + ":订单号:"+v.ref_id+"&nbsp;&nbsp;" + v.message + '<br/>';
				});
				initData(paginationCurrentPage - 1);
				$('#dialog-auto-alert-tip').html(html);
			} else {
				$('#dialog-auto-alert-tip').html(json.message);
				
			}

		},
		error : function(data, status, e) {
			//alertTip(e,500);
		}
	})

	return false;

}


function getUserDefinedTag() {
	$.ajax({
		type : "get",
		url : "/order/order/user-defined-tag",
		data : {},
		dataType : 'json',
		success : function(json) {
			var html = '';
			$.each(json, function(k, v) {
				html += '<li ot_id="' + v.ot_id + '" class="define_item statusTag" style="float: none; display: none;" status="' + v.k + '"><a href="javascript:void (0)">* ' + v.tag_name + '</a></li>';
			})

			$('.define_item').remove();
			$('.userDefinedSelect ul').append(html);
		}
	});
}

function updateOrderStatus(param){
	$.ajax({
		type : "POST",
		url : "/order/order/update-status",
		data : param,
		dataType : 'json',
		success : function(json) {
			var html = '';
			$.each(json, function(k, v) {
				html += v.ref_id+':'+v.message + "<br/>";
			})
			alertTip(html, 500);
			initData(paginationCurrentPage - 1);
		}
	});
}
$(function() {
	$('.orderEdit').live('click',function(){
		var url = $(this).attr('url');
		var ref_id = $(this).attr('ref_id');
        //$(window.parent.document).find("#iframe-container-overlay").attr('src', url).show();
//		var width = parent.windowWidth();
//		var height = parent.windowHeight();
//        parent.openIframeDialogNew(url,width-100,height-80,'<{t}>order_detail<{/t}>',quickId,paginationCurrentPage,paginationPageSize);
        
        leftMenu('order/create','<{t}>edit_order<{/t}>',url);
	})
	
	$('.orderDetail').live('click',function(){
		var url = $(this).attr('url');
		var ref_id = $(this).attr('ref_id');
        //$(window.parent.document).find("#iframe-container-overlay").attr('src', url).show();
//		var width = parent.windowWidth();
//		var height = parent.windowHeight();
//        parent.openIframeDialogNew(url,width-100,height-80,'<{t}>order_detail<{/t}>',quickId,paginationCurrentPage,paginationPageSize);
        leftMenu('order/detail','<{t}>order_detail<{/t}>',url);
	})
	
	$('.cpyBtn').live('click',function(){
		var url = $(this).attr('url');
		var ref_id = $(this).attr('ref_id');
        //$(window.parent.document).find("#iframe-container-overlay").attr('src', url).show();
//		var width = parent.windowWidth();
//		var height = parent.windowHeight();
//        parent.openIframeDialogNew(url,width-100,height-80,'<{t}>order_detail<{/t}>',quickId,paginationCurrentPage,paginationPageSize);
        var cp_order = $(this).attr('order_type')=='return'?'<{t}>cpy_return_order<{/t}>':'<{t}>cpy_order<{/t}>';
        if(url.indexOf("createdhl")>=0){
        	leftMenu('order/createdhl',cp_order,url);
        }else
        	leftMenu('order/create',cp_order,url);
	})
	
	
	
	/**
	 * RMA退款类型--自动不金额
	 */
	$('.rmaRefundType').live('change',function(){
		var orderAmt = $("#dialog-edit-alert-tip").find("#hideE8").val();
		var rmaAmtElement = $("#dialog-edit-alert-tip").find("#E8");
		if(!rmaAmtElement.val() && $(this).val() == '0'){
			rmaAmtElement.val(orderAmt);
			$(this).next().show();			//全额退款，显示“同时取消ebay订单”
		}else{
			if(orderAmt == orderAmt){
				rmaAmtElement.val('');
			}
			$(this).next().hide();
		}
	});
	
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
	
	/**
	 * RMA退件原因
	 */
	$('.rmaReason').live('change',function(){
		if($(this).val() == '3'){//退件重复，隐藏退款类型（选中全款）--不会产生paypal退款，审核后产生新订单
			adjustFormatForRma(1);
		}else if($(this).val() == '7'){
			//建立退件
			alertConfirmTip("<span class='tip-warning-message'>您确认需要创建“仓库退件”？</span>",function(){
				$("#dialog-edit-alert-tip").dialog("close");
				var id = $("#ez-wms-rma-edit-dialog").find('#ordersId').val();
				var url = '/order/returns/open-Return-Order?paramId=' + id;
				var width = 950;//parent.windowWidth() - 100;
				var height = parent.windowHeight() - 100;
				var title = '建立退件';
				parent.openIframeDialogNew(url, width, height,title,quickId,paginationCurrentPage,paginationPageSize);
    		});
		}else{
			adjustFormatForRma(0);
		}
	});
	
	/**
	 * 调整RMA显示模板
	 * type: 0=默认格式，1=退件重发格式，其他根据业务进行
	 */
	function adjustFormatForRma(type){
		var rmaRefundElement = $("#dialog-edit-alert-tip").find("#E15");
		var rmaCommonElement = $("#dialog-edit-alert-tip").find("#E19");
		var rmaAmtElement = $("#dialog-edit-alert-tip").find("#E8");
		var orderAmt = $("#dialog-edit-alert-tip").find("#hideE8").val();
		var rmaCancelOrderElement = $("#dialog-edit-alert-tip").find(".rmaCancelOrder");
		
		//取消“同时取消ebay订单”的勾选、同时隐藏这个勾选框
		if(rmaCancelOrderElement.is(":checked")){
			rmaCancelOrderElement.click();
		}
		rmaRefundElement.next().hide();
		
		if(type == 0){
			rmaRefundElement.val("");									//退款类型选中空值
			rmaCommonElement.html("");
			rmaAmtElement.val("");
			$(".rmaResendHide").show();
		}else if(type == 1){
			rmaRefundElement.val("");									//退款类型选中空值
			rmaCommonElement.html("退件重发");							//备注使用退件原因
			rmaAmtElement.val("");										//退款金额使用订单金额
			$(".rmaResendHide").hide();									//隐藏退款类型，金额，卖家留言
		}
	}
	
	/**
	 * RMA管理
	 */
	$('.orderRma').live('click',function(){
		//拿到订单ID
		var ordersId = $(this).attr('ordersId');
		
		var e = $("#ez-wms-rma-edit-dialog");
		//$("[name='E13']","#ez-wms-rma-edit-dialog").val($.trim($('#rmaOrderNo'+ordersId).html()));
		//为弹出框的中的参数赋值
		e.find('#ordersId').val(ordersId);
		
		e.find('.E13').val($.trim($('#rmaOrderNo'+ordersId).text()));
		e.find('#dE13').html($.trim($('#rmaOrderNo'+ordersId).text()));
		e.find('.E9').val($.trim($('#rmaCurrencyCodeId'+ordersId).text()));
		e.find('#dE9').html($.trim($('#rmaCurrencyCodeId'+ordersId).text()));
		e.find('.rmaSku').val($.trim($(this).attr("rmaSku")));
		e.find('.rmaWarehouseSku').val($.trim($(this).attr("rmaWarehouseSku")));
		e.find('#dRmaWarehouseSku').html($.trim($(this).attr("rmaWarehouseSku")));
		
		e.find('.rmaWarehouseSkuQty').val($.trim($(this).attr("rmaWarehouseSkuQty")));
		
		var url = '/order/rma/';
		
		e.EzRmaAddDataDialog({
			url:url,
            editUrl: url + "edit",
            dWidth:"450"
        });
		
		$("#dialog-edit-alert-tip").find("#hideE8").val($.trim($("#rmaAmountpaid"+ordersId).html()));
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
    })
	/**
	 * 库存查询
	 */
	$('.inventoryBtn').live('click',function(){
		
		var sku = $(this).attr('sku');
		var warehouseCode = $(this).attr('warehouse_code');
		$.ajax({
			  type : "POST",
			  url : "/order/order-list/inventory",
			  data : {'sku':sku},
			  dataType : 'json',
			  success : function(json) {
				  if(json.ask){
					  var html = '';
					  $.each(json.data,function(k,v){
						  if(warehouseJson[v.warehouse_id].warehouse_code==warehouseCode){
				              html += "<tr class='table-module-b5' style='background:#D59881;'>" ;							  
						  }else{
				              html += (k + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
						  }
						  html+='<td>'+json.sku+'</td>';
						  html+='<td>'+warehouseJson[v.warehouse_id].warehouse_code+'['+warehouseJson[v.warehouse_id].warehouse_desc+']</td>';
						  html+='<td>'+v.pi_onway+'</td>';
						  html+='<td>'+v.pi_pending+'</td>';
						  html+='<td>'+v.pi_sellable+'</td>';
						  html+='<td>'+v.pi_reserved+'</td>';
						  html+='</tr>';
						  
					  });
					  $('#inventory_div #inventory_detail').html(html);
					  $('#inventory_div').dialog('open');
					  //alertTip(html,600);
					  
				  }else{
					  alertTip(json.message);
				  }
			  }
		  });
	});
	jQuery.fn.EzRmaAddDataDialog = function (options) {
	    var defaults = {
	        jsonData: {},
	        Field: 'paramId',
	        paramId: 0,
	        url: '/',
	        editUrl: '/',
	        dWidth: "550",
	        dHeight: "auto",
	        dTitle: "RMA建立/Operations",
	        successMsg: ""
	    };
	    var options = $.extend(defaults, options);
	    var div = $(this);
	    $('<div title="' + options.dTitle + '" id="dialog-edit-alert-tip" class="dialog-edit-alert-tip"><form id="editDataForm" name="editDataForm" class="submitReturnFalse">' + div.html() + '</form><div class="validateTips" id="validateTips"></div></div>').dialog({
	        autoOpen: true,
	        width: options.dWidth,
	        height: options.dHeight,
	        modal: true,
	        show: "slide",
	        buttons: [
	            {
	                text: "确定(Ok)",
	                click: function () {
	                	if (options.editUrl == '/') {
	                		return;
	                	}
	                	
	                	var ordersId = $("#dialog-edit-alert-tip").find("#ordersId").val();
	                	var orderTotal = $.trim($("#rmaAmountpaid"+ordersId).html());
	                	var modTotal =	$("#dialog-edit-alert-tip").find("#E8").val();
	                	
	                	if(parseFloat(orderTotal) < parseFloat(modTotal)){
	                		alertConfirmTip("退款金额，超出订单金额，请问是否继续？",function(){
	                			$("#editDataForm").submit();
	                		});
	                	}else{
	                		var load_tips = "<span class='tip-load-message'>请等待...</span>"; 
	                		alertTip(load_tips);
	                		var params = $("#editDataForm").serialize();
	                		$.ajax({
	                			type: "POST",
	                			data: params,
	                			dataType:"json",
	                			url: "/order/rma/get-Rma-Detail",
	                			async:true,
	                			success:function(jsonData){
	                				var tips = '';
	                				var confirmBol =false;
	                				if(isJson(jsonData)){
	                					if(jsonData['state'] == 1){
	                						$("#editDataForm").submit();
	                					}else if(jsonData['state'] == 0){
	                						tips = "<span class='tip-warning-message'>" + jsonData.message + "</span>"; 
	                						confirmBol = true;
	                					}else{
	                		                if (jsonData.errorMessage == null)return;
	                		                $.each(jsonData.errorMessage, function (key, val) {
	                		                	tips += "<span class='tip-error-message'>" + val + "</span>";
	                		                });
	                					}
	                				}else{
	                					tips = "<span class='tip-error-message'>请求异常，请联系相关技术人员.</span>"; 
	                				}
	                				$("#dialog-auto-alert-tip").dialog("close");
	                				if(tips != '' && confirmBol){
	                					alertConfirmTip(tips,function(){
	                						alertTip(load_tips);
	                						$("#editDataForm").submit();
	                		    		});
	                				}else if(tips != ''){
	                					alertTip(tips);
	                				}
	                			}
	                			});
	                	}
	                	
	                }
	            },
	            {
	                text: "取消(Cancel)",
	                click: function () {
	                    $(this).dialog("close");
	                    adjustFormatForRma(0);
	                }
	            }
	        ], close: function () {
	        	$(this).detach();
	            adjustFormatForRma(0);
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
    
	    $("#editDataForm").myAjaxForm({api: options.editUrl, success: editSuccess});
	};

	/**
	 * 建立退件
	$('.orderReturn').live('click',function(){
		var id = $(this).attr('dataId');
		var url = '/order/returns/open-Return-Order?paramId=' + id;
		var width = 950;//parent.windowWidth() - 100;
		var height = parent.windowHeight() - 100;
		var title = '建立退件';
		parent.openIframeDialogNew(url, width, height,title,quickId,paginationCurrentPage,paginationPageSize);
	});
	*/
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
			$(".statusTagAll").addClass('chooseTag');			
		}else{
			$(".statusTag"+status).addClass('chooseTag');			
		}
		var opHtml = '';
		opHtml += '';
		$('.set_it_for_allot').hide();
		// alert($(this).attr('type'));
		if(status!='2'){
			$("#can_merge").val('');
			$('.merge_filter').removeClass('current');
		}
		if(status==2){
			$('.set_it_for_allot').show();
		}
		/*
		if(status==3||status==4||status==6||status==7){
			$("#div_for_status_3_4").show();
		}else{
			$("#div_for_status_3_4").hide();
			$("#ship_warehouse").removeAttr('name');			
		}
		*/
		if(statusArr[status]){
			$.each(statusArr[status]['actions'],function(k,v){
				opHtml += v;
			})
		}else if(status==''){
			//opHtml += '<input type="button" value="站内信通知" class="messageBtn baseBtn">';
			//opHtml += '<input type="button" style="display:none;" value="标记发货到eBay" class="completeSale baseBtn">';
			
		}else{
			//opHtml += '<input type="button" value="发货审核" class="orderVerify baseBtn ">';
			//opHtml += '<input type="button" value="冻结" class="updateStatus baseBtn" status="5">';	
			//opHtml += '<input type="button" value="站内信通知" class="messageBtn baseBtn">';		
		}		

		$(".opDiv").html(opHtml);
		$('#status').val(obj.attr('status'));	
		$('.submitToSearch:visible').click();
		
		//控制按钮隐藏和显示
		hideOperateBt();
		// loadData(1, paginationPageSize);
	}
	$('.sub_status').live('click',function(){
		var obj = $(this);
		$('#ot_id').val('');
		$('.ot_id').removeClass('selected');
		$('#sub_status').val(obj.attr('sub_status'));
		obj.addClass('selected');
		statusBtnClick(obj);
	})
	/**
	 * 该方法 作废
	 */
	$('.abnormal_type').live('click',function(){
		var obj = $(this);
		$('#ot_id').val('');
		$('.ot_id').removeClass('selected');
		$('#abnormal_type').val(obj.attr('abnormal_type'));
		$('#process_again').val(obj.attr('process_again'));
		$('.abnormal_type').removeClass('selected');
		$('.process_again').removeClass('selected');
		obj.addClass('selected');
		statusBtnClick(obj);
	})
	/**
	 * 该方法 作废
	 */
	$('.process_again').live('click',function(){
		var obj = $(this);
		$('#user_tag').val('');
		$('.user_tag').removeClass('selected');
		$('#abnormal_type').val(obj.attr('abnormal_type'));
		$('#process_again').val(obj.attr('process_again'));
		$('.abnormal_type').removeClass('selected');
		$('.process_again').removeClass('selected');
		obj.addClass('selected');
		statusBtnClick(obj);
	})
	/**
	 * 子分类切换
	 */
	$('.ot_id').live('click',function(){
		var obj = $(this);
		$('#abnormal_type').val(obj.attr('abnormal_type'));
		$('#process_again').val(obj.attr('process_again'));
		$('#ot_id').val(obj.attr('ot_id'));
		$('.abnormal_type').removeClass('selected');
		$('.process_again').removeClass('selected');
		$('.ot_id').removeClass('selected');
		obj.addClass('selected');
		statusBtnClick(obj);
	});
	
	/**
	 * 订单状态切换
	 */
	$("#module-container .statusTag").live('click', function() {
		var obj = $(this);
		$('#abnormal_type').val('');
		$('#process_again').val('');
		$('#sub_status').val('');
		$('#ot_id').val('');
		$('.abnormal_type').removeClass('selected');
		$('.process_again').removeClass('selected');
		$('.sub_status').removeClass('selected');
		$('.ot_id').removeClass('selected');
		statusBtnClick(obj);
	});

	$('.search_more').click(function() {
		if($('#is_more').val()=='0'){
			$("#search_more_div").show();
			$('#is_more').val('1');
		}else{
			$("#search_more_div").hide();
			$('#is_more').val('0');			
		}		
	});
	/**
	 * 订单导出按钮
	 */
	$(".exportBtn0").live('click', function() {
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<{t}>pls_select_orders<{/t}>');
			return false;
		}
		$("#export_wrap_div").dialog('open');
	});
	/**
	 * 客服留言按钮
	 */
	$(".operatorNoteBtn").live('click', function() {
		if ($(".checkItem:checked").size() != 1) {
			alertTip('请选择一个订单');
			return false;
		}
		var ref_id = $(".checkItem:checked").eq(0).attr('ref_id');
		$('#operator_note_ref_id').val(ref_id);
		$("#operator_note_wrap_div").dialog('open');
	});

	/**
	 * 
	 */
	$('.updateStatus').live('click', function() {
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<{t}>pls_select_orders<{/t}>');
			return false;
		}
		if (!window.confirm('<{t}>are_you_sure<{/t}>')) {
			return false;
		}
		var status = $(this).attr('status');
		var param = $("#listForm").serialize();
		param += '&status=' + status;
		updateOrderStatus(param);
	});
	/**
	 * 未付款订单转待发货审核，主要是为了客户在ebay未付款，但是在paypal上已付款的订单
	 */
	$('.unPaidToPaidBtn').live('click',function(){
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<{t}>pls_select_orders<{/t}>');
			return false;
		}
		var refIds = [];
		$(".checkItem:checked").each(function(){
			refIds.push($(this).attr('ref_id'));
		});		
		var msg='当将未付款订单转为待发货审核订单时，请确认客户已经付款。\n否则可能造成客户未付款却发货的情况!\n确认要将选中订单转为待发货审核吗?'
		if (!window.confirm(msg)) {
			return false;
		}
		if (!window.confirm('真的确认？')) {
			return false;
		}
		var status = $(this).attr('status');
		var param = $("#listForm").serialize();
		param += '&status=' + status;
		updateOrderStatus(param);	
	});
	/**
	 * 订单作废按钮
	 */
	$('.deleteBtn0').live('click', function() {
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<{t}>pls_select_orders<{/t}>');
			return false;
		}
		var refIds = [];
		$(".checkItem:checked").each(function(){
			refIds.push($(this).attr('ref_id'));
		})
		var msg = '';
//		$.ajax({
//			type: "POST",
//			async: false,
//			dataType: "json",
//			url: '/order/order/check-export/',
//			data: {'ref_ids':refIds},
//			error: function () {
//
//				return;
//			},
//			success: function (json) {
//				$.each(json,function(k,v){
//					if(v.ask==1){
//						msg+=''+v.ref_id+'['+v.wms_ref_id+']:'+v.message+"\n";
//					}
//				})
//			}
//
//		});
//		if(msg!==''){
//			msg+="请确保订单从导出的excel中删除掉\n";
//		}
		msg+='<{t}>are_you_sure_cancel_orders<{/t}>'
		if (!window.confirm(msg)) {
			return false;
		}
		var status = $(this).attr('status');
		var param = $("#listForm").serialize();
		param += '&status=' + status;
		updateOrderStatus(param);
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
//		var msg = '';
//		$.ajax({
//			type: "POST",
//			async: false,
//			dataType: "json",
//			url: '/order/order/check-export/',
//			data: {'ref_ids':refIds},
//			error: function () {
//
//				return;
//			},
//			success: function (json) {
//				$.each(json,function(k,v){
//					if(v.ask==1){
//						msg+=''+v.ref_id+'['+v.wms_ref_id+']:'+v.message+"\n";
//					}
//				})
//			}
//
//		});
//		if(msg!==''){
//			msg+="如果截单，请确保订单从导出的excel中移除\n";
//			msg+='确认继续吗?'
//			if (!window.confirm(msg)) {
//				return false;
//			}
//		}		
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
	})
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
						url : "/order/order/cancel-order",
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
			}
			],
			close: function () {
			},
			open:function(){
				$('#reason').val('');
				$('#reason_type').val('');
			}
		});

	/**
	 * 添加客服留言
	 */
	$('#operator_note_wrap_div').dialog({
		autoOpen: false,
		width: 400,
		maxHeight: 480,
		modal: true,
		show: "slide",
		buttons: [
			{
				text: '提交',
				click: function () {
					var note_content = $('#note_content').val();
					if($.trim(note_content)==''){
						//alertTip('请输入留言内容');
						//return false;
					}
					var this_ = $(this);
					this_.dialog("close");	
					alertTip('数据处理中...',300);	
					var param = $("#operator_note_wrap_form").serialize();
					$.ajax({
						type : "POST",
						url : "/order/order/order-note",
						data : param,
						dataType : 'json',
						success : function(json) {	
							var html = '';
							html+=json.message;
							$('#dialog-auto-alert-tip').html(html);	
							if(json.ask){
								loadData(paginationCurrentPage-1,paginationPageSize);
							}
							
						}
					});
					
				}
			},
			{
				text: '关闭',
				click: function () {
					var this_ = $(this);
					this_.dialog("close");	
				}
			}
			],
			close: function () {
			},
			open:function(){
				$('#note_content').val('');	 
				var ref_id = $('#operator_note_ref_id').val();
				$.ajax({
					type : "get",
					url : "/order/order/order-note",
					data : {'ref_id':ref_id},
					dataType : 'json',
					success : function(json) {	
						$('#note_content').val(json.operator_note);	 
					}
				});
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
		$('#verify_div_verify').dialog('open');
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
	/**
	 * 订单分仓
	 */
	$("#verify_div").dialog({
		autoOpen : false,
		width : 600,
		maxHeight : 480,
		modal : true,
		show : "slide",
		close : function() {

		},
		open : function() {
			$.ajax({
				type : "POST",
				url : "/order/order/get-warehouse-shipping",
				data : {},
				dataType : 'json',
				success : function(json) {
					warehouseShipping = json;
					var html = '';
					$.each(warehouseShipping, function(k, v) {
						var warehouse_virtual_title = v.warehouse_virtual=='1'?'第三方仓库':'自营仓库';
						html += '<option value="' + v.warehouse_id + '">' + v.warehouse_code+'['+v.warehouse_desc+']【' +warehouse_virtual_title+ '】</option>';
					});
					$('#warehouse').html(html);
					$('#warehouse').change();

				}
			});
		},
		buttons : [{
			text : 'Ok',
			click : function() {
				var this_ = $(this);
				this_.dialog('close');
				alertTip('数据处理中...',300);
				var warehouseid = $('#warehouse').val();
				var shipping_method = $('#shipping_method_arr').val();
				var param = $("#listForm").serialize();
				param += '&warehouse_id=' + warehouseid+'&shipping_method='+shipping_method;
				$.ajax({
					type : "POST",
					url : "/order/order/order-set-warehouse-ship",
					data : param,
					dataType : 'json',
					success : function(json) {
						$('#dialog-auto-alert-tip').dialog('close');
						var html = '';
						if(json.ask){
							html+="处理结果:<br/>";
							$.each(json.result,function(kk,vv){
								html+=vv.ref_id+":"+vv.message+"<br/>"; 							
							});
							
							setTimeout(function(){
								initData(paginationCurrentPage - 1);
							},500);
							
						}else{
							html+=json.message;
						}
						
						alertTip(html, 800,500);
					}
				});
			}
		}, {
			text : 'Close',
			click : function() {
				$(this).dialog("close");
			}
		}]
	});
	
	var order_type = $('#order_type').val();
	if(order_type=='return'){
		$('.verify_type1').parent().parent().hide();
	}
	/**
	 * 订单审核
	 */
	$("#verify_div_verify").dialog({
		autoOpen : false,
		width : 800,
		maxHeight : 480,
		modal : true,
		show : "slide",
		close : function() {

		},
		open : function() {
			$('.verify_type0').click();
			$.ajax({
				type : "POST",
				url : "/order/order/get-warehouse-shipping",
				data : {},
				dataType : 'json',
				success : function(json) {
					warehouseShipping = json;
					var html = '';
					$.each(warehouseShipping, function(k, v) {
						html += '<option value="' + v.warehouse_id + '">' + v.warehouse_code+'</option>';
					});

					$('#warehouse_verify').html(html);
					$('#warehouse_verify').change();
				}
			});
		},
		close :function(){
			//$('.verify_type0').click();
		},
		buttons : [{
			text : 'Ok',
			click : function() {
				var this_ = $(this);
				this_.dialog('close');
				alertTip('Loading...',300);
				var verify_type = $('.verify_type:checked').val();
				var refIds = [];
				$("#listForm .checkItem:checked").each(function(){
					refIds.push($(this).attr('ref_id'));
				});
				var param = {'refIds':refIds};				
				if(verify_type=='1'){
					var warehouseid = $('#warehouse_verify').val();
					var shipping_method = $('#shipping_method_arr_verify').val();
					param.warehouse_id = warehouseid;
					param.shipping_method = shipping_method;								
				}
				$.ajax({
					type : "POST",
					url : "/order/order/order-verify",
					data : param,
					dataType : 'json',
					success : function(json) {
						$('#dialog-auto-alert-tip').dialog('close');
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
						
						alertTip(html, 800,500);
					}
				});	
			}
		}, {
			text : 'Cancel',
			click : function() {
				$(this).dialog("close");
			}
		}]
	});
	
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

	$('.deleteTag').live('click', function() {
		var val = $(this).attr('val');
		if (!window.confirm('确定要删除自定义标签？')) {
			return false;
		}
		var this_ = $(this);
		$.ajax({
			type : "POST",
			url : "/order/order/delete-defined-tag",
			data : {
				'ot_id' : val
			},
			dataType : 'json',
			success : function(json) {
				alertTip(json.message);
				if (json.ask) {
					this_.parent().remove();
					getUserDefinedTag();
				}
			}
		});
	});
	$(".zidingyibiaoji").live('click', function() {
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<{t}>pls_select_orders<{/t}>');
			return false;
		}
		var order_status = $(this).attr('order_status');
		$('#ot_id_order_status').val(order_status);
		$("#tag_form_div").dialog('open');
	});
	
	$("#biaojifahuo_div").dialog({
		autoOpen : false,
		width : 600,
		maxHeight : 480,
		modal : true,
		show : "slide",
		buttons : [{
			text : 'Ok',
			click : function() {
				$(this).dialog("close");
				$('#fileUploadBtn').click();
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
					'type' : '2'
				},
				dataType : 'json',
				success : function(json) {
					if (json.ask) {
						var html = '';
						$.each(json.result, function(i, v) {
							html += '<option value="' + v.excel_defined_id + '">' + v.excel_defined_name + '</option>';
						});
						$("#import_template").html(html);
					} else {
						alert(json.message);
					}
				}
			});
		}
	});

	$('.biaojifahuo').live('click', function() {
		$('#biaojifahuo_div').dialog('open');
	});

	$('.completeSale').live('click', function() {
		
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<{t}>pls_select_orders<{/t}>');
			return false;
		}
		if(!window.confirm('确认将选择的订单标记发货？')){
			return false;
		}
		var ref_ids = [];
		$(".checkItem:checked").each(function(){
			var refId = $(this).attr('ref_id');
			ref_ids.push(refId);
		})
		$.ajax({
			type : "POST",
			url : "/order/order/complete-sale",
			data : {'ref_id':ref_ids},
			dataType : 'html',
			success : function(html) {
				alertTip(html, 500);
			}
		});	
	});
		
	$('.biaojifahuoSelect').live('click', function() {
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<{t}>pls_select_orders<{/t}>');
			return false;
		}
		if(!window.confirm('确认将选择的订单标记发货？')){
			return false;
		}
		var param = {};
		var ref_ids = [];
		$('.checkItem:checked').each(function(k,v){
			ref_ids.push($(this).attr('ref_id'));
		});
		param.ref_ids = ref_ids;
		$.ajax({
			type : "POST",
			url : "/order/order/batch-ship-flag",
			data : param,
			dataType : 'json',
			success : function(json) {
				var html = '';
				$.each(json, function(k, v) {
					html += v.ref_id+":"+v.message + "<br/>";
				})
				alertTip(html, 500);				
				initData(paginationCurrentPage - 1);
				
				
			}
		});	
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
var allotCondition = {};
function getAllotCondition(){
	$.ajax({
		type : "POST",
		url : "/order/order/get-allot-condition",
		data : {},
		dataType : 'json',
		success : function(json) {
			allotCondition = json;
			var html = '';
//			html += '<p>已经保存的匹配规则:</p>';
			html += '<table style="width:500px;">';
			html +='<tr style="line-height:22px;">';
			html +='<td >名称</td>';
			html +='<td width="50">仓库</td>';
			html +='<td width="60">运输方式</td>';
			html +='<td width="60">操作</td>'
			html +='</tr>';		
			$.each(json,function(k,v){
				html +='<tr style="line-height:22px;">';
				html +='<td><a href="javascript:;" class="allot_condition" index="'+k+'">'+v.oac_name+'</a></td>';
				html +='<td>'+v.warehouse_code+'</td>';
				html +='<td>'+v.shipping_method+'</td>';
				html +='<td><a href="javascript:;" class="del_oac_btn" id="'+v.oac_id+'">删除</a></td>'
				//html +='<td><a href="javascript:;" class="od_oac_btn" id="'+v.oac_id+'">应用该规则</a></td>'
				html +='</tr>';				
			})
			html += '</table>';
			$('#allot_condition_div').html(html);
		}
	});
}
$(function() {
	//getAllotCondition();
	$('.allot_condition').live('click',function(){
		$("#allot_condition_div").dialog('close');
		var index = $(this).attr('index');
		var con = allotCondition[index].oac_condition;
		
		$.each(con,function(k,v){
			if(k=='is_more'&&v!='0'){
				$('#search_more_div').show();
			}
			$('#searchForm :input[name="'+k+'"]').val(v);
		});
		setTimeout(function(){
			$('#searchForm .submitToSearch').click();			
		},50);
	});
	
	$('.del_oac_btn').live('click',function(){
		if(!window.confirm('确实要删除该匹配规则？')){
			return false;
		}
		var oac_id = $(this).attr('id');
		$.ajax({
			type : "POST",
			url : "/order/order/delete-allot-condition",
			data : {'oac_id':oac_id},
			dataType : 'json',
			success : function(json) {
				alertTip(json.message);
				if(json.ask){
					getAllotCondition();
				}				
			}
		});
	});
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
		$(".checkAll").attr('checked', $(this).is(':checked'));
		
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
	$("#message_feedback_div").dialog({
		autoOpen : false,
		width : 800,
		maxHeight : 600,
		modal : true,
		show : "slide",
		position : 'top',
		buttons : [{
			text : 'Ok',
			click : function() {
				//$(".ui-button-text-only:eq(0)", $(this).parent()).attr('disabled', true);
				var this_ = $(this);
				var param = $("#feedBackForm").serialize();
				this_.dialog("close");
				$.ajax({
					type : "POST",
					url : "/order/order/feed-back-message-for-order",
					data : param,
					dataType : 'json',
					success : function(json) {
						//$(".ui-button-text-only:eq(0)", this_.parent()).attr('disabled', false);
						var html = '';
						alertTip(json.message, 500);
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
			$('#editor_wrap').html('');
			$("#orderIds").val('');
			$("#messageSubject").val('');
			area.val('');
			//area.xheditor(false);
		},
		open : function() {
			$('#editor_wrap').html(area);
			var select = '<option value="" index="">可选择模板</option>';
			$.each(templateList, function(k, v) {
				select += '<option value="' + v.language + '" index="' + k + '">' +v.template_short_name+" ["+ v.template_name+"]" +"["+v.language+"]"+ '</option>';
			});

			$("#select_module").html(select);
			setTimeout(function() {
//				area.css('background', 'none');
//				area.xheditor({
//					tools : 'Fullscreen,About'
//				});
			}, 500);

			var param = $("#listForm").serialize();
			$.ajax({
				type : "POST",
				url : "/order/order/get-orders-item",
				data : param,
				dataType : 'json',
				success : function(json) {
					if (json.ask) {
						var html = '';
						$.each(json.data, function(k, v) {
							html += '<option value="' + v.product_title + '" item="' + k + '">' + v.product_title + '</option>';
						})
						html += '';
						$('#title').html(html).change();
					} else {
						alertTip(json.message);
					}
				}
			});

		}
	});
	$('#title').live('change', function() {
		var item = $('option:selected', this).attr('item');
		$('#item').val(item);
	});
	$('.messageBtn').live('click', function() {
		// content =
		// "<p>当前实例调用的Javascript源代码为:</p><pre>loadJS('../xheditor-zh-cn.js',function(){$('#elm1').xheditor();});</pre>";
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<{t}>pls_select_orders<{/t}>');
			return false;
		}
		var str = '';
		$(".checkItem:checked").each(function() {
			str += $(this).val() + ";";

		});
		// alert(str);
		$("#orderIds").val(str);
		$('#message_feedback_div').dialog('open');
	});

	
	$('#select_module').live('change', function() {
		var key = $('option:selected', this).attr('index');
		if (key !== '') {
			area.val(templateList[key].content);
//			$("#messageSubject").val(templateList[key].subject + ',物品名称:' + $('#title').val() + ' 物品编号:' + $('#item').val());
			$("#messageSubject").val(templateList[key].subject + ',Item Title:[item_title]  ItemID:[item_id]');
		}
	});
	
	$("#set_it_for_allot").click(function(){
		$('#allot_div').dialog('open');
	});

	$('#warehouse_allot').live('change',function(){
		var warehouseId = $(this).val();
		var shipping = warehouseShipping[warehouseId].shipping;
		var html = '';
		$.each(shipping,function(k,v){
			html += '<option value="' + v.code + '">' + v.code + '</option>';			
		});
		$('#shipping_method_allot').html(html);
	});
	
	$("#allot_div").dialog({
		autoOpen : false,
		width : 600,
		maxHeight : 480,
		modal : true,
		show : "slide",
		close : function() {

		},
		open : function() {
			$.ajax({
				type : "POST",
				url : "/order/order/get-warehouse-shipping",
				data : {},
				dataType : 'json',
				success : function(json) {
					warehouseShipping = json;
					var html = '';
					$.each(warehouseShipping, function(k, v) {
						var warehouse_virtual_title = v.warehouse_virtual=='1'?'第三方仓库':'自营仓库';
						html += '<option value="' + v.warehouse_id + '">' + v.warehouse_code+'['+v.warehouse_desc+']【' +warehouse_virtual_title+ '】</option>';					
					});
					$('#warehouse_allot').html(html);
					$('#warehouse_allot').change();
				}
			});
		},
		buttons : [{
			text : 'Ok',
			click : function() {
				var this_ = $(this);
				var warehouseid = $('#warehouse_allot').val();
				var shipping_method = $('#shipping_method_allot').val();
				var allot_name = $('#save_allot_name').val();
				var param = $("#searchForm").serialize();
				param += '&warehouse_id=' + warehouseid;
				param +='&shipping_method='+shipping_method;
				param +='&allot_name='+allot_name;
				$.ajax({
					type : "POST",
					url : "/order/order/set-allot-condition",
					data : param,
					dataType : 'json',
					success : function(json) {
						var html = '';
						html+=json.message;
						alertTip(html, 500);
						if(json.ask){
							this_.dialog('close');
							getAllotCondition();
						}
					}
				});
			}
		}, {
			text : 'Close',
			click : function() {
				$(this).dialog("close");
			}
		}]
	});
	/**
	 * 保存的搜索条件 弹窗时间
	 */
	$('#select_allot_condition').click(function(){
		$("#allot_condition_div").dialog('open');
	});
	/**
	 * 实际作用不是很大
	 * 保存的搜索条件 弹窗设置
	 */
	$("#allot_condition_div").dialog({
		autoOpen : false,
		width : 600,
		maxHeight : 480,
		modal : true,
		show : "slide",
		close : function() {

		},
		open : function() {},
		buttons : [ {
			text : 'Close',
			click : function() {
				$(this).dialog("close");
			}
		}]
	});
	/**
	 * @废弃
	 * 修改sku
	 */
	$('.changeSkuBtn').live('click',function(){
		var val = $(this).siblings('.input_text').val();
		var op_id = $(this).siblings('.input_text').attr('op_id');
		if($.trim(val)==''){
			alertTip('请输入sku');
			return false;
		}
		$.ajax({
			type : "POST",
			url : "/order/order/update-product",
			data : {'sku':val,'op_id':op_id},
			dataType : 'json',
			success : function(json) {
				var html = '';
				html+=json.message;
				alertTip(html, 500);
				if(json.ask){
					loadData(paginationCurrentPage-1,paginationPageSize);
				}
			}
		});
	});
	/**
	 * ？？？？？？？？？？？？？？？？？？？？？？同一买家有多个订单？？？？？？？？？？？？？？？？？？？？？？？？？？
	 * 可合并订单过滤条件
	 */
	$('.merge_filter').live('click',function(){
		var type = $(this).attr('merge_type');
		$('#can_merge').val(type);
		$('.merge_filter').removeClass('current');
		$(this).addClass('current');
		if(type==1){
			$("#search_more_div").show();
			$('#is_more').val('1');
		}
		$('#fix_header_content .statusTag2').click();
	});
	
	/**
	 * 订单合并操作按钮
	 */
	$('.orderMerge').live('click',function(){
		if ($(".checkItem:checked").size() <= 1) {
			alertTip('请选择至少两个订单');
			return false;
		}
		var tip = "选择合并订单地址信息如下:\n";
		$('.checkItem:checked').each(function(){
			var orderId = $(this).val();
			tip+=$('#consignee_'+orderId).text()+"\n";
			
		})
		if(!window.confirm(tip+'确认将选择的订单进行合并？')){
			return false;
		}
		var param = $("#listForm").serialize();
		$.ajax({
			type : "POST",
			url : "/order/order/order-merge",
			data : param,
			dataType : 'json',
			success : function(json) {
				var html = '';
				html+=json.message;
				alertTip(html, 800);
				if(json.ask){
					//$('#buyer_id').val('');
					loadData(paginationCurrentPage-1,paginationPageSize);
				}
			}
		});
	})
	/**
	 * 废弃。。。。。。。。。。。。。。。。。。。。。。。。。。。。。。。。。。
	 * 订单取消合并
	 */
	$('.orderMergeReverse').live('click',function(){
		if ($(".checkItem:checked").size() != 1) {
			alertTip('请选择一个订单');
			return false;
		}
		if(!window.confirm('确认将选择的订单撤销合并？')){
			return false;
		}
		var param = $("#listForm").serialize();
		$.ajax({
			type : "POST",
			url : "/order/order/order-merge-reverse",
			data : param,
			dataType : 'json',
			success : function(json) {
				var html = '';
				html+=json.message;
				alertTip(html, 800);
				if(json.ask){
					loadData(paginationCurrentPage-1,paginationPageSize);
				}
			}
		});
	});
	/**
	 * 多个订单的买家
	 */
	$('#mult_order_buyer option').live('click',function(){
		if($('#buyer_type').val()=='buyer_name'){
			$('#buyer_id').val($(this).attr('name'));
		}else if($('#buyer_type').val()=='buyer_mail'){
			$('#buyer_id').val($(this).attr('mail'));
		}else{
			$('#buyer_id').val($(this).attr('value'));
		}
		
	});

	/**
	 * 清除搜索条件
	 */
	$('.clearBtn').live('click',function(){
		$('.pack_manager input').val('');
		$('.pack_manager a').removeClass('current');
		$('.pack_manager').each(function(){
			$('a:first',this).addClass('current');
		});

		$('#searchForm .input_text').val('');
		$('#searchForm select').val('');
	});
	/**
	 * 订单拆单按钮
	 */
	$('.orderSplit').live('click',function(){
		if ($(".checkItem:checked").size() != 1) {
			alertTip('请选择一个订单');
			return false;
		}
		var order_id = $(".checkItem:checked").eq(0).val();
		var w = parent.windowWidth();
		var h = parent.windowHeight();
		parent.openIframeDialogNew('/order/order/split/orderId/'+order_id,w-100,h-80,'订单拆分',quickId,paginationCurrentPage,paginationPageSize);
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

	/**
	 * 手动分仓
	 */
	$('.orderSetWarehouseShipAutoBtn').live('click',function(){
		if(!window.confirm("如果指定了订单，系统会将指定的订单按照已经设定好的分仓规则来分仓。\n如果不指定订单，则将所有待发货审核订单按照已经设定好的分仓规则来分仓，\n你确定吗？")){
			return false;
		}
		var refIds = [];
		$(".checkItem:checked").each(function(){
			refIds.push($(this).attr('ref_id'));
		})
		alertTip('订单正在自动分配仓库和运输方式，请稍候...');
		$.ajax({
			type : "post",
			url : "/order/order/order-set-warehouse-ship-auto",
			data : {ref_id:refIds},
			dataType : 'json',
			success : function(json) {
				var html = '';
				if(json.ask){
					$('#dialog-auto-alert-tip').dialog('close');
					html = '处理结果如下:成功'+json.success_count+'个，失败'+json.fail_count+'个';
					var step = 1;

					if(json.fail.length){
						html+='<p>以下为分配失败订单</p>';						
					}
					$.each(json.fail, function(k, v) {
						html += '<p>'+(step++)+":"+v.ref_id+':'+v.message+'</p>';
					})
					if(json.success.length){
						html+='<p>以下为分配成功订单</p>';
					}
					$.each(json.success, function(k, v) {
						html += '<p>'+(step++)+":"+v.ref_id+':'+v.message+'</p>';
					})
	                initData(paginationCurrentPage); 
					
					alertTip(html,1024,600);
				}else{
					html = json.message;
					$('#dialog-auto-alert-tip').html(html);
				}
			}
		});

	});
	/**
	 * 订单审核，选择审核类型，按照默认分仓 还是重新分仓
	 */
	$('.verify_type').live('click',function(){
		var verify_type = $(this).val();
		if(verify_type=='1'){
			$('.verify_warehouse_sel').show();
		}else{
			$('.verify_warehouse_sel').hide();			
		}
	});
});
$(function(){
	/**
	 * 滚动保持头部固定
	 */
	var clone = $('#fix_header_content .clone').clone();
	$('#fix_header').append(clone);
	var clone1 = $('#fix_header_content .table-module-list-header-tpl .table-module-title').clone();
	$(':checkbox',clone1).remove();
	$('span',clone1).remove();
	$('#table-module-list-header').html(clone1);
	$(window).scroll(function(){
		var off = $('#fix_header_content').offset();
		var top = off.top;
		var scrollTop = $(this).scrollTop();
		if(scrollTop>top){
			$('#fix_header').show().width($('#module-table').width());
			$('.sub_status_container').hide();
		}else{
			$('#fix_header').hide();			
		}
		
	});
	$(window).resize(function(){
		$('#fix_header').width($('#module-table').width());	
	});
	/**
	 * 选择订单checkbox
	 */
	$('.checkItem').live('click',function(){
		var size = $('.checkItem:checked').size();
		$('.checked_count').text(size);
	});
	
	/**
	 * 查看订单操作日志
	 */
	$('.logIcon').live('click',function(){
		var ref_id = $(this).attr('ref_id');
		$.ajax({
			type: "POST",
			url: "/order/order/get-order-log",
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
					//html +='<ul class="log_list" style=" list-style-type: decimal;padding-left: 20px;">';
					html += '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table-module"><tbody>';
					$.each(json.data,function(k,v){
						/*
						html +='<li>';
						html +=v.create_time+' : '+v.log_content;
						//html +=',操作人ID:'+v.op_username;
						html +='</li>';
						*/
						html += (k + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
						html += '<td style="width:70px;">'+v.op_username+'</td>';
						html += '<td style="width:140px;">'+v.create_time+'</td>';
						html += '<td>'+v.log_content+'</td>';
						html += '</tr>';
					});
					//html +='</ul>';
					html += '</tbody></table>';
					alertTip(html,800,480);
				}else{
					var html = json.message;	
					alertTip(html,800,480);
					//alertTip(html);					   
				}
			}
		});
	});
	/**
	 * 查看ebay上的订单数据
	 */
	$('.viewIcon').live('click',function(){
		var ref_id = $(this).attr('ref_id');
		$.ajax({
			type: "POST",
			url: "/order/order/get-ebay-data",
			data: {'ref_id':ref_id},
			dataType:'html',
			success: function(html){
				alertTip(html,800,480);
			}
		});
	});
	/**
	 * 手动标记订单发货
	 */
	$('.completeIcon').live('click',function(){
		var ref_id = $(this).attr('ref_id');
		var ref_ids = [];
		ref_ids.push(ref_id);
		$.ajax({
			type: "POST",
			url: "/order/order/complete-sale",
			data: {'ref_id':ref_ids},
			dataType:'html',
			success: function(html){
				alertTip(html,800,480);
			}
		});
	});

	/**
	 * 查看订单下载记录
	 */
	$('.loadLogIcon').live('click',function(){
		var ref_id = $(this).attr('ref_id');
		$.ajax({
			type: "POST",
			url: "/order/order/order-load-log",
			data: {'ref_id':ref_id},
			dataType:'json',
			success: function(json){
				var html = '';
				if(json.ask){					
					html +='<ul class="log_list" style=" list-style-type: decimal;padding-left: 20px;">';
					$.each(json.data,function(k,v){
						html+='<li>'+v.create_time+' : '+v.log_content+'</li>';
					});
					html +='</ul>';
					alertTip(html,800,480);
				}else{
					var html = json.message;	
					alertTip(html,800,480);
					//alertTip(html);					   
				}
			}
		});
	});
	
	$('#type').live('change',function(){
		
	})
	
})
$(function(){
	$('#search-module .input_select[name="country_code"]').chosen({width:'300px',search_contains:true});
	$('#search-module .input_select[name="product_code"]').chosen({width:'300px',search_contains:true});
}) 