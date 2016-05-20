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
		<b><{$order['two_code']}></b>
	</div>
	<div class="depotState" style="">
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="depotForm">
			<tbody>
				<tr>
					<td width="12%" class="depot_path"><b style="font-size: 14px;"><{t}>status<{/t}></b>：</td>
					<td width="20%"><b style="font-size: 14px;"><{$order['order_status_title']}></b></td>
					<td width="10%" class="depot_path"><{t}>源仓库<{/t}>：</td>
					<td width="20%"><{$order['warehouse_title']}></td>
					<td width="10%" class="depot_path"><{t}>shipping_method<{/t}>：</td>
					<td width="28%"><{$order['shipping_method_title']}></td>
				</tr>
				<tr>
					<td class="depot_path"><{t}>跟踪单号<{/t}>：</td>
					<td><{$order['shipping_method_no']}></td>
					<td width="8%" class="depot_path"><{t}>目的仓库<{/t}>：</td>
					<td width="22%"><{$order['to_warehouse_title']}></td>
					<td class="depot_path"></td>
					<td></td>
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
					<td ><{t}>product_barcode<{/t}></td>
					<td><{t}>quantity<{/t}></td>
					<td><{t}>收货数量<{/t}></td>
					<td><{t}>上架数量<{/t}></td>
					<td style="width: 250px;"><{t}>add_time<{/t}></td>
					<td style="width: 250px;"><{t}>update_time<{/t}></td>
				</tr>
				<tbody id="table-module-list-data">
					<{foreach from=$order_products item=row}>
					<tr class='table-module-b1'>
						<td class="ellipsis" title="<{$row.pl_note}>"><{$row.product_barcode}></td>
						<td><{$row.quantity}></td>
						<td><{$row.quantity_receiving}></td>
						<td><{$row.quantity_putaway}></td>
						<td><{$row.add_time}></td>
						<td><{$row.update_time}></td>
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
    })
</script>