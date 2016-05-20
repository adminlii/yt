<style>
.ellipsis {
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
	cursor: pointer;
}
</style>
<div id="content" xmlns="http://www.w3.org/1999/html">
	<div class="depotState_title">
		<img align="absmiddle" style="margin-right: 5px;" src="/images/pack/icon_depot01.gif">
		<span style="font-size: 14px; font-weight: bold;"><{t}>order_deatil<{/t}></span>&nbsp;&nbsp;&gt;&nbsp;&nbsp;
		<b><{$order['refrence_no_platform']}></b>
	</div>
	<div class="depotState" style="">
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="depotForm">
			<tbody>
				<tr>
					<td width="12%" class="depot_path"><b style="font-size: 14px;"><{t}>status<{/t}></b>：</td>
					<td width="20%"><b style="font-size: 14px;"><{$order['order_status_title']}></b></td>
					<td width="10%" class="depot_path"><{t}>warehouse_name<{/t}>：</td>
					<td width="20%"><{$order['warehouse_title']}></td>
					<td width="10%" class="depot_path"><{t}>shipping_method<{/t}>：</td>
					<td width="28%"><{$order['shipping_method_title']}></td>
				</tr>
				<tr>
					<td class="depot_path"><{t}>reference_no<{/t}>：</td>
					<td><{$order['refrence_no']}></td>
					<td width="8%" class="depot_path"><{t}>platform<{/t}>：</td>
					<td width="22%"><{$order['platform']}></td>
					<td class="depot_path"></td>
					<td></td>
				</tr>
				<tr>
					<td class="depot_path"><{t}>order_instructions<{/t}>：</td>
					<td><{$order['order_desc']}></td>
					<td width="8%" class="depot_path"></td>
					<td width="22%"></td>
					<td class="depot_path"></td>
					<td></td>
				</tr>
			</tbody>
		</table>
		<div class="clr"></div>
	</div>
	<div class="depotState" style="">
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="depotForm">
			<tbody>
				<tr>
					<td width="12%" class="depot_path"><{t}>consignee_country<{/t}>：</td>
					<td width="20%"><{$order['consignee_country_code']}>  [<{$order['consignee_country_name']}>]</td>
					<td width="10%" class="depot_path"><{t}>consignee_company<{/t}>：</td>
					<td width="20%"><{$order['consignee_company']}></td>
					<td width="10%" class="depot_path"></td>
					<td width="28%"></td>
				</tr>
				<tr>
					<td width="12%" class="depot_path"><{t}>consignee_state<{/t}>：</td>
					<td width="20%"><{$order['consignee_state']}></td>
					<td width="10%" class="depot_path"><{t}>consignee_name<{/t}>：</td>
					<td width="20%"><{$order['consignee_name']}></td>
					<td width="10%" class="depot_path"></td>
					<td width="28%"></td>
				</tr>
				<tr>
					<td width="12%" class="depot_path"><{t}>consignee_city<{/t}>：</td>
					<td width="20%"><{$order['consignee_city']}></td>
					<td width="10%" class="depot_path"><{t}>consignee_phone<{/t}>：</td>
					<td width="20%"><{$order['consignee_phone']}></td>
					<td width="10%" class="depot_path"></td>
					<td width="28%"></td>
				</tr>
				<tr>
					<td width="12%" class="depot_path"><{t}>consignee_zip<{/t}>：</td>
					<td width="20%"><{$order['consignee_postal_code']}></td>
					<td width="10%" class="depot_path"><{t}>consignee_email<{/t}>：</td>
					<td width="20%"><{$order['consignee_email']}></td>
					<td width="10%" class="depot_path"></td>
					<td width="28%"></td>
				</tr>
				<tr>
					<td width="12%" class="depot_path"><{t}>consignee_doorplate<{/t}>：</td>
					<td width="20%"><{$order['consignee_doorplate']}></td>
					<td width="10%" class="depot_path"></td>
					<td width="20%"></td>
					<td width="10%" class="depot_path"></td>
					<td width="28%"></td>
				</tr>
				<tr>
					<td width="12%" class="depot_path"><{t}>consignee_street1<{/t}>：</td>
					<td width="20%"><{$order['consignee_street1']}></td>
					<td width="10%" class="depot_path"></td>
					<td width="20%"></td>
					<td width="10%" class="depot_path"></td>
					<td width="28%"></td>
				</tr>
				<tr>
					<td width="12%" class="depot_path"><{t}>consignee_street2<{/t}>：</td>
					<td width="20%"><{$order['consignee_street2']}></td>
					<td width="10%" class="depot_path"></td>
					<td width="20%"></td>
					<td width="10%" class="depot_path"></td>
					<td width="28%"></td>
				</tr>
			</tbody>
		</table>
		<div class="clr"></div>
	</div>
	<div class="depotTab2">
		<ul>
			<li class="">
				<a href="javascript:void(0);" id='tab_order' class='tab' style="cursor: pointer"><{t}>orderProduct<{/t}></a>
			</li>
			<li class="chooseTag">
				<a href="javascript:void(0);" id='tab_log' class='tab' style="cursor: pointer"><{t}>order_log<{/t}></a>
			</li>
			<li class="chooseTag">
				<a href="javascript:void(0);" id='tab_track' class='tab' style="cursor: pointer"><{t}>track<{/t}></a>
			</li>
			<li class="chooseTag">
				<a href="javascript:void(0);" id='tab_fee' class='tab' style="cursor: pointer"><{t}>orders_expenses<{/t}></a>
			</li>
		</ul>
	</div>
	<div class="depotContent" style="">
		<div class="tabContent" id="order">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
				<tr class="table-module-title">
					<td width='10'>ID</td>
					<td ><{t}>product_barcode<{/t}></td><!--ruston0903-->
					<td width="150"><{t}>quantity<{/t}>&nbsp;&nbsp;&nbsp;<{t}>sku_quantity<{/t}>(<span id="quantity" style="color:red"></span>)</td><!--ruston0903-->
					<td style="width: 250px;"><{t}>add_time<{/t}></td>
					<td style="width: 250px;"><{t}>update_time<{/t}></td>
				</tr>
				<tbody id="table-module-list-data">
					<{foreach from=$order_products item=row key=key}>
					<tr class='table-module-b1'>
						<td><{$key+1}></td><!--ruston0903-->
						<td class="ellipsis" title="<{$row.pl_note}>"><{$row.product_barcode}></td>
						<td class="quantity"><{$row.op_quantity}></td>
						<td><{$row.op_add_time}></td>
						<td><{$row.op_update_time}></td>
					</tr>
					<{/foreach}>
				</tbody>
			</table>
		</div>
		<div class="tabContent" id="log" style="display: none;">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
				<tr class="table-module-title">
					<td style="width: 250px;"><{t}>log_user<{/t}></td>
					<td><{t}>log_content<{/t}></td>
					<td style="width: 250px;"><{t}>log_time<{/t}></td>
				</tr>
				<tbody id="table-module-list-data">
					<{foreach from=$order_log item=row}>
					<tr class='table-module-b1'>
						<td><{$row.op_username}></td>
						<td class="ellipsis" title="<{$row.log_content}>"><{$row.log_content}></td>
						<td><{$row.create_time}></td>
					</tr>
					<{/foreach}>
				</tbody>
			</table>
		</div>
		<div class="tabContent" id="track" style="display: none;">
			<div style="padding: 5px; font-weight: bold"><{t k='20'}>lastest_num_record<{/t}></div>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
				<tr class="table-module-title">
					<td><{t}>location<{/t}></td>
					<td><{t}>track_description<{/t}></td>
					<td style="width:250px;"><{t}>date<{/t}></td>
				</tr>
				<tbody>
					<{foreach from=$trackingInfo item=v}>
					<tr class='table-module-b1'>
						<td><{$v.occur_address}></td>
						<td><{$v.track_code_title}></td>
						<td><{$v.occur_date}></td>
					</tr>
					<{/foreach}>
				</tbody>
			</table>
		</div>
        <div class="tabContent" id="fee" style="display: none;">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
                <tr class="table-module-title">
                    <td><{t}>cost_type<{/t}></td>
                    <td><{t}>amount<{/t}></td>
                    <td><{t}>charging_time<{/t}></td>
                </tr>
                <tbody>
                <{foreach from=$fees item=fee}>
                    <tr class='table-module-b1'>
                        <td><{$feeType[$fee.ft_code]}></td>
                        <td><{$fee.bi_amount}> <{$fee.currency_code}></td>
                        <td><{$fee.bi_billing_date}></td>
                    </tr>
                    <{/foreach}>
                </tbody>
            </table>
        </div>
	</div>
	<script type="text/javascript">
    $(function(){
        $(".tab").click(function(){
            $(".tabContent").hide();

            $(this).parent().removeClass("chooseTag");
            $(this).parent().siblings().addClass("chooseTag");
            $("#"+$(this).attr("id").replace("tab_","")).show();
        });
        //ruston0903 订单详情添加序列号和sku总数
        math=0;
        $('.quantity').each(function(i,v){
	        math=math+parseInt(v.innerHTML)
        })
        $('#quantity').html(math)
    })
</script>