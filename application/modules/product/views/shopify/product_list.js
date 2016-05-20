
var user_account_arr_json = <{$user_account_arr_json}>;
var warehouseJson = <{$warehouseJson}>;
//paginationPageSize =1;
EZ.url = '/product/shopify/';
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
		var variant = '';
		$.each(val.variants,function(k,v){
			
			variant += "<p>";  
			variant += $.getMessage('item_selling_price')+':【'+v.price+"】";
			if(v.compare_at_price){
				variant += '/【<span style="text-decoration:line-through;color:red;">'+v.compare_at_price+"</span>】";
			}
			variant +='&nbsp;&nbsp;';
			variant +="Shopify"+$.getMessage('sys_inventory')+":【"+(v.inventory_quantity)+"】&nbsp;&nbsp;&nbsp;";
			variant +="Wms"+$.getMessage('sys_inventory')+":【"+(v.wms_inventory)+"】&nbsp;&nbsp;&nbsp;";
			variant +="缺货占用"+$.getMessage('sys_inventory')+":【"+(v.out_of_stock_qty)+"】&nbsp;&nbsp;&nbsp;";
			variant +="未审到仓库占用"+$.getMessage('sys_inventory')+":【"+(v.no_to_wms_qty)+"】&nbsp;&nbsp;&nbsp;";
			
			variant += 'SKU:&nbsp;<a sku="'+v.sku+'" class="inventoryBtn" href="javascript:;"  title="'+$.getMessage('click_to_view_inventory')+'">'+v.sku+'</a>&nbsp;&nbsp;'+$.getMessage('sys_property')+'：&nbsp;'+v.title;
			

			/*
			variant += '<ul class="dropdown_nav">';
			variant += '<li>';
			variant += '<a href="javascript:;">黑名单&nbsp;<strong style=""></strong></a>';
			variant += '<ul class="sub_nav" data-width="110" data-float="right" >';

			variant += '<li><a href="javascript:;" class="addToBlackListBtn" item_id="'+val.item_id+'" sku="'+v.sku+'" account="'+val.user_account+'">加入补货黑名单</a></li>';
			variant += '<li><a href="javascript:;" class="releaseBlackListBtn" sku="'+v.sku+'" account="'+val.user_account+'">解除补货黑名单</a></li>';
			
			variant += '</ul>';
			variant += '</li>';
			variant += '</ul>';
			*/
			variant += "</p>";
		});
		
		clazz+=' item_'+val.id;
		html += "<tr class='"+clazz+"'>" ;
		html += "<td class='ec-center'  valign='top'>" +'<input type="checkbox" class="checkItem" name="item_id[]" ref_id="'+val.id+'" value="' + val.id + '"/>' + "</td>";
		html += "<td valign='top'>";
		html += '<img src="'+val.src+'" width="90" height="90" style="float:left;padding-right:5px;"/>';
		html += "【ProductID:<a href='javascript:;' class='shopify_product_id' id='"+val.id+"'>"+val.id+"</a>】<br/> &nbsp;&nbsp;"+val.title+"["+val.product_type+"]"+'<br/>';
		html += "</td>";
		html += "<td  valign='top' class='ec-center'>"+val.inventory_quantity+"</td>";
		html += "<td  valign='top'>"+variant+"</td>";
		html += "<td  valign='top'>"+val.status_title+"<br/>"+val.recommand_title+"</td>";
		html += "<td  valign='top'>";
		html += $.getMessage('sys_created_time')+":<br/>" + val.created_at;//创建时间
		html += '<br/>'+$.getMessage('sys_last_updated')+':<br/>'+val.updated_at;//更新时间
		html += '<br/>'+$.getMessage('sys_published_time')+':<br/>'+val.published_at ;//发布时间
		
		html += "</td>";
		
