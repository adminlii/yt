<style>
<!--
ul {
	line-height: 22px;
	max-height: 500px;
	overflow-x: hidden;
	overflow-y: auto;
}

li {
	cursor: pointer;
}

li.selected {
	background: #ccc;
}

li a {
	display: block;
	width: 100%;
}

.dialog_div {
	display: none;
}

.pagination {
	display: none;
}
-->
</style>
<style>
#table-module-list-data td {
	word-warp: break-word; /*内容将在边界内换行*/
	word-break: break-all; /*允许非亚洲语言文本行的任意字内断开*/
}
</style>
<script type="text/javascript">
<!--
var warehouseShippingMethodJson = <{$warehouseShippingMethodJson}>;
var warehouseArrJson = <{$warehouseArrJson}>;

EZ.url = '/auth/shipping-service-mapping/get-ship-bind-';
EZ.getListData = function (json) {
    var html = '';
    var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
    $.each(json.data, function (key, val) {
        html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
        html += "<td >" + val.platform + "</td>";
        html += "<td >" + val.platform_shipping_service + "</td>";
        html += "<td >" + warehouseArrJson[val.warehouse_id].warehouse_code+'['+warehouseArrJson[val.warehouse_id].warehouse_desc+']' + "</td>";
        html += "<td >" + val.warehouse_shipping_service + "</td>";
        html += "<td >" + val.platform_shipping_mark + "</td>";
        html += "<td >" + val.add_time + "</td>";
        html += "<td >" + val.update_time + "</td>";
        html += "<td>";
        html += "<a href='javascript:;' mapping_id='"+val.mapping_id+"' class='delBtn'>" + EZ.del + "</a>";
        html += "</td>";
        html += "</tr>";
    });
    return html;
}

$(function(){
	
}); 

$(function(){
	//shipping_service_mapping();
	$('.platform_shipping_service li').live('click',function(){
	    $(this).siblings('li').removeClass('selected');
	    $(this).addClass('selected');
    });
	$('.warehouse_shipping_service li').live('click',function(){
	    $(this).siblings('li').removeClass('selected');
	    $(this).addClass('selected');
    });
	$('.warehouse li').live('click',function(){
	    $(this).siblings('li').removeClass('selected');
	    $(this).addClass('selected');
	    var warehouse_id = $(this).attr('warehouse_id');
	    var html = '';
	    $.each(warehouseShippingMethodJson[warehouse_id],function(k,v){
	        html+='<li warehouse_shipping_service="'+v.sm_code+'">'+v.sm_code+'['+v.sm_name_cn+']'+'</li>';
		});
		$('.warehouse_shipping_service').html(html);
    });
    $('.bindBtn').click(function(){

    })

    $('.delBtn').live('click',function(){
        var mapping_id = $(this).attr('mapping_id');
        if(!window.confirm('确定删除绑定关系? ')){
            return;
        }
        $.ajax({
            type: "POST",
            async: false,
            dataType: "json",
            url: "/auth/shipping-service-mapping/del-ship-bind/",
            data: {'mapping_id':mapping_id},
            success: function (json) {
                var html = json.message;  
                if(json.ask){
                	initData(0);
                }             
                alertTip(html);
            }
        });
    });

    $('.shipping_service_mapping li').live('mouseover',function(){
        $(this).addClass('selected');
    });

    $('.shipping_service_mapping li').live('mouseout',function(){
        $(this).removeClass('selected');
    });

    $('#edit-dialog').dialog({
        autoOpen: false,
        width: 1024,
        modal: true,
        show: "slide",
        buttons: [
            {
                text: '绑定',
                click: function () {
                    var this_ = $(this);

                    if($('.platform_shipping_service .selected').size()!=1){
                        alertTip('请选择ebay运输方式');
                    	return false;
                    }
                    if($('.warehouse .selected').size()!=1){
                        alertTip('请选择仓库');
                    	return false;
                    }
                    if($('.warehouse_shipping_service .selected').size()!=1){
                        alertTip('请选择仓库运输方式');
                    	return false;
                    }
                    var platform_shipping_service = $('.platform_shipping_service .selected').attr('platform_shipping_service');
                    var warehouse_shipping_service = $('.warehouse_shipping_service .selected').attr('warehouse_shipping_service');
                    var warehouse_shipping_service_title = $('.warehouse_shipping_service .selected').html();
                    var warehouse_id = $('.warehouse .selected').attr('warehouse_id');
                    var warehouse_name = $('.warehouse .selected').html();
                    var param = {};
                    param.platform_shipping_service = platform_shipping_service;
                    param.warehouse_shipping_service = warehouse_shipping_service;
                    param.warehouse_id = warehouse_id;

                    if(!window.confirm('确定ebay运输方式 '+platform_shipping_service+'绑定 仓库'+warehouse_name+' 对应运输方式 '+warehouse_shipping_service_title)){
                        return;
                    }
                    $.ajax({
                        type: "POST",
                        async: false,
                        dataType: "json",
                        url: "/auth/shipping-service-mapping/ship-bind/",
                        data: param,
                        success: function (json) {
                            var html = json.message;  
                            if(json.ask){
                                this_.dialog("close");
                                initData(0);
                            }             
                            alertTip(html);
                        }
                    });
                    
                }
            },
            {
                text: '关闭',
                click: function () {
                    $(this).dialog("close");
                }
            }
        ],
        close: function () {
            
        },
        open:function(){
            $('#edit-dialog li').removeClass('selected');
            $('.warehouse_shipping_service').html('');
        }
    });

    $('#createBtn').click(function(){
    	$('#edit-dialog').dialog('open');
    });
})

