<script type="text/javascript">
<{include file='product/views/seller-item-list/seller_item_list.js'}>
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
.dialog_div{display: none;}

.dropdown_show .not_first{display:block;}
.dropdown_hide .not_first{display:none;}
#listForm .getEbayItem{display:none;}
#listForm .viewEbayItem{display:none;}
#update_supply_qty_form #supply_qty_set_div .disableSupplyBtn{color:#0090E1;}
.quantity {
    width: 70px;
}
.price_text{width:60px;}
.supply_warehouse{cursor:pointer;color:#0090e1;}
.img_wrap{float:left;width:90px;height:120px;}
-->
</style>
<div id="module-container" style=''>
	<div id="search-module">
		<form class="submitReturnFalse" name="searchForm" id="searchForm">
			<table width="100%" cellspacing="0" cellpadding="0" border="0" id="searchfilterArea1" class="searchfilterArea">
				<tbody>
					<tr>
						<td>
							<div class="searchFilterText" style="width: 125px;"><{t}>sale_status<{/t}>：</div><!-- 销售状态 -->
							<div class="pack_manager">
								<input type="hidden" class="input_text keyToSearch" id="item_status" name="item_status" value="Active">
								<input type="hidden" class="input_text keyToSearch" id="no_stock_online" name="no_stock_online" value="">
								<a href="javascript:void(0)" class='item_status' status=''><{t}>all<{/t}></a>
								<a href="javascript:void(0)" class='current item_status' status='Active'><{t}>sales<{/t}></a><!-- 销售中 -->
								<a href="javascript:void(0)" class='item_status' status='Completed'><{t}>the_shelves_out<{/t}></a><!-- 已下架 -->
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="searchFilterText" style="width: 125px;"><{t}>OutOfStockControl<{/t}>：</div><!-- 销售状态 -->
							<div class="pack_manager">
								<input type="hidden" class="input_text keyToSearch" id="out_of_stock_control" name="out_of_stock_control" value="">
								<a href="javascript:void(0)" class='out_of_stock_control current'  onclick="searchFilterSubmit('out_of_stock_control','',this)"><{t}>all<{/t}></a>
								<a href="javascript:void(0)" class='out_of_stock_control' onclick="searchFilterSubmit('out_of_stock_control','true',this)"><{t}>是<{/t}></a><!-- 无货在线 -->
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="searchFilterText" style="width: 125px;"><{t}>sales_type<{/t}>：</div><!-- 销售类型 -->
							<div class="pack_manager">
								<input type="hidden" class="input_text keyToSearch" id="sell_type" name="sell_type" value="">
								<a onclick="searchFilterSubmit('sell_type','',this)" href="javascript:void(0)" class='current'><{t}>all<{/t}></a>
								<a onclick="searchFilterSubmit('sell_type','1',this)" href="javascript:void(0)"><{t}>a_price<{/t}>(<{t}>single_product<{/t}>)</a><!-- 一口价(单品) -->
								<a onclick="searchFilterSubmit('sell_type','2',this)" href="javascript:void(0)"><{t}>a_price<{/t}>(<{t}>multi_product<{/t}>)</a><!-- 一口价(多品) -->
								<a onclick="searchFilterSubmit('sell_type','0',this)" href="javascript:void(0)"><{t}>auction<{/t}></a><!-- 拍卖 -->
							</div>
						</td>
					</tr>
					<tr style='display:none;'>
						<td>
							<div class="searchFilterText" style="width: 125px;"><{t}>replenishment_type<{/t}>：</div><!-- 补货类型 -->
							<div class="pack_manager">
								<input type="hidden" class="input_text keyToSearch" id="need_supply" name="need_supply" value="">
								<a onclick="searchFilterSubmit('need_supply','',this)" href="javascript:void(0)" class='current'><{t}>all<{/t}></a>
								<a onclick="searchFilterSubmit('need_supply','1',this)" href="javascript:void(0)"><{t}>automatic_replenishment<{/t}></a><!-- 自动补货 -->
								<a onclick="searchFilterSubmit('need_supply','0',this)" href="javascript:void(0)"><{t}>without_replenishment<{/t}></a><!-- 无需补货 -->
							</div>
						</td>
					</tr>
					<tr style='display:none;'>
						<td>
							<div class="searchFilterText" style="width: 125px;"><{t}>replenishment_set<{/t}>：</div><!-- 补货设置类型 -->
							<div class="pack_manager">
								<input type="hidden" class="input_text keyToSearch" id="auto_supply" name="auto_supply" value="">
								<a onclick="searchFilterSubmit('auto_supply','',this)" href="javascript:void(0)" class='current'><{t}>all<{/t}></a>
								<a onclick="searchFilterSubmit('auto_supply','1',this)" href="javascript:void(0)"><{t}>system<{/t}></a><!-- 系统设置 -->
								<a onclick="searchFilterSubmit('auto_supply','0',this)" href="javascript:void(0)"><{t}>user<{/t}></a><!-- 用户设置 -->
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<input type="hidden" value="" id="filterActionId" />
			<div class="search-module-condition">
				<span style="width: 125px;" class="searchFilterText"><{t}>ebay_account<{/t}>：</span><!-- ebay账号 -->
				<select id="user_account" name="user_account">
					<option value=''><{t}>all<{/t}></option>
					<{foreach from=$user_account_arr name=ob item=ob}>
					<option value='<{$ob.user_account}>'><{$ob.platform_user_name}> [<{$ob.user_account}>]</option>
					<{/foreach}>
				</select>
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 125px;"><{t}>product<{/t}>：</span><!-- 产品 -->
				<select name="type" class='input_text_select' id='type'>
					<option value="item_id">ItemID</option>
					<option value="sku">SKU</option>
				</select>
				<input type="text" class="input_text keyToSearch" id="code" name="code"   placeholder=''  style='width: 250px;'/>
			</div>
			
			
			
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 125px;"><{t}>salable<{/t}>：</span><!-- 可售数 -->
				<input type="text" class="input_text keyToSearch" id="sell_qty_from" name="sell_qty_from" style='width: 50px;' placeholder='<{t}>integer<{/t}>'><!-- 整数 -->
				~
				<input type="text" class="input_text keyToSearch" id="sell_qty_to" name="sell_qty_to" style='width: 50px;' placeholder='<{t}>integer<{/t}>'>
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 125px;"><{t}>has_sold<{/t}>：</span><!-- 已售数 -->
				<input type="text" class="input_text keyToSearch" id="sold_qty_from" name="sold_qty_from" style='width: 50px;' placeholder='<{t}>integer<{/t}>'>
				~
				<input type="text" class="input_text keyToSearch" id="sold_qty_to" name="sold_qty_to" style='width: 50px;' placeholder='<{t}>integer<{/t}>'>
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
					<!-- 补货同步 -->
					<input type="button" value="<{t}>synchronized_replenishment<{/t}>" class="syncBtn baseBtn ">
					<!-- 数据下载同步 -->
					<input type="button" value="<{t}>data_download<{/t}>" class="syncDownloadBtn baseBtn " style='display:none;'>
					<!-- 产品补货设置 -->
					<input type="button" value="<{t}>product_replenishment_settings<{/t}>" class="batchEditBtn baseBtn">
					<!-- 产品批量改价 -->
					<input type="button" value="<{t}>product_edit_price_batch<{/t}>" class="batchEditPriceBtn baseBtn">
					<!-- 批量设置数 -->
					<input type="button" value="<{t}>set_the_number_of_batches<{/t}>" class="batchEditQtySetBtn baseBtn">
					<!-- 批量提交 -->
					<input type="button" value="<{t}>batch_submission<{/t}>" class="batchEditSubmitBtn baseBtn">
					<!-- 补货黑名单 -->
					<input type="button" value="<{t}>replenishment_blacklist<{/t}>" class="blackListBtn baseBtn" style='float: right;'>
					<!-- 补货日志 -->
					<input type="button" value="<{t}>replenishment_log<{/t}>" class="supLogBtn baseBtn" style='float: right;display:none;margin:0 5px 0 0;'>
					<!-- 无货在线设置-->
					<input type="button" value="<{t}>no_stock_online_set<{/t}>" class="noStockOnLineSetBtn baseBtn" style=''>
					<!-- 重新销售-->
					<input type="button" value="<{t}>cancel_no_stock_online_set<{/t}>" class="cancelNoStockOnLineSetBtn baseBtn" style='display:none;'>
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
						<td  width='30%'><{t}>p_product_title<{/t}><!-- 产品名称 --></td>
						<td><{t}>product_sales_value<{/t}><!-- 售价 --> / <{t}>salable<{/t}><!-- 可售数 --> / <{t}>has_sold<{/t}><!-- 已售数 --></td>
					    <td><{t}>supply_type<{/t}>/<{t}>status<{/t}></td>
						<td><{t}>sale_status<{/t}><!-- 销售状态 -->/<{t}>type<{/t}><!-- 类型 --></td>
						<td><{t}>date<{/t}></td>
						<td width='90'><{t}>operate<{/t}></td>
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
<table style='display: none;' id='varation_table_tpl'>
	<tbody>
		<tr class='varation_tr'>
			<td class="ec-center">&nbsp;</td>
			<td colspan='8'>
				<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
					<tbody>
						<tr class="table-module-title">
							<td class='var_sku'>SKU</td>
							<td class='var_qty'><{t}>salable<{/t}></td><!-- 可售数 -->
							<td class='var_sold_qty'><{t}>has_sold<{/t}></td><!-- 已售数 -->
							<td class='var_start_price'><{t}>product_sales_value<{/t}></td><!-- 售价 -->
							<td class='var_attr' width='30%'><{t}>property<{/t}></td><!-- 属性 -->
							<td class='var_supply_qty' width='80'><{t}>replenishment_number<{/t}></td><!-- 补货数 -->
							<td class='var_op' width='90'><{t}>operate<{/t}></td>
						</tr>
					</tbody>
					<tbody class="table-module-list-data">
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<table style='display: none;' id='varation_table_sub_tpl'>
	<tr class="attr_tr">
		<td class='var_sku'>SKU</td>
		<td class='var_qty'><{t}>salable<{/t}></td><!-- 可售数 -->
		<td class='var_sold_qty'><{t}>has_sold<{/t}></td><!-- 已售数 -->
		<td class='var_start_price'><{t}>product_sales_value<{/t}></td><!-- 售价 -->
		<td class='var_attr'><{t}>property<{/t}></td><!-- 属性 -->
		<td class='var_supply_qty'>
			<!-- 整数 -->
			<input type="text" placeholder="<{t}>integer<{/t}>" style="width: 70px;" name="supply_qty[]" class="input_text supply_qty" disabled='disabled'>
		</td>
		<td class='var_op'><{t}>operate<{/t}></td>
	</tr>
</table>
<!-- 产品库存  -->
<div id='inventory_div' title='<{t}>product_inventory<{/t}>' class='dialog_div'>
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
		<tbody>
			<tr class="table-module-title">
				<td><{t}>p_product_barcode<{/t}></td><!-- 产品代码 -->
				<td width='200'><{t}>warehouse_name<{/t}></td><!-- 仓库 -->
				<td><{t}>pi_onway<{/t}></td><!-- 在途 -->
				<td><{t}>pi_pending<{/t}></td><!-- 待上架 -->
				<td><{t}>pi_sellable<{/t}></td><!-- 可售 -->
				<td><{t}>pi_hold<{/t}></td><!-- 冻结 -->
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
		<input type="button" value="<{t}>设置<{/t}>" class="baseBtn supply_qty_set_btn">
		
		<!-- 设置Item的补货数=上架数 -->
		<input type="button" value="<{t}>set_replenishment_is_equal_to_the_number_of_shelves<{/t}>" class="baseBtn set_supply_qty_eq_sell_qty_btn" style='float:right;display:none;'>
	</div>
	<form class="submitReturnFalse" name="searchForm" id="update_supply_qty_form" onsubmit='return false;'>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module" id='supply_qty_set_div'>
			<tbody>
				<tr class="table-module-title">
					<td width='50%'>ItemID/<{t}>online_sku<{/t}><!-- 在线SKU --></td>
					<td width='70'><{t}>salable<{/t}></td><!-- 可售数 -->
					<!-- 补货类型 -->
					<td width='300'><{t}>supply_type<{/t}></td>
					<!-- 补货状态 -->
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
     <div >
		<span style="line-height: 30px;">
			<{t}>pls_delete_no_need_update_price_item<{/t}>			
		</span>
		<input type="button" value="<{t}>delete<{/t}>" class="baseBtn delPriceBatchBtn" style='float:right;'>		
	</div>
	<form class="submitReturnFalse" name="searchForm" id="update_price_form" onsubmit='return false;' style='clear:both;'>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td>ItemID/<{t}>online_sku<{/t}><!-- 在线SKU --></td>
					<td  width='80'><{t}>product_sales_value<{/t}></td>
					<td  width='110'><{t}>price_set<{/t}></td>
					<td  width='80'><{t}>operation<{/t}>&nbsp;<input type="checkbox" class="checkDelAll"></td>
				</tr>
			</tbody>
			<tbody class="table-module-list-data">
			</tbody>
		</table>
	</form>
</div>
<!-- 日志信息 -->
<div class="dialog_div" id='logDiv' title='<{t}>order_log<{/t}>'>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module" >
    	<tbody>
    		<tr class="table-module-title">
    			<td width='35'>No.</td>
    			<td width='130'><{t}>date<{/t}></td><!-- 时间 -->
    			<td><{t}>content<{/t}></td><!-- 内容 -->
    			<td width='80'><{t}>operater<{/t}></td><!-- 操作人 -->
    		</tr>
    	</tbody>
    	<tbody class="table-module-list-data">
    		
    	</tbody>
    </table>
</div>
<!-- 补货日志 -->
<div class="dialog_div" id='supLogDiv' title='<{t}>replenishment_log<{/t}>'>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module" >
    	<tbody>
    		<tr class="table-module-title">
    			<td width='35'>No.</td>
    			<td width='130'><{t}>date<{/t}></td><!-- 时间 -->
    			<td>SKU</td>
    			<!-- <td><{t}>online_number<{/t}></td> --> <!-- 在线数 -->
    			<td><{t}>replenishment_number<{/t}></td><!-- 补货数 -->
    			<td width='80'><{t}>operater<{/t}></td><!-- 操作人 -->
    		</tr>
    	</tbody>
    	<tbody class="table-module-list-data">
    		<tr class="table-module-b1">
    			<td>No</td>
    			<td><{t}>date<{/t}></td><!-- 时间 -->
    			<td>SKU</td>
    			<td><{t}>online_number<{/t}></td><!-- 在线数 -->
    			<td><{t}>replenishment_number<{/t}></td><!-- 补货数 -->
    			<td><{t}>operater<{/t}></td><!-- 操作人 -->
    		</tr>
    	</tbody>
    </table>
</div>