//		html += "<td valign='top'>";
//		html += '<ul class="dropdown_nav">';
//		html += '<li>';
//		html += '<a href="javascript:;">操作&nbsp;<strong style=""></strong></a>';
//		html += '<ul class="sub_nav" data-width="110" data-float="right">';		
//		//html += '<li><a href="javascript:;" class="opLogBtn" item_id="'+val.item_id+'" type="1">操作日志</a></li>';
//		html += '</ul>';
//		html += '</li>';
//		html += '</ul>';
//		html += "</td>";
		
		html += "</tr>";
		
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
	$('.shopify_product_id').live('click',function(){
		if(!window.confirm('重新从shopify上加载数据?')){
			return;
		}
		var id = $(this).attr('id');
		$.ajax({
			type : "POST",
			url : "/product/shopify/get-product",
			data : {'id':id},
			dataType : 'json',
			success : function(json) {
				if(json.ask){
					
				}else{
					
				}
				alertTip(json.message);
			}
		});
	})
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
		width: 1024,
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
		//'设置产品补货数量？'
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

					//initData(paginationCurrentPage-1)
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

	$('.syncDownBtn').click(function(){
		var param =  $('#listForm').serialize();
		if($('.checkItem:checked').size()==0){
			//请勾选需要下架的产品
			alertTip($.getMessage('pls_select_off_shelf_product'));
			return;
		}
		//'确定要将产品下架吗？'
		if(!window.confirm($.getMessage('confirm_off_shelf_product'))){
			return false;
		}
		var ids = [];
		$('.checkItem:checked').each(function(k,v){
			var id = $(this).val();
			ids.push(id);
		})
		
		//数据处理中,同步时间可能会需要较长时间，请耐心等待....
		alertTip($.getMessage('sys_common_wait'),1024,600);
		$.ajax({
			type: "post",
			dataType: "json",
			url: '/product/shopify/unpublish',
			data: {'id':ids.join(',')},
			success: function (json) {
				var html = '';
				$.each(json,function(k,v){
					html+= v.id+":"+v.message;
				})
				if($('#dialog-auto-alert-tip').size()>0){
					$('#dialog-auto-alert-tip').html(html);
				}else{
					alertTip(html,1024,600);
				}
				initData(paginationCurrentPage-1);
			}
		});

	});

	$('.blackListBtn').click(function(){
		//'黑名单'
		openIframeDialog('/product/seller-item/get-black-list',1024,600,$.getMessage('blacklist'));
	});
	

	/*
	 * 双击(dblclick)的流程是：mousedown，mouseout，click，mousedown，mouseout，click，dblclick；
	 * 在双击事件(dblclick)，触发的两次单击事件(click)中，第一次的单击事件(click)会被屏蔽掉，但第二次不会。
	 * 所以可以使用计时的方式去除掉一个多余的单击事件就行了
	 * (ebay消息，按某个title排序)
	 */
	var _timeSort = null;
	$(".table-module-title .sort").click(function() {
		clearTimeout(_timeSort);
		var _this = $(this);
		_timeSort = setTimeout(function(){
            var sort = _this.attr('sort');
            if(sort==''||sort=='desc'){
            	nowSort = 'asc';
            }else{
            	nowSort = 'desc';
            } 
            
            $('.sort').attr('class','sort');
            _this.addClass(nowSort);
            _this.attr('sort',nowSort);
            $('.sort span').text('');
            $('.asc span').text('↑');
            $('.desc span').text('↓');
            
            
            var type = _this.attr('type');
            $('#sort').val(type+" "+nowSort);
            
            $('.submitToSearch').click();
        },300);
	}).dblclick(function(){
		clearTimeout(_timeSort);
		//干掉排序提示
		$('.sort span').text('');
		//干掉排序值
		$('#sort').val("");
		$('.submitToSearch').click();
	});
	
	
})
function recommand(){
	$.ajax({
		type: "post",
		dataType: "json",
		url: '/product/shopify/recommand',
		data: {},
		success: function (json) {
			
		}
	});
}
$(function(){
	recommand();
	$('.supplyLogBtn').live('click',function(){
		var type = $(this).attr('type');
		var itemId = $(this).attr('item_id');
		//数据获取中...
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
					//'补货日志[最近100条]'
					$('#dialog-auto-alert-tip').dialog('option','title',$.getMessage('replenishment_log')+'['+$.getMessage('recently_100')+']').html(html);
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
					$('#dialog-auto-alert-tip').dialog('option','title',$.getMessage('sys_operation_log')+'['+$.getMessage('recently_100')+']').html(html);
				}else{
					alertTip(html);
				}
			}
		});
	});

})

