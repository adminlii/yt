
var warehouseShippingMethodJson = <{$warehouseShippingMethodJson}>;
var warehouseArrJson = <{$warehouseArrJson}>;
var platUserJson = <{$platUserJson}>;

EZ.url = '/order/order-allot/';
EZ.getListData = function(json) {
	if(json.state){
		var html = '';
		$.each(json.data, function(key, val) {				
			html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
	        html += "<td >" +(key + 1)+' : '+ val.allot_set_name + "</td>";
	        html += "<td >" + warehouseArrJson[val.warehouse_id].warehouse_code+'['+warehouseArrJson[val.warehouse_id].warehouse_desc+']' + "</td>";
	        html += "<td >" + val.shipping_method + "</td>";
	        html += "<td >" + val.allot_level + "</td>";
	        html += "<td >" + val.user_name + "</td>";
	        val.con = '';
	        html += "<td title='"+val.con+"'>";
	        if(json.user_id==val.user_id){
		        html += "<a href='javascript:;' allot_set_id='"+val.allot_set_id+"' class='delBtn'>" + EZ.del + "</a>&nbsp;&nbsp;";
		        html += "<a href='javascript:;' allot_set_id='"+val.allot_set_id+"' class='editBtn'>" + EZ.edit + "</a>&nbsp;&nbsp;";	        	
	        }else{
		        html += "<span>" + EZ.del + "</span>&nbsp;&nbsp;";
		        html += "<span>" + EZ.edit + "</span>&nbsp;&nbsp;";
	        }
	        html += "<a href='javascript:;' allot_set_id='"+val.allot_set_id+"' class='viewBtn'>查看</a>&nbsp;&nbsp;";
	        html += "</td>";
	        html += "</tr>";
		});
	}
	
	return html;
}
$(function(){
	$('#ship_warehouse').live('change',function(){
		var warehouse_id = $(this).val();
		var html = '';
		$('#shipping_method').html(html);
		if(warehouse_id==''){
			html+='<option value="">请选择仓库</option>';
			$('#shipping_method').html(html);
			return;
		}
		$.each(warehouseShippingMethodJson[warehouse_id],function(k,v){
			html+='<option value="'+v.sm_code+'">'+v.sm_code+'['+v.sm_name_cn+']'+'</option>';
		}); 
		$('#shipping_method').html(html);
	});

	
	
	$('#order_allot_form :checkbox').each(function(){
		var id= $(this).attr('id');
		$('#selected_condition').append('<div id="selected_'+id+'"></div>');
	})
	$('#order_allot_form :checkbox').click(function(){
		var checked = $(this).is(':checked');
		var id= $(this).attr('id');
		if(checked){
			var val = $(this).parent().next('td').find('a').size()?'':'1';//没有选择按钮默认为1
			var td_tag = $(this).parent().next();
			var div_tag = td_tag.find('DIV:hidden');
			var html = div_tag.html()+'<input type="hidden" name="condition['+id+']" value="'+val+'"/>';
			$('#selected_condition #selected_'+id).html(html);
		}else{
			$('#selected_condition #selected_'+id).html('');
		}
	});	
	
});
function allot_init(){

	var allot_set_id = $('#allot_set_id').val();

	$('#selected_condition div').html('');
	$('#order_allot_form :checkbox').attr('checked',false);
	
	//默认绑定账号
	setTimeout(function(){
		$('#order_allot_form #user_account').attr('checked',true);
		$('#order_allot_form #user_account').attr('disabled',true);
		var id = 'user_account';
		var val = $('#'+id).parent().next('td').find('a').size()?'':'1';//没有选择按钮默认为1
		var td_tag = $('#'+id).parent().next();
		var div_tag = td_tag.find('DIV:hidden');
		var html = div_tag.html()+'<input type="hidden" name="condition['+id+']" value="'+val+'"/>';
		$('#selected_condition #selected_'+id).html(html);
		
	},100);

	
	if(allot_set_id!=''){
		$.ajax({
			type : "POST",
			url : "/order/order-allot/detail",
			data : {'allot_set_id':allot_set_id},
			dataType : 'json',
			success : function(json) {
				if(json.ask){
					$.each(json.allot_set,function(k,v){			        						
						$('#'+k).val(v);  
						if(k=='ship_warehouse'){
							$('#'+k).change();
						}
					});						
					$.each(json.allot_set_con,function(k,v){			        						
						$(':checkbox#'+v.condition_type).attr('checked',true);
						var html = '';
						//通用情况
						if($('#'+v.condition_type).size()>0){
							var td_tag = $('#'+v.condition_type).parent().next();
							var td_div = td_tag.find("DIV:hidden");
							html = td_div.html()+'<input type="hidden" name="condition['+v.condition_type+']" value="'+v.set_value+'"/>'; 
						}
						//特殊情况
						if(v.condition_type=='product_count'){

							var input = v.set_value;
							input = input.split(';');
							var param = {};
							$.each(input,function(k,v){
								var vv = v.split(':');
								param[vv[0]] = vv[1];
							}) 

							var from_type = param.from_type;
							var from_val = param.from_val;
							var to_type = param.to_type;
							var to_val = param.to_val;
							var aHtml = '';
							if(from_val){
								switch(from_type){
									case 'gt':
										aHtml+='大于';
										break;
									case 'ge':
										aHtml+='大于等于';
										break;
								}
								aHtml+=from_val;
							}
							if(to_val){
								aHtml+='&nbsp;';
								switch(to_type){
									case 'lt':
										aHtml+='小于';
										break;
									case 'le':
										aHtml+='小于等于';
										break;
								}
								aHtml+=to_val;
							}
							v.set_value = aHtml;
						}
						else if(v.condition_type=='weight'){

							var input = v.set_value;
							input = input.split(';');
							var param = {};
							$.each(input,function(k,v){
								var vv = v.split(':');
								param[vv[0]] = vv[1];
							}) 

							var from_type = param.from_type;
							var from_val = param.from_val;
							var to_type = param.to_type;
							var to_val = param.to_val;
							var aHtml = '';
							if(from_val){
								switch(from_type){
									case 'gt':
										aHtml+='大于';
										break;
									case 'ge':
										aHtml+='大于等于';
										break;
								}
								aHtml+=from_val;
							}
							if(to_val){
								aHtml+='&nbsp;';
								switch(to_type){
									case 'lt':
										aHtml+='小于';
										break;
									case 'le':
										aHtml+='小于等于';
										break;
								}
								aHtml+=to_val;
							}
							v.set_value = aHtml;
						}else if(v.condition_type=='order_puchase'){

							var input = v.set_value;
							input = input.split(';');
							var param = {};
							$.each(input,function(k,v){
								var vv = v.split(':');
								param[vv[0]] = vv[1];
							}) 

							var from_type = param.from_type;
							var from_val = param.from_val;
							var to_type = param.to_type;
							var to_val = param.to_val;
							var currency = param.currency;
							var aHtml = '';
							if(from_val){
								switch(from_type){
									case 'gt':
										aHtml+='大于';
										break;
									case 'ge':
										aHtml+='大于等于';
										break;
								}
								aHtml+=from_val;
							}
							if(to_val){
								aHtml+='&nbsp;';
								switch(to_type){
									case 'lt':
										aHtml+='小于';
										break;
									case 'le':
										aHtml+='小于等于';
										break;
								}
								aHtml+=to_val;
							}
							aHtml+=currency;
							v.set_value = aHtml;
						}else if(v.condition_type=='ship_fee'){

							var input = v.set_value;
							input = input.split(';');
							var param = {};
							$.each(input,function(k,v){
								var vv = v.split(':');
								param[vv[0]] = vv[1];
							}) 

							var from_type = param.from_type;
							var from_val = param.from_val;
							var to_type = param.to_type;
							var to_val = param.to_val;
							var currency = param.currency;
							var aHtml = '';
							if(from_val){
								switch(from_type){
									case 'gt':
										aHtml+='大于';
										break;
									case 'ge':
										aHtml+='大于等于';
										break;
								}
								aHtml+=from_val;
							}
							if(to_val){
								aHtml+='&nbsp;';
								switch(to_type){
									case 'lt':
										aHtml+='小于';
										break;
									case 'le':
										aHtml+='小于等于';
										break;
								}
								aHtml+=to_val;
							}
							aHtml+=currency;
							v.set_value = aHtml;
						}
						$('#selected_condition #selected_'+v.condition_type).html(html);
						$('#selected_condition #selected_'+v.condition_type+' .tdu').html(v.set_value);

					});

				}else{
					alertTip(json.message);
				}
			}
		});	        		  
	}else{
		$('#order_allot_form :input').val('');
		$('#ship_warehouse').change();

	}
}
$(function(){
	$('#createBtn').click(function(){
		$('#allot_set_id').val('');
		$('#order_allot_div').dialog('open');
		$('#order_allot_div').dialog('option','title','订单分仓规则--新建');
		$('#selected_condition').removeClass("order_allot_selected_options_view");
		$('#allotSaveBtn').show();
		$('#order_allot_div :input').attr('disabled',false);
		$('#order_allot_div :checkbox').attr('disabled',false);
	});

	$('.editBtn').live('click',function(){
		$('#allot_set_id').val($(this).attr('allot_set_id'));
		$('#order_allot_div').dialog('open');
		$('#order_allot_div').dialog('option','title','订单分仓规则--编辑');
		$('#selected_condition').removeClass("order_allot_selected_options_view");
		$('#allotSaveBtn').show();
		$('#order_allot_div :input').attr('disabled',false);
		$('#order_allot_div :checkbox').attr('disabled',false);
	})
	
	$('.viewBtn').live('click',function(){
		$('#allot_set_id').val($(this).attr('allot_set_id'));
		
		$('#order_allot_div').dialog('open');
		$('#order_allot_div').dialog('option','title','订单分仓规则--查看');
		$('#selected_condition').addClass("order_allot_selected_options_view");
		$('#allotSaveBtn').hide();
		$('#order_allot_div :input').attr('disabled',true);
		$('#order_allot_div :checkbox').attr('disabled',true);
		setTimeout(function(){
			$('#selected_condition a').removeClass('tdu');			
		},800);
		
	})
	
	$('.delBtn').live('click',function(){
		var allot_set_id = $(this).attr('allot_set_id');
		if(!window.confirm('删除该规则?')){
			return;
		}
		$.ajax({
			type : "POST",
			url : "/order/order-allot/delete",
			data : {'allot_set_id':allot_set_id},
			dataType : 'json',
			success : function(json) {		
				var html = '';
				html+=json.message;	
				alertTip(html);
				if(json.ask){
					loadData(paginationCurrentPage-1,paginationPageSize);
				}

			}
		});

	})
	
	$('#order_allot_div').dialog({
		autoOpen: false,
		width: 1024,
		maxHeight: 650,
		modal: true,
		show: "slide",
		buttons: [
		          {
		        	  text: '保存',
		        	  id:'allotSaveBtn',
		        	  click: function () { 
		        		  var this_ = $(this);
		        		  alertTip('数据处理中...',300);	
		        		  var param = $("#order_allot_form").serialize();
		        		  $.ajax({
		        			  type : "POST",
		        			  url : "/order/order-allot/save",
		        			  data : param,
		        			  dataType : 'json',
		        			  success : function(json) {		
		        				  var html = '';
		        				  html+=json.message;	
		        				  if(json.ask){
			        				  $('#dialog-auto-alert-tip').html(html);
		    		        		  this_.dialog("close");	
		        					  loadData(paginationCurrentPage-1,paginationPageSize);
		        				  }else{
		        					  html+='<br/>';
		        					  $.each(json.err_arr,function(k,v){
		        						  html+='‘'+$('#'+k).attr('title')+'’ '+v+'<br/>';
		        					  })
			        				  $('#dialog-auto-alert-tip').html(html);
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
		        	  allot_init();
		          }
	});
	$('#user_account_div').dialog({
		autoOpen: false,
		width: 400,
		maxHeight: 600,
		modal: true,
		show: "slide",
		buttons: [
		          {
		        	  text: '确定',
		        	  click: function () { 
		        		  var this_ = $(this);
		        		  this_.dialog("close");
		        		  
		        		  var li_selected = [];
		        		  $('#'+this.id+' li.selected').each(function(){
		        			  li_selected.push($(this).attr('val'));
		        		  });
		        		  var aHtml = '';
		        		  var input = '';
		        		  if(li_selected.length>0){
		        			  aHtml = li_selected.join(';');
		        			  input = li_selected.join(';');
		        		  }else{
		        			  aHtml='指定账号';
		        			  input = '';
		        		  }
		        		  //alert(selected_account.join(';'));
		        		  $('#selected_user_account .tdu').html(aHtml);
		        		  $('#selected_user_account input').val(input);
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
		        	  $('li',this).removeClass('selected');
	        		  var input = $('#selected_user_account input').val();
	        		  if(input!=''){
	        			  input = input.split(';');
	        			  $.each(input,function(k,v){
	        				  $('.li_'+v).addClass('selected');
	        			  })
	        		  }
		          }
	});

	$('#order_site_div').dialog({
		autoOpen: false,
		width: 400,
		maxHeight: 600,
		modal: true,
		show: "slide",
		buttons: [
		          {
		        	  text: '确定',
		        	  click: function () { 
		        		  var this_ = $(this);
		        		  this_.dialog("close");
		        		  
		        		  var li_selected = [];
		        		  $('#'+this.id+' li.selected').each(function(){
		        			  li_selected.push($(this).attr('val'));
		        		  });
		        		  var aHtml = '';
		        		  var input = '';
		        		  if(li_selected.length>0){
		        			  aHtml = li_selected.join(';');
		        			  input = li_selected.join(';');
		        		  }else{
		        			  aHtml='指定账号';
		        			  input = '';
		        		  }
		        		  //alert(selected_account.join(';'));
		        		  $('#selected_order_site .tdu').html(aHtml);
		        		  $('#selected_order_site input').val(input);
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
		        	  $('li',this).removeClass('selected');
	        		  var input = $('#selected_order_site input').val();
	        		  if(input!=''){
	        			  input = input.split(';');
	        			  $.each(input,function(k,v){
	        				  $('.li_'+v).addClass('selected');
	        			  })
	        		  }
		          }
	});
	
	$('#shipping_method_platform_div').dialog({
		autoOpen: false,
		width: 300,
		maxHeight: 600,
		modal: true,
		show: "slide",
		buttons: [
		          {
		        	  text: '确定',
		        	  click: function () { 
		        		  var this_ = $(this);
		        		  this_.dialog("close");	


		        		  var li_selected = [];
		        		  $('#'+this.id+' li.selected').each(function(){
		        			  li_selected.push($(this).attr('val'));
		        		  });
		        		  var aHtml = '';
		        		  var input = '';
		        		  if(li_selected.length>0){
		        			  aHtml = li_selected.join(';');
		        			  input = li_selected.join(';');
		        		  }else{
		        			  aHtml='指定运输类型';
		        			  input = '';
		        		  }
		        		  //alert(selected_account.join(';'));
		        		  $('#selected_shipping_method_platform .tdu').html(aHtml);
		        		  $('#selected_shipping_method_platform input').val(input);
		        		  
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
		        	  $('li',this).removeClass('selected');
	        		  var input = $('#selected_shipping_method_platform input').val();
	        		  if(input!=''){
	        			  input = input.split(';');
	        			  $.each(input,function(k,v){
	        				  $('.li_'+v).addClass('selected');
	        			  })
	        		  }
		          }
	});

	$('#consignee_country_div').dialog({
		autoOpen: false,
		width: 400,
		maxHeight: 600,
		modal: true,
		show: "slide",
		buttons: [
		          {
		        	  text: '确定',
		        	  click: function () { 
		        		  var this_ = $(this);
		        		  this_.dialog("close");	


		        		  var li_selected = [];
		        		  var li_selected_short = [];
		        		  $('#'+this.id+' li.selected').each(function(){
		        			  li_selected_short.push($(this).attr('val'));
		        			  li_selected.push($(this).text());
		        		  });
		        		  var aHtml = '';
		        		  var input = '';
		        		  if(li_selected.length>0){
		        			  aHtml = li_selected.join(';');
		        			  input = li_selected_short.join(';');
		        		  }else{
		        			  aHtml='指定国家';
		        			  input = '';
		        		  }
		        		  //alert(selected_account.join(';'));
		        		  $('#selected_consignee_country .tdu').html(aHtml);
		        		  $('#selected_consignee_country input').val(input);
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
		        	  $('li',this).removeClass('selected');
	        		  var input = $('#selected_consignee_country input').val();
	        		  if(input!=''){
	        			  input = input.split(';');
	        			  $.each(input,function(k,v){
	        				  $('.li_'+v).addClass('selected');
	        			  })
	        		  }
		          }
	});

	$('#order_puchase_div').dialog({
		autoOpen: false,
		width: 600,
		maxHeight: 600,
		modal: true,
		show: "slide",
		buttons: [
		          {
		        	  text: '确定',
		        	  click: function () { 
		        		  var this_ = $(this);
		        		  this_.dialog("close");	

		        		  var purchase_from_type = $('#purchase_from_type').val();
		        		  var purchase_from_type_title = $('#purchase_from_type option:selected').text();
		        		  var purchase_from = $.trim($('#purchase_from').val());
		        		  var purchase_to_type = $('#purchase_to_type').val();
		        		  var purchase_to_type_title = $('#purchase_to_type option:selected').text();
		        		  var purchase_to = $.trim($('#purchase_to').val());
		        		  var currency = $.trim($('#purchase_currency').val());

		        		  
		        		  var aHtml = '';
		        		  var input = '';
		        		  if(purchase_from!=''||purchase_to!=''){
		        			  input = [];
		        			  if(purchase_from){
		        				  aHtml+=purchase_from_type_title+purchase_from
		        			  }
		        			  if(purchase_to){
		        				  aHtml+='&nbsp;'+purchase_to_type_title+purchase_to		        				  
		        			  }
	        				  aHtml+='&nbsp;'+currency	
		        			  input.push('from_type:'+purchase_from_type);
		        			  input.push('from_val:'+purchase_from);
		        			  input.push('to_type:'+purchase_to_type);
		        			  input.push('to_val:'+purchase_to);
		        			  input.push('currency:'+currency);
		        			  input = input.join(';');
		        			  
		        		  }else{
		        			  aHtml='指定范围';
		        			  input = '';
		        		  }
		        		  //alert(selected_account.join(';'));
		        		  $('#selected_order_puchase .tdu').html(aHtml);
		        		  $('#selected_order_puchase input').val(input);
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
		        	  
		        	  $(':input',this).val('');
		        	  var input = $('#selected_order_puchase input').val();
		        	  if($.trim(input)!=''){
		        		  var param = {};
		        		  input = input.split(';');
		        		  $.each(input,function(k,v){
		        			  var vv = v.split(':');
		        			  param[vv[0]] = vv[1];
		        			  
		        		  })
			        	  $('#purchase_from_type').val(param.from_type);
			        	  $('#purchase_from').val(param.from_val);
			        	  $('#purchase_to_type').val(param.to_type);
			        	  $('#purchase_to').val(param.to_val);
			        	  $('#purchase_currency').val(param.currency);
		        	  }

		          }
	});

	$('#ship_fee_div').dialog({
		autoOpen: false,
		width: 600,
		maxHeight: 600,
		modal: true,
		show: "slide",
		buttons: [
		          {
		        	  text: '确定',
		        	  click: function () { 
		        		  var this_ = $(this);
		        		  this_.dialog("close");	

		        		  var shipfee_from_type = $('#shipfee_from_type').val();
		        		  var shipfee_from_type_title = $('#shipfee_from_type option:selected').text();
		        		  var shipfee_from = $.trim($('#shipfee_from').val());
		        		  var shipfee_to_type = $('#shipfee_to_type').val();
		        		  var shipfee_to_type_title = $('#shipfee_to_type option:selected').text();
		        		  var shipfee_to = $.trim($('#shipfee_to').val());
		        		  var currency = $.trim($('#shipfee_currency').val());

		        		  
		        		  var aHtml = '';
		        		  var input = '';
		        		  if(shipfee_from!=''||shipfee_to!=''){
		        			  input = [];
		        			  if(shipfee_from){
		        				  aHtml+=shipfee_from_type_title+shipfee_from
		        			  }
		        			  if(shipfee_to){
		        				  aHtml+='&nbsp;'+shipfee_to_type_title+shipfee_to		        				  
		        			  }
	        				  aHtml+='&nbsp;'+currency	
		        			  input.push('from_type:'+shipfee_from_type);
		        			  input.push('from_val:'+shipfee_from);
		        			  input.push('to_type:'+shipfee_to_type);
		        			  input.push('to_val:'+shipfee_to);
		        			  input.push('currency:'+currency);
		        			  input = input.join(';');
		        			  
		        		  }else{
		        			  aHtml='指定范围';
		        			  input = '';
		        		  }
		        		  //alert(selected_account.join(';'));
		        		  $('#selected_ship_fee .tdu').html(aHtml);
		        		  $('#selected_ship_fee input').val(input);
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
		        	  $(':input',this).val('');
		        	  var input = $('#selected_ship_fee input').val();
		        	  if($.trim(input)!=''){
		        		  var param = {};
		        		  input = input.split(';');
		        		  $.each(input,function(k,v){
		        			  var vv = v.split(':');
		        			  param[vv[0]] = vv[1];
		        			  
		        		  })
			        	  $('#shipfee_from_type').val(param.from_type);
			        	  $('#shipfee_from').val(param.from_val);
			        	  $('#shipfee_to_type').val(param.to_type);
			        	  $('#shipfee_to').val(param.to_val);
			        	  $('#shipfee_currency').val(param.currency);
		        	  }
		          }
	});

	$('.searchProductBtn').live('click',function(){
		var param = $("#productSearchForm").serialize();
		
		var input = $('#selected_product_sku input').val();
		input = input.split(';');
		var selected = {};
		$.each(input,function(k,v){
			selected[v] = v;
		})
		$.ajax({
			type: "post",
			async: false,
			dataType: "json",
			url: '/order/order-allot/product-list/pageSize/100',
			data: param,
			success: function (json) {
				var html = '';
				if(json.state){
					$.each(json.data, function (key, val) {
						html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";	        
						html += "<td >" + val.product_sku + "</td>";
						html += "<td >" + val.product_title_en + "</td>";
						html += "<td >" + val.product_title + "</td>";
						var checked = selected[val.product_sku]?'checked':'';

						html += '<td><input type="checkbox" class="checkItem" value="'+val.product_sku+'" class="sku_'+val.product_sku+'" '+checked+'/></td>';

						html += "</tr>";
					});
					var size = $('.checkItem:checked').size();

					if(size==$('.checkItem').size()){
						$(".checkAll").attr('checked',true);
					}else{
						$(".checkAll").attr('checked',false);			
					}
				}else{
					html +=  "<tr class='table-module-b1'>";	        
					html += "<td colspan='4'>" + json.message + "</td>";					
					html += "</tr>";
				}

				$('#product_sku_div .table-module-list-data').html(html);
			}
		});
	})
	
	$(".checkAll").live('click', function() {
		$(".checkItem").attr('checked', $(this).is(':checked'));
		var size = $('.checkItem:checked').size();
		$('.checked_count').text('已经选择了个'+size+'SKU');
	});
	$('.checkItem').live('click',function(){
		var size = $('.checkItem:checked').size();
		$('.checked_count').text('已经选择了个'+size+'SKU');
		if(size==$('.checkItem').size()){
			$(".checkAll").attr('checked',true);
		}else{
			$(".checkAll").attr('checked',false);			
		}
	})
	$('#sku_search_type').change(function(){
		var placeholder = $('option:selected',this).attr('p');
		$('#search_product_sku').attr('placeholder',placeholder);
	}).change();
	
	$('#product_sku_div').dialog({
		autoOpen: false,
		width: 800,
		height: 480,
		modal: true,
		show: "slide",
		buttons: [
		          {
		        	  text: '确定',
		        	  click: function () { 
		        		  var this_ = $(this);
		        		  this_.dialog("close");	

		        		  var li_selected = [];
		        		  $('#'+this.id+' .checkItem:checked').each(function(){
		        			  li_selected.push($(this).val());
		        		  });
		        		  var aHtml = '';
		        		  var input = '';
		        		  if(li_selected.length>0){
		        			  aHtml = li_selected.join(';');
		        			  input = li_selected.join(';');
		        		  }else{
		        			  aHtml='指定货品';
		        			  input = '';
		        		  }
		        		  //alert(selected_account.join(';'));
		        		  $('#selected_product_sku .tdu').html(aHtml);
		        		  $('#selected_product_sku input').val(input);
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
		        	  $('.checked_count').text('');
		        	  $('#product_sku_div .table-module-list-data').html(''); 
	        		  var input = $('#selected_product_sku input').val();
        			  $('#search_product_sku').val(input);
	        		  if(input!=''){
	        			  $('#sku_search_type').val('1');
	        			  $('.searchProductBtn').click();
	        		  }
		          }
	});

	$('#product_count_div').dialog({
		autoOpen: false,
		width: 400,
		maxHeight: 600,
		modal: true,
		show: "slide",
		buttons: [
		          {
		        	  text: '确定',
		        	  click: function () { 
		        		  var this_ = $(this);
		        		  this_.dialog("close");	

		        		  var qty_from_type = $('#qty_from_type').val();
		        		  var qty_from_type_title = $('#qty_from_type option:selected').text();
		        		  var qty_from = $.trim($('#qty_from').val());
		        		  var qty_to_type = $('#qty_to_type').val();
		        		  var qty_to_type_title = $('#qty_to_type option:selected').text();
		        		  var qty_to = $.trim($('#qty_to').val());

		        		  
		        		  var aHtml = '';
		        		  var input = '';
		        		  if(qty_from!=''||qty_to!=''){
		        			  input = [];
		        			  if(qty_from){
		        				  aHtml+=qty_from_type_title+qty_from
		        			  }
		        			  if(qty_to){
		        				  aHtml+='&nbsp;'+qty_to_type_title+qty_to		        				  
		        			  }
		        			  input.push('from_type:'+qty_from_type);
		        			  input.push('from_val:'+qty_from);
		        			  input.push('to_type:'+qty_to_type);
		        			  input.push('to_val:'+qty_to);
		        			  input = input.join(';');
		        			  
		        		  }else{
		        			  aHtml='指定范围';
		        			  input = '';
		        		  }
		        		  //alert(selected_account.join(';'));
		        		  $('#selected_product_count .tdu').html(aHtml);
		        		  $('#selected_product_count input').val(input);
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
		        	  $('input',this).val('');
		        	  var input = $('#selected_product_count input').val();
		        	  if($.trim(input)!=''){
		        		  var param = {};
		        		  input = input.split(';');
		        		  $.each(input,function(k,v){
		        			  var vv = v.split(':');
		        			  param[vv[0]] = vv[1];
		        			  
		        		  })
			        	  $('#qty_from_type').val(param.from_type);
			        	  $('#qty_from').val(param.from_val);
			        	  $('#qty_to_type').val(param.to_type);
			        	  $('#qty_to').val(param.to_val);
		        	  }

		          }
	});
	$('#weight_div').dialog({
		autoOpen: false,
		width: 400,
		maxHeight: 600,
		modal: true,
		show: "slide",
		buttons: [
		          {
		        	  text: '确定',
		        	  click: function () { 
		        		  var this_ = $(this);
		        		  this_.dialog("close");	

		        		  var weight_from_type = $('#weight_from_type').val();
		        		  var weight_from_type_title = $('#weight_from_type option:selected').text();
		        		  var weight_from = $.trim($('#weight_from').val());
		        		  var weight_to_type = $('#weight_to_type').val();
		        		  var weight_to_type_title = $('#weight_to_type option:selected').text();
		        		  var weight_to = $.trim($('#weight_to').val());

		        		  
		        		  var aHtml = '';
		        		  var input = '';
		        		  if(weight_from!=''||weight_to!=''){
		        			  input = [];
		        			  if(weight_from){
		        				  aHtml+=weight_from_type_title+weight_from
		        			  }
		        			  if(weight_to){
		        				  aHtml+='&nbsp;'+weight_to_type_title+weight_to		        				  
		        			  }
		        			  input.push('from_type:'+weight_from_type);
		        			  input.push('from_val:'+weight_from);
		        			  input.push('to_type:'+weight_to_type);
		        			  input.push('to_val:'+weight_to);
		        			  input = input.join(';');
		        			  
		        		  }else{
		        			  aHtml='指定范围';
		        			  input = '';
		        		  }
		        		  //alert(selected_account.join(';'));
		        		  $('#selected_weight .tdu').html(aHtml);
		        		  $('#selected_weight input').val(input);
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
		        	  $('input',this).val('');
		        	  var input = $('#selected_weight input').val();
		        	  if($.trim(input)!=''){
		        		  var param = {};
		        		  input = input.split(';');
		        		  $.each(input,function(k,v){
		        			  var vv = v.split(':');
		        			  param[vv[0]] = vv[1];
		        			  
		        		  })
			        	  $('#weight_from_type').val(param.from_type);
			        	  $('#weight_from').val(param.from_val);
			        	  $('#weight_to_type').val(param.to_type);
			        	  $('#weight_to').val(param.to_val);
		        	  }

		          }
	});
	
	$('#selected_user_account .tdu').live('click',function(){
		$('#user_account_div').dialog('open');
	})
	
	$('#selected_order_site .tdu').live('click',function(){
		$('#order_site_div').dialog('open');
	})
	$('#selected_shipping_method_platform .tdu').live('click',function(){
		$('#shipping_method_platform_div').dialog('open');
	})
	$('#selected_consignee_country .tdu').live('click',function(){
		$('#consignee_country_div').dialog('open');
	})
	$('#selected_order_puchase .tdu').live('click',function(){
		$('#order_puchase_div').dialog('open');
	})
	$('#selected_ship_fee .tdu').live('click',function(){
		$('#ship_fee_div').dialog('open');
	})
	$('#selected_product_sku .tdu').live('click',function(){
		$('#product_sku_div').dialog('open');
	})
	$('#selected_product_count .tdu').live('click',function(){
		$('#product_count_div').dialog('open');
	})
	$('#selected_weight .tdu').live('click',function(){
		$('#weight_div').dialog('open');
	})

	$('.dialog_div li').live('click',function(){
		if($(this).hasClass('selected')){
		    $(this).removeClass('selected');			
		}else{
		    $(this).addClass('selected');			
		}
		if($(this).hasClass('li_gt9')){//选择了大于9，取消其他选择
			$(this).siblings('li').removeClass('selected');
		}else{
			$('.li_gt9').removeClass('selected');
		}
    });
})
