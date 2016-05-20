<script type="text/javascript">
<{include file='product/views/seller-item/seller_item_black_list.js'}>
</script>
<style>
</style>
<div id="module-container" style=''>
	<div id="search-module">
		<form class="submitReturnFalse" name="searchForm" id="searchForm">
			<div class="search-module-condition">
				<span style="line-height: 25px;" class="">&nbsp;<{t}>shop_account<{/t}>：</span>
				<select id="user_account" name="user_account">
					<option value=''><{t}>all<{/t}></option>
					<{foreach from=$user_account_arr name=ob item=ob}>
					<option value='<{$ob.user_account}>'><{$ob.platform_user_name}></option>
					<{/foreach}>
				</select>
				
				<span style="width: 60px;line-height: 25px;" >&nbsp;&nbsp;SKU：</span>				
				<input type="text" placeholder="<{t}>like_search_before&after<{/t}>" style="" name="sku" class="input_text keyToSearch">
				&nbsp;
				<input type="button" class="baseBtn submitToSearch" value="<{t}>search<{/t}>">
				<input type="button" style="float: right;" class="batchSetBtn baseBtn" value="<{t}>Batch_settings<{/t}>"><!-- 批量设置 -->
			</div>
		</form>
	</div>
	<div id="module-table" style='overflow: auto;'>
		<form action="" id='listForm' method='POST'>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
				<tbody>
					<tr class="table-module-title">
						<td width="3%" class="ec-center">
							<input type="checkbox" class="checkAll">
						</td>
						<td><{t}>shop_account<{/t}><!-- 账号 --></td>
						<td><{t}>online_sku<{/t}><!-- 在线SKU --></td>
						<td width='90'><{t}>operate<{/t}><!-- 操作 --></td>
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
<!-- 批量设置 -->
<div title='<{t}>Batch_settings<{/t}>' class='dialog_div' id='batch_op_div' style='display:none;'>
	<form id='batchAddBlackListForm' onsubmit='return false;'>
		<div class="search-module-condition">
			<span class="searchFilterText" style="width: 90px;">&nbsp;<{t}>shop_account<{/t}><!-- 账号 -->：</span>
			<select name="user_account" id="user_account">
    			<{foreach from=$user_account_arr name=ob item=ob}>
    			<option value='<{$ob.user_account}>'><{$ob.platform_user_name}></option>
    			<{/foreach}>
			</select>
		</div>
		<div class="search-module-condition">
			<span class="searchFilterText" style="width: 90px;">&nbsp;SKU：</span>
			<!-- 批量输入SKU号码，每个SKU换行 -->
			<textarea rows="" cols="" style='width:300px;height:100px;overflow:auto;font-size:12px;' placeholder='<{t}>seller_item_black_list_search_tips<{/t}>' id='sku'></textarea>
		</div>
		<div style='font-size:13px;padding-left:55px;'>
			<{t}>seller_item_black_list_operate_tips<{/t}>
		    <!-- 操作提示：如果您的产品即将下架或清仓，则将此类产品加入补货黑名单，加入黑名单中的产品将不会自动补货>... -->
		</div>
	</form>
</div>