$(function(){
	$('.syncUpBtn').click(function(){
		if($('.checkItem:checked').size()==0){
			//'请勾选需要上架的产品'
			alertTip($.getMessage('pls_select_shelves_product'));
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
			
			$.each(val.variants,function(k,v){
				html+='<tr class="'+clazz+'">';
				html+='<td>'+val.id+' / '+v.sku+' / '+v.title+'</td>';
				html+='<td>'+(v.inventory_quantity)+'</td>';
				html+='<td>'+(v.wms_inventory)+'</td>';
				html+='<td><input type="text" class="input_text supply_qty supply_qty_'+val.id+'_'+v.id+'" name="id_qty['+val.id+'_'+v.id+']" style="width: 60px;" placeholder="'+$.getMessage('sys_Integer')+'" value="'+v.wms_inventory+'"></td>';
				html+='</tr>';						
			});
		});
		$('#update_publish_div .table-module-list-data').html(html);
		$('#update_publish_div').dialog('open');
	});

	$('.syncUpOnlyBtn').click(function(){
		if($('.checkItem:checked').size()==0){
			//'请勾选需要上架的产品'
			alertTip($.getMessage('pls_select_shelves_product'));
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
			
			$.each(val.variants,function(k,v){
				html+='<tr class="'+clazz+'">';
				html+='<td>'+val.id+' / '+v.sku+' / '+v.title+'<input type="hidden" class="input_text supply_qty supply_qty_'+val.id+'_'+v.id+'" name="product_id['+val.id+']" style="width: 60px;" value="'+val.id+'"></td>';
				html+='<td>'+(v.inventory_quantity)+'</td>';
				html+='<td>'+(v.wms_inventory)+'</td>';
				//html+='<td><input type="text" class="input_text supply_qty supply_qty_'+val.id+'_'+v.id+'" name="id_qty['+val.id+'_'+v.id+']" style="width: 60px;" placeholder="'+$.getMessage('sys_Integer')+'" value="'+v.wms_inventory+'"></td>';
				//html+='<td><input type="text" class="input_text supply_qty supply_qty_'+val.id+'_'+v.id+'" name="product_id['+val.id+']" style="width: 60px;" value="'+val.id+'"></td>';
				html+='</tr>';						
			});
		});
		$('#update_publish_div .table-module-list-data').html(html);
		$('#update_publish_div').dialog('open');
		$('#update_publish_div').dialog('option','title',$.getMessage('shelves_product'));//产品上架
		$('#upSubmitBtn').show();
		$('#downSubmitBtn').hide();
	});

	$('.syncDownOnlyBtn').click(function(){
		if($('.checkItem:checked').size()==0){
			//'请勾选需要下架的产品'
			alertTip($.getMessage('pls_select_off_shelf_product'));
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
			
			$.each(val.variants,function(k,v){
				html+='<tr class="'+clazz+'">';
				html+='<td>'+val.id+' / '+v.sku+' / '+v.title+'<input type="hidden" class="input_text supply_qty supply_qty_'+val.id+'_'+v.id+'" name="product_id['+val.id+']" style="width: 60px;" value="'+val.id+'"></td>';
				html+='<td>'+(v.inventory_quantity)+'</td>';
				html+='<td>'+(v.wms_inventory)+'</td>';
				//html+='<td><input type="text" class="input_text supply_qty supply_qty_'+val.id+'_'+v.id+'" name="id_qty['+val.id+'_'+v.id+']" style="width: 60px;" placeholder="'+$.getMessage('sys_Integer')+'" value="0"></td>';
				//html+='<td><input type="text" class="input_text supply_qty supply_qty_'+val.id+'_'+v.id+'" name="product_id['+val.id+']" style="width: 60px;" value="'+val.id+'"></td>';
				html+='</tr>';						
			});
		});
		$('#update_publish_div .table-module-list-data').html(html);
		$('#update_publish_div').dialog('open');
		$('#update_publish_div').dialog('option','title',$.getMessage('off_shelf_product'));//产品下架
		$('#upSubmitBtn').hide();
		$('#downSubmitBtn').show();
	});
	$('.disableSupplyBtn').live('click',function(){
		$(this).siblings('.supply_qty').val('-1');
	})
	$('#update_publish_div').dialog({
		autoOpen: false,
		width: 1024,
		maxHeight:600,
		modal: true,
		show: "slide",
		buttons: [
		          {
		        	  text: '上架',
		        	  id:'upSubmitBtn',
		        	  click: function () {
		        		  	//确定设置上架数量？
		        			if(!window.confirm('<{t}>are_you_sure<{/t}>')){
		        				return false;
		        			}
		        			var this_ = $(this);
		        			//请求中，请等待
		        			alertTip($.getMessage('sys_common_wait'));
		        			var param = $('#update_publish_form').serialize();
		        			$.ajax({
		        				type: "post",
		        				dataType: "json",
		        				url: '/product/shopify/publish/product_status/1',
		        				data: param,
		        				success: function (json) {
		        					var html = '';
		        					html+=json.message;		        					   
	        						$('#dialog-auto-alert-tip').dialog('option','maxHeight',400).html(html);
	        						if(json.ask){
			  		        		    this_.dialog("close");  
			  		        		    initData(paginationCurrentPage-1)
		        					}
		        				}
		        			});
		        	  }
		          },
		          {
		        	  text: '下架',
		        	  id:'downSubmitBtn',
		        	  click: function () {
		        		  	//确定设置下架数量？
		        			if(!window.confirm('<{t}>are_you_sure<{/t}>')){
		        				return false;
		        			}
		        			var this_ = $(this);
		        			//请求中，请等待
		        			alertTip($.getMessage('sys_common_wait'));
		        			var param = $('#update_publish_form').serialize();
		        			$.ajax({
		        				type: "post",
		        				dataType: "json",
		        				url: '/product/shopify/publish/product_status/2',
		        				data: param,
		        				success: function (json) {
		        					var html = '';
		        					html+=json.message;	    					
	        						$('#dialog-auto-alert-tip').dialog('option','maxHeight',400).html(html);
		        					if(json.ask){
			  		        		    this_.dialog("close");  
			  		        		    initData(paginationCurrentPage-1)
		        					}  
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
			
			$.each(val.variants,function(k,v){
				$('.supply_qty_'+val.id+'_'+v.id).val(v.inventory_quantity);					
			});
		});
	});
	
	
	$('#type').change(function(){
		if($(this).val()=='sku'){
			//'模糊搜索'
			$('#code').attr('placeholder',$.getMessage('sys_fuzzy_srarch'));
		}else if($(this).val()=='item_id'){
			//'请输入ItemID,多个ItemID以空格隔开'
			$('#code').attr('placeholder',$.getMessage('sys_multiple_search_values',['ItemID']));			
		}
	}).change();
});

$(function(){
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
		        		  //确定设置下架数量？
	        			  if(!window.confirm('<{t}>are_you_sure<{/t}>')){
		        			  return false;
		        		  }
		        		  var this_ = $(this);
		        		  //请求中，请等待
		        		  alertTip($.getMessage('sys_common_wait'));
		        		  var param = $('#update_supply_qty_form').serialize();
		        		  $.ajax({
		        			  type: "post",
		        			  dataType: "json",
		        			  url: '/product/shopify/update-supply-qty',
		        			  data: param,
		        			  success: function (json) {
		        				  var html = '';
		        				  html+=json.message;	    					
		        				  $('#dialog-auto-alert-tip').dialog('option','maxHeight',400).html(html);
		        				  if(json.ask){
		        					  this_.dialog("close");  
		        					  initData(paginationCurrentPage-1)
		        				  }  
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
	$('.changeQtyBtn').click(function(){
		if($('.checkItem:checked').size()==0){
			//'请勾选需要下架的产品'
			alertTip($.getMessage('请选择产品'));
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
			
			$.each(val.variants,function(k,v){
				html+='<tr class="'+clazz+'">';
				html+='<td>'+val.id+' / '+v.sku+' / '+v.title+'</td>';
				html+='<td>'+(v.inventory_quantity)+'</td>';
				html+='<td>'+(v.wms_inventory)+'</td>';
				html+='<td>'+(v.out_of_stock_qty)+'</td>';
				html+='<td>'+(v.no_to_wms_qty)+'</td>';
				var sup_qty = v.wms_inventory-v.out_of_stock_qty-v.no_to_wms_qty;
				sup_qty = sup_qty>0?sup_qty:0;
				html+='<td><input type="text" class="input_text supply_qty supply_qty_'+val.id+'_'+v.id+'" name="id_qty['+val.id+'_'+v.id+']" style="width: 60px;" placeholder="'+$.getMessage('sys_Integer')+'" value="'+(sup_qty)+'"></td>';
				html+='</tr>';						
			});
		});
		$('#update_supply_qty_div .table-module-list-data').html(html);
		$('#update_supply_qty_div').dialog('open');
	})
});


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
		        		  //确定设置下架数量？
		        		  if(!window.confirm('<{t}>are_you_sure<{/t}>')){
		        			  return false;
		        		  }
		        		  var this_ = $(this);
		        		  //请求中，请等待
		        		  alertTip($.getMessage('sys_common_wait'));
		        		  var param = $('#update_price_form').serialize();
		        		  $.ajax({
		        			  type: "post",
		        			  dataType: "json",
		        			  url: '/product/shopify/update-price',
		        			  data: param,
		        			  success: function (json) {
		        				  var html = '';
		        				  html+=json.message;	    					
		        				  $('#dialog-auto-alert-tip').dialog('option','maxHeight',400).html(html);
		        				  if(json.ask){
		        					  this_.dialog("close");  
		        					  initData(paginationCurrentPage-1)
		        				  }  
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
	$('.changePriceBtn').click(function(){
		if($('.checkItem:checked').size()==0){
			//'请勾选需要下架的产品'
			alertTip($.getMessage('请选择产品'));
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
			
			$.each(val.variants,function(k,v){
				html+='<tr class="'+clazz+'">';
				html+='<td>'+val.id+' / '+v.sku+' / '+v.title+'</td>';
				html+='<td>'+(v.price)+'</td>';
				html+='<td>'+(v.compare_at_price)+'</td>';
				html+='<td><input type="text" class="input_text supply_qty supply_qty_'+val.id+'_'+v.id+'" name="id_price['+val.id+'_'+v.id+'][price]" style="width: 60px;" value="'+v.price+'"></td>';
				html+='<td><input type="text" class="input_text supply_qty supply_qty_'+val.id+'_'+v.id+'" name="id_price['+val.id+'_'+v.id+'][compare_at_price]" style="width: 60px;" value="'+v.compare_at_price+'"></td>';

				html+='</tr>';						
			});
		});
		$('#update_price_div .table-module-list-data').html(html);
		$('#update_price_div').dialog('open');
	})
})