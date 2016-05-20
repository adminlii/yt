<script type="text/javascript"> 
    EZ.url = '/product/product-combine-relation-invoice/';
    EZ.getListData = function (json) {
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
            html += "<td class='ec-center'>" + (i++) + "</td>";

            html += "<td class='' acc='"+val.user_account+"' comp='"+val.company_code+"'>" + val.product_sku + "</td>";
            html += "<td >" + val.pcr_product_sku+" * " + val.pcr_quantity + "</td>";
            //html += "<td >" + val.pcr_pu_price+" " + "</td>";
            //html += "<td >" + val.pcr_percent+"% " + "</td>";
            html += "<td >" + (val.user_account?val.user_account:$.getMessage('all_account'))+ "</td>";//所有账号
            html += "<td >" + val.pcr_add_time + "</td>";
            html += "<td >";
            html += "<a href='javascript:;' class='platform_sku' sku='"+val.product_sku+"' acc='"+val.user_account+"' comp='"+val.company_code+"' style=''>"+EZ.edit+"</a>&nbsp;&nbsp;";
            html += "<a href='javascript:;' class='delBtn' sku='"+val.product_sku+"' acc='"+val.user_account+"'>"+EZ.del+"</a>";
            html += "</td>";
            html += "</tr>";
        });
        return html;
    };

    $(function(){

    	$("#add_product_combine").dialog({
   			autoOpen: false,
	        width: 600,
	        height:350,
	        maxHeight: 480,
	        modal: true,
	        show: "slide",
	        buttons: [
	            {
	                text: '确认(Ok)',
	                click: function () {
						//提交中
	                	alertTip($.getMessage('sys_common_processing'),300);
	                	
		            	var params = $("#addRelationForm").serialize();
						var this_ = $(this);
		            	$.ajax({
		        			type: "POST",
		        			async: true,
		        			dataType: "json",
		        			url: '/product/product-combine-relation/add',
		        			data: params,
		        			error: function () {
		        				return;
		        			},
		        			success: function (json) {
		        				$('#dialog-auto-alert-tip').dialog('close');
			        			if(json.ask){
									this_.dialog("close");
									alertTip("<span class='tip-success-message'>"+json.message+"</span>");
				        		}else{
					        		var tmp_tips = "";
									$.each(json.error_message,function(k,v){
										tmp_tips += "<span class='tip-error-message'>"+v+"</span>";
									});
									$("#validateTips").html(tmp_tips);
					        	}
		        			}
		        		});
		              
	                }
	            },{
	                text: '取消(Close)',
	                click: function () {
	                    $(this).dialog("close");
	                }
	            }
	        ],
	        close: function () {
	        	$("#addRelationForm").find(".validateTips").html("");
	        	$("#addRelationForm").find(".row_data").html("");
	        	$("#addRelationForm").find(":input[type!='button']").val("");
	        }
        });
        
        $('#uploadDiv').dialog({
            autoOpen: false,
            width: 500, 
            height:180,
            modal: true,
            show: "slide",           
            close: function () {
                initData(0);   
            }
        }); 

        $('.editBtn').live('click',function(){
            var sku = $(this).attr('sku');
            var acc = $(this).attr('acc');
        	$.ajax({
    			type: "POST",
    			async: false,
    			dataType: "html",
    			url: '/product/product-combine-relation/get-detail/type/html',
    			data: {'sku':sku,'acc':acc},
    			error: function () {
    				return;
    			},
    			success: function (html) {

    				$(html).dialog({
    			        autoOpen: true,
    			        width: 400,
    			        maxHeight: 400,
    			        modal: true,
    			        show: "slide",
    			        buttons: [
    			            {
    			                text: 'Submit',
    			                click: function () {
        			                //更新产品关系？
    			                    if(!window.confirm($.getMessage('updated_product_relationship_confirm'))){
    			                        return false;
    			                    }
    			                    var this_ = $(this);
    			                    var params = $('#editRelationForm').serialize();
    			                	$.ajax({
    			            			type: "POST",
    			            			async: false,
    			            			dataType: "json",
    			            			url: '/product/product-combine-relation/update',
    			            			data: params,
    			            			error: function () {
    			            				return;
    			            			},
    			            			success: function (json) {
    			            				alertTip(json.message);
    	    			    				if(json.ask){
    	    			    					this_.dialog("close");
    	    			    					initData(paginationCurrentPage - 1);
    	    			    				}
    			            			}
    			            		});
    			                    
    			                }
    			            },{
    			                text: 'Close',
    			                click: function () {
    			                    $(this).dialog("close");
    			                }
    			            }
    			        ],
    			        close: function () {
    			            $(this).detach();
    			        }
    			    });
    			}
    		});
        })
        $('.delBtn').live('click',function(){
            var sku = $(this).attr('sku');
            var acc = $(this).attr('acc');
        	$.ajax({
    			type: "POST",
    			async: false,
    			dataType: "json",
    			url: '/product/product-combine-relation/get-detail',
    			data: {'sku':sku,'acc':acc},
    			error: function () {
    				return;
    			},
    			success: function (json) {
    				if(json.ask){
        				var product_sku = '';
        				var account = '';
        				var sub_product_sku = [];
        				$.each(json.data,function(k,v){
        					product_sku = v.product_sku;
        					account = v.user_account;
        					sub_product_sku.push(v.pcr_product_sku+'*'+v.pcr_quantity);
        				});
        				//SKU:'+product_sku+';对应子SKU：'+sub_product_sku.join(',')+';所属账号:'+(account==''?"所有账号":account);
        				var html = $.getMessage('del_sku_correspondence_between_confirm_01',[product_sku,sub_product_sku.join(','),(account==''?$.getMessage('all_account'):account)]);
        				html+="\n" + $.getMessage('del_sku_correspondence_between_confirm_02');//确认要删除该组合关系吗？
    				    if(window.confirm(html)){
    				    	$.ajax({
    			    			type: "POST",
    			    			async: false,
    			    			dataType: "json",
    			    			url: '/product/product-combine-relation/delete',
    			    			data: {'sku':sku,'acc':acc},
    			    			error: function () {

    			    				return;
    			    			},
    			    			success: function (json) {
        			    			alertTip(json.message);
    			    				if(json.ask){
    			    					initData(paginationCurrentPage - 1);
    			    				}
    			    			}

    			    		});
    				    }
    				}else{
    				    alertTip(json.message);
    				}
    			}

    		});
        })
    });
    
    function upload(){
        $("#uploadDiv").dialog('open');
    }

    function add(){
    	$("#add_product_combine").dialog('open');
    }

</script>
<script>
$(function(){
	$('.delPcrSku').live('click',function(){
	    var k = $(this).attr('key');
	    //"确定要删除该子SKU吗？"
	    if(!window.confirm($.getMessage('delete_the_sub_sku'))){
	        return false;
	    }
	    $('#pcr_wrap #pcr_table_'+k).remove();	    
	});

	$('.viewPlatformUserBtn').click(function(){
    	$.ajax({
            type: "post",
            dataType: "json",
            url: '/product/product-combine-relation/get-platform-user/',
            data: {},
            success: function (json) {
                var html = '';
                html += '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table-module"><tbody>';
				html += '<tr><td style="width:110px;"><{t}>shop_account<{/t}></td><td style="width:105px;"><{t}>display_name<{/t}></td></tr>';
				$.each(json,function(k,v){
					html += (k + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
					html += '<td >'+v.user_account+'</td>';
					html += '<td >'+v.platform_user_name+'</td>';
					html += '</tr>';
				});
				html += '</tbody></table>';
                alertTip(html,500,300);
            }
        });
    });
})
</script>
<style>
#editRelationForm tr {
	height: 30px;
}

.delPcrSku {
	margin-left: 25px;
	display: none;
}

.searchFilterText {
	width: 100px;
}
</style>
<!-- SKU关系上传 -->
<div id='uploadDiv' class='dislog_div' title='<{t}>sku_relationship_upload<{/t}>' style='display: none;'><{include file='product/views/product-combine-relation-invoice/upload.tpl'}></div>
<!-- 添加SKU关系 -->
<{include file='product/views/product-combine-relation-invoice/add.tpl'}>
<div id="module-container">
	<div id="ez-wms-edit-dialog" title="上传产品模板" style="display: none;"></div>
	<div id="search-module">
		<form id="searchForm" name="searchForm" class="submitReturnFalse">
			<div class="search-module-condition">
				<span class="searchFilterText"><{t}>Platform_Sales_SKU<{/t}>：</span>
				<!-- 平台销售SKU -->
				<input type="text" name="product_sku" id="product_sku" class="input_text keyToSearch" style="text-transform: uppercase; width: 262px;" placeholder="<{t}>like_search_before&after<{/t}>" />
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText"><{t}>申报代码<{/t}>：</span>
				<select id="sub_sku_query_type" name="sub_sku_query_type" class="input_text_select">
					<option value='1'>
						准确搜索
						<!-- 准确搜索 -->
					</option>
					<option value='2'>
						模糊搜索
						<!-- 模糊搜索 -->
					</option>
				</select>
				<input type="text" name="pcr_product_sku" id="pcr_product_sku" class="input_text keyToSearch" style="text-transform: uppercase; width: 180px;" title="<{t}>like_search_before&after<{/t}>" />
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText"><{t}>shop_account<{/t}>：</span>
				<!-- 店铺帐号 -->
				<select id="user_account" name="user_account" class="input_text_select" style="width: 132px;">
					<option value=''><{t}>all<{/t}></option>
					<!-- 全部 -->
					<{foreach from=$user_account_arr name=ob item=ob}>
					<option value='<{$ob.user_account}>'><{$ob.platform_user_name}></option>
					<{/foreach}>
				</select>
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText"><{t}>corresponding_sku<{/t}><{t}>quantity<{/t}>：</span>
				<input type="text" name="pcr_quantity_from" class="input_text keyToSearch" style="width: 50px;" />
				~
				<input type="text" name="pcr_quantity_to" class="input_text keyToSearch" style="width: 50px;" />
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText">&nbsp;</span>
				<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" style='' />
			</div>
		</form>
	</div>
	<div id="module-table" style="margin-top: 5px;">
		<div class="opration_area">
			<div style="width: 270px; float: right;">
				<!-- SKU关系上传 -->
				<!-- 
					<input type="button" class="baseBtn uploadBtn" value="<{t}>sku_relationship_upload<{/t}>" onclick='upload();' style='float:right;display:none;'>
	        		
	        		<input type="button" class="baseBtn addBtn" value="<{t}>create<{/t}>" onclick='add();' style="float: right;margin-right: 5px;">
	        		 -->
				<input type="button" class="baseBtn addBtn platform_sku" value="<{t}>create<{/t}>" style="float: right; margin-right: 5px;">
				<!-- 
				<a class="viewPlatformUserBtn" href='javascript:;' style='float:right;margin-right:10px;line-height:25px;cursor:pointer;'><{t}>View_platform_account<{/t}></a>
				-->
			</div>
		</div>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
			<tr class="table-module-title">
				<td width="3%" class="ec-center">NO.</td>
				<td><{t}>Platform_Sales_SKU<{/t}></td>
				<!-- 平台销售SKU -->
				<td><{t}>corresponding_sku<{/t}>*<{t}>quantity<{/t}></td>
				<!-- 对应关系SKU -->
				<!--<td><{t}>purchase_price<{/t}>￥</td>-->
				<!-- 采购价￥ -->
				<td><{t}>platform_account<{/t}></td>
				<!-- 平台账号 -->
				<td><{t}>createDate<{/t}></td>
				<!-- 创建时间 -->
				<td><{t}>operate<{/t}></td>
			</tr>
			<tbody id="table-module-list-data"></tbody>
		</table>
	</div>
	<div class="pagination"></div>
</div>
<!-- 平台SKU申报信息映射 -->
<div id='product_relation_div' title='<{t}>平台SKU申报信息映射<{/t}>' class='dialog_div'></div>
<script>

$(function(){

	function autoInit(){
		$('.invoice_code').each(function(){
			var this_ = this;
			var tr = $(this).parent().parent();
			$(this).autocomplete({
				//source: "/product/product/get-by-keyword/limit/20",
				minLength: 2,
				delay:100,
				source:function(request, response) {
					//增加等待图标
					$(this_).addClass("autocomplete-loading");
					$(this_).autocomplete({delay: 100});
					var parameter = {};
					parameter["term"] = $(this_).val();
					$.ajax({
						type :"POST",
						url: "/invoice/invoice/get-by-keyword/limit/20",
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
	        	    $('.invoice_enname',tr).text(ui.item.invoice_enname);
	        	    $('.invoice_unitcharge',tr).text(ui.item.invoice_unitcharge);
	        	    $('.hs_code',tr).text(ui.item.hs_code);
	        	    $('.invoice_note',tr).text(ui.item.invoice_note);
	        	    $('.invoice_url',tr).text(ui.item.invoice_url);
				},
				search: function( event, ui ) {
	        		$('.invoice_text',tr).html('');
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

	/**
	 * sku关系映射
	 */
	$("#product_relation_div").dialog({
		autoOpen : false,
		width : 920,
		maxHeight : 480,
		modal : true,
		show : "slide",
		close : function() {
			$(this).html('');
		},
		open : function() {
		},
		buttons : [
		{
			text : '确定(Ok)',
			click : function() {
				var this_ = $(this);	
				alertTip('处理中,请稍候...');
				var param = $('#invoice_form').serialize();
				$.ajax({
					type : "POST",
					url : "/product/product-combine-relation-invoice/update-new",
					data : param,
					dataType : 'json',
					success : function(json) {
						$("#dialog-auto-alert-tip").dialog("close");
						var html = '';
						html+='<p>'+json.message+'</p>';						
						alertTip(html);
						if(json.ask){			
							this_.dialog('close');
							initData(paginationCurrentPage-1);	
						}
					}
				});			
			}
		},
		{
			text : '关闭(Close)',
			click : function() {
				$(this).dialog("close");
			}
		}]
	});
	$('.addInvoiceBtn').live('click',function(){
		var clone = $('#invoice_div .table-module-b1').eq(0).clone();	
		$(':input',clone).val('');
		$('.invoice_text',clone).text('');
		$('#products').append(clone);
		autoInit();
	});
	

	$('.delInvoiceBtn').live('click',function(){
		if($('.delInvoiceBtn').size()<=1){			
			alertTip('<{t}>最后一个申报信息不可删除<{/t}>');
			return;
		}
		var this_ = $(this);
		jConfirm('<{t}>are_you_sure<{/t}>', '<{t}>删除申报信息<{/t}>', function(r) {
    		if(r){
    			this_.parent().parent().remove();
    		}
    	});       
	});
	
	$('.platform_sku').live('click',function(){
		var user_account = $(this).attr('acc');
		var company_code = $(this).attr('comp');
		var product_sku = $(this).attr('sku');
		$.ajax({
			type: "POST",
//			async: false,
			dataType: "html",
			url: '/product/product-combine-relation-invoice/edit-relation-single',
			data: {'user_account':user_account,'company_code':company_code,'product_sku':product_sku},
			success: function (html) {
				$("#product_relation_div").html(html).dialog('open');
				//alertTip(html,800,400);
				setTimeout(function(){
					autoInit();					
				},200);
				
			}
		});
	});
	

})
</script>