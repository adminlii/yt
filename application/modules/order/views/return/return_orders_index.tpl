<style type="text/css">
.searchFilterText {
	width: 90px;
}

.Tab .current {
	font-weight: bold;
	color: #000;
}
.opBtn {
    margin: 5px 10px 5px 0;
}
</style>
<script type="text/javascript">
    EZ.url = '/order/return/';
    var warehouseAllJson =<{$warehouseJson}>;
    var asnStatusActionJson =<{$asnStatusActionJson}>;
    var roIds = '0';
    EZ.getListData = function (json) {
    	$('.checkAll').attr('checked',false);
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            roIds += ',' + val.E0;
            html += "<tr class='table-module-b2'>";
            html += "<td class='ec-center'><input class='returnOrderCodes' type='checkbox' name='returnOrderCodes[]'id='returnOrderCodes[]' value='" + val.E18 + "'></td>";
            html += "<td>&nbsp;&nbsp;序&nbsp;&nbsp;号：" + (i++);
            html += "<br>退件号：<a href='javascript:;' class='viewDetail' ref_id='"+val.E18+"'>" + val.E18+"</a>";
            //html += "<br>订单号：<a href=\"javascript:void(0)\" " +
                    //"onclick='leftMenu(\"" + val.E2 + "\",\"订单:" + val.E2 + "\",\"/order/order-list/detail/paramId/" + val.order_id + "\")'>" + val.E2 + "</a>";

            html += "<br>订单号：" + val.E2 + "";            
            
            html += "</td>";
            html += "<td>" + (warehouseAllJson[val.E3] ? warehouseAllJson[val.E3] : '') + "</td>";
            html += "<td>" + (json.status[val.E7] ? json.status[val.E7] : '') + "</td>";
            html += "<td>";
            html +="退件类型：" + (json.createType[val.E10] ? json.createType[val.E10] : '')+'<br/>';
            html +="处理方式：" + (json.proType[val.E9] ? json.proType[val.E9] : '')+'<br/>';
            html += "</td>";
            html += "<td>" + (val.E11 == null ? '' : val.E11) + "</td>";
            html += "<td>创建：" + val.E13 + "<br>预到：" + val.E6 + "<br>确认：" + val.E14 + "<br>更新：" + val.E15 + "</td>";
            html += "</tr>";

            html += "<tr><td style='background-color:#fff'></td><td colspan='7' style='border:0;padding:0;'>";
            html += '<table width="100%" class="settings" cellspacing="0" cellpadding="0" border="0" id="returnOrderTr_' + val.E0 + '">';
            html += '<tr class="table-module-head">';
            html += '<td>产品代码</td>';
            html += '<td>数量</td>';
            html += '<td>处理指令</td>';
            html += '<td>增值服务</td>';
            html += '</tr></table>';
            html += "</td></tr>";
        });
        getReturnOrdersItems(roIds);
        return html;
    }
    //getItems
    function getReturnOrdersItems(ids) {
        var html = '';
        $.ajax({
            type: "POST",
            url: "/order/return/get-items",
            data: {roIds: ids},
            dataType: 'json',
            success: function (json) {
                if (json.state) {
                    $.each(json.data, function (k, v) {
                        if ($("#returnOrderTr_" + v.ro_id)) {
                            html = (k + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
                            html += '<td>' + v.product_barcode + '</td>';
                            html += '<td>' + v.rop_quantity + '</td>';
                            html += '<td>' + (json.instruction[v.exception_process_instruction] == null ? '' : json.instruction[v.exception_process_instruction]) + '</td>';
                            html += '<td>' + (json.valueAddedType[v.value_added_type] == null ? '' : json.valueAddedType[v.value_added_type].vat_name_cn) + '</td>';
                            html += '</tr>';
                            $("#returnOrderTr_" + v.ro_id).append(html);
                        }
                    });
                }
            }
        });
    }

    $(function () {
        $('.viewDetail').live('click',function(){
            var param = {};
            var code = $(this).attr('ref_id');
            param.code = code;
        	$.ajax({
                type: "post",
                dataType: "json",
                url: "/order/return/view-op-node",
                data: param,
                success: function (json) {
                    var html = '';
                    if(json.ask){
                        $.each(json.data,function(k,v){
                            html+='<p>['+v.roon_add_time+']'+v.ot_code+'</p>';
                        })
                    }else{
                        html+=""+json.message;
                    }
                    alertTip(html,500,500);
                }
            });
        })
        $("#E1").focus();
        $(".checkAll").click(function () {
            $(this).EzCheckAll($(".returnOrderCodes"));
        });
        $(".datepicker").datepicker({ dateFormat: "yy-mm-dd", defaultDate: -1});
        $(".datepickerTo").datepicker({ dateFormat: "yy-mm-dd", defaultDate: +1});

        /**
         * 状态点击处理
         */
        $(".statusTag").click(function(){
			var re_status = $(this).attr("status");
			$("#E7").val(re_status);
			$(".statusTag").removeClass("current");
			$(".indexTag" + re_status).addClass("current");
			$('.opration_area_wrap .opDiv').html('');
			$(asnStatusActionJson[re_status]).each(function(k,v){
			    $('.opration_area_wrap .opDiv').append(v);
			})
			$('.submitToSearch:visible').click();
        });
        $('.asnDiscardBtn').live('click',function(){
        	if ($(".returnOrderCodes:checked").size() < 1) {
                alertTip('<span class="tip-error-message"><{t}>pls_select_receiving_code<{/t}></span>');
                return false;
            }
        	jConfirm('<{t}>are_you_sure<{/t}>', '<{t}>return_order_discard<{/t}>', function(r) {
        		if(r){
                    var codeArr = [];
                    $(".returnOrderCodes:checked").each(function(){
                        var code = $(this).val();
                        codeArr.push(code);
                    })
                    var param = {'code':codeArr};
                	$.ajax({
                        type: "post",
                        dataType: "json",
                        url: "/order/return/discard",
                        data: param,
                        success: function (json) {
                            var html = '';
                            $.each(json,function(k,v){
                                html+='<p>'+v.message+'</p>';
                            })
                            alertTip(html,800);
                            initData(paginationCurrentPage-1);
                        }
                    });
        		}
        	});
           
        });

        $('.asnVerifyBtn').live('click',function(){
        	if ($(".returnOrderCodes:checked").size() < 1) {
                alertTip('<span class="tip-error-message"><{t}>pls_select_receiving_code<{/t}></span>');
                return false;
            }
        	jConfirm('<{t}>are_you_sure<{/t}>', '<{t}>return_order_verify<{/t}>', function(r) {
        		if(r){
                    var codeArr = [];
                    $(".returnOrderCodes:checked").each(function(){
                        var code = $(this).val();
                        codeArr.push(code);
                    })
                    var param = {'code':codeArr};
                	$.ajax({
                        type: "post",
                        dataType: "json",
                        url: "/order/return/verify",
                        data: param,
                        success: function (json) {
                            var html = '';
                            $.each(json,function(k,v){
                                html+='<p>'+v.message+'</p>';
                            })
                            alertTip(html,800);
                            initData(paginationCurrentPage-1);
                        }
                    });
        		}
        	});
           
        })
    });
</script>

<div id="module-container">
	<div id="search-module">
		<form id="searchForm" name="searchForm" class="submitReturnFalse">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="searchfilterArea" id="searchfilterArea">
				<tbody>
					<tr>
						<td>
							<div class="searchFilterText" style="width: 90px;">处理方式：</div>
							<div class="pack_manager">
								<input type="hidden" class="input_text keyToSearch" id="E9" name="E9">
								<a onclick="searchFilterSubmit('E9','',this)" href="javascript:void(0)">全部</a>
								<{foreach from=$proType name=o item=o key=k}>
								<a onclick="searchFilterSubmit('E9','<{$k}>',this)" href="javascript:void(0)"><{$o}></a>
								<{/foreach}>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="searchFilterText" style="width: 90px;">退件类型：</div>
							<div class="pack_manager">
								<input type="hidden" class="input_text keyToSearch" id="E10" name="E10">
								<a onclick="searchFilterSubmit('E10','',this)" href="javascript:void(0)">全部</a>
								<{foreach from=$createType name=o item=o key=k}>
								<a onclick="searchFilterSubmit('E10','<{$k}>',this)" href="javascript:void(0)"><{$o}></a>
								<{/foreach}>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<div id="search-module-baseSearch">
				<div class="search-module-condition">
					<span class="searchFilterText">收货仓库：</span>
					<{ec name='E3' default='Y' search='Y' class='selectCss2'}>userWarehouse<{/ec}>
				</div>
				<div class="search-module-condition">
					<span class="searchFilterText">订单号：</span>
					<input type="text" name="E2" id="E2" class="input_text keyToSearch" />
				</div>
				<div class="search-module-condition">
					<span class="searchFilterText">退件单号：</span>
					<input type="text" name="E18" id="E18" class="input_text keyToSearch" />
				</div>
				<div class="search-module-condition">
					<span class="searchFilterText"><{t}>createDate<{/t}>：</span>
					<input type="text" name="dateFor" id="dateFor" class="datepicker input_text keyToSearch" />
					<{t}>To<{/t}>
					<input type="text" name="dateTo" id="dateTo" class="datepickerTo input_text keyToSearch" />
				</div>
				<div class="search-module-condition">
					<span class="searchFilterText"></span>
					<input type="hidden" class="input_text keyToSearch" id="E7" name="E7">
					<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" />
				</div>
			</div>
		</form>
	</div>
	<div class="clone">
		<div class="Tab">
			<ul>
				<li class="">
				    <a href="javascript:void (0)" class="indexTag statusTag chooseTag" status="" data-id=""><{t}>all<{/t}></a>
				</li>
				<{foreach from=$statusArr name=o item=o key=k}>
				<li class="">
					<a href="javascript:void (0)" class="indexTag<{$k}> statusTag chooseTag" status="<{$k}>" data-id=""> <{$o}> </a>
				</li>
				<{/foreach}>
			</ul>
		</div>
	</div>
	<div id="module-table">
		<div class="opration_area_wrap" style="">
			<div class='opDiv'>
			
			</div>
		</div>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
			<tr class="table-module-title">
				<td width="3%" class="ec-center">
					<input type="checkbox" class="checkAll" value="">
				</td>
				<td width="220">序号/订单号</td>
				<td>仓库</td>
				<td>状态</td>
				<td>类型</td>
				<td>描述</td>
				<td width="180">时间</td>
			</tr>
			<tbody id="table-module-list-data"></tbody>
		</table>
	</div>
	<div class="pagination"></div>
</div>
