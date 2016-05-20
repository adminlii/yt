<script type="text/javascript">
<{include file='product/views/shopify/product_list.js'}>
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
.hidden{display:none;}

.sort{cursor:pointer;color:#FF5453;}
-->
</style>
<div id="module-container" style=''>
	<div id="search-module">
		<form class="submitReturnFalse" name="searchForm" id="searchForm">
			<table width="100%" cellspacing="0" cellpadding="0" border="0" id="searchfilterArea1" class="searchfilterArea">
				<tbody>
					<tr class=''>
						<td>
							<div class="searchFilterText" style="width: 90px;"><{t}>sale_status<{/t}>：</div><!-- 销售状态 -->
							<div class="pack_manager">
								<input type="hidden" class="input_text keyToSearch" id="status" name="status" value="">
								<a onclick="searchFilterSubmit('status','',this)" href="javascript:void(0)"  class='current'><{t}>all<{/t}></a>
								<a onclick="searchFilterSubmit('status','1',this)" href="javascript:void(0)"><{t}>s_Published<{/t}></a><!-- 已发布 -->
								<a onclick="searchFilterSubmit('status','0',this)" href="javascript:void(0)"><{t}>s_Unpublished<{/t}></a><!-- 未发布 -->
							</div>
						</td>
					</tr>
					<tr class=''>
						<td>
							<div class="searchFilterText" style="width: 90px;"><{t}>s_Recommend<{/t}>：</div><!-- 推荐上下架 -->
							<div class="pack_manager">
								<input type="hidden" class="input_text keyToSearch" id="recommand" name="recommand" value="">
								<a onclick="searchFilterSubmit('recommand','',this)" href="javascript:void(0)"  class='current'><{t}>all<{/t}></a>
								<!-- 产品已经下架，仓库有货，该情况，系统推荐产品上架 -->
								<a onclick="searchFilterSubmit('recommand','1',this)" href="javascript:void(0)" title="<{t}>s_Recommend_Off_shelves_title<{/t}>"><{t}>s_Recommend_Shelves<{/t}></a><!-- 推荐上架 -->
								<!-- 产品已经上架，仓库无货，该情况，系统推荐产品下架 -->
								<a onclick="searchFilterSubmit('recommand','2',this)" href="javascript:void(0)" title="<{t}>s_Recommend_Shelves_title<{/t}>"><{t}>s_Recommend_Off_shelves<{/t}></a><!-- 推荐下架 -->
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			
			<div class="search-module-condition">
				<span style="width: 90px;" class="searchFilterText">&nbsp;<{t}>shop_account<{/t}>：</span><!-- 账户 -->
				<select id="user_account" name="user_account">
					<option value=''><{t}>all<{/t}></option>
					<{foreach from=$user_account_arr name=ob item=ob}>
					<option value='<{$ob.user_account}>'><{$ob.platform_user_name}></option>
					<{/foreach}>
				</select>
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;"><{t}>product<{/t}>：</span><!-- 产品信息 -->
				<select name="type" class='input_text_select' id='type'>
					<option value="sku">SKU</option>
					<option value="item_id">ProductID</option>
				</select>
				<input type="text" class="input_text keyToSearch" id="code" name="code"   placeholder=''  style='width: 250px;'/>
			</div>			
			
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;"><{t}>salable<{/t}>：</span><!-- 可售数 -->
				<input type="text" class="input_text keyToSearch" id="sell_qty_from" name="sell_qty_from" style='width: 50px;' placeholder='<{t}>integer<{/t}>'>
				~
				<input type="text" class="input_text keyToSearch" id="sell_qty_to" name="sell_qty_to" style='width: 50px;' placeholder='<{t}>integer<{/t}>'>
			</div>
			<div class="search-module-condition">
				<span style="width: 90px;" class="searchFilterText">&nbsp;</span>
			    <input id="sort" type="hidden" name="sort" value=''>
				<input type="button" class="baseBtn submitToSearch" value="<{t}>search<{/t}>">    
			</div>
		</form>
	</div>
	<div id="module-table" style='overflow: auto;'>
		<div class="opration_area">
			<div style="margin: 10px 10px 0;" class="opration_area">
				<div class="opDiv">
					<input type="button" value="下架" class="syncDownBtn baseBtn hidden">
					<input type="button" value="上下架" class="syncUpBtn baseBtn hidden" style=''>
					
					<!-- 上架 -->
					<input type="button" value="<{t}>s_Shelves<{/t}>" class="syncUpOnlyBtn baseBtn " style=''>
					<!-- 下架 -->
					<input type="button" value="<{t}>s_Off_shelves<{/t}>" class="syncDownOnlyBtn baseBtn " style=''>
					<!-- 修改在线数 -->
					<input type="button" value="<{t}>change_qty<{/t}>" class="changeQtyBtn baseBtn " style=''>
					<!-- 改价 -->
					<input type="button" value="<{t}>change_price<{/t}>" class="changePriceBtn baseBtn " style=''>
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
						<td width='30%'><{t}>p_product_title<{/t}><!-- 产品名称 --></td>
						<td  width='65' class='sort' sort='' type='inventory_quantity' ><{t}>salable<{/t}><!-- 可售数 --><span></span></td>
						<td><{t}>detail<{/t}></td>
						<td width='65' class='sort ' sort='' type='status' ><{t}>data_platform<{/t}><!-- 平台 --><span></span></td>
						<td width='165'><{t}>date<{/t}></td>
						<!-- <td width='90'><{t}>operate<{/t}></td> -->
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
							<!-- 
								<td class='var_op' width='90'><{t}>operate<{/t}></td>
							 -->
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
			<input type="text" placeholder="<{t}>integer<{/t}>" style="width: 70px;" name="supply_qty[]" class="input_text supply_qty" disabled='disabled'>
		</td>
		<td class='var_op'><{t}>operate<{/t}></td>
	</tr>
</table>
<!-- 产品库存 -->
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
<!-- 产品改数量 -->
<div id='update_supply_qty_div' title='<{t}>change_qty<{/t}>' class='dialog_div'>
	<div class="search-module-condition " style='padding-bottom: 0px;'>
		<span style="line-height: 25px;" class="searchFilterText"><{t}>the_number_of_unified_set_replenishment<{/t}><!-- 将补货数统一设置为 -->：</span>
		<input type="text" placeholder="<{t}>integer<{/t}>" style="width: 80px;" id="supply_qty_set" class="input_text">
		<!-- 确定 -->
		<input type="button" value="<{t}>设置<{/t}>" class="baseBtn supply_qty_set_btn">
		<!-- 设置更新数量=Shopify可售数 -->
		<input type="button" value="<{t}>s_Update_Qty_Shopify_sale<{/t}>" class="baseBtn set_supply_qty_eq_sell_qty_btn" style='float:right;'>
	</div>
	<div class="search-module-condition hidden" style='padding-bottom: 10px;color:red;text-align:right;'>
		<!-- 补货数设置为0，表示缺货，设置为负数，表示不自动补货 -->
		<{t}>replenishment_tips<{/t}>
	</div>
	<form class="submitReturnFalse" name="searchForm" id="update_supply_qty_form" onsubmit='return false;'>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module" id='supply_qty_set_div'>
			<tbody>
				<tr class="table-module-title">
					<td>ProductID/<{t}>online_sku<{/t}><!-- 在线SKU --></td>
					<td width='100'>Shopify <{t}>salable<{/t}><!-- 可售数 --></td>
					<td width='90'><{t}>warehouse<{/t}> <{t}>salable<{/t}><!-- 可售数 --></td>
					<td width='90'><{t}>缺货占用<{/t}><!-- 缺货占用 --></td>
					<td width='120'><{t}>未审到仓库占用<{/t}><!-- 缺货占用 --></td>
					<td width='80'><{t}>s_Update_qty<{/t}><!-- 更新数量 --></td>
				</tr>
			</tbody>
			<tbody class="table-module-list-data">
			</tbody>
		</table>
	</form>
</div>

<!-- 产品改价 -->
<div id='update_price_div' title='<{t}>change_price<{/t}>' class='dialog_div'>	
    <div style='color:red;line-height:25px;'><{t}>change_price_tip<{/t}></div>
	<form class="submitReturnFalse" name="searchForm" id="update_price_form" onsubmit='return false;'>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td width='50%'>ProductID/<{t}>online_sku<{/t}><!-- 在线SKU --></td>
					<td width='90'><{t}>old_price<{/t}><!-- 已有价格 --></td>
					<td width='90'><{t}>compare_at_price<{/t}><!-- 已有价格 --></td>
					<td><{t}>new_price<{/t}><!-- 修改价格 --></td>
					<td><{t}>compare_at_price<{/t}><!-- 比较价格 --></td>
				</tr>
			</tbody>
			<tbody class="table-module-list-data">
			</tbody>
		</table>
	</form>
</div>

<!-- 产品上下架设置 -->
<div id='update_publish_div' title='<{t}>s_Upper_and_lower_shelf_products<{/t}>' class='dialog_div'>	
	<form class="submitReturnFalse" name="searchForm" id="update_publish_form" onsubmit='return false;'>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td width='50%'>ProductID/<{t}>online_sku<{/t}><!-- 在线SKU --></td>
					<td width='100'>Shopify <{t}>salable<{/t}><!-- 可售数 --></td>
					<td width='90'><{t}>warehouse<{/t}> <{t}>salable<{/t}><!-- 可售数 --></td>
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
    			<td><{t}>online_number<{/t}></td><!-- 在线数 -->
    			<td><{t}>replenishment_number<{/t}></td><!-- 补货数 -->
    			<td width='80'><{t}>operater<{/t}></td><!-- 操作人 -->
    		</tr>
    	</tbody>
    	<tbody class="table-module-list-data">
    	</tbody>
    </table>
</div>