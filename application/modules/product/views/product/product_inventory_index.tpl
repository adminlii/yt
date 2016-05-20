<script type="text/javascript">
var whJson = <{$whJson}>;
    EZ.url = '/product/inventory/';
    EZ.getListData = function (json) {
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            var warehouse = json.warehouse[val.warehouse_id]?json.warehouse[val.warehouse_id]:false;
            
            val.w = warehouse ? warehouse.warehouse_code+'<br/>['+warehouse.warehouse_desc+']' : '';
            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
            html += "<td class='ec-center'>" + (i++) + "</td>";
            html += "<td >"+
                		"<a href='/product/product/detail/product_id/"+val.product_id+"' target='_blank'>" + val.product_barcode + "</a><br/>"+
                		val.product_title + "<br/>" +
                		val.sale_status_title + 
                	"</td>";
            html += "<td >" + ((val.warehouse_product_barcode)?val.warehouse_product_barcode:"") + "</td>";
            //html += "<td >" + val.product_title + "</td>";
            html += "<td >" + val.w + "</td>";
            if(val.pi_onway>0){
                html += "<td ><a title='查看批次在途' href='javascript:;' class='viewOnwayBtn' wid='"+val.warehouse_id+"' pid='"+val.product_id+"'>" + val.pi_onway + "</a></td>";
            }else{
                html += "<td >" + val.pi_onway + "</td>";
            }
            //if(val.pi_pending>0){
                //html += "<td ><a title='查看批次待上架' href='javascript:;' class='viewPendingBtn' wid='"+val.warehouse_id+"' pid='"+val.product_id+"'>" + val.pi_pending + "</a></td>";
            //}else{
                html += "<td >" + val.pi_pending + "</td>";
            //}            
            html += "<td >" + val.pi_in_used+" / "+val.pi_warning_qty + "</td>";
            html += "<td >" ;
            html += val.pi_sellable+' /'+val.pi_can_sale_days+'天';
            if(val.pi_warning){
                html += '<span title="'+val.pi_warning_message+'" style="font-size:20px;font-weight:bold;padding:0 5px;color:red;">!!</span>';
            }
            html+= "</td>";
            html += "<td >" + val.pi_reserved + "</td>";
            //if(val.pi_no_stock>0){
              //  html += "<td ><a href='javascript:;' class='viewNoStockOrder' wid='"+val.warehouse_id+"' pid='"+val.product_id+"'>" + val.pi_no_stock+' / '+val.pi_no_stock_days + "天</a></td>";
            //}else{
                html += "<td >" + val.pi_no_stock+' / '+val.pi_no_stock_days + "天</td>";
            //}
            html += "<td >" + val.pi_unsellable + "</td>";//' / '+val.pi_outbound+ "</td>";
            //if(val.abnormal_count>0){
              //  html += "<td ><a href='javascript:;' class='viewabnormalCount' wid='"+val.warehouse_id+"' pid='"+val.product_id+"'>" + val.abnormal_count+ "</a></td>";
            //}else{
                //html += "<td >" + val.abnormal_count + "</td>";
            //}
            html += "<td >" + val.buyer_name + "</td>";
            html += "<td >" + val.pi_update_time + "</td>";
            html += "</tr>";
        });
        return html;
    }
    $(function(){
        $('#receiving-abnormal-dialog-div').dialog({
            autoOpen: false,
            width: 800,
            modal: true,
            show: "slide",
            buttons: {
                '关闭': function () {
                    $(this).dialog('close');
                }
            },
            close: function () {
            }
        });
        
        $('#no-stock-dialog-div').dialog({
            autoOpen: false,
            width: 800,
            modal: true,
            show: "slide",
            buttons: {
                '关闭': function () {
                    $(this).dialog('close');
                }
            },
            close: function () {
            }
        });
        
        $('#export_div').dialog({
            autoOpen: false,
            width: 400,
            modal: true,
            show: "slide",
            buttons: {
                '导出': function () {
                	//必须选择仓库
             		if ($("#warehouse_export").val().length == 0 ||$("#warehouse_export").val() == '') {
             			alertTip("请选择要导出的仓库！");
                         return;
                     }
             		$("#export_form").attr('action', '/warehouse/Inventory/export-inventory');
                    $("#export_form").submit();
                    $(this).dialog('close');
                },
                '关闭': function () {
                    $(this).dialog('close');
                }
            },
            close: function () {
            }
        });

        $('#on-way-dialog-div').dialog({
            autoOpen: false,
            width: 800,
            modal: true,
            show: "slide",
            buttons: {
                '关闭': function () {
                    $(this).dialog('close');
                }
            },
            close: function () {
            }
        });

        $(".viewabnormalCount").live('click',function(){
        	alert("s");
        	var params = {};
            params.wid = $(this).attr('wid');
            params.pid = $(this).attr('pid');

            $.ajax({
                type: "post",
                async: false,
                dataType: "json",
                url: '/warehouse/inventory/get-receiving-abnormal/',
                data: params,
                success: function (json) {
                    var html='';
                    $.each(json,function(k,val){
                        html += (k + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";

                        var warehouse = whJson[val.warehouse_id]?whJson[val.warehouse_id]:false;
                        
                        val.w = warehouse ? warehouse.warehouse_code+' ['+warehouse.warehouse_desc+']' : '';
                        
                        html += "<td >" + val.ra_code + "</td>";
                        html += "<td >" + val.receiving_code + "</td>";
                        html += "<td >" + val.po_code + "</td>";
                        html += "<td >" + val.w + "</td>";
                        html += "<td >" + val.product_barcode + "</td>";
                        html += "<td >" + val.rad_quantity + "</td>";
                        html += "<td >" + val.status_title + "</td>";
                        html += "<td >" + val.ra_add_time + "</td>";
                        html += "</tr>";
                    })
                    $('#receiving-abnormal-dialog-div .table-module-list-data').html(html);
                    $('#receiving-abnormal-dialog-div').dialog('open')
                }
            });
         });

        
        $('.viewNoStockOrder').live('click',function(){
            var params = {};
            params.wid = $(this).attr('wid');
            params.pid = $(this).attr('pid');
        	$.ajax({
                type: "post",
                async: false,
                dataType: "json",
                url: '/warehouse/inventory/get-no-stock-order/',
                data: params,
                success: function (json) {
                    var html='';
                    $.each(json,function(k,val){
                        html += (k + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";

                        var warehouse = whJson[val.warehouse_id]?whJson[val.warehouse_id]:false;
                        
                        val.w = warehouse ? warehouse.warehouse_code+' ['+warehouse.warehouse_desc+']' : '';
                        
                        html += "<td >" + val.order_code + "</td>";
                        html += "<td >" + val.status_title + "</td>";
                        html += "<td >" + val.product_barcode + "</td>";
                        html += "<td >" + val.w + "</td>";
                        html += "<td >" + val.op_quantity + "</td>";
                        html += "<td >" + val.add_time + "</td>";
                        html += "</tr>";

                    })
                    $('#no-stock-dialog-div .table-module-list-data').html(html);
                    $('#no-stock-dialog-div').dialog('open')
                }
            });


        })
        
        $('.calProductWarningQtyBtn').live('click',function(){
            alertTip('Working...');
        	$.ajax({
                type: "post",
                async: true,
                dataType: "html",
                url: '/warehouse/inventory/cal-product-warning-qty/',
                data: {},
                success: function (html) {
                    $('#dialog-auto-alert-tip').html(html);
                }
            });


        })
        
        $('.viewOnwayBtn').live('click',function(){
            var params = {};
            params.wid = $(this).attr('wid');
            params.pid = $(this).attr('pid');
            params.status=5;
        	$.ajax({
                type: "post",
                async: false,
                dataType: "json",
                url: '/product/inventory/get-on-way-detail/',
                data: params,
                success: function (json) {
                    var html='';
                    $.each(json,function(k,val){
                        html += (k + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";

                        //a.receiving_code,a.receiving_add_time,b.product_barcode,b.product_id,b.rd_receiving_qty
                        var warehouse = whJson[val.warehouse_id]?whJson[val.warehouse_id]:false;
                        
                        val.w = warehouse ? warehouse.warehouse_code+' ['+warehouse.warehouse_desc+']' : '';
                        
                        html += "<td >" + val.receiving_code + "</td>";
                        html += "<td >" + val.status_title + "</td>";
                        html += "<td >" + val.product_barcode + "</td>";
                        html += "<td >" + val.w + "</td>";
                        html += "<td >" + val.rd_receiving_qty + "</td>";
                        html += "<td >" + val.receiving_add_time + "</td>";
                        html += "</tr>";
                    })
                    $('#on-way-dialog-div .table-module-list-data').html(html);
                    $('#on-way-dialog-div').dialog('open')
                }
            });
        })

        $('.viewPendingBtn').live('click',function(){
            var params = {};
            params.wid = $(this).attr('wid');
            params.pid = $(this).attr('pid');
            params.status=6;
        	$.ajax({
                type: "post",
                async: false,
                dataType: "json",
                url: '/warehouse/inventory/get-pending-detail/',
                data: params,
                success: function (json) {
                    var html='';
                    $.each(json,function(k,val){
                        html += (k + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";

                        //a.receiving_code,a.receiving_add_time,b.product_barcode,b.product_id,b.rd_receiving_qty
                        var warehouse = whJson[val.warehouse_id]?whJson[val.warehouse_id]:false;
                        
                        val.w = warehouse ? warehouse.warehouse_code+' ['+warehouse.warehouse_desc+']' : '';
                        
                        html += "<td >" + val.receiving_code + "</td>";
                        html += "<td >" + val.status_title + "</td>";
                        html += "<td >" + val.product_barcode + "</td>";
                        html += "<td >" + val.w + "</td>";
                        html += "<td >" + val.rd_receiving_qty + "</td>";
                        html += "<td >" + val.receiving_add_time + "</td>";
                        html += "</tr>";
                    })
                    $('#on-way-dialog-div .table-module-list-data').html(html);
                    $('#on-way-dialog-div').dialog('open')
                }
            });
        })
        
    })
    $(function () {
        $("#product_barcode", "#searchForm").focus();

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

    	$("#exportProductInventory").click(function(){
        	$("#export_div").dialog("open");
    	});

    });

</script>

<style>
<!--
.sort {
	cursor: pointer;
	color: red;
}

.note {
	display: none;
}

#dialog-auto-alert-tip span {
	display: none;
}
.dialog-div{display:none;}
#warehouse_id{width:120px;}
-->

.searchFilterText{
	width:110px;
}
.export_table td{
	padding:5px;
}
</style>

<div id="receiving-abnormal-dialog-div" class='dialog-div' title='产品销毁单'>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
        <tr class="table-module-title">
            <td>处理单号</td>
            <td>收货单号</td>
            <td>采购单号</td>
            <td>仓库</td>
            <td>产品代码</td>
            <td>数量</td>
            <td>处理状态</td>
            <td width='140'>创建时间</td>
        </tr>
        <tbody class="table-module-list-data"></tbody>
    </table>
</div>

<div id="no-stock-dialog-div" class='dialog-div' title='产品缺货订单'>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
        <tr class="table-module-title">
            <td>订单号</td>
            <td>订单状态</td>
            <td>产品代码</td>
            <td>仓库</td>
            <td width='50'>数量</td>
            <td width='140'>订单创建时间</td>
        </tr>
        <tbody class="table-module-list-data"></tbody>
    </table>
</div>
    
<!-- 产品在途库存 -->
<div id="on-way-dialog-div" class='dialog-div' title='<{t}>product_onway_inventory<{/t}>'>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
        <tr class="table-module-title">
            <td>ASN</td>
            <td><{t}>status<{/t}></td>
            <td>SKU</td>
            <td><{t}>warehouse<{/t}></td>
            <td width='50'>Qty</td>
            <td width='140'><{t}>createDate<{/t}></td>
        </tr>
        <tbody class="table-module-list-data"></tbody>
    </table>
</div>
<div id="module-container">
    <div id="search-module">
        <form id="searchForm" name="searchForm">
        	<div class="pack_manager_content" style="padding: 0">
				<table width="100%" cellspacing="0" cellpadding="0" border="0" id="searchfilterArea">
					<tbody>
						<tr>
							<td>
								<div class="searchFilterText" ><{t}>sale_status<{/t}>：</div><!-- 销售状态 -->
								<div class="pack_manager">
									<input type="hidden" class="input_text keyToSearch" id="sale_status" name="sale_status" value="">
									<a onclick="searchFilterSubmit('sale_status','',this)" href="javascript:void(0)" class="current"><{t}>all<{/t}></a>
									<{foreach from=$saleStatus key=k name=o item=o}>
										<a onclick="searchFilterSubmit('sale_status','<{$k}>',this)" href="javascript:void(0)"><{$o}></a>									
									<{/foreach}>							
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
        
        	<!-- 产品代码 -->
        	<div class="search-module-condition" name="purchase_AdvancedSearch">
             	<span class="searchFilterText">
	             	<select name="barcode_type" style="width: 90px;">
	             		<option value='1'><{t}>productBarcode<{/t}></option>
	             		<option value='2'><{t}>warehouseBroductBarcode<{/t}></option>
	             	</select>：
             	</span>
             	<input type="text" name="product_barcode" id="product_barcode" class="input_text keyToSearch" placeholder='<{t}>like_search_before&after<{/t}>'/>
             </div>
             
             <!-- 仓库 -->
        	<div class="search-module-condition" name="purchase_AdvancedSearch">
             	<span class="searchFilterText"><{t}>warehouse<{/t}>：</span>
             	<select id='warehouse_id' name='warehouse_id' style="width: 150px;">
					<option value=""><{t}>pleaseSelected<{/t}></option>
					<{foreach from=$warehouse item=w name=w}>
					<option value='<{$w.warehouse_id}>'><{$w.warehouse_code}>[<{$w.warehouse_desc}>]</option>
					<{/foreach}>
				</select>	
             	<input type="hidden" value="" name='order_by' id='order_by' />
             </div>
             
             <!-- 数量类型 -->
        	<div class="search-module-condition" name="purchase_AdvancedSearch">
             	<span class="searchFilterText"><{t}>type_number<{/t}>：</span><!-- 数量类型 -->
             	<select name='qty_type'>
				    <option value=''><{t}>pleaseSelected<{/t}></option><!-- 请选择 -->
				    <option value='pi_onway'><{t}>pi_onway<{/t}></option><!-- 在途 -->
				    <option value='pi_pending'><{t}>pi_pending<{/t}></option><!-- 待上架 -->
				    <option value='pi_sellable'><{t}>pi_sellable<{/t}></option><!-- 可售 -->
				    <option value='pi_reserved'><{t}>pi_reserved<{/t}></option><!-- 待出库 -->
				    <option value='pi_warning_qty'><{t}>warning_qty<{/t}></option><!-- 预警库存 -->
				    <!-- 
				    <option value='pi_unsellable'><{t}>Unavailable<{/t}></option>
				    <option value='pi_shipped'><{t}>Shipped<{/t}></option>				    
				     -->
				</select>
				<input type="text" name="qty_from"  class="input_text keyToSearch" style='width:50px;margin:0 5px;'/>
				~<input type="text" name="qty_to" class="input_text keyToSearch"  style='width:50px;margin:0 5px;'/>
             </div>
             
             <!-- 默认采购员 -->
        	<div class="search-module-condition" name="purchase_AdvancedSearch">
             	<span class="searchFilterText"><{t}>default_purchaser<{/t}>：</span><!-- 默认采购员 -->
             	<select name='buyer_id' style='width:100px;'>
				    <option value=''><{t}>pleaseSelected<{/t}></option><!-- 请选择 -->
				    <{foreach from=$users item=o name=o}>
				    <option value='<{$o.user_id}>'><{$o.user_name}></option>				    
				    <{/foreach}>
				</select>
             </div>
             
            <div class="search-module-condition" name="purchase_AdvancedSearch">
             	<span class="searchFilterText">&nbsp;</span>
             	<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch"/>
			</div>
        </form>
    </div>
    <!-- 
    <div class="opration_area">
    	<div class = "opration_area_rit">
    		<input type="button" value="导出库存" id="exportProductInventory" class="baseBtn exportProductInventory" style="margin-left: 10px;"/>
    		<input type="button" value="手动更新预警库存" id="calProductWarningQtyBtn" class="baseBtn calProductWarningQtyBtn" style="margin-left: 10px;"/>
    	</div>
    </div>
     -->
    <div id="module-table">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
            <tr class="table-module-title">
                <td width="34" class="ec-center">NO.</td>
                <td ><{t}>product<{/t}></td><!-- 产品代码 -->
                <td ><{t}>warehouseBroductBarcode<{/t}></td><!-- 仓库产品代码 -->
                <td width='80'><{t}>warehouse<{/t}></td><!-- 仓库 -->
                <td name="pi_onway" sort="" class="sort"  width='60px'><{t}>pi_onway<{/t}></td><!-- 在途数量 -->
                <td name="pi_pending" sort="" class="sort" width='60px'><{t}>pi_pending<{/t}></td><!-- 待上架数量 -->
                <td><span  name="pi_in_used" sort="" class="sort"><{t}>pi_available<{/t}><!-- 可用数量 --></span>&nbsp;/&nbsp;<span  name="pi_warning_qty" sort="" class="sort"><{t}>warning_number<{/t}><!-- 预警数量 --></span></td>
                <td><span  name="pi_sellable" sort="" class="sort"><{t}>pi_sellable<{/t}><!-- 可销售数量 --></span>&nbsp;/&nbsp;<{t}>sellable_days<{/t}><!-- 可售天数 --></td>
                <td name="pi_reserved" sort="" class="sort" width='60px'><{t}>pi_reserved<{/t}><!-- 待出库存 --></td>
                <td><span  name="pi_no_stock" sort="" class="sort"><{t}>pi_no_stock<{/t}><!-- 缺货库存 --></span>&nbsp;/&nbsp;<{t}>days_out<{/t}><!-- 缺货天数 --></td>
                <td width='65'><span name="pi_unsellable" sort="" class="sort" ><{t}>pi_unsellable<{/t}><!-- 不良品 --></span><!-- &nbsp;/<br/><span name="pi_outbound" sort="" class="sort"><{t}><{/t}>待出库不良品</span> --></td>               
               <!-- 
                <td name="pi_unsellable" sort="" class="sort" width='66'><{t}><{/t}>待销毁库存</td>
                 -->
                <td name="buyer_id" sort="" class="sort" width='67'><{t}>default_purchaser<{/t}><!-- 默认采购员 --></td>
                <td width="80"><{t}>updateTime<{/t}><!-- 更新时间 --></td>
            </tr>
            <tbody id="table-module-list-data"></tbody>
        </table>
    </div>
    <div class="pagination"></div>
</div>
<div id = "export_div" title = "导出库存">
	<form id = "export_form" name = "export_form">
		<table class = "export_table">
			<tr>
				<td style = "text-align: right;">产品代码：</td>
				<td><input type="text" name="product_barcode" class="input_text keyToSearch"/></td>
			</tr>
			<tr>
				<td style = "text-align: right;">仓库：</td>
				<td>
					<select id='warehouse_export' name='warehouse_export' style="width: 150px;">
						<option value=""><{t}>pleaseSelected<{/t}></option>
						<{foreach from=$warehouse item=w name=w}>
						<option value='<{$w.warehouse_id}>'><{$w.warehouse_code}>[<{$w.warehouse_desc}>]</option>
						<{/foreach}>
					</select>	
				</td>
			</tr>
			<tr>
				<td style = "text-align: right;">采购员：</td>
				<td>
					<select name='buyer_id' style='width:100px;'>
				    <option value=''>请选择</option>
				    <{foreach from=$users item=o name=o}>
				    <option value='<{$o.user_id}>'><{$o.user_name}></option>				    
				    <{/foreach}>
				</select>
				</td>
			</tr>
		</table>
	</form>
</div>