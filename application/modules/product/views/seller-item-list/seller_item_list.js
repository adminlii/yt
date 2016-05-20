
var user_account_arr_json = <{$user_account_arr_json}>;
var warehouseJson = <{$warehouseJson}>;
//paginationPageSize =1;
EZ.url = '/product/seller-item-list/';
var data = {};
EZ.getListData = function (json) {
	$('.checkAll').attr('checked',false);
	var html = '';
	var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
	var c = $('#listForm .table-module-title td').size();
	data = json.data;
	var step = 0;
	var now = new Date();
	now = now.getHours();
	//now = 1;
	$.each(json.data, function (key, val) {
		var clazz = (step + 1) % 2 == 1 ? "table-module-b1" : "table-module-b2";
		step++;
		var v = val.variation;
				
		clazz+=' item_'+val.variation_id;
		html += "<tr class='"+clazz+"'>" ;
		html += "<td class='ec-center' >" +'<input type="checkbox" class="checkItem" name="variation_id[]" ref_id="'+v.variation_id+'" value="' + val.variation_id + '"/>' + "</td>";
		html += "<td>";
		html += '<div class="img_wrap"><img src="/product/seller-item/variation-img?item_id='+val.item_id+'&sku='+v.sku+'&t='+now+'" width="90" height="90" style="float:left;padding-right:5px;"/></div>';
		html += '<a href="'+val.item_url+'" target="_blank">'+val.item_title+'</a><br/>';
		html += $.getMessage('sys_account') + "<!--账号-->:"+(user_account_arr_json[val.user_account]?user_account_arr_json[val.user_account].platform_user_name:val.user_account);
		
		html += "/ItemID:"+val.item_id ;
		html += '<br/>'+$.getMessage('online_sku')+'<!--在线SKU-->:<a sku="'+val.sku+'" class="inventoryBtn" href="javascript:;"  title="'+$.getMessage('click_to_view_inventory')+'">'+val.sku+'</a><br/>';
		if(val.sell_type=='2'||val.sell_type==2){
			var attr = '';
			if(v.attr){
				$.each(v.attr,function(kk,vv){
					attr+=vv.name+' : <b>'+vv.val+'</b>,';
				})
			}
			html += $.getMessage('sys_property')+'<!--属性 -->：&nbsp;'+attr
		}		
		html += '<div>';

		html += '<{t}>WarehousSKU<{/t}>:';
		$.each(val.variation.relation,function(kk,vv){
			html+='<a sku="'+vv.pcr_product_sku+'" class="inventoryBtn" href="javascript:;"  title="'+$.getMessage('click_to_view_inventory')+'">'+vv.pcr_product_sku+'</a> *'+vv.pcr_quantity+'&nbsp;&nbsp;';
		})
		
		html += '</div>';
		html += "</td>";
		html += "<td >" +v.start_pice+' '+v.currency+' / '+ (v.qty-v.qty_sold) +' / '+v.qty_sold+ "</td>";
		//html += "<td >" + val.sell_qty +' / <span class="item_sup_qty_'+val.item_id+'">'+val.supply_qty+ "</span></td>";
		//html += "<td >" + val.sell_qty + "</span></td>";
		
		html+='<td>';
		if(val.variation.supplySet.account_supply_tag){
			var supplySet = val.variation.supplySet;
			html+="<span class='orgNode'><b><{t}>supply_type<{/t}>:</b><br/>"+supplySet.supply_type_title+"</span><br/>";
			html+="<span class='orgNode'><b><{t}>status<{/t}>:</b>"+supplySet.status_title+"</span><br/>";
			if(supplySet.status==1){
				html+="<span class='orgNode'><b>当前<{t}>sync_status<{/t}>:</b>"+supplySet.supply_sync_status+"</span><br/>";
				html+="<span class='orgNode'><b>上一次<{t}>sync_time<{/t}>:</b><br/>"+supplySet.supply_sync_time+"</span><br/>";
			}
			if(supplySet.supply_type==1){
				html+="<a class='orgNode supply_warehouse' onclick='supply_warehouse(\""+val.user_account+"\",\""+val.sku+"\",\""+supplySet.supply_warehouse+"\");'>查看可补货数量</a><br/>";
			}
		}else{
			html+="<span class='orgNode'><b><{t}>店铺失效或补货状态未设置<{/t}></b>";
		}
		html+='</td>';
		html += "<td >" + val.item_status_title+' / '+val.sell_type_title + "</td>";
		html += "<td >";
		html += $.getMessage('sys_start_time')+"<!--开始时间-->:<br/>" + val.start_time;
		html += '<br/>'+$.getMessage('sys_end_time')+'<!--结束时间-->:<br/>'+val.end_time;
		html += '<br/>'+$.getMessage('sys_last_updated')+'<!--最后更新时间-->:<br/>'+val.last_modify_time ;	
		if(val.sync_time){
			//html += '<br/>'+$.getMessage('recently_replenishment_time')+'<!--最近补货时间-->:<br/>'+val.sync_time ;			
		}
		html += "</td>";
		html += "<td valign='top'>";
		
		html += '<ul class="dropdown_nav">';
		html += '<li>';
		html += '<a href="javascript:;">'+$.getMessage('sys_common_operate')+'<!--操作-->&nbsp;<strong style=""></strong></a>';
		html += '<ul class="sub_nav" data-width="125" data-float="right">';
		
		html += '<li><a href="javascript:;" class="opLogBtn" item_id="'+val.item_id+'" type="1" title="'+$.getMessage('sys_operation_log')+'">'+$.getMessage('sys_operation_log')+'<!--操作日志--></a></li>';
		html += '<li class="not_first"><a href="javascript:;" class="supplyLogBtn" item_id="'+val.item_id+'" sku="'+val.sku+'" type="2" title="'+$.getMessage('replenishment_log')+'">'+$.getMessage('replenishment_log')+'<!--补货日志--></a></li>';
		html += '<li class="not_first"><a href="javascript:;" class="updateEbayItem" item_id="'+val.item_id+'" title="'+$.getMessage('manually_update')+'">'+$.getMessage('manually_update')+'<!--手动更新数据--></a></li>';
		html += '<li class="not_first getEbayItem" item_id="'+val.item_id+'"><a href="javascript:;" class="" >手动获取ebay数据</a></li>';
		html += '<li class="not_first viewEbayItem" item_id="'+val.item_id+'" style=""><a href="javascript:;" class="" >查看已下载ebay数据</a></li>';
		//html += '<p><a href="javascript:;">补货同步</a></p>';
		html += '<li class="not_first"><a href="javascript:;" class="addToBlackListBtn" item_id="'+val.item_id+'" sku="'+val.sku+'" account="'+val.user_account+'" title="'+$.getMessage('join_replenishment_blacklist')+'">'+$.getMessage('join_replenishment_blacklist')+'<!--加入补货黑名单--></a></li>';
		html += '<li class="not_first"><a href="javascript:;" class="releaseBlackListBtn" sku="'+val.sku+'" account="'+val.user_account+'" title="'+$.getMessage('lifting_replenishment_blacklist')+'">'+$.getMessage('lifting_replenishment_blacklist')+'<!--解除补货黑名单--></a></li>';
		
		html += '</ul>';
		html += '</li>';
		html += '</ul>';
	
		
		html += "</td>";
		html += "</tr>";
		

//		
//		html += "<tr class='"+clazz+"'>" ;
//		html += "<td class='ec-center' >&nbsp;</td>";
//		html += "<td colspan='1'>"; 
//		html += '<img width="40" height="40" style="float:left;padding-right:5px;" class="imgPreview"  src="/product/seller-item/variation-img?item_id='+val.item_id+'&sku='+v.sku+'&t='+now+'"/>';
//		html += 'SKU:&nbsp;<a sku="'+v.sku+'" class="inventoryBtn" href="javascript:;"  title="'+$.getMessage('click_to_view_inventory')+'">'+v.sku+'</a>,&nbsp;'+$.getMessage('sys_property')+'<!--属性 -->：&nbsp;'+attr;
//		html += '<div style="padding-left:25px;">';
//		html += '<{t}>WarehousSKU<{/t}>:';
//		$.each(v.relation,function(kk,vv){
//			html+='<a sku="'+vv.pcr_product_sku+'" class="inventoryBtn" href="javascript:;"  title="'+$.getMessage('click_to_view_inventory')+'">'+vv.pcr_product_sku+'</a> *'+vv.pcr_quantity+'&nbsp;&nbsp;';
//		})
//		html += '</div>';
//		html += "</td>";
//		html += "<td >" + v.start_pice +' '+v.currency+ "</td>"; 
//		html += "<td >" + (v.qty-v.qty_sold) +' / '+v.qty_sold + "</td>";
//		//html += "<td >" + v.qty +' / <span class="item_sup_qty_'+val.item_id+'_'+v.variation_id+'">'+v.supply_qty+ "</span></td>";
//		//html += "<td >" + v.qty + "</span></td>";
//		//html += "<td colspan='1'>" +  "</td>";
//		html+='<td colspan="4">';
//		if(v.supplySet.account_supply_tag){
//			html+="<span class='orgNode'><b><{t}>supply_type<{/t}>:</b>"+v.supplySet.supply_type_title+"</span>&nbsp;&nbsp;";
//			html+="<span class='orgNode'><b><{t}>status<{/t}>:</b>"+v.supplySet.status_title+"</span>&nbsp;&nbsp;";
//			if(v.supplySet.status==1){
//				html+="<span class='orgNode'><b>当前<{t}>sync_status<{/t}>:</b>"+v.supplySet.supply_sync_status+"</span>&nbsp;&nbsp;";
//				html+="<span class='orgNode'><b>上一次<{t}>sync_time<{/t}>:</b>"+v.supplySet.supply_sync_time+"</span>&nbsp;&nbsp;";							
//			}
//
//			if(v.supplySet.supply_type==1){
//				html+="<a class='orgNode supply_warehouse' onclick='supply_warehouse(\""+val.user_account+"\",\""+v.sku+"\",\""+v.supplySet.supply_warehouse+"\");'>查看可补货数量</a><br/>";
//			}
//		}else{
//			html+="<span class='orgNode'><b><{t}>店铺失效或补货状态未设置<{/t}></b>";						
//		}
//		
//		html+='</td>';
//		/*html += "<td valign='top'>";
//
//		
//		html += '<ul class="dropdown_nav">';
//		html += '<li>';
//		html += '<a href="javascript:;">黑名单&nbsp;<strong style=""></strong></a>';
//		html += '<ul class="sub_nav" data-width="110" data-float="right" >';
//
//		html += '<li><a href="javascript:;" class="addToBlackListBtn" item_id="'+val.item_id+'" sku="'+v.sku+'" account="'+val.user_account+'">加入补货黑名单</a></li>';
//		html += '<li><a href="javascript:;" class="releaseBlackListBtn" sku="'+v.sku+'" account="'+val.user_account+'">解除补货黑名单</a></li>';
//		
//		html += '</ul>';
//		html += '</li>';
//		html += '</ul>';
//		
//		html += "</td>";
//		*/
//		html += "</tr>";
		
	});
	return html;
}
function checkItem(){
	$('.checkItem').each(function(){
		var checked = $(this).is(':checked');
		var item_id = $(this).val();
		if(checked){
			$('.item_'+item_id+'').addClass('active');
			$('.item_'+item_id+' .supply_qty').attr('disabled',false);
		}else{
			$('.item_'+item_id+'').removeClass('active');
			$('.item_'+item_id+' .supply_qty').attr('disabled',true);
		}
	})
}
$(function(){
	$('.dropdown').live('mouseover',function(){
		$(this).addClass('dropdown_show').removeClass('dropdown_hide');
	}).live('mouseout',function(){
		$(this).addClass('dropdown_hide').removeClass('dropdown_show');		
	})
	$(".checkAll").live('click', function() {
		$(".checkItem").attr('checked', $(this).is(':checked'));
		checkItem();
		var size = $('.checkItem:checked').size();
		$('.checked_count').text(size);
	});
	$('.checkItem').live('click',function(){
		checkItem();
	})
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
		$.ajax({
			type : "POST",
			url : "/order/order-list/inventory",
			data : {'sku':sku},
			dataType : 'json',
			success : function(json) {
				if(json.ask){
					var html = '';
					$.each(json.data,function(k,v){
						html += (k + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
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
	
	
	$('.batchEditSubmitBtn').click(function(){
		var param =  $('#listForm').serialize();
		if($('.checkItem:checked').size()==0){
			//请勾选需要设置补货的产品，并设置补货数量
			alertTip($.getMessage('check_the_replenishment_products_and_set'));
			return;
		}
		//确定设置产品补货数量？
		if(!window.confirm($.getMessage('set_up_the_product_to_determine_replenishment_quantities'))){
			return false;
		}
		//数据处理中...
		alertTip($.getMessage('sys_common_wait'));
		$.ajax({
			type: "post",
			dataType: "json",
			//url: '/product/seller-item/update-supply-qty',
			url: '/product/seller-item/save-supply-type',
			data: param,
			success: function (json) {
				var html = '';
				if(json.ask){
					html+=(json.message);
					if(json.success.length>0){
						html+='<p>'+$.getMessage('operation_successful_product')+'</p>';//操作成功产品
					}
					$.each(json.success,function(k,v){
						html+='<p>'+v.message+'</p>';
					});

					if(json.fail.length>0){
						html+='<p>'+$.getMessage('operation_failed_product')+'</p>';//操作失败产品
					}

					$.each(json.fail,function(k,v){
						html+='<p>'+v.item_id+'['+v.sku+']'+','+$.getMessage('reasons_for_failure')+'：'+v.message+'</p>';//失败原因
					});

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

	});

	$('.batchEditQtySetBtn').click(function(){
		//批量设置补货数量  ---  将补货数量统一设置为
		$('<div title="'+$.getMessage('set_the_number_of_the_batch_replenishment')+'"><p align="">'+$.geMessage('the_unified_set_replenishment_quantity')+'：<input type="text" id="qty_set"/></p></div>').dialog({
			autoOpen: true,
			width: 350,
			modal: true,
			show: "slide",
			buttons: [
			          {
			        	  text: '确定(Ok)',
			        	  click: function () {
			        		  var qty = $('#qty_set',this).val();
			        		  $('.active .supply_qty').val(qty);
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
			        	  $(this).detach();
			          }
		});

	});

	$('.syncBtn').click(function(){
		var param =  $('#listForm').serialize();
		if($('.checkItem:checked').size()==0){
			//请勾选需要同步的产品
			alertTip($.getMessage('please_check_the_products_need_to_be_synchronized'));
			return;
		}
		//确定要同步到eBay吗？
		if(!window.confirm($.getMessage('you_sure_you_want_to_sync_to_eBay'))){
			return false;
		}

		//数据处理中,同步时间可能会需要较长时间，请耐心等待....
		alertTip($.getMessage('sys_common_wait'),800,600);
		$.ajax({
			type: "post",
			dataType: "json",
			url: '/product/seller-item/sync-supply-qty-item-variation',
			data: param,
			success: function (json) {
				var html = '';
				if(json.ask){
					html+=(json.message);
					if(json.rs.length==0){
						html+='<p>'+$.getMessage('there_is_no_need_to_synchronize_the_item')+'</p>';//没有需要同步的Item
					}
					if(json.success.length>0){
						html+='<p>'+$.getMessage('operation_successful_product')+'</p>';//操作成功产品
					}
					$.each(json.success,function(k,v){
						html+='<p>'+v.item_id+'['+v.sku+'] '+$.getMessage('replenishment_number')+':'+v.supply_qty+'</p>';//补货数
					});

					if(json.fail.length>0){
						html+='<p>'+$.getMessage('operation_failed_product')+'</p>';//操作失败产品
					}

					$.each(json.fail,function(k,v){
						html+='<p>'+v.item_id+'['+v.sku+']'+','+$.getMessage('reasons_for_failure')+':'+v.message+'</p>';//失败原因
					});

					initData(paginationCurrentPage-1)
				}else{
					html+=(json.message);
				}
				if($('#dialog-auto-alert-tip').size()>0){
					$('#dialog-auto-alert-tip').html(html);
				}else{
					alertTip(html,800,600);
				}
			}
		});

	});

	$('.blackListBtn').click(function(){
		//黑名单
		openIframeDialog('/product/seller-item/get-black-list',800,600,$.getMessage('blacklist'));
	})
})

function blackListOp(url,sku,account,itemId){
	//'数据处理中...'
	alertTip($.getMessage('sys_common_wait'));
	$.ajax({
		type: "post",
		dataType: "json",
		url: url,
		data: {'sku':sku,'user_account':account,'item_id':itemId},
		success: function (json) {
			var html = '';
			if(json.ask){
				html+=(json.message);
				$.each(json.result,function(k,v){
					html+='<p>'+v.message+'</p>';
				})
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

	$('.addToBlackListBtn').live('click',function(){
		var sku = $(this).attr('sku');
		var account = $(this).attr('account');
		var itemId = $(this).attr('item_id');
		//将SKU:'+sku+'加入黑名单？
		if(!window.confirm($.getMessage('the_sku_blacklist',[sku]))){
			return false;
		}

		blackListOp('/product/seller-item/add-to-black-list',sku,account,itemId);
	});

	$('.releaseBlackListBtn').live('click',function(){
		var sku = $(this).attr('sku');
		var account = $(this).attr('account');
		var itemId = $(this).attr('item_id');
		//'将SKU:'+sku+'从黑名单解除？'
		if(!window.confirm($.getMessage('the_sku_released_from_the_blacklist',[sku]))){
			return false;
		}

		blackListOp('/product/seller-item/release-black-list',sku,account,itemId);
	});
	

//	.add('.opLogBtn')
	$('.getEbayItem').live('click',function(){
		var itemId = $(this).attr('item_id');
		var item_ids = [];
		item_ids.push(itemId);
		//数据获取中...
		alertTip($.getMessage('sys_common_wait'),650,500);
		$.ajax({
			type: "post",
			dataType: "json",
			url: '/product/seller-item/get-ebay-item',
			data: {'item_id':itemId},
			success: function (json) {
				var html = '';
				html+=(json.message);
				
				if($('#dialog-auto-alert-tip').size()>0){
					$('#dialog-auto-alert-tip').html(html);
				}else{
					alertTip(html,650,500);
				}
			}
		});
	});

	$('.viewEbayItem').live('click',function(){
		var itemId = $(this).attr('item_id');
		//'数据获取中...'
		alertTip($.getMessage('sys_common_wait'),650,500);
		
		$.ajax({
			type: "post",
			dataType: "json",
			url: '/product/seller-item/view-ebay-item',
			data: {'item_id':itemId},
			success: function (json) {
				var html = '';
				html+=(json.message);				
				if($('#dialog-auto-alert-tip').size()>0){
					$('#dialog-auto-alert-tip').dialog('option','position',"top");
					$('#dialog-auto-alert-tip').html(html);
				}else{
					alertTip(html,650,500);
				}
			}
		});
	});
//	.add('.opLogBtn')
	$('.updateEbayItem').live('click',function(){
		var itemId = $(this).attr('item_id');
		var item_ids = [];
		item_ids.push(itemId);
		//确定要下载最新数据到本地？
		if(!window.confirm($.getMessage('to_download_the_latest_data_to_determine_local'))){
			return false;
		}
		//'数据获取中...'
		alertTip($.getMessage('sys_common_wait'),650,500);
		$.ajax({
			type: "post",
			dataType: "json",
			url: '/product/seller-item/update-item',
			data: {'item_id':item_ids},
			success: function (json) {
				var html = '';
				if(json.ask){
					$.each(json.data,function(k,v){
						html+='<p>';
						html+='ItemID:<span>'+v.ItemID+'</span>&nbsp;';
						html+='Ack:<span>'+v.Ack+'</span>&nbsp;';
						if(v.Error){
							html+='Error:<span>'+v.Error+'</span>&nbsp;';							
						}
						html+='</p>';
					});
					
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
	});
	
	$('.syncDownloadBtn').live('click',function(){
		if($('.checkItem:checked').size()==0){
			//请勾选产品
			alertTip($.getMessage('please_check_the_product'));
			return;
		}
		//确定要下载最新数据到本地？
		if(!window.confirm($.getMessage('to_download_the_latest_data_to_determine_local'))){
			return false;
		}
		var item_ids = [];
		$('.checkItem:checked').each(function(k,v){
			var itemId = $(this).attr('ref_id');
			item_ids.push(itemId);
		});
		//'数据获取中...'
		alertTip($.getMessage('sys_common_wait'),650,500);
		$.ajax({
			type: "post",
			dataType: "json",
			url: '/product/seller-item/update-item',
			data: {'item_id':item_ids},
			success: function (json) {
				var html = '';
				if(json.ask){
					$.each(json.data,function(k,v){
						html+='<p>';
						html+='ItemID:<span>'+v.ItemID+'</span>&nbsp;';
						html+='Ack:<span>'+v.Ack+'</span>&nbsp;';
						if(v.Error){
							html+='Error:<span>'+v.Error+'</span>&nbsp;';							
						}
						html+='</p>';
					});
					
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
	})
//	.add('.opLogBtn')
	$('.supplyLogBtn').live('click',function(){
		var type = $(this).attr('type');
		var itemId = $(this).attr('item_id');
		var sku = $(this).attr('sku');
		//'数据获取中...'
		alertTip($.getMessage('sys_common_wait'),650,500);
		$.ajax({
			type: "post",
			dataType: "json",
			url: '/product/seller-item/get-sup-log',
			data: {'type':type,'item_id':itemId,'sku':sku},
			success: function (json) {
				var html = '';
				if(json.ask){
					$.each(json.data,function(k,v){
						var clazz = k%2==0?'table-module-b1':'table-module-b5';
						html+='<tr class="'+clazz+'">';
						html+='<td>'+(k+1)+'</td>';
						html+='<td>'+v.create_time+'</td>';
						html+='<td>'+v.sku+'</td>';
						//html+='<td>'+v.sell_qty+'</td>';
						html+='<td>'+v.supply_qty+'</td>';
						html+='<td>'+v.user_name+'</td>';
						html+='</tr>';
					});
					var clone = $('#supLogDiv').clone();
					$('.table-module-list-data',clone).html(html);
					html = clone.html();
				}else{
					html+=(json.message);
				}
				if($('#dialog-auto-alert-tip').size()>0){
					//补货日志[最近100条]
					$('#dialog-auto-alert-tip').dialog('option','title',$.getMessage('replenishment_log')+'['+$.getMessage('recently_100')+']'+'ItemID['+itemId+']').html(html);
				}else{
					alertTip(html);
				}
			}
		});
	});
	$('.opLogBtn').live('click',function(){
		var type = $(this).attr('type');
		var itemId = $(this).attr('item_id');
		//请求中，请等待
		alertTip($.getMessage('sys_common_wait'),650,500);
		$.ajax({
			type: "post",
			dataType: "json",
			url: '/product/seller-item/get-log',
			data: {'type':type,'item_id':itemId},
			success: function (json) {
				var html = '';
				if(json.ask){
					$.each(json.data,function(k,v){
						var clazz = k%2==0?'table-module-b1':'table-module-b5';
						html+='<tr class="'+clazz+'">';
						html+='<td>'+(k+1)+'</td>';
						html+='<td>'+v.create_time+'</td>';
						html+='<td>'+v.content+'</td>';
						html+='<td>'+v.user_name+'</td>';
						html+='</tr>';
					});
					var clone = $('#logDiv').clone();
					$('.table-module-list-data',clone).html(html);
					html = clone.html();
				}else{
					html+=(json.message);
				}
				if($('#dialog-auto-alert-tip').size()>0){
					//'操作日志[最近100条]'
					$('#dialog-auto-alert-tip').dialog('option','title',$.getMessage('sys_operation_log')+'['+$.getMessage('recently_100')+']'+'ItemID['+itemId+']').html(html);
				}else{
					alertTip(html);
				}
			}
		});
	});

	$('.supLogBtn').live('click',function(){
		openIframeDialog('/product/seller-item/get-sup-log-list',800,500,'<{t}>replenishment_log<{/t}>');
	})
})

$(function(){
	$('.batchEditBtn').click(function(){
		if($('.checkItem:checked').size()==0){
			//请勾选需要同步的产品
			alertTip($.getMessage('please_check_the_products_need_to_be_synchronized'));
			return;
		}
		var html = '';
		$('.checkItem:checked').each(function(k,v){
			var item_id = $(this).val();
			var val = data[item_id];

			var supplySet = val.variation.supplySet;
			var clazz = "table-module-b1";
			clazz = (k + 1) % 2 == 1 ? "table-module-b1" : "table-module-b5";
			var flag = val.item_status=='Active'?true:false;
			var flag = val.item_status=='Active'||val.item_status=='Completed'?true:false;
			if(flag){
				if(val.sell_type=='2'){ 
					var v = val.variation;
					html+='<tr class="'+clazz+'">';
					html+='<td>'+val.item_id+' / '+v.sku+'</td>';
					html+='<td>'+(v.qty-v.qty_sold)+'</td>';
					//整数,负数表示不自动补货    ---  不自动补货
					//html+='<td><input type="text" class="input_text supply_qty supply_qty_'+val.item_id+'_'+v.variation_id+'" name="supply_qty['+val.item_id+'_'+v.variation_id+']" style="width: 150px;" placeholder="'+$.getMessage('no_automatic_replenishment')+'" value="'+v.supply_qty+'"> <a href="javascript:;" class="disableSupplyBtn">'+$.getMessage('not_automatic_replenishment')+'</a></td>';
					html+='<td>';
					html+="<select name='var["+v.variation_id+"][supply_type]'  class='supply_type' default='"+v.supplySet.supply_type+"'>";
					<{foreach from=$supply_type_arr key=k name=ob item=ob}>
				    html+="<option value='<{$k}>'><{$ob}></option>";
					<{/foreach}>
					html+="</select>";
					html+="<select name='var["+v.variation_id+"][supply_warehouse]' class='warehouse_code' default='"+v.supplySet.supply_warehouse+"'>";
					<{foreach from=$warehouseArr key=k name=ob item=ob}>
				    html+="<option value='<{$ob.warehouse_code}>'><{$ob.warehouse_code}>[<{$ob.warehouse_desc}>]</option>";
					<{/foreach}>
					html+="</select>";
					html+="<input type='text' name='var["+v.variation_id+"][supply_qty]' class='quantity input_text' default='"+v.supplySet.supply_qty+"' placeholder='<{t}>supply_qty<{/t}>'  value='"+v.supplySet.supply_qty+"'/>";
					
					
					html+='</td>';

					html+='<td>';
					html+="<select name='var["+v.variation_id+"][status]'  class=' status' default='"+supplySet.status+"'>";
					<{foreach from=$status_arr key=k name=ob item=ob}>
				    html+="<option value='<{$k}>'><{$ob}></option>";
					<{/foreach}>
					html+="</select>";
					html+='</td>';
					
					html+='</tr>';
				}else if(val.sell_type=='1'){
					html+='<tr class="'+clazz+'">';
					html+='<td>'+val.item_id+' / '+val.sku+'</td>';
					html+='<td>'+(val.sell_qty-val.sold_qty)+'</td>';
					//整数,负数表示不自动补货    ---  不自动补货
					//html+='<td><input type="text" class="input_text supply_qty supply_qty_'+val.item_id+'" name="supply_qty['+val.item_id+']" style="width: 150px;" placeholder="'+$.getMessage('no_automatic_replenishment')+'" value="'+val.supply_qty+'"> <a href="javascript:;" class="disableSupplyBtn">'+$.getMessage('not_automatic_replenishment')+'</a></td>';
					html+='<td>';
					html+="<select name='pu["+val.item_id+"][supply_type]'  class='supply_type' default='"+supplySet.supply_type+"'>";
					<{foreach from=$supply_type_arr key=k name=ob item=ob}>
				    html+="<option value='<{$k}>'><{$ob}></option>";
					<{/foreach}>
					html+="</select>";
					html+="<select name='pu["+val.item_id+"][supply_warehouse]' class='warehouse_code' default='"+supplySet.supply_warehouse+"'>";
					<{foreach from=$warehouseArr key=k name=ob item=ob}>
				    html+="<option value='<{$ob.warehouse_code}>'><{$ob.warehouse_code}>[<{$ob.warehouse_desc}>]</option>";
					<{/foreach}>
					html+="</select>";
					html+="<input type='text' name='pu["+val.item_id+"][supply_qty]' class='quantity input_text' default='"+supplySet.supply_qty+"' placeholder='<{t}>supply_qty<{/t}>'  value='"+supplySet.supply_qty+"'/>";
					
					
					html+='</td>';

					html+='<td>';
					html+="<select name='pu["+val.item_id+"][status]'  class=' status' default='"+supplySet.status+"'>";
					<{foreach from=$status_arr key=k name=ob item=ob}>
				    html+="<option value='<{$k}>'><{$ob}></option>";
					<{/foreach}>
					html+="</select>";
					html+='</td>';
					html+='</tr>';
				}
			}
		});
		$('#update_supply_qty_div .table-module-list-data').html(html);
		$('#update_supply_qty_div').dialog('open');

		$('#update_supply_qty_div .table-module-list-data select').each(function(){
            $(this).val($(this).attr('default'));
        })
        
		$('.supply_type').each(function(){
			$(this).change();	
		});
	});
	$('.disableSupplyBtn').live('click',function(){
		$(this).siblings('.supply_qty').val('-1');
	})
	$('#update_supply_qty_div').dialog({
		autoOpen: false,
		width: 1024,
		maxHeight:600,
		modal: true,
		show: "slide",
		buttons: [
		          {
		        	  text: '确定(Ok)',
		        	  click: function () {
		        		  	//设置产品补货数量？
		        			if(!window.confirm($.getMessage('set_up_the_product_to_determine_replenishment_quantities'))){
		        				return false;
		        			}
		        			var this_ = $(this);
		        			//数据处理中...
		        			alertTip($.getMessage('sys_common_wait'));
		        			var param = $('#update_supply_qty_form').serialize();
		        			$.ajax({
		        				type: "post",
		        				dataType: "json",
		        				//url: '/product/seller-item/update-supply-qty',
		        				url: '/product/seller-item/save-supply-type',
		        				data: param,
		        				success: function (json) {
		        					var html = '';
		        					if(json.ask){
		        						html+=(json.message);
//		        						if(json.success.length>0){
//		        							html+='<p>'+$.getMessage('operation_successful_product')+'</p>';//操作成功产品
//		        						}
//		        						$.each(json.success,function(k,v){
//		        							html+='<p>'+v.message+'</p>';
//		        							$('.item_sup_qty_'+v.clazz).text(v.qty);
//		        						});
//
//		        						if(json.fail.length>0){
//		        							html+='<p>'+$.getMessage('operation_failed_product')+'</p>';//操作失败产品
//		        						}
//
//		        						$.each(json.fail,function(k,v){
//		        							html+='<p>'+v.item_id+'['+v.sku+']'+','+$.getMessage('reasons_for_failure')+'：'+v.message+'</p>';//失败原因
//		        						});

		        						this_.dialog("close");
		        						initData(paginationCurrentPage-1);
		        					}else{
		        						html+=(json.message);
		        					}
	        						$('#dialog-auto-alert-tip').dialog('option','maxHeight',400).html(html);
		        				}
		        			});
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
	
	$('.supply_qty_set_btn').live('click',function(){
		var qty = $('#supply_qty_set').val();
		$('#supply_qty_set_div .supply_qty').val(qty);
	});

	/**
	 * 设置补货数为上架数
	 */
	$('.set_supply_qty_eq_sell_qty_btn').live('click',function(){

		$('.checkItem:checked').each(function(k,v){
			var item_id = $(this).val();
			var val = data[item_id];
			
			if(val.sell_type=='2'){ 
				$.each(val.variation,function(k,v){
					$('.supply_qty_'+val.item_id+'_'+v.variation_id).val(v.qty);					
				});
			}else if(val.sell_type=='1'){
				$('.supply_qty_'+val.item_id).val(val.sell_qty);
			}
		});
	});
	
	
	$('#type').change(function(){
		if($(this).val()=='sku'){
			//'模糊搜索'
			$('#code').attr('placeholder',$.getMessage('sys_fuzzy_srarch'));
		}else if($(this).val()=='item_id'){
			//可输入ItemID,每个以空格隔开
			$('#code').attr('placeholder',$.getMessage('sys_multiple_search_values',['ItemID']));			
		}
	}).change();
})

$(function(){
	$('.item_status').click(function(){
		var status = $(this).attr('status');
		//alert(status);
		$('.item_status').removeClass('current');
		$(this).addClass('current');
		switch(status){
			case 'NoStockOnLine':
				$('#no_stock_online').val('1,2,3');
				$('#item_status').val('');
				$('.cancelNoStockOnLineSetBtn').show();
				$('.noStockOnLineSetBtn').hide();
				break;
			case 'Active':
				
			case 'Completed':

				$('#no_stock_online').val('0,4,5,6');
				$('#item_status').val(status);
				$('.cancelNoStockOnLineSetBtn').hide();
				$('.noStockOnLineSetBtn').show();
				break;
			default:

				$('#no_stock_online').val('');
				$('.cancelNoStockOnLineSetBtn').hide();
				$('.noStockOnLineSetBtn').hide();
			
		}
		$('#item_status').val(status);
		initData(0);
	});

	$('.item_status').click(function(){
		var status = $(this).attr('status');
		//alert(status);
		$('.item_status').removeClass('current');
		$(this).addClass('current');
		switch(status){
			case 'NoStockOnLine':
				$('#no_stock_online').val('1,2,3');
				$('#item_status').val('');
				$('.cancelNoStockOnLineSetBtn').show();
				$('.noStockOnLineSetBtn').hide();
				break;
			case 'Active':
				
			case 'Completed':

				$('#no_stock_online').val('0,4,5,6');
				$('#item_status').val(status);
				$('.cancelNoStockOnLineSetBtn').hide();
				$('.noStockOnLineSetBtn').show();
				break;
			default:

				$('#no_stock_online').val('');
				$('.cancelNoStockOnLineSetBtn').hide();
				$('.noStockOnLineSetBtn').hide();
			
		}
		$('#item_status').val(status);
		initData(0);
	});
	
	function no_stock_online_post(val){

		if($('.checkItem:checked').size()==0){
			//请勾选需要同步的产品
			alertTip($.getMessage('please_check_the_products_need_to_be_synchronized'));
			return;
		}
		
		if(!window.confirm('<{t}>are_you_sure<{/t}>')){
			return;
		}
		var param =  $('#listForm').serialize();
		$.ajax({
			type: "post",
			dataType: "json",
			url: '/product/seller-item/set-no-stock-online?no_stock_online='+val,
			data: param,
			success: function (json) {
				var html = '';
				if(json.ask){
					html+=(json.message);				
				}else{
					html+=(json.message);
				}
				alertTip(html);
				initData(paginationCurrentPage-1)
			}
		});
	}
	$('.noStockOnLineSetBtn').click(function(){
		no_stock_online_post(1);
	});
	$('.cancelNoStockOnLineSetBtn').click(function(){
		no_stock_online_post(4);		
	});
})
$(function(){
	var maxW = 300;//预览图片最大宽度
    $('body').append('<div id="previewDiv"></div>');
    $('.imgPreview').live('mouseover',function(e){
    	var off = $(this).offset();
    	var x = off.left+20;
    	var y = off.top;
    	var b_h = $('body').height();
    	var tempIMG = new Image();
        tempIMG.src = $(this).attr('src');
    	tempIMG.onload = function(){
    	    var w = tempIMG.width;
    	    var h = tempIMG.height;
    	    
    	    var new_w = w>maxW?maxW:w;
    	    var new_h = h*new_w/w;

    	    w = new_w;
    	    h = new_h;
        	//alert(b_h+' '+(y+h));
        	if(y+h>b_h){
        		y -=y+h-b_h;
        	}
        		
        	var img = '<img src="'+$(this).attr('src')+'" width="'+w+'"/>'
        	$('#previewDiv').html(img).css({'position':'absolute','top':y+'px','left':x+'px','border':'1px solid #ccc'}).show();
    	};
    	
    }).live('mouseout',function(e){
    	$('#previewDiv').hide();    	
    });	
    $('#previewDiv').live('mouseover',function(e){
    	$(this).hide();           
    }); 
})

$(function(){
    $('.supply_type').live('change',function(){
    	if($(this).val()=='1'){
            $(this).siblings('.warehouse_code').show().attr('disabled',false);
            $(this).siblings('.quantity').hide().attr('disabled',true);
        }else{
            $(this).siblings('.warehouse_code').hide().attr('disabled',true);
            $(this).siblings('.quantity').show().attr('disabled',false);
        }
    }).change();

	$('.supply_qty_set_btn').live('click',function(){		
		if(!window.confirm('<{t}>are_you_sure<{/t}>')){
			return false;
		}
		var supply_type = $('#supply_type').val();
		var warehouse_code = $('#warehouse_code').val();
		var qty = $('#supply_qty_set').val();

		var status = $('#status').val();
		
		$('#supply_qty_set_div .supply_type').val(supply_type).change();
		$('#supply_qty_set_div .warehouse_code').val(warehouse_code);
		$('#supply_qty_set_div .quantity').val(qty);
		
		$('#supply_qty_set_div .status').val(status);
	});

});

$(function(){
	$('.batchEditPriceBtn').click(function(){
		if($('.checkItem:checked').size()==0){
			//请勾选需要同步的产品
			alertTip($.getMessage('please_check_the_products_need_to_be_synchronized'));
			return;
		}
		var html = '';
		$('.checkItem:checked').each(function(k,v){
			var item_id = $(this).val();
			var val = data[item_id];

			var clazz = "table-module-b1";
			clazz = (k + 1) % 2 == 1 ? "table-module-b1" : "table-module-b5";
			var flag = val.item_status=='Active'?true:false;
			var flag = val.item_status=='Active'||val.item_status=='Completed'?true:false;
			if(flag){
				if(val.sell_type=='2'){ 
					var v = val.variation;
						if(v.sku){
							html+='<tr class="'+clazz+'">';
							html+='<td>';
							html+=val.item_id+' / '+v.sku;
							html+="&nbsp;<b>["+v.sku_desc+"]</b>";							
							html+='</td>';
							html+='<td>';
						
							html+=v.start_pice+" "+v.currency;
							
							html+='</td>';

							html+='<td>';					
							html+="<input type='text' name='price["+val.item_id+"]["+v.sku+"]' class='price_text input_text ' value='"+v.start_pice+"'/>"+" "+v.currency;	
							html+='</td>';

							html+='<td>';
							html+="<a href='javascript:;' class='delPriceLine'>"+EZ.del+"</a>";	
							html+='&nbsp;<input type="checkbox" class="delPriceLineCheckbox">';					 
							html+='</td>'
								
							html+='</tr>';
						}
				}else if(val.sell_type=='1'){
					if(val.sku){
						html+='<tr class="'+clazz+'">';
						html+='<td>'+val.item_id+' / '+val.sku+'</td>';
						html+='<td>';
						html+=val.price_sell+" "+val.currency;					 
						html+='</td>';

						html+='<td>'; 
						html+="<input type='text' name='price["+val.item_id+"]["+val.sku+"]' class='price_text input_text ' value='"+val.price_sell+"'/>"+" "+val.currency;	
						html+='</td>';
						html+='<td>'; 
						html+="<a href='javascript:;' class='delPriceLine'>"+EZ.del+"</a>";
						html+='&nbsp;<input type="checkbox" class="delPriceLineCheckbox">';
						html+='</td>';
						html+='</tr>';
					}
					
				}
			}
		});
		$('#update_price_div .table-module-list-data').html(html);
		$('#update_price_div').dialog('open');
	});
	
	$('.delPriceLine').live('click',function(){
		if(!window.confirm('<{t}>are_you_sure<{/t}>')){
			return;
		}
		$(this).parent().parent().remove();
	}) 
	$('.delPriceBatchBtn').live('click',function(){
		if(!window.confirm('<{t}>are_you_sure<{/t}>')){
			return;
		}
		$('.delPriceLineCheckbox:checked').each(function(){
			$(this).parent().parent().remove();
		});
	}) 
	
	$(".checkDelAll").live('click', function() {
		$(".delPriceLineCheckbox").attr('checked', $(this).is(':checked'));		
	});
	$('#update_price_div').dialog({
		autoOpen: false,
		width: 1024,
		maxHeight:600,
		modal: true,
		show: "slide",
		buttons: [
		          {
		        	  text: '确定(Ok)',
		        	  click: function () {
		        		  	//设置产品补货数量？
		        			if(!window.confirm('<{t}>are_you_sure<{/t}>')){
		        				return false;
		        			}
		        			var this_ = $(this);
		        			//数据处理中...
		        			alertTip($.getMessage('sys_common_wait'));
		        			var param = $('#update_price_form').serialize();
		        			$.ajax({
		        				type: "post",
		        				dataType: "json",
		        				url: '/product/seller-item/update-price',
		        				data: param,
		        				success: function (json) {
		        					var html = '';
		        					if(json.ask){
		        						html+=(json.message);
		        						$.each(json.rs,function(k,v){
		        							html+='<p><a href="javascript:;" style="color:#0090e1;" onclick="alertTip(\''+v.data_str+'\',800,500);">ItemID:'+v.request.item_id+',SKU:'+v.request.sku+'修改价格'+v.data.Ack+'</a></p>';
		        						});//

		        						this_.dialog("close");
		        						initData(paginationCurrentPage-1);
		        					}else{
		        						html+=(json.message);
		        					}
	        						$('#dialog-auto-alert-tip').dialog('option','maxHeight',400).html(html);
		        				}
		        			});
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
	
}) 

function supply_warehouse(acc,sku,wh_code){
	$.ajax({
		type: "post",
		dataType: "html",
		url: '/product/seller-item/get-warehouse-inventory-log',
		data: {'acc':acc,'sku':sku,'wh_code':wh_code},
		success: function (html) {
			alertTip(html,800,600);
		}
	});
}