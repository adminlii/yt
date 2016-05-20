<script type="text/javascript" src="/lodop/print_label.js?v=20140314"></script>
<script type="text/javascript">
var categoryJson = <{$categoryJson}>;
var statusJson = <{$statusJson}>;
var saleStatusJson = <{$saleStatusJson}>;
EZ.url = '/product/product/';
EZ.getListData = function (json) {
    var html = '';
    var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
    var type, status, warehouse;
    $.each(json.data, function (key, val) {
        html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
        html += "<td class='ec-center'><input class='itemProduct' type='checkbox' value='"+val.product_id+"' pd_id='"+val.pd_id+"' sku='"+val.product_sku+"'></td>";
        html += "<td><img src='"+json.wms_url+"/default/system/view-product-img/id/"+val.product_id+"/xx.jpg'  width='16' height='16'  class='imgPreview'/></td>";
        html += "<td><a href='javascript:;' class='detailBtn' product_id='"+val.product_id+"'  product_sku='"+val.product_sku+"'>"+val.product_sku+"</a></td>";
        html += "<td>"+val.product_title+"</td>";
		var category = '';
        if(isEnLocale){
        	category = (categoryJson[val.pc_id]?categoryJson[val.pc_id].pc_name_en:'');
        }else{
        	category = (categoryJson[val.pc_id]?categoryJson[val.pc_id].pc_name:'');
        }
        html += "<td>"+category+"</td>";
        html += "<td>"+val.product_length+"*"+val.product_width+"*"+val.product_height+"</td>";
        html += "<td>"+val.product_weight+"</td>";
        //html += "<td>"+val.customer_code+"</td>";
        html += "<td>"+json.status[val.product_status]+' / '+saleStatusJson[val.sale_status]+"</td>";
        //html += "<td>";
        //if(val.product_status==1||val.product_status==2){
        	//html +="<a href='javascript:;' class='changeStatus' prod_id='"+val.product_id+"' status='"+val.product_status+"' product_sku='"+val.product_sku+"'>修改销售状态</a>";
        //}else{
        //	html +="<span>修改销售状态</span>";
        //}	
        //html += "<a href='javascript:;' class='countSaleFee' onclick=\"countSaleFee('"+val.product_sku+"')\"> | 建议销售金额</a>";
        /* 
    	html +="<a href='javascript:;' class='changeStatus' prod_id='"+val.product_id+"' status='"+val.product_status+"'  sale_status='"+val.sale_status+"'  product_sku='"+val.product_sku+"'>修改销售状态</a>";
        */
        //html += "</td>";
        html += "</tr>";
    });
    return html;
};

//建议销售金额
function countSaleFee(product_code){
	$("#dialogCountSaleFee").dialog("open");
	$("input[name='ProductCode']").val(product_code);
	$("#warehouseId").val('');
	$("#smId").val('');
	$("#countryId").val('');
	$("#productMargin").val('');
	$("#saleFee").html('');
}

