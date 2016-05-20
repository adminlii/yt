
var user_account_arr_json = <{$user_account_arr_json}>;
var warehouseJson = <{$warehouseJson}>;
//paginationPageSize =1;
EZ.url = '/product/amazon-merchant-list/';
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
		clazz+=' item_'+val.product_id;
		html += "<tr class='"+clazz+"'>" ;
		html += "<td class='ec-center' >" +'<input type="checkbox" class="checkItem" name="listing_id[]" ref_id="'+val.listing_id+'" value="' + val.listing_id + '"/>' + "</td>";

		html+='<td>';
		var url = 'javascript:;';
		var target='';
		if(val.lookup){
			url = val.lookup.detail_page_url;		
			target='target="_blank" ';
			html+='<img src="'+val.lookup.large_image+'" height="100" class="imgPreview"/>';	
		}else{
			html+='<img src="/images/base/noimg.jpg" width="100" height="100"/>';				
		}
		html+='</td>';
		html+='<td>';
		html+='<a href="'+url+'" '+target+'><b>'+val.item_name+'</b></a><br/>';
		html+='<b>user_account:</b>'+val.user_account+'&nbsp;&nbsp;';
		html+='<b>listing_id:</b>'+val.listing_id+'&nbsp;&nbsp;';
		html+='<b>product_id:</b>'+val.product_id+'<br/>';
		html+='<b>seller_sku:</b><a title="点击查看库存" href="javascript:;" class="inventoryBtn" sku="'+val.seller_sku+'">'+val.seller_sku+'</a>'+'<br/>';
		html+='<b>warehouse_sku:</b><br/>';
		var warehouse_sku_arr = [];
		$.each(val.warehouse_sku,function(kkk,vvv){
			var node='<a title="点击查看库存" href="javascript:;" class="inventoryBtn" sku="'+vvv.sub_sku+'">'+vvv.sub_sku+'</a>'+'*'+vvv.sub_qty;
			warehouse_sku_arr.push(node);
		})
		html+=warehouse_sku_arr.join(';&nbsp;');
		html+='</td>';
		
//		html+='<td>'+val.item_description+'</td>';

		html+='<td>';
		html+='<div style="border:2px dotted #ccc;"><b>price:</b>'+val.price+'</div>';
		if(val.my_price){
			var my_price = val.my_price;
			html+='<b>landed:</b>'+my_price.landed_price_amount+' '+my_price.landed_price_currency+'<br/>';
			html+='<b>listing:</b>'+my_price.listing_price_amount+' '+my_price.listing_price_currency+'<br/>';
			html+='<b>shipping:</b>'+my_price.shipping_amount+' '+my_price.shipping_currency+'<br/>';
			html+='<b>regular:</b>'+my_price.regular_price_amount+' '+my_price.regular_price_currency+'<br/>';
		}else{
		}
		
		html+='</td>';
		
		html+='<td>'+val.quantity+'</td>'; 
//		html+='<td>'+val.pending_quantity+'</td>';
//		html+='<td>'+val.image_url+'</td>';
//		html+='<td>'+val.item_is_marketplace+'</td>';
//		html+='<td>'+val.product_id_type+'</td>';
//		html+='<td>'+val.item_note+'</td>';
//		html+='<td>'+val.item_condition+'</td>';
//		html+='<td>'+val.zshop_shipping_fee+'</td>';
//		html+='<td>'+val.zshop_category1+'</td>';
//		html+='<td>'+val.zshop_browse_path+'</td>';
//		html+='<td>'+val.zshop_storefront_feature+'</td>';
//		html+='<td>'+val.zshop_boldface+'</td>';
		html+='<td>';
		html+='<b>asin1:</b>'+val.asin1+'<br/>';
		html+='<b>asin2:</b>'+val.asin2+'<br/>';
		html+='<b>asin3:</b>'+val.asin3+'<br/>';
		html+='</td>';
