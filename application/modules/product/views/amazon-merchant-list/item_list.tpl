<script type="text/javascript" src="/js/jquery-ui-timepicker-addon.js" ></script>
<script type="text/javascript">
<{include file='product/views/amazon-merchant-list/item_list.js'}>
</script>
<style>
<!--
#listForm .supply_qty {
	display: none;
}

#module-table .editForm .var_supply_qty .supply_qty {
	display: inline;
}

#module-table .editForm .var_supply_qty span {
	display: none;
}

.batchEditSubmitBtn,.batchEditQtySetBtn {
	display: none;
}

#listForm .table-module-b2 {
	background: none repeat scroll 0 0 #CCCCFF
}

.dialog_div {
	display: none;
}

.dropdown_show .not_first {
	display: block;
}

.dropdown_hide .not_first {
	display: none;
}

#listForm .getEbayItem {
	display: none;
}

#listForm .viewEbayItem {
	display: none;
}

#update_supply_qty_form #supply_qty_set_div .disableSupplyBtn {
	color: #0090E1;
}

.hidden {
	display: none;
}

.quantity {
	width: 70px;
}
.EndDate,.StartDate{width:120px;background-color:#fff;}
.StandardPrice,.SalePrice{width:50px;}
.supply_warehouse{cursor:pointer;color:#0090e1;}
-->
</style>
<div id="module-container" style=''>
	<div id="search-module">
		<form class="submitReturnFalse" name="searchForm" id="searchForm">
			<table width="100%" cellspacing="0" cellpadding="0" border="0" id="searchfilterArea1" class="searchfilterArea">
				<tbody>
					<tr>
						<td>
							<div class="searchFilterText" style="width: 125px;"><{t}>sale_status<{/t}>：</div>
							<!-- 销售状态 -->
							<div class="pack_manager">
								<input type="hidden" class="input_text keyToSearch" id="item_status" name="item_status" value="">
								<a href="javascript:void(0)" class=' current item_status' status=''><{t}>all<{/t}></a>
								<a href="javascript:void(0)" class=' item_status' status='on_sale'><{t}>on_sale<{/t}></a>
								<!-- 销售中 -->
								<a href="javascript:void(0)" class='item_status' status='stop_sale'><{t}>stop_sale<{/t}></a>
								<!-- 已下架 -->
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="searchFilterText" style="width: 125px;"><{t}>fulfillment_type<{/t}>：</div>
							<!-- 销售状态 -->
							<div class="pack_manager">
								<input type="hidden" class="input_text keyToSearch" name="fulfillment_type" value="">
								<a href="javascript:void(0)" class=' current fulfillment_type' onclick="searchFilterSubmit('fulfillment_type','',this)"><{t}>all<{/t}></a>
								<a class="link_E6" onclick="searchFilterSubmit('fulfillment_type','FBA',this)" href="javascript:void(0)">FBA</a>
								<a class="link_E6" onclick="searchFilterSubmit('fulfillment_type','MERCHANT',this)" href="javascript:void(0)">MERCHANT</a>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<input type="hidden" value="" id="filterActionId" />
			<div class="search-module-condition">
				<span style="width: 125px;" class="searchFilterText"><{t}>user_account<{/t}>：</span>
				<!-- ebay账号 -->
				<select id="user_account" name="user_account">
					<option value=''><{t}>all<{/t}></option>
					<{foreach from=$user_account_arr name=ob item=ob}>
					<option value='<{$ob.user_account}>'><{$ob.platform_user_name}></option>
					<{/foreach}>
				</select>
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 125px;"><{t}>product<{/t}>：</span>
				<!-- 产品 -->
				<select name="type" class='input_text_select' id='type'>
					<option value="listing_id">Amazon ListingId</option>
					<option value="product_id">Amazon ProductID</option>
					<option value="asin1">Amazon Asin1</option>
					<option value="asin2">Amazon Asin2</option>
					<option value="asin3">Amazon Asin3</option>
					<option value="fulfillment_channel">Amazon FulfillmentChannel</option>
					<option value="seller_sku">Amazon SellerSKU</option>
					<option value="seller_sku_arr">Amazon SellerSKU Batch</option>
				</select>
				<input type="text" class="input_text keyToSearch" id="code" name="code" placeholder='' style='width: 250px;' />
			</div>
			<div class="search-module-condition" style='display: none;'>
				<span class="searchFilterText" style="width: 125px;">同步状态：</span>
				<select name="sync_status" class='input_text_select' id='sync_status'>
					<option value=""><{t}>all<{/t}></option>
					<option value="0">0</option>
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
				</select>
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 125px;"><{t}>salable<{/t}>：</span>
				<!-- 可售数 -->
				<input type="text" class="input_text keyToSearch" id="sell_qty_from" name="sell_qty_from" style='width: 50px;' placeholder='<{t}>integer<{/t}>'>
				<!-- 整数 -->
				~
				<input type="text" class="input_text keyToSearch" id="sell_qty_to" name="sell_qty_to" style='width: 50px;' placeholder='<{t}>integer<{/t}>'>
			</div>
			<div class="search-module-condition">
				<span style="width: 125px;" class="searchFilterText">&nbsp;</span>
				<input type="button" class="baseBtn submitToSearch" value="<{t}>search<{/t}>">
			</div>
		</form>
	</div>
	<div id="module-table" style='overflow: auto;'>
		<div class="opration_area0">
			<div class="opration_area">
				<div class="opDiv">
					<!-- 批量改价 -->
					<input type="button" value="<{t}>product_edit_price_batch<{/t}>" class="batchEditPriceBtn baseBtn">
					<!-- 批量编辑 -->
					<input type="button" value="<{t}>product_replenishment_settings<{/t}>" class="batchEditBtn baseBtn">				
					
					<!-- 补货黑名单 -->
					<input type="button" value="<{t}>replenishment_blacklist<{/t}>" class="blackListBtn baseBtn" style='float: right;'>
					<!-- 补货日志 -->
					<input type="button" value="<{t}>replenishment_log<{/t}>" class="supLogBtn baseBtn" style='float: right; display: none; margin: 0 5px 0 0;'>
					<!-- requestReport -->
					<input type="button" class="requestReportBtn baseBtn" style='display:none;' value='RequestReport'>
					<!-- submitFeed -->
					<input type="button" class="submitFeedBtn baseBtn" style='display:none;' value='SubmitFeed'>
					
				</div>
			</div>
		</div>
		<form action="" id='listForm' method='POST'>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
				<tbody>
					<tr class="table-module-title">
						<td width="3%" class="ec-center">
							<input type="checkbox" class="checkAll">
						</td>
						<td width='100'><{t}>Image<{/t}></td>
						<td width="30%"><{t}>p_product_title<{/t}></td>
						<td width='120'><{t}>product_sales_value<{/t}></td>
						<td width='60'><{t}>quantity<{/t}></td>
						<!-- <td width='120'>pending_quantity</td> -->
						<td>ASIN</td>
						<td><{t}>fulfillment<{/t}></td>
						<td><{t}>sale_status<{/t}>/<{t}>price_set<{/t}></td><!-- item_status -->
						<td><{t}>supply_type<{/t}>/<{t}>status<{/t}></td>
						<td><{t}>date<{/t}></td>
						<td width='100'><{t}>operate<{/t}></td>
					</tr>
				</tbody>
				<tbody id="table-module-list-data">
				</tbody>
			</table>
		</form>
		<div class="pagination"></div>
	</div>
	<div id='loading'></div>
</div>
<!-- 产品库存  -->
<div id='inventory_div' title='<{t}>product_inventory<{/t}>' class='dialog_div'>
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
		<tbody>
			<tr class="table-module-title">
				<td><{t}>p_product_barcode<{/t}></td>
				<!-- 产品代码 -->
				<td width='200'><{t}>warehouse_name<{/t}></td>
				<!-- 仓库 -->
				<td><{t}>pi_onway<{/t}></td>
				<!-- 在途 -->
				<td><{t}>pi_pending<{/t}></td>
				<!-- 待上架 -->
				<td><{t}>pi_sellable<{/t}></td>
				<!-- 可售 -->
				<td><{t}>pi_hold<{/t}></td>
				<!-- 冻结 -->
			</tr>
		</tbody>
		<tbody id='inventory_detail'>
		</tbody>
	</table>
</div>
<!-- 产品补货设置 -->
<div id='update_supply_qty_div' title='<{t}>product_replenishment_settings<{/t}>' class='dialog_div'>
	<div class="search-module-condition" style='padding-bottom: 0px;'>
		<span style="line-height: 25px;" class="searchFilterText">
			<{t}>the_number_of_unified_set_replenishment<{/t}>
			<!-- 将补货数统一设置为 -->
			：
		</span>
		<select id="supply_type" class='supply_type'>
			<{foreach from=$supply_type_arr key=k name=ob item=ob}>
			<option value='<{$k}>'><{$ob}></option>
			<{/foreach}>
		</select>
		<select id="warehouse_code" class='warehouse_code'>
			<{foreach from=$warehouseArr key=k name=ob item=ob}>
			<option value='<{$ob.warehouse_code}>'><{$ob.warehouse_code}>[<{$ob.warehouse_desc}>]</option>
			<{/foreach}>
		</select>
		<!-- 整数 -->
		<input type="text" placeholder="<{t}>integer<{/t}>" style="width: 80px;" id="supply_qty_set" class="input_text quantity">
		<select id='status'>
			<{foreach from=$status_arr key=k name=ob item=ob}>
			<option value='<{$k}>'><{$ob}></option>
			<{/foreach}>
		</select>
		<!-- 确定 -->
		<input type="button" value="<{t}>define<{/t}>" class="baseBtn supply_qty_set_btn">
		<!-- 设置Item的补货数=上架数 -->
		<input type="button" value="<{t}>set_replenishment_is_equal_to_the_number_of_shelves<{/t}>" class="baseBtn set_supply_qty_eq_sell_qty_btn" style='float: right; display: none;'>
	</div>
	<div class="search-module-condition" style='padding-bottom: 10px; color: red; text-align: right;'>
		<!-- 补货数设置为0，表示缺货，设置为负数，表示不自动补货 -->
		<!-- <{t}>replenishment_tips<{/t}> -->
	</div>
	<form class="submitReturnFalse" name="searchForm" id="update_supply_qty_form" onsubmit='return false;'>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module" id='supply_qty_set_div'>
			<tbody>
				<tr class="table-module-title">
					<td><{t}>user_account<{/t}></td>
					<td width='250'>
						ProductID/<{t}>online_sku<{/t}>
						<!-- 在线SKU -->
					</td>
					<td width='70'><{t}>salable<{/t}></td>
					<!-- 可售数 -->
					<td width='300'><{t}>supply_type<{/t}></td>
					<!-- 补货数 -->
					<td><{t}>status<{/t}></td>
				</tr>
			</tbody>
			<tbody class="table-module-list-data">
			</tbody>
		</table>
	</form>
</div>

<!-- 产品改价设置 -->
<div id='update_price_div' title='<{t}>product_edit_price_batch<{/t}>' class='dialog_div'>	
    <div style="padding-bottom: 10px; color: red; " class="search-module-condition">     
    <{t escape='1'}>注意：<br/>1、如果listing_price=regular_price,amazon_time将被清空，无需填写<br/>2、如果listing_price为空，listing_price=regular_price,amazon_time将被清空，无需填写<{/t}>
	</div>
	<form class="submitReturnFalse" name="searchForm" id="update_price_form" onsubmit='return false;'>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td width='100'><{t}>user_account<{/t}></td>
					<td width='250'>
						ProductID/<{t}>online_sku<{/t}>
						<!-- 在线SKU -->
					</td>
					<td width='120'><{t}>product_sales_value<{/t}></td>
					<td><{t}>price_set<{/t}></td>
				</tr>
			</tbody>
			<tbody class="table-module-list-data">
			</tbody>
		</table>
	</form>
</div>
<!-- 日志信息 -->
<div class="dialog_div" id='logDiv' title='<{t}>order_log<{/t}>'>
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
		<tbody>
			<tr class="table-module-title">
				<td width='35'>No.</td>
				<td width='130'><{t}>date<{/t}></td>
				<!-- 时间 -->
				<td><{t}>content<{/t}></td>
				<!-- 内容 -->
				<td width='80'><{t}>operater<{/t}></td>
				<!-- 操作人 -->
			</tr>
		</tbody>
		<tbody class="table-module-list-data">
		</tbody>
	</table>
</div>
<!-- 补货日志 -->
<div class="dialog_div" id='supLogDiv' title='<{t}>replenishment_log<{/t}>'>
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
		<tbody>
			<tr class="table-module-title">
				<td width='35'>No.</td>
				<td width='130'><{t}>date<{/t}></td>
				<!-- 时间 -->
				<td>SKU</td>
				<td><{t}>online_number<{/t}></td>
				<!-- 在线数 -->
				<td><{t}>replenishment_number<{/t}></td>
				<!-- 补货数 -->
				<td width='80'><{t}>operater<{/t}></td>
				<!-- 操作人 -->
			</tr>
		</tbody>
		<tbody class="table-module-list-data">
			<tr class="table-module-b1">
				<td>No</td>
				<td><{t}>date<{/t}></td>
				<!-- 时间 -->
				<td>SKU</td>
				<td><{t}>online_number<{/t}></td>
				<!-- 在线数 -->
				<td><{t}>replenishment_number<{/t}></td>
				<!-- 补货数 -->
				<td><{t}>operater<{/t}></td>
				<!-- 操作人 -->
			</tr>
		</tbody>
	</table>
</div>
<div class="dialog_div" id='requestReport' title='创建报告'>
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
		<tbody class="table-module-list-data">
			<tr class="table-module-b1">
				<td width='110'>UserAccount</td>
				<td>
					<select id="report_user_account">
						<{foreach from=$user_account_arr name=ob item=ob}>
						<option value='<{$ob.user_account}>'><{$ob.platform_user_name}></option>
						<{/foreach}>
					</select>
				</td>
			</tr>
			<tr class="table-module-b1">
				<td>ReportType</td>
				<td>
					<select id="report_type">
						<option value='_GET_MERCHANT_LISTINGS_DATA_LITE_'>_GET_MERCHANT_LISTINGS_DATA_LITE_</option>
						<option value='_GET_MERCHANT_LISTINGS_DATA_'>_GET_MERCHANT_LISTINGS_DATA_</option>			
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div class="dialog_div" id='submitFeed' title='创建报告'>
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
		<tbody class="table-module-list-data">
			<tr class="table-module-b1">
				<td width='110'>UserAccount</td>
				<td>
					<select id="feed_user_account">
						<{foreach from=$user_account_arr name=ob item=ob}>
						<option value='<{$ob.user_account}>'><{$ob.platform_user_name}></option>
						<{/foreach}>
					</select>
				</td>
			</tr>
			<tr class="table-module-b1">
				<td>FeedType</td>
				<td>
					<select id="feed_type">
						<option value='_POST_PRODUCT_PRICING_DATA_'>_POST_PRODUCT_PRICING_DATA_</option>
						<option value='_POST_INVENTORY_AVAILABILITY_DATA_'>_POST_INVENTORY_AVAILABILITY_DATA_</option>			
				</td>
			</tr>
		</tbody>
	</table>
</div>