//-->
</script>
<div id="module-container">
	<div style="display: none;" id="edit-dialog" title='添加ebay运输方式绑定仓库运输方式'>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td width='300'>ebay运输方式</td>
					<td width='300'>仓库</td>
					<td>仓库运输方式</td>
				</tr>
			</tbody>
			<tbody>
				<tr class="table-module-b1">
					<td valign='top'>
						<ul style='width: 300px; float: left; border: 1px solid #ccc;' class='platform_shipping_service'>
							<{foreach from=$smps name=o item=o}>
							<li platform_shipping_service='<{$o.shipping_method_code}>'><{$o.shipping_method_code}></li>
							<{/foreach}>
						</ul>
					</td>
					<td valign='top'>
						<ul style='width: 300px; float: left; border: 1px solid #ccc;' class='warehouse'>
							<{foreach from=$warehouseArr name=o item=o}>
							<li warehouse_id='<{$o.warehouse_id}>'><{$o.warehouse_code}>[<{$o.warehouse_desc}>]</li>
							<{/foreach}>
						</ul>
					</td>
					<td valign='top'>
						<ul style='width: 300px; float: left; border: 1px solid #ccc;' class='warehouse_shipping_service'>
						</ul>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="search-module">
		<form class="submitReturnFalse" name="searchForm" id="searchForm">
			<div style="padding: 0">
				ebay运输方式：
				<input type="text" class="input_text keyToSearch" id="E2" name="E2">
				&nbsp;&nbsp;
				<input type="button" class="baseBtn submitToSearch" value="搜索">
				<input type="button" class="baseBtn" value="添加" id="createBtn">
			</div>
		</form>
	</div>
	<div id="module-table">
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td>平台</td>
					<td>ebay运输方式</td>
					<td>仓库</td>
					<td>仓库运输方式</td>
					<td>标记发货运输方式</td>
					<td>新增时间</td>
					<td>修改时间</td>
					<td>操作</td>
				</tr>
			</tbody>
			<tbody id="table-module-list-data">
			</tbody>
		</table>
	</div>
	<div class="pagination"></div>
</div>