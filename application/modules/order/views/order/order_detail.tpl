<link type="text/css" rel="stylesheet" href="/css/public/layout.css" />
<style>
<!--
#module-container .guild h2 {
	cursor: pointer;
	background: none repeat scroll 0% 0% transparent;
}

#module-container .guild h2.act {
	cursor: pointer;
	background: none repeat scroll 0% 0% #fff;
}

#search_more_div {
	display: none;
}

#search_more_div div {
	padding-top: 8px;
}

.input_text {
	width: 100px;
}

.fast_link {
	padding: 8px 10px;
}

.fast_link a {
	margin: 5px 8px 5px 0;
}

.order_detail {
	width: 100%;
	border-collapse: collapse;
}

.order_detail td {
	border: 1px solid #ccc;
}

#opDiv .baseBtn {
	margin-right: 10px;
}

.table-module th {
	border-bottom: 1px solid #FFFFFF;
	border-right: 1px solid #FFFFFF;
	line-height: 20px;
	padding: 5px;
}

.table-module {
	border-collapse: collapse;
}

.table-module-list-data td {
	text-align: left;
	border: 1px solid #ccc;
}

.table-module-list-data th {
	text-align: right;
	border: 1px solid #ccc;
}

.input_text {
	display: none;
}

.orderProductSubmitBtn,.orderProductSubmitToVerifyBtn,.orderSubmitBtn {
	display: none
}

.btnRight {
	float: right;
	margin-right: 5px;
}

.log_list {
	list-style-type: decimal;
	padding-left: 20px;
}

.disabled {
	background: #dddddd;
}

.depotState h2 {
	display: none;
}

.tabContent {
	padding: 5px 10px;
}

.table-module {
	background: none repeat scroll 0 0 #FFFFFF;
}

