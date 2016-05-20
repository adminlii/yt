<style>
.sku,.warehouseSkuWrap {
	float: left;
	text-align: left;
}
.sku{
	width: 30%;
}
.warehouseSkuWrap{
	width: 35%;
}
.link_platform .count{color:red;padding:0 0 0 5px;}
</style>
<div id="search-module">
	<form class="submitReturnFalse" name="searchForm" id="searchForm">
		<table width="100%" cellspacing="0" cellpadding="0" border="0" id="searchfilterArea1" class="searchfilterArea">
			<tbody>
				<tr>
					<td>
						<div class="searchFilterText" style="width: 105px;"><{t}>平台<{/t}>：</div><!-- 订单分类 -->
						<div class="pack_manager">
							<input type="hidden" class="input_text keyToSearch" id="platform" name="platform" value="">
							<a class="link_platform platform_" platform='' onclick="searchFilterSubmit('platform','',this)" href="javascript:void(0)"><{t}>all<{/t}><span class='count'>(0)</span></a><!-- 全部 -->
							<a class="link_platform platform_ebay" platform='ebay' onclick="searchFilterSubmit('platform','ebay',this)" href="javascript:void(0)"><{t}>ebay<{/t}><span class='count'>(0)</span></a>
							<a class="link_platform platform_amazon" platform='amazon' onclick="searchFilterSubmit('platform','amazon',this)" href="javascript:void(0)"><{t}>amazon<{/t}><span class='count'>(0)</span></a>
							<a class="link_platform platform_aliexpress" platform='aliexress' onclick="searchFilterSubmit('platform','aliexpress',this)" href="javascript:void(0)"><{t}>aliexpress<{/t}><span class='count'>(0)</span></a>
						    <a class="link_platform platform_mabang" platform='mabang' onclick="searchFilterSubmit('platform','mabang',this)" href="javascript:void(0)"><{t}>mabang<{/t}><span class='count'>(0)</span></a>
						</div>
					</td>
				</tr>
				
			</tbody>
		</table>
		<!--  
		<input type="hidden" value="" id="filterActionId" />
		 -->
		<div class="search-module-condition" style="padding-bottom:10px;">
			<span class="searchFilterText" style="width: 105px;"><{t}>标记发货<{/t}>：</span><!-- 标记发货 -->	
			<div class="pack_manager">		
				<input type="hidden" class="input_text keyToSearch" id="platform_ship_status" name="platform_ship_status" value="">
				<a onclick="searchFilterSubmit('platform_ship_status','',this)" href="javascript:void(0)"><{t}>all<{/t}></a><!-- 全部 -->
				<a onclick="searchFilterSubmit('platform_ship_status','1',this)" href="javascript:void(0)"><{t}>是<{/t}></a><!-- 是 -->
				<a onclick="searchFilterSubmit('platform_ship_status','0',this)" href="javascript:void(0)"><{t}>否<{/t}></a><!-- 否 -->
			</div>
		</div>
		<div class="search-module-condition">
			<span class="searchFilterText" style="width: 105px;"><{t}>单号<{/t}>：</span>
			<input type="text" class="input_text keyToSearch" id="code" name="refrence_no" style='width: 400px;' placeholder='<{t}>multi_split_space<{/t}>' />
		</div>
		<div class="search-module-condition">
			<span class="searchFilterText" style="width: 105px;"><{t}>关键字<{/t}>：</span><!-- 单号 -->			
			<input type="text" class="input_text keyToSearch" id="keyword" name="keyword" style='width: 800px;' placeholder='平台单号,参考号,运输方式,目的国家,买家ID,收件人名称,收件人邮箱,ItemID,eBay交易流水号,收件人手机,收件人邮编,收件人门牌号' />
		</div>
		<div class="search-module-condition">			
			<span class="searchFilterText" style="width: 105px;">&nbsp;&nbsp;&nbsp;&nbsp;<{t}>shop_account<{/t}>：</span><!-- 店铺帐号 -->
			<select id="user_account" name="user_account" class="input_text_select">
				<option value=''><{t}>all<{/t}></option>
				<{foreach from=$user_account_arr name=ob item=ob}>
				<option value='<{$ob.user_account}>'><{$ob.platform_user_name}></option>
				<{/foreach}>
			</select>
		</div>
		<div class="search-module-condition">			
			<span class="searchFilterText" style="width: 105px;">&nbsp;&nbsp;&nbsp;&nbsp;<{t}>consignee_country<{/t}>：</span><!-- 国家 -->
			<select id="country" name="country" style="width: 500px;">
				<option value=''><{t}>all<{/t}></option>
				<{foreach from=$countryArr name=ob item=ob}>
				<option value='<{$ob.country_code}>'><{$ob.country_code}> (<{$ob.country_enname}> | <{$ob.country_cnname}>)</option>
				<{/foreach}>
			</select>
		</div>
		
		
		<div class="search-module-condition">
			<span class="searchFilterText" style="width: 105px;">&nbsp;</span>
			<input type="button" class="baseBtn submitToSearch" value="<{t}>search<{/t}>"><!-- 搜索 -->
			<input type='hidden' name='status' id='status' value='2' />		
			<input type="button" style="float:right;" value="手动拉单" class="getOrderBtn baseBtn" status="5">	
		</div>
	</form>
</div>