//		html+='<td>'+val.will_ship_internationally+'</td>';
//		html+='<td>'+val.expedited_shipping+'</td>';
//		html+='<td>'+val.bid_for_featured_placement+'</td>';
//		html+='<td>'+val.add_delete+'</td>';
		html+='<td>';
		html+='<b><{t}>fulfillment_channel<{/t}>:</b>'+val.fulfillment_channel+'<br/>';
		html+='<b><{t}>fulfillment_type<{/t}>:</b>'+val.fulfillment_type+'<br/>';
		html+='</td>';


		html+='<td>';
		html+='<b><{t}>sale_status<{/t}>:</b>'+val.item_status+'<br/>';
		if(val.price_set){
			var price_set = val.price_set;
			html+='<b title="'+price_set.sync_status+'"><{t}>regular<{/t}>:</b>'+price_set.regular_price+' '+price_set.regular_price_currency+'<br/>';
			html+='<b><{t}>listing<{/t}>:</b>'+price_set.listing_price+' '+price_set.listing_price_currency+'<br/>';
			html+='<b><{t}>amazon_date<{/t}>:</b><br/>'+price_set.start_date+' ~ '+price_set.end_date+'<br/>';
			html+='<b><{t}>sync_status<{/t}>:</b>'+price_set.sync_status_title+'<br/>';
		}
		
		
		html+='</td>';
		html+='<td>';
		if(val.supplySet.account_supply_tag){
			html+="<span class='orgNode'><b><{t}>supply_type<{/t}>:</b><br/>"+val.supplySet.supply_type_title+"</span><br/>";
			html+="<span class='orgNode'><b><{t}>status<{/t}>:</b>"+val.supplySet.status_title+"</span><br/>";
			if(val.supplySet.status==1){
				html+="<span class='orgNode'><b>当前<{t}>sync_status<{/t}>:</b>"+val.supplySet.supply_sync_status+"</span><br/>";
				html+="<span class='orgNode'><b>上一次<{t}>sync_time<{/t}>:</b><br/>"+val.supplySet.supply_sync_time+"</span><br/>";
			}
			if(val.supplySet.supply_type==1){
				html+="<a class='orgNode supply_warehouse' onclick='supply_warehouse(\""+val.acc+"\",\""+val.sku+"\",\""+val.supplySet.supply_warehouse+"\");'>查看可补货数量</a><br/>";
			}
		}else{
			html+="<span class='orgNode'><b><{t}>店铺失效或补货状态未设置<{/t}></b>";
		}
