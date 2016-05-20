<script type="text/javascript">
<{include file='product/views/item-entry/item_list.js'}>
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

#update_supply_qty_form #supply_qty_set_div .disableSupplyBtn {
	color: #0090E1;
}

.word-break {
	word-break: break-all; /*支持IE，chrome，FF不支持*/
	word-wrap: break-word; /*支持IE，chrome，FF*/
}
-->
</style>
<div id="module-container" style=''>
	<div id="search-module">
		<form class="submitReturnFalse" name="searchForm" id="searchForm">
			<input type="hidden" value="" id="filterActionId" />
			<div class="search-module-condition">
				<span style="width: 90px;" class="searchFilterText">&nbsp;账号：</span>
				<select id="user_account" name="user_account">
					<option value=''>全部</option>
					<{foreach from=$user_account_arr name=ob item=ob}>
					<option value='<{$ob.user_account}>'><{$ob.platform_user_name}></option>
					<{/foreach}>
				</select>
			</div>
			<div class="search-module-condition">
				<span style="width: 90px;" class="searchFilterText">&nbsp;类型：</span>
				<select id="account_details_entry_type" name="account_details_entry_type">
					<option value=''>全部</option>
					<{foreach from=$account_details_entry_type name=ob item=ob}>
					<option value='<{$ob.account_details_entry_type}>'><{$ob.account_details_entry_type}></option>
					<{/foreach}>
				</select>
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;">产品信息：</span>
				<input type="text" class="input_text keyToSearch" id="item_id" name="item_id" placeholder='请输入ItemID' style='width: 250px;' />
			</div>
			<div class="search-module-condition">
				<span style="width: 90px;" class="searchFilterText">&nbsp;</span>
				<input type="button" class="baseBtn submitToSearch" value="搜索">
			</div>
		</form>
	</div>
	<div id="module-table" style='overflow: auto;'>
		<form action="" id='listForm' method='POST'>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
				<tbody>
					<tr class="table-module-title">
						<!-- 
						<td width="3%" class="ec-center">
							<input type="checkbox" class="checkAll">
						</td>
						 -->
						<td width='70'>账号</td>
						<td width='130'>类型/描述/金额</td>
						<td>ItemID/标题</td>
						<td>说明</td>
						<td width='70'>操作</td>
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