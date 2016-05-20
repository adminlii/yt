<script type="text/javascript">
<{include file='product/views/allow-sku/allow_sku_list.js'}>
</script>
<style>
</style>
<div id="module-container" style=''>
	<div id="search-module">
		<form class="submitReturnFalse" name="searchForm" id="searchForm">
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 105px;"><{t}>shop_account<{/t}>：</span>
				<select id="user_account" name="user_account">
					<option value=''><{t}>all<{/t}></option>
					<{foreach from=$user_account_arr name=ob item=ob}>
					<option value='<{$ob.user_account}>'><{$ob.platform_user_name}></option>
					<{/foreach}>
				</select>
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 105px;"><{t}>SKU<{/t}>：</span>
				<input type="text" placeholder="<{t}>like_search_before&after<{/t}>" style="" name="sku" class="input_text keyToSearch">
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 105px;">&nbsp;</span>
				<input type="button" class="baseBtn submitToSearch" value="<{t}>search<{/t}>">
			</div>				
		</form>
	</div>
	<div id="module-table" style='overflow: auto;'>
		<div class="opration_area">
    		<div style="width: 270px;float: right;">
	    		<input type="button" style="float: right;" class="batchSetBtn baseBtn" value="<{t}>trial_run_upload_sku<{/t}>">
    		</div>
    	</div>		
		<form action="" id='listForm' method='POST'>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
				<tbody>
					<tr class="table-module-title">
						<td width="3%" class="ec-center">
							<input type="checkbox" class="checkAll">
						</td>
						<td><{t}>shop_account<{/t}></td>
						<td><{t}>online_sku<{/t}></td><!-- 在线SKU -->
						<td><{t}>date<{/t}></td><!-- 时间 -->
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
<!-- 批量操作 -->
<div title='<{t}>trial_run_upload_sku<{/t}>' class='dialog_div' id='batch_op_div' style='display:none;'>
	<form id='batchAddBlackListForm' onsubmit='return false;'>
		<div class="search-module-condition">
			<span class="searchFilterText" style="width: 90px;">&nbsp;<{t}>shop_account<{/t}>：</span>
			<select name="user_account" id="user_account">
    			<{foreach from=$user_account_arr name=ob item=ob}>
    			<option value='<{$ob.user_account}>'><{$ob.platform_user_name}></option>
    			<{/foreach}>
			</select>
		</div>
		<div class="search-module-condition">
			<span class="searchFilterText" style="width: 90px;">&nbsp;SKU：</span>
			<!-- 批量输入SKU号码，每个SKU换行 -->
			<textarea rows="" cols="" style='width:300px;height:100px;overflow:auto;font-size:12px;' placeholder='<{t}>trial_run_upload_tips02<{/t}>' id='sku'></textarea>
		</div>
		<div  class="search-module-condition">
			<span class="searchFilterText" style="width: 90px;"><{t}>kind_tips<{/t}>：</span>
		    <{t}>trial_run_upload_tips01<{/t}>
		    <!-- 操作提示：当启用试运行时，平台订单产品全部在试运行之内，才会生成系统操作订单 -->
		</div>
	</form>
</div>