//		html+="<span class='orgNode'><b><{t}>supply_type<{/t}>:</b><br/>"+val.supply_type_title+"</span><br/>";
//		html+="<span class='orgNode'><b><{t}>status<{/t}>:</b>"+val.status_title+"</span><br/>";
//		html+="<span class='orgNode'><b><{t}>sync_status<{/t}>:</b>"+val.supply_sync_status+"</span><br/>";
//		html+="<span class='orgNode'><b><{t}>sync_time<{/t}>:</b><br/>"+val.supply_sync_time+"</span><br/>";
		
		html+='</td>';
		
		html+='<td>';
		html+='<b>open_date:</b><br/>'+val.open_date+'<br/>';
		html+='<b>add_time:</b><br/>'+val.add_time+'<br/>';
		html+='<b>update_time:</b><br/>'+val.update_time+'<br/>';
		html+='</td>';

		html+='<td>';
		html+='<a href="javascript:;" class="viewAmazonPriceBtn" id="'+val.id+'">查看平台价格</a><br/>';
		html+='</td>';
		html += "</tr>";
		
	});
	return html;
}
$(function(){
	$('.viewAmazonPriceBtn').live('click',function(){
		if(!window.confirm('<{t}>are_you_sure<{/t}>')){
			return;
		}
		var id = $(this).attr('id');
		var url = "/product/amazon-merchant-list/get-my-price-for-sku?id="+id;
		openIframeDialog(url, 800, 600,'Amazon价格');
	});

})
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
	});
	$('#requestReport').dialog({
		autoOpen: false,
		width: 600,
		modal: true,
		show: "slide",
		buttons: [            
		          {
		        	  text: '确定(Ok)',
		        	  click: function () {
		        		  var acc = $("#report_user_account").val();
		        		  var type = $('#report_type').val();
		        		  var param = {};
		        		  param.acc = acc;
		        		  param.type=type;
		        		  $(this).dialog("close");
		        		  $.ajax({
		        				type : "POST",
		        				url : "/product/amazon-report/request-report",
		        				data : param,
		        				dataType : 'html',
		        				success : function(html) {
		        					alertTip(html,800);	
		        				}
		        			});
		        	  }
		          },{
		        	  text: "取消(Cancel)",
		        	  click: function () {
		        		  $(this).dialog("close");
		        	  }
		          }
		          ], close: function () {
          }
	});
	$('.requestReportBtn').click(function(){
		$('#requestReport').dialog('open');		
	});

	$('#submitFeed').dialog({
		autoOpen: false,
		width: 600,
		modal: true,
		show: "slide",
		buttons: [            
		          {
		        	  text: '确定(Ok)',
		        	  click: function () {
		        		  var acc = $("#feed_user_account").val();
		        		  var type = $('#feed_type').val();
		        		  var param = {};
		        		  param.acc = acc;
		        		  param.type=type;
		        		  $(this).dialog("close");
		        		  $.ajax({
		        				type : "POST",
		        				url : "/product/amazon-feed/submit-feed",
		        				data : param,
		        				dataType : 'html',
		        				success : function(html) {
		        					alertTip(html,800);	
		        				}
		        			});
		        	  }
		          },{
		        	  text: "取消(Cancel)",
		        	  click: function () {
		        		  $(this).dialog("close");
		        	  }
		          }
		          ], close: function () {
          }
	});
	$('.submitFeedBtn').click(function(){
		$('#submitFeed').dialog('open');		
	});
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
			url: '/product/seller-item/update-supply-qty',
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

	$('.supplyLogBtn').live('click',function(){
		var type = $(this).attr('type');
		var itemId = $(this).attr('item_id');
		//'数据获取中...'
		alertTip($.getMessage('sys_common_wait'),650,500);
		$.ajax({
			type: "post",
			dataType: "json",
			url: '/product/seller-item/get-sup-log',
			data: {'type':type,'item_id':itemId},
			success: function (json) {
				var html = '';
				if(json.ask){
					$.each(json.data,function(k,v){
						var clazz = k%2==0?'table-module-b1':'table-module-b5';
						html+='<tr class="'+clazz+'">';
						html+='<td>'+(k+1)+'</td>';
						html+='<td>'+v.create_time+'</td>';
						html+='<td>'+v.sku+'</td>';
						html+='<td>'+v.sell_qty+'</td>';
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

    $('.supply_type').live('change',function(){
    	if($(this).val()=='1'){
            $(this).siblings('.warehouse_code').show().attr('disabled',false);
            $(this).siblings('.quantity').hide().attr('disabled',true);
        }else{
            $(this).siblings('.warehouse_code').hide().attr('disabled',true);
            $(this).siblings('.quantity').show().attr('disabled',false);
        }
    })
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

			var clazz = "table-module-b1";
			clazz = (k + 1) % 2 == 1 ? "table-module-b1" : "table-module-b5";
			var flag = val.item_status=='Active'?true:false;
			var flag = val.fulfillment_channel=='DEFAULT'?true:false;
			if(flag){
				html+='<tr class="'+clazz+'">';
				html+='<td>'+(val.user_account)+'</td>';
				html+='<td>'+val.product_id+' / '+val.seller_sku+'</td>';
				html+='<td>'+(val.quantity)+'</td>';
				//整数,负数表示不自动补货    ---  不自动补货
				//html+='<td><input type="text" class="input_text supply_qty supply_qty_'+val.listing_id+'" name="supply_qty['+val.listing_id+']" style="width: 50px;" placeholder="'+$.getMessage('no_automatic_replenishment')+'" value="'+val.supply_qty+'"></td>';
				html+='<td>';
				html+="<select name='pu["+val.listing_id+"][supply_type]'  class='supply_type' default='"+val.supplySet.supply_type+"'>";
				<{foreach from=$supply_type_arr key=k name=ob item=ob}>
			    html+="<option value='<{$k}>'><{$ob}></option>";
				<{/foreach}>
				html+="</select>";
				html+="<select name='pu["+val.listing_id+"][supply_warehouse]' class='warehouse_code' default='"+val.supplySet.supply_warehouse+"'>";
				<{foreach from=$warehouseArr key=k name=ob item=ob}>
			    html+="<option value='<{$ob.warehouse_code}>'><{$ob.warehouse_code}>[<{$ob.warehouse_desc}>]</option>";
				<{/foreach}>
				html+="</select>";
				html+="<input type='text' name='pu["+val.listing_id+"][supply_qty]' class='quantity input_text' default='"+val.supplySet.supply_qty+"' placeholder='<{t}>supply_qty<{/t}>'  value='"+val.supplySet.supply_qty+"'/>";
				
				
				html+='</td>';

				html+='<td>';
				html+="<select name='pu["+val.listing_id+"][status]'  class=' status' default='"+val.supplySet.status+"'>";
				<{foreach from=$status_arr key=k name=ob item=ob}>
			    html+="<option value='<{$k}>'><{$ob}></option>";
				<{/foreach}>
				html+="</select>";
				html+='</td>';
				//html+='<td>'+(val.quantity)+'</td>';
				html+='</tr>';
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
		        				url: '/product/amazon-merchant-list/save-supply-type',
		        				data: param,
		        				success: function (json) {
		        					var html = '';
		        					if(json.ask){
		        						html+=(json.message);
		        						this_.dialog("close");
		        						initData(paginationCurrentPage-1);
		        					}else{
		        						html+=(json.message);
		        					}
	        						$('#dialog-auto-alert-tip').dialog('option','maxHeight',800).html(html);
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
		var type = $('option:selected',this).text();
		if($(this).val()=='seller_sku'){
			//'模糊搜索'
			$('#code').attr('placeholder',$.getMessage('sys_fuzzy_srarch'));
		}else if($(this).val()=='fulfillment_channel'){
			//'模糊搜索'
			$('#code').attr('placeholder',type);
		}else{
			//可输入ItemID,每个以空格隔开
			$('#code').attr('placeholder',$.getMessage('sys_multiple_search_values',[type]));			
		}
	}).change();
})
$(function(){ 

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
		        			alertTip($.getMessage('sys_common_wait'),600);
		        			var param = $('#update_price_form').serialize();
		        			$.ajax({
		        				type: "post",
		        				dataType: "json",
		        				url: '/product/amazon-merchant-list/save-product-price',
		        				data: param,
		        				success: function (json) {
		        					var html = '';
		        					if(json.ask){
		        						html+=(json.message);
		        						this_.dialog("close");
		        						initData(paginationCurrentPage-1);
		        					}else{
		        						html+=(json.message);
		        					}
	        						$('#dialog-auto-alert-tip').dialog('option','maxHeight',800).html(html);
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
			var flag = val.fulfillment_channel=='DEFAULT'?true:false;
			if(flag){
				html+='<tr class="'+clazz+'">';
				html+='<td>'+(val.user_account)+'</td>';
				html+='<td>'+val.product_id+' / '+val.seller_sku+'</td>';

				html+='<td>';
				html+='<div style="border:2px dotted #ccc;"><b>price:</b>'+val.price+'</div>';
				if(val.my_price){
					var my_price = val.my_price;
					html+='<b>landed:</b>'+my_price.landed_price_amount+' '+my_price.landed_price_currency+'<br/>';
					html+='<b>listing:</b>'+my_price.listing_price_amount+' '+my_price.listing_price_currency+'<br/>';
					html+='<b>shipping:</b>'+my_price.shipping_amount+' '+my_price.shipping_currency+'<br/>';
					html+='<b>regular:</b>'+my_price.regular_price_amount+' '+my_price.regular_price_currency+'<br/>';
				}

				html+='</td>';
				html+='<td>';
				if(val.my_price){//没有价格就没有币种
					val.my_price.start_date = '';
					val.my_price.end_date = '';
					if(val.price_set){
						val.my_price.regular_price_amount = val.price_set.regular_price;
						val.my_price.regular_price_currency = val.price_set.regular_price_currency;
						val.my_price.listing_price_amount = val.price_set.listing_price;
						val.my_price.listing_price_currency = val.price_set.listing_price_currency;
						val.my_price.start_date = val.price_set.start_date;
						val.my_price.end_date = val.price_set.end_date;
					}
					var my_price = val.my_price;
					html+='<div style="height:30px;">';
					html+='<b>regular_price<{t}><{/t}></b>';
					html+="&nbsp;<input type='text' name='pu["+val.listing_id+"][StandardPrice]' class='StandardPrice input_text' value='"+my_price.regular_price_amount+"'/>";
					html+='&nbsp;<select  class="input_text_select" name="pu['+val.listing_id+'][StandardPriceCurrency]"><option value="'+my_price.regular_price_currency+'">'+my_price.regular_price_currency+'</option></select>';

					html+='</div>';
					html+='<div style="height:30px;">';
					html+='<b>listing_price<{t}><{/t}></b>';
					html+="&nbsp;<input type='text' name='pu["+val.listing_id+"][SalePrice]' class='SalePrice input_text' value='"+my_price.listing_price_amount+"'/>";
					html+='&nbsp;<select  class="input_text_select" name="pu['+val.listing_id+'][SalePriceCurrency]"><option value="'+my_price.listing_price_currency+'">'+my_price.listing_price_currency+'</option></select>';

					html+='</div>';
					html+='<div style="height:30px;">';
					html+='<b>amazon_time<{t}><{/t}></b>';
					html+="<input type='text' name='pu["+val.listing_id+"][StartDate]' class='StartDate input_text datepicker' value='"+my_price.start_date+"'/>";
					html+='&nbsp;~&nbsp;';
					html+="<input type='text' name='pu["+val.listing_id+"][EndDate]' class='EndDate input_text datepicker' value='"+my_price.end_date+"'/>";

					html+='</div>';

				}				
				html+='</td>';

				html+='</tr>';
			}
		});
		$('#update_price_div .table-module-list-data').html(html);
		$('#update_price_div').dialog('open');


		
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
	    $('.datepicker').datetimepicker({
			dayNamesMin : dayNamesMin,
			monthNamesShort : monthNamesShort,
			changeMonth : true,
			changeYear : true,
			dateFormat : 'yy-mm-dd'
		});
	    
	});
	
});

$(function(){
	$('.item_status').click(function(){
		var status = $(this).attr('status'); 
		$('.item_status').removeClass('current');
		$(this).addClass('current');
		switch(status){
			
			default:
				$('#item_status').val(status);
			
		}
		initData(0);
	});
	
})

$(function(){
	var maxW = 300;//预览图片最大宽度
    $('body').append('<div id="previewDiv"></div>');
    $('.imgPreview').live('mouseover',function(e){
        var off = $(this).offset();
    	var x = off.left+105;
    	var y = off.top;
    	var b_h = $('body').height();
    	var tempIMG = new Image();
        tempIMG.src = $(this).attr('src');
    	tempIMG.onload = function(){
    	    var w = tempIMG.width;
    	    w = w>maxW?maxW:w;
    	    var h = tempIMG.height;

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