h3 {
	line-height: 35px;
}
-->
</style>
<script type="text/javascript"> 
</script>
<div id="module-container" style='padding: 0px 20px;'>
	<div id="module-table">
		<h3>1、<{t}>基本信息<{/t}> <{$order.shipper_hawbcode}></h3>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
			<tbody class="table-module-list-data orderInfo">
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><{t}>客户单号<{/t}></th>
					<td width='150'><{$order.refer_hawbcode}></td>
					<th width='150'><{t}>服务商单号<{/t}></th>
					<td><{$order.server_hawbcode}></td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><{t}>买家ID<{/t}></th>
					<td><{$order.buyer_id}></td>
					<th><{t}>运输方式<{/t}></th>
					<td><{$order.product_code}></td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><{t}>计费重<{/t}></th>
					<td><{$order.order_weight}>(KG)</td>
					<th><{t}>货物状态<{/t}></th>
					<td><{$order.order_status}></td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><{t}>件数<{/t}></th>
					<td><{$order.order_pieces}></td>
					<th><{t}>附加服务<{/t}></th>
					<td><{$extservice}></td>
				</tr>
				
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><{t}>体积<{/t}></th>
					<td><{$order.length}>CM*<{$order.width}>CM*<{$order.height}>CM</td>
					
					<td  colspan='2'></td>
				</tr>
				
			</tbody>
		</table>
		<h3>3、<{t}>收发件人信息<{/t}></h3>
		<table width="45%" cellspacing="0" cellpadding="0" border="0" class="table-module" style='float:left;'>
		    <tbody>
				<tr class="table-module-title">
					<td colspan='2' align='center'><{t}>发件人信息<{/t}></td>
				</tr>
			</tbody>
			<tbody class="table-module-list-data orderInfo">
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><{t}>公司名称<{/t}></th>
					<td><{$shipperConsignee.shipper_company}></td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><{t}>姓名<{/t}></th>
					<td><{$shipperConsignee.shipper_name}></td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><{t}>国家<{/t}></th>
					<td><{$shipperConsignee.shipper_countrycode}></td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><{t}>省/州<{/t}></th>
					<td><{$shipperConsignee.shipper_province}></td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><{t}>城市<{/t}></th>
					<td><{$shipperConsignee.shipper_city}></td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><{t}>邮编<{/t}></th>
					<td><{$shipperConsignee.shipper_postcode}></td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><{t}>地址<{/t}></th>
					<td><{$shipperConsignee.shipper_street}></td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><{t}>联系电话<{/t}></th>
					<td><{$shipperConsignee.shipper_telephone}></td>
				</tr>
				<!-- 
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'>&nbsp;</th>
					<td style='text-align:right;'><a href='javascript:;' style='padding:0 10px;'><{t}>展开更多<{/t}></a></td>
				</tr>
				 -->
			</tbody>
		</table>		
		<table width="45%" cellspacing="0" cellpadding="0" border="0" class="table-module" style='float:left;margin-left:5%;'>
		    <tbody>
				<tr class="table-module-title">
					<td colspan='2' align='center'><{t}>收件人信息<{/t}></td>
				</tr>
			</tbody>
			<tbody class="table-module-list-data orderInfo">
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><{t}>公司名称<{/t}></th>
					<td><{$shipperConsignee.consignee_company}></td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><{t}>姓名<{/t}></th>
					<td><{$shipperConsignee.consignee_name}></td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><{t}>国家<{/t}></th>
					<td><{$shipperConsignee.consignee_countrycode}></td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><{t}>省/州<{/t}></th>
					<td><{$shipperConsignee.consignee_province}></td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><{t}>城市<{/t}></th>
					<td><{$shipperConsignee.consignee_city}></td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><{t}>邮编<{/t}></th>
					<td><{$shipperConsignee.consignee_postcode}></td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><{t}>地址1<{/t}></th>
					<td><{$shipperConsignee.consignee_street}></td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><{t}>地址2<{/t}></th>
					<td><{$shipperConsignee.consignee_street2}></td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><{t}>地址3<{/t}></th>
					<td><{$shipperConsignee.consignee_street3}></td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><{t}>联系电话<{/t}></th>
					<td><{$shipperConsignee.consignee_telephone}></td>
				</tr>
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'><{t}>联系手机<{/t}></th>
					<td><{$shipperConsignee.consignee_mobile}></td>
				</tr>
				<!-- 
				<tr class="table-module-b1" style='height: 35px;'>
					<th width='150'>&nbsp;</th>
					<td style='text-align:right;'><a href='javascript:;' style='padding:0 10px;'><{t}>展开更多<{/t}></a></td>
				</tr>
				 -->
			</tbody>
		</table>
		
		<h3 style='clear:both;'>3、<{t}>海关申报信息<{/t}></h3>
		<table cellspacing="0" cellpadding="0" width="100%" border="0" class="table-module">
				<tbody>
					<tr class="table-module-title">
						<td width="180"><{t}>英文品名<{/t}></td>
						<td width="180"><{t}>中文品名<{/t}></td>
						<td width="40"><{t}>数量<{/t}></td>
						<td width="60"><{t}>单位<{/t}></td>
						<td width="60"><{t}>单价<{/t}>($)</td>
						<td width="80"><{t}>总价<{/t}></td>
						<td width="80"><{t}>重量<{/t}>(kg)</td>
						<td width="80"><{t}>总重<{/t}></td>
						<td width="120"><{t}>SKU<{/t}></td>
						<td width="120"><{t}>HSCODE<{/t}></td>
						<td width="120"><{t}>配货信息<{/t}></td>
						<td><{t}>销售地址<{/t}></td>
					</tr>
				</tbody>
				<tbody id="products" class="table-module-list-data">
					<{foreach from=$invoice name=i item=i}>
					<tr class="table-module-b1">
						<td>
							<{$i.invoice_enname}>
						</td>
						<td>
							<{$i.invoice_cnname}>
						</td>
						<td>
							<{$i.invoice_quantity}>
						</td>
						<td>
						    <{$i.unit_code}>							 
						</td>
						<td>
							<{$i.invoice_unitcharge}>
						</td>
						<td class='total'><{$i.invoice_totalcharge}></td>
						<td>
							<{$i.invoice_weight}>
						</td>
						<td class='total'><{$i.invoice_totalWeight}></td>
						
						<td>
							<{$i.sku}>
						</td>
						<td>
							<{$i.hs_code}>
						</td>
						<td>
							<{$i.invoice_note}>
						</td>
						<td><{$i.invoice_url}>
						</td>
						
					</tr>
					<{/foreach}>
				</tbody>
			</table>
		<h3>4、<{t}>日志信息<{/t}></h3>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
		    <tbody>
				<tr class="table-module-title">
					<td width='150'><{t}>日期<{/t}></td>
					<td><{t}>日志<{/t}></td>
					<td width='150'><{t}>操作人<{/t}></td>
				</tr>
			</tbody>
			<tbody class="table-module-list-data orderInfo">				
				<{foreach from=$logArr name=i item=i}>
				<tr class="table-module-b1" style='height: 35px;'>
					<td><{$i.create_time}></td>
					<td><{$i.log_content}></td>
					<td><{$i.user_name}></td>
				</tr>
				<{/foreach}>
				
				
			</tbody>
		</table>
	</div>
</div>
