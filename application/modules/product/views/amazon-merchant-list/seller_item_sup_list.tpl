<script type="text/javascript">
<{include file='product/views/seller-item/seller_item_sup_list.js'}>
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
				<span style="width: 60px;line-height: 25px;" >&nbsp;&nbsp;ItemID：</span>				
				<input type="text"  style="" name="item_id" class="input_text keyToSearch">
				<span style="width: 60px;line-height: 25px;" >&nbsp;&nbsp;SKU：</span>				
				<input type="text" placeholder="<{t}>like_search_before&after<{/t}>" style="" name="sku" class="input_text keyToSearch">
				&nbsp;
				<input type="button" class="baseBtn submitToSearch" value="<{t}>search<{/t}>">
			</div>
		</form>
	</div>
	<div id="module-table" style='overflow: auto;'>
		<form action="" id='listForm' method='POST'>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
				<tbody>
					<tbody>
                		<tr class="table-module-title">
                			<td width='35'>No.</td>
                			<td>Account</td>
                			<td>ItemID</td>
                			<td>SKU</td>
                			<td><{t}>online_number<{/t}></td><!-- 在线数 -->
                			<td><{t}>replenishment_number<{/t}></td><!-- 补货数 -->
                			<!--<td width='80'><{t}>operater<{/t}></td>--><!-- 操作人 -->
                			<td width='130'><{t}>date<{/t}></td><!-- 时间 -->
                		</tr>
                	</tbody>
                	<tbody id="table-module-list-data">
                		
                	</tbody>
				</tbody>
				
			</table>
		</form>
		<div class="pagination"></div>
	</div>
</div>