$(function(){
	$('#code_type').change(function(){
		var op = $(this).val();
	    switch(op){
	    	case 'sku':
	    	    $('#code').attr('placeholder',$.getMessage('sys_fuzzy_srarch'));
		    	break;
	    	case 'title':
	    	    $('#code').attr('placeholder',$.getMessage('sys_fuzzy_srarch'));
		    	break;
	    	case 'barcode':
	    	    $('#code').attr('placeholder',$.getMessage('sys_fuzzy_srarch'));
	    }
	}).change();
    $(".checkAll").click(function () {
          $(this).EzCheckAll($(".itemProduct"));
      });
    $(".datepicker").datepicker({ dateFormat: "yy-mm-dd", defaultDate: -1});
    $(".datepickerTo").datepicker({ dateFormat: "yy-mm-dd", defaultDate: +1});

    $('.detailBtn').live('click',function(){
        var product_id = $(this).attr('product_id');
        var product_sku = $(this).attr('product_sku');
        var width = parent.windowWidth();
		var height = parent.windowHeight();
		//产品详情
        parent.openIframeDialog('/product/product/detail/product_id/'+product_id,width-100 ,height-80,$.getMessage('product_details'));
        //leftMenu('DevelopProduct'+product_id,'产品:'+product_sku,'/product/develop/detail/product_id/'+product_id);
    });
    var maxW = 300;//预览图片最大宽度
    $('body').append('<div id="previewDiv"></div>');
    $('.imgPreview').live('mouseover',function(e){
        var off = $(this).offset();
    	var x = off.left+20;
    	var y = off.top;
    	var tempIMG = new Image();
        tempIMG.src = $(this).attr('src');
    	tempIMG.onload = function(){
    	    var w = tempIMG.width;
    	    w = w>maxW?maxW:w;
        	var img = '<img src="'+$(this).attr('src')+'" width="'+w+'"/>'
        	$('#previewDiv').html(img).css({'position':'absolute','top':y+'px','left':x+'px','border':'1px solid #ccc'}).show();
    	};
    	
    	
    }).live('mouseout',function(e){
    	$('#previewDiv').hide();    	
    });

    $("#dialogPrint").dialog({
        autoOpen: false,
        width: 550,
        height: 'auto',
        modal: true,
        show: "slide",
        buttons: [
            {
                text: "确定(Ok)",
                click: function () {
                    //校验仓库
					if($("#print_warehouse").val() == "" || $("#print_warehouse").val().length == 0){
						$("#warehouse_message").show();
						return;
					}else{
						//参数校验
						$("#warehouse_message").hide();
						$.ajax({
	                        type: "post",
	                        async: false,
	                        dataType: "json",
	                        url: '/product/product/print-verity/',
	                        data: $("#editDataForm").serializeArray(),
	                        success: function (json) {
	                            
	                        	if (isJson(json)) {
				                    if(json.count > 0){
					                    if(json.message.length){
					                    	html = '';
					                    	$.each(json.message, function (key, val) {
					                    		html += '<span class="tip-warning-message">' + val + '</span>';				
					                    	});
					                    	alertTip(html);
					                    }
				                    }
				                }
	                        }
	                    });
	                    
	                    //$("#editDataForm").submit();
						//操作打印
	                    var product_label_arr = [];
                        $('.product_qty').each(function(){
                            var product_id = $(this).attr('product_id');
                            var qty = $(this).val();
			                product_label_arr.push(product_id+"_"+qty);
                        });

		                var param = {'session_id':'<{$session_id}>'};
		                param.paper = '70x30';
		                param.lodop = '1';
		                param.product = product_label_arr.join(',');
		                param.wahouse_id = $("#print_warehouse").val();
		                
                        $(this).dialog("close");
                        $.ajax({
						   type: "POST",
						   async: false,
						   url: "/product/product/print/",
						   data: param,
						   dataType:'script',
						   success: function(msg){
							   print_lodop(); 
							  
						   }
						});



	                    if($(".checkAll").attr('checked')){
	                        $(".checkAll").attr('checked',false);
	                    }
	                    $(".productCodes").attr('checked',false);
	                    $(this).dialog("close");
					}
                    
                	
                }
            },
            {
                text: "取消(Cancel)",
                click: function () {
                    $(this).dialog("close");
                }
            }
        ]
    });
    $("#printButton").click(function(){
        productCodes = "";
        $("#printData").html("");
        $(".itemProduct:checked").each(function(){
            productCodes+=$(this).attr('sku')+",";
            id = $(this).val();
            sku = $(this).attr('sku');
            html = "<tr><td>"+sku+"</td>" +
                    "<td><input type='text' value='1' class='input_text product_qty' style='width:100px;' product_id = '"+id+"'  name='product_"+id+"'/> </td></tr>";
            $("#printData").append(html);
        });
        if(productCodes==""){
            alert("请选择产品");
            return false;
        }
        $("[name='productCodes']").val(productCodes);
        $("#dialogPrint").dialog('open');
    });

    $("#exportBtn").click(function(){
        if(!window.confirm("如果指定了产品，则导出相应的产品\n如果未指定产品，则导出搜索条件对应的产品\n你确定该操作吗？")){
            return false;
        }
    	$('#exportForm').html('');
        if($(".itemProduct:checked").size()<1){
            var param = $('#searchForm').serialize();
            $.ajax({
                type: "post",
                async: false,
                dataType: "json",
                url: '/product/develop/get-export-pd-id/',
                data: param,
                success: function (json) {                
                   	$(json).each(function(k,v){
                         var id = v.pd_id;
                       	 var input = $('<input name="pd_id[]" type="text" value="'+id+'"/>');
                       	 $('#exportForm').append(input);
                    });
                    setTimeout(function(){$('#exportForm').submit();},500);
                }
            });
            
        	
        }else{
         	 $(".itemProduct:checked").each(function(){
               var id = $(this).attr('pd_id');
             	 var input = $('<input name="pd_id[]" type="text" value="'+id+'"/>');
             	 $('#exportForm').append(input);
           });
           setTimeout(function(){$('#exportForm').submit();},500);
        }
         
        
    });
    $('.changeStatus').live('click',function(){
        var product_id = $(this).attr('prod_id'); 
        var sku = $(this).attr('product_sku'); 
        var sale_status = $(this).attr('sale_status');
        var buttons = [
                       /*{
                           text: '平台下架',
                           click: function () {
                               if(!window.confirm('一旦下架，则产品的采购建议将清空，未审核，未付款的采购单将撤销,确认吗?')){
                                   return;
                               }
                               $(this).dialog("close");
                               $.ajax({
                                   type: "post",
                                   async: false,
                                   dataType: "json",
                                   url: '/product/develop/off-shelf/',
                                   data: {'product_id':product_id},
                                   success: function (json) {
                                       alertTip(json.message);
                                       if(json.ask){
                                       	initData(paginationCurrentPage-1);
                                       }
                                   }
                               });
                           }
                       },{
                           text: '暂停销售',
                           click: function () {
                               $(this).dialog("close");
                               $.ajax({
                                   type: "post",
                                   async: false,
                                   dataType: "json",
                                   url: '/product/develop/temp-stop-sale/',
                                   data: {'product_id':product_id},
                                   success: function (json) {
                                       alertTip(json.message);
                                       if(json.ask){
                                       	initData(paginationCurrentPage-1);
                                       }
                                   }
                               });
                           }
                       },*/                       
                       {
                           text: '确定(Ok)',
                           click: function () {
                        	   if(!window.confirm('确认修改销售状态?')){
                                   return;
                               }
                               var sale_status = $('#new_sale_status').val();
                               $(this).dialog("close");
                               $.ajax({
                                   type: "post",
                                   async: false,
                                   dataType: "json",
                                   url: '/product/develop/update-sale-status/',
                                   data: {'product_id':product_id,'sale_status':sale_status},
                                   success: function (json) {
                                       alertTip(json.message);
                                       if(json.ask){
                                       	initData(paginationCurrentPage-1);
                                       }
                                   }
                               });
                           }
                       }
                       ,{
                           text: '关闭(Close)',
                           click: function () {
                               $(this).dialog("close");
                           }
                       }
                   ];       
        var text = '平台下架：产品将不可用，采购建议将清空，未审核，未付款的采购单将撤销<br/>暂停销售：产品将暂时不可用';
        var sel = '<br/><br/><b>修改销售状态</b>&nbsp;&nbsp;<select name="sale_status" id="new_sale_status">';
        $.each(saleStatusJson,function(k,v){
            var selected = sale_status==k?'selected':'';
            sel+='<option value="'+k+'" '+selected+'>'+v+'</option>';
        })
        sel+='</select>';
        text = text+sel;
    	$('<div title="修改 '+sku+' 销售状态" id="change-product-status-tip">'+text+'</div>').dialog({
            autoOpen: true,
            width: 650,
            modal: true,
            show: "slide",
            buttons: buttons,
            close: function () {
                $(this).detach();
            }
        });
    })

    //批量更新产品属性
	$('#uploadProductBaseBtn').click(function(){ 
        openIframeDialog('/product/product/upload-product-base/',800,180,'批量更新产品属性');	
	});
    //批量更新产品质检项
	$('#uploadQualityItemBaseBtn').click(function(){ 
        openIframeDialog('/product/product/upload-quality-item-base/',800,300,'批量更新产品质检项');	
	});
    //批量更新产品包材
	$('#uploadProductPackageBtn').click(function(){ 
        openIframeDialog('/product/product/upload-product-package-base/',800,300,'批更更新产品包材');	
	});

    //建议销售价
    $("#dialogCountSaleFee").dialog({
        autoOpen: false,
        width: 550,
        height: 'auto',
        modal: true,
        show: "slide",
        buttons: [
            {
                text: "确定(Ok)",
                click: function () {
                	if(!verifyFeeParams()){
						return;
                	}else{
                		$.ajax({
                            type: "post",
                            async: false,
                            dataType: "json",
                            url: '/product/develop/count-sale-fee/',
                            data: $("#countFee").serializeArray(),
                            success: function (json) {
                            	if (!isJson(json)) {
				                    alertTip('Internal error.');
				                    return;
				                }

								html = '';
								if(json.state){
									$("#saleFee").html(json.data);
								}else{
                                    $.each(json.message, function (k, v) {
                                        html += '<span class="tip-error-message">' + v + '</span>';
                                    });
                                    alertTip(html);
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
                }
            }
        ]
    });
})

$(function () {
	$('.sort').click(function(){		
	    var name= $(this).attr('name');
	    var sort= $(this).attr('sort');
	    var nowSort = '';
	    if(sort==''||sort=='asc'){
	    	nowSort = 'desc';
	    }else{
	    	nowSort = 'asc';
	    }
		$('.sort').attr('class','sort');
		$(this).addClass(nowSort);
		$(this).attr('sort',nowSort);
		$('.sort span').text('');
		$('.asc span').text('↑');
		$('.desc span').text('↓');
	    $('#order_by').val(name+' '+nowSort);
	    
	    initData(paginationCurrentPage-1);
		
	});
});

function testRegex(object){
	var regex=/^[0-9]+\.?[0-9]*$/;
	if(regex.test(object.val())==false){
		//如果不是数字则提示客户
		object.val('');
		object.removeAttr("placeholder");
		object.attr("placeholder","请输入数字");
		object.css("background","#FF85FF");
		return false;
	}else{
		object.removeAttr("placeholder");
		object.css("background","");
	}
	return true;
}

//数据校验
function verifyFeeParams(){
	//校验仓库
	var check = true;
	if(!verifySelect($("#warehouseId"))){
		check = false;	
	}
	
	//校验运输代码
	if(!verifySelect($("#smId"))){
		check = false;
	}

	//校验国家
	if(!verifySelect($("#countryId"))){
		check = false;
	}

	//校验利润率
	if(!verifyText($("#productMargin"))){
		check = false;
	}

	if($("#productMargin").val().length > 0){
		if(!testRegex($("#productMargin"))){
			check = false;
		}
	}

	return check;
}
//校验必填文本框
function verifyText(obj){
	var verifyResult = true;
	if(obj.val() == "" || obj.val().length == 0){
		obj.attr("placeholder","不能为空");
		obj.css("background","#FF85FF");

		verifyResult = false;
	}else{
		obj.removeAttr("placeholder");
		obj.css("background","");
	}

	return verifyResult;
}

//校验必填下拉框
function verifySelect(obj){
	var verifyResult = true;
	var pay_type = obj.val();
	if(pay_type == "" || pay_type.length == 0){
		//var option = $("<option class='tip_msg' selected = 'selected' value=''>").text("不能为空");
		//option.appendTo(obj);
		obj.css("background","#FF85FF");
		verifyResult = false;
	}else{
		obj.css("background","");
	}

	return verifyResult;
}
</script>
<style>
.sort {
	cursor: pointer;
	color: red;
}
</style>
<div title="打印产品条码" id="dialogPrint" style="display: none">
	<form id="editDataForm" name="editDataForm" action="/product/product/print" method="post" target="_blank">
		<input type="hidden" value="" name="productCodes" />
		<input type="hidden" value="5x2" name="print_type">
		<table class="table-module" cellspacing="0" cellpadding="0" border="0" width="100%">
        	<tr class='table-module-b1'>
        		<td colspan="2">
        			<span class="searchFilterText" style="margin-top:7px;">仓库：</span>
                	<select class="selectCss" name="print_warehouse" id="print_warehouse" style="margin:10px;">
             				<option value="">--请选择--</option> 
			                 <{foreach from=$warehouse item=val key=key}>
				                 <option value="<{$val.warehouse_id}>">
				                   	<{$val.warehouse_code}>【<{$val.warehouse_desc}>】
				                 </option>
			                  <{/foreach}>
                	</select><span id = "warehouse_message" style = "display:none;color:red;">请选择仓库</span>
        		</td>
        	</tr>
			<tr>
				<td>产品代码</td>
				<td>数量</td>
			</tr>
			<tbody id="printData">
			</tbody>
		</table>
	</form>
</div>
<div id="module-container">
	<div id="search-module" style="border-bottom: 1px solid #E0E0E0;">
		<form id="searchForm" name="searchForm" class="submitReturnFalse" action='/product/develop/list'>
			<div class="pack_manager_content" style="padding: 0">
				<table width="100%" cellspacing="0" cellpadding="0" border="0" id="searchfilterArea">
					<tbody>
						<tr>
							<td>
								<div class="searchFilterText" style="width: 90px;"><{t}>product_status<{/t}>：</div><!-- 产品状态 -->
								<div class="pack_manager">
									<input type="hidden" class="input_text keyToSearch" id="product_status" name="product_status" value="">
									<a onclick="searchFilterSubmit('product_status','',this)" href="javascript:void(0)" class="current"><{t}>all<{/t}></a><!-- 全部 -->
									<{foreach from=$status key=k name=o item=o}>
									<a onclick="searchFilterSubmit('product_status','<{$k}>',this)" href="javascript:void(0)"><{$o}></a>									
									<{/foreach}>								
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="searchFilterText" style="width: 90px;"><{t}>sale_status<{/t}>：</div><!-- 销售状态 -->
								<div class="pack_manager">
									<input type="hidden" class="input_text keyToSearch" id="sale_status" name="sale_status" value="">
									<a onclick="searchFilterSubmit('sale_status','',this)" href="javascript:void(0)" class="current"><{t}>all<{/t}></a>
									<{foreach from=$saleStatus key=k name=o item=o}>
									<a onclick="searchFilterSubmit('sale_status','<{$k}>',this)" href="javascript:void(0)"><{$o}></a>									
									<{/foreach}>
									<!-- 
									<a onclick="searchFilterSubmit('product_status','1',this)" href="javascript:void(0)">已确认</a>
									<a onclick="searchFilterSubmit('product_status','0',this)" href="javascript:void(0)" class="">已下架</a>
									<a onclick="searchFilterSubmit('product_status','2',this)" href="javascript:void(0)">开发中</a>
									<a onclick="searchFilterSubmit('product_status','3',this)" href="javascript:void(0)">暂停销售</a>
									 -->									
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div style="padding-top: 5px" class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;">
					<select name='type' id='code_type' class="input_text" style="width: 70px;">
						<option value='sku'>SKU</option>
						<option value='title'><{t}>p_product_title<{/t}></option><!-- 产品名称 -->
						<option value='barcode'><{t}>external_barcode<{/t}></option><!-- 外部条码 -->
					</select>
				</span>
				<input type="text" name="code" id="code" class="input_text keyToSearch" />
			</div>
			<div id="search-module-baseSearch">
				<div style="padding-top: 5px" class="search-module-condition">
					<span class="searchFilterText" style="width: 90px;">&nbsp;</span>
					<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" />
					<a onclick="toAdvancedSearch()" class="toAdvancedSearch" href="javascript:void(0)"><{t}>showAdvancedSearch<{/t}></a>
				</div>
			</div>
			<div id="search-module-advancedSearch">
				<div class="search-module-condition">
					<span class="searchFilterText" style="width: 90px;"><{t}>category<{/t}>：</span><!-- 品类 -->
					<select class="input_text2 keyToSearch selectCss2" id="category_id" name="category_id">
						<option value=""><{t}>all<{/t}></option>
						<{foreach from=$category name=c item=c}>
						<option value="<{$c.pc_id}>"><{$c.pc_shortname}>[<{$c.pc_name_en}>][<{$c.pc_name}>]</option>
						<{/foreach}>
					</select>
				</div>
				<div class="search-module-condition" >
					<span class="searchFilterText" style="width: 90px;"><{t}>createDate<{/t}>：</span>
					<input type="text" name="dateFrom" id="dateFrom" class="datepicker input_text keyToSearch" />
					<{t}>To<{/t}>
					<input type="text" name="dateTo" id="dateTo" class="datepickerTo input_text keyToSearch" />
				</div>
				<div class="search-module-condition">
					<span class="searchFilterText" style="width: 90px;"><{t}>cost_price<{/t}>：</span><!-- 成本价格 -->
					<input type="text" name="priceFrom" id="priceFrom" class=" input_text keyToSearch" />
					<{t}>To<{/t}>
					<input type="text" name="priceTo" id="priceTo" class=" input_text keyToSearch" />
				</div>
				<!-- 
				<div class="search-module-condition">
					<span class="searchFilterText">安全库存：</span>
					<input type="text" name="inventoryFrom" id="inventoryFrom" class=" input_text keyToSearch" />
					<{t}>To<{/t}>
					<input type="text" name="inventoryTo" id="inventoryTo" class=" input_text keyToSearch" />
				</div>
				-->
				<div class="search-module-condition">
					<span class="searchFilterText" style="width: 90px;">&nbsp;</span>
					<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" />
			        <input type="hidden" value="" name='order_by' id='order_by' />
					<a onclick="toBaseSearch()" class="toBaseSearch" href="javascript:void(0)"><{t}>hideAdvancedSearch<{/t}></a>
				</div>
			</div>
		</form>
		<!-- 
		<input type="button" class="baseBtn" value="打印条码" id="printButton" style='float: right; margin-top: -25px;'>
		<input type="button" class="baseBtn" value="导出产品" id="exportBtn" style='float: right; margin-top: -25px; margin-right: 100px;'>
		<input type="button" class="baseBtn" value="批量更新产品属性" id="uploadProductBaseBtn" style='float: right; margin-top: -25px; margin-right: 200px;'>
		<input type="button" class="baseBtn" value="导入产品质检项" id="uploadQualityItemBaseBtn" style='float: right; margin-top: -25px;margin-right:476px;'>
		<input type="button" class="baseBtn" value="导入产品包材" id="uploadProductPackageBtn" style='float: right; margin-top: -25px;margin-right:351px; '>
		 -->
	</div>
	<div id="module-table">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
			<tr class="table-module-title">
				<td width="20" class="ec-center">
					<input type="checkbox" class="checkAll" />
				</td>
				<td><{t}>product_image<{/t}></td><!-- 产品图片 -->
				<td>SKU</td>
				<td><{t}>p_product_title<{/t}></td><!-- 产品名称 -->
				<td><{t}>category<{/t}></td><!-- 品类 -->
				<td width="200px"><{t}>product_length<{/t}>*<{t}>product_width<{/t}>*<{t}>product_height<{/t}>(CM)</td><!-- 长*宽*高 -->
				<td><{t}>product_weight<{/t}>(KG)</td><!-- 重量 -->
				<!-- 
				<td>客户代码</td>
				 -->
				<td ><span class="sort" sort="" name="product_status"><{t}>product_status<{/t}><!-- 产品状态 --><span></span></span> / <span class="sort" sort="" name="sale_status"><{t}>sale_status<{/t}><!-- 销售状态 --><span></span></span></td>
				<!-- 
				<td>操作</td>
				 -->
			</tr>
			<tbody id="table-module-list-data"></tbody>
		</table>
	</div>
	<div class="pagination"></div>
	<form action="/product/develop/export" id='exportForm' target='_blank' style='display: none;' method='POST'></form>
</div>
<div id="dialogCountSaleFee" title="建议销售金额" style="display: none;">
	<form action="" id="countFee">
		<div style = "border:1px solid #BAB7A6;border-radius: 3px 3px 3px 3px;">
			<div class="search-module-condition">
	       		<span class="searchFilterText" style = "width: 100px;">产品代码：</span>
	            <input readonly="readonly" type="text" name="ProductCode" id="searchCode" class="input_text keyToSearch" style='background:transparent;border:0px solid #ffffff;text-align: left;'/>
	        </div>
	        
	        <div class="search-module-condition">
	       		<span class="searchFilterText" style = "width: 100px;">仓库：</span>
	            <select class="selectCss" name="warehouseId" id="warehouseId" style="margin-top:7px;">
	             	<option value="">--请选择--</option> 
			        <{foreach from=$warehouse item=val key=key}>
			           <option value="<{$val.warehouse_id}>">
			           <{$val.warehouse_code}>【<{$val.warehouse_desc}>】
			           </option>
		            <{/foreach}>
	            </select>           			
	        </div>
	        
	        <div class="search-module-condition">
	       		<span class="searchFilterText" style = "width: 100px;">运输代码：</span>
	            <select class="selectCss" name="smId" id="smId" style="margin-top:7px;">
	             	<option value="">--请选择--</option> 
	                <{foreach from=$shippingMethod item=val key=key}>
		                <option value="<{$val.sm_id}>">
		                <{$val.sm_code}>【<{$val.sm_name_cn}>】
		                </option>
	                <{/foreach}>
	            </select>		
	        </div>
	        
	        <div class="search-module-condition">
	       		<span class="searchFilterText" style = "width: 100px;">国家：</span>
	            <select class="selectCss" name="countryId" id="countryId" style="margin-top:7px;">
	             	<option value="">--请选择--</option> 
	                <{foreach from=$country item=val key=key}>
		                <option value="<{$val.country_id}>">
		                <{$val.country_code}>【<{$val.country_name}>】
		                </option>
	                <{/foreach}>
	            </select>		
	        </div>
	        
	        <div class="search-module-condition">
	       		<span class="searchFilterText" style = "width: 100px;">利润率：</span>
	            <input type="text" name="productMargin" id="productMargin" class="input_text keyToSearch"/>%
	        </div>
		</div>
		<div style = "border:1px solid #BAB7A6;margin-top:8px;height:36px;padding-top: 10px;border-radius: 3px 3px 3px 3px;">
	        	<span class="searchFilterText" style = "width: 100px;color:red;">建议销售金额：</span>
	        	<span id = "saleFee" style = "color: red;"></span><span style = "color:red;">&nbsp;RMB</span>
	    </div>
	</form>
</div>
