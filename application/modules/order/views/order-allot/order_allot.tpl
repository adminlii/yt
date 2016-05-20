<style>
<!--
ul {
	line-height: 22px;
	max-height: 500px;
	overflow-x: hidden;
	overflow-y: auto;
}

li {
	cursor: pointer;
}

li.selected {
	background: #ccc;
}

li a {
	display: block;
	width: 100%;
}

.dialog_div {
	display: none;
}

.pagination {
	display: none;
}

#table-module-list-data td {
	word-warp: break-word; /*内容将在边界内换行*/
	word-break: break-all; /*允许非亚洲语言文本行的任意字内断开*/
}

.checkbox_td {
	width: 20px;
}

.order_allot_selected_options A{
	color: #1E5494;
}

.table-module-title {
	font-weight: bold;
}

.table-module-b1 p {
	font-weight: bold;
}

.dialog_div ul {
	border: 1px solid #ccc;
	padding: 0 0 0 40px;
	list-style-type: decimal;
}
#selected_condition{ 
    word-warp:break-word; /*内容将在边界内换行*/
    word-break:break-all; /*允许非亚洲语言文本行的任意字内断开*/
}

.order_allot_options{
	height: 360px; 
	overflow: auto;
}
.order_allot_options label{
	cursor: pointer;
	color:#348D8A;
}
.order_allot_options A{
	text-decoration:none;
}
.order_allot_selected_options_view a{
	color:#AFAFAF;
	text-decoration:none;
}
-->
</style>
<script type="text/javascript">
<!--
<{include file='order/js/order-allot/order_allot.js'}>
//-->
</script>
<div id="module-container">     
	<div id="search-module">
		<form id="searchForm" name="searchForm" class="submitReturnFalse">
			<div style="padding: 0">
				<span style="width: 90px;" class="searchFilterText">规则名称：</span>
				<input type="text" name="allot_set_name" class="input_text keyToSearch">
				&nbsp;&nbsp;
				<input type="button" value="搜索" class="baseBtn submitToSearch">
				<input type="button" id="createBtn" value="添加" class="baseBtn">
			</div>
		</form>
	</div>
	<div id="module-table">
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td>规则名称</td>
					<td>仓库</td>
					<td>仓库运输方式</td>
					<td>优先级</td>
					<td>建立者</td>
					<td>操作</td>
				</tr>
			</tbody>
			<tbody id="table-module-list-data"><tr class="table-module-b1"><td colspan="6">请搜索</td></tr></tbody>
		</table>
	</div>
	<div class="pagination"></div>
</div>
<div id='order_allot_div' class='dialog_div' title='订单自动规则'>
	<form action="" onsubmit='return false;' id='order_allot_form'>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
			<tbody class="">
				<tr class="table-module-title">
					<td>已选条件</td>
					<td width="350">可选条件</td>
				</tr>
			</tbody>			
			<tbody>
				<tr class="table-module-b1">
					<td valign="top">
						<div id='selected_condition' class='order_allot_selected_options'>
						</div>
					</td>
					<td valign="top" rowspan='2' class='condition'>
						<div class="order_allot_options">
							<p>平台/账号</p>
							<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
								<tbody>
									<tr class="table-module-b1">
										<td valign="middle" class='checkbox_td'>
											<input type='checkbox' id='user_account' title='订单来源' />
										</td>
										<td valign="top" title="订单的来源(账号)">
											<label for="user_account">
												订单来源
											</label>
											<div style="display:none;">
												订单来源为
												<a href="javascript:void(0);" class="tdu">指定账号</a>
											</div>
										</td>
									</tr>
									<tr class="table-module-b1">
										<td valign="middle" class='checkbox_td'>
											<input type='checkbox' id='order_site' title='订单站点' />
										</td>
										<td valign="top" title="订单站点">
											<label for="order_site">
												订单站点
											</label>
											<div style="display:none;">
												订单站点为
												<a href="javascript:void(0);" class="tdu">指定站点</a>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
							<p>物流</p>
							<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
								<tbody>
									<tr class="table-module-b1">
										<td valign="middle" class='checkbox_td'>
											<input type='checkbox' id='shipping_method_platform' title='买家选择的运输类型' />
										</td>
										<td valign="top" title="买家选择的运输类型">
											<label for="shipping_method_platform">
												买家选择的运输类型
											</label>
											<div style="display:none;">
												买家选择的运输类型为
												<a href="javascript:void(0);" class="tdu">指定运输类型</a>
											</div>
										</td>
									</tr>
									<tr class="table-module-b1">
										<td valign="middle" class='checkbox_td'>
											<input type='checkbox' id='consignee_country' title='订单目的地' />
										</td>
										<td valign="top" title="订单的目的地国家">
											<label for="consignee_country">
												订单目的地
											</label>
											<div style="display:none;">
												订单目的地为
												<a href="javascript:void(0);" class="tdu">指定国家</a>
											</div>
										</td>
									</tr>
									<!-- 
									<tr class="table-module-b1">
										<td valign="middle" class='checkbox_td'>
											<input type='checkbox' id='consignee_region' title='订单收货地址省/州'/>
										</td>
										<td valign="top" title="订单收货地址省/州包含指定字符串">
											订单收货地址省/州包含
											<a href="javascript:void(0);" class="tdu">指定字符串</a>
										</td>
									</tr>
									<tr class="table-module-b1">
										<td valign="middle" class='checkbox_td'>
											<input type='checkbox' id='consignee_city' title='订单收货地址城市'/>
										</td>
										<td valign="top" title="订单收货地址城市包含指定字符串">
											订单收货地址城市包含
											<a href="javascript:void(0);" class="tdu">指定字符串</a>
										</td>
									</tr>
									<tr class="table-module-b1">
										<td valign="middle" class='checkbox_td'>
											<input type='checkbox' id='consignee_address' title='订单收货地址街道'/>
										</td>
										<td valign="top" title="订单收货地址街道包含指定字符串">
											订单收货地址街道包含
											<a href="javascript:void(0);" class="tdu">指定字符串</a>
										</td>
									</tr>
									<tr class="table-module-b1">
										<td valign="middle" class='checkbox_td'>
											<input type='checkbox' id='consignee_address_length' title='订单收货地址街道信息字符长度'/>
										</td>
										<td valign="top" title="订单收货地址街道信息字符长度小于指定长度">
											订单收货地址街道信息字符长度小于
											<a href="javascript:void(0);" class="tdu"> 指定长度</a>
										</td>
									</tr>
									<tr class="table-module-b1">
										<td valign="middle" class='checkbox_td'>
											<input type='checkbox' id='consignee_zip' title='订单收货邮编'/>
										</td>
										<td valign="top" title="订单收货邮编指定格式">
											订单收货邮编
											<a href="javascript:void(0);" class="tdu">指定格式</a>
										</td>
									</tr>
									<tr class="table-module-b1">
										<td valign="middle" class='checkbox_td'>
											<input type='checkbox' id='consignee_zip_length' title='订单收货邮编字符'/>
										</td>
										<td valign="top" title="订单收货邮编字符小于指定长度">
											订单收货邮编字符小于
											<a href="javascript:void(0);" class="tdu">指定长度</a>
										</td>
									</tr>
									 -->
								</tbody>
							</table>
							
							<p>金额</p>
							<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
								<tbody>
									<tr class="table-module-b1">
										<td valign="middle" class='checkbox_td'>
											<input type='checkbox' id='order_puchase' title='订单总金额' />
										</td>
										<td valign="top" title="订单金额范围">
											<label for="order_puchase">
												订单金额范围
											</label>
											<div style="display:none;">
												订单总金额
												<a href="javascript:void(0);" class="tdu">指定范围</a>
											</div>
										</td>
									</tr>
									<tr class="table-module-b1">
										<td valign="middle" class='checkbox_td'>
											<input type='checkbox' id='ship_fee' title='买家支付的运费' />
										</td>
										<td valign="top" title="买家支付的运费">
											<label for="ship_fee">
												买家支付的运费范围
											</label>
											<div style="display:none;">
												买家支付的运费
												<a href="javascript:void(0);" class="tdu">指定范围</a>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
							
							<!-- 
							<p>重量</p>
							<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
								<tbody>
							        <tr class="table-module-b1">
										<td valign="middle" class='checkbox_td'>
											<input type='checkbox' id='weight' title='订单重量' />
										</td>
										<td valign="top" title="订单重量指定范围">
											订单重量
											<a href="javascript:void(0);" class="tdu">指定范围</a>
										</td>
									</tr>
								</tbody>
							</table>
							 -->
							 <!-- -->
							<p>货品</p>
							<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
								<tbody>
									<tr class="table-module-b1">
										<td valign="middle" class='checkbox_td'>
											<input type='checkbox' id='product_sku' title='订单货品' />
										</td>
										<td valign="top" title="订单货品包含指定货品(SKU)">
											<label for="product_sku">
												订单包含某样货品(SKU)
											</label>
											<div style="display:none;">
												订单货品包含
												<a href="javascript:void(0);" class="tdu">指定货品(SKU)</a>
											</div>
										</td>
									</tr>
									<tr class="table-module-b1">
										<td valign="middle" class='checkbox_td'>
											<input type='checkbox' id='product_count' title='订单货品总数量' />
										</td>
										<td valign="top" title="订单货品总数量指定范围">
											<label for="product_count">
												订单货品总数量范围
											</label>
											<div style="display:none;">
												订单货品总数量
												<a href="javascript:void(0);" class="tdu">指定范围</a>
											</div>
										</td>
									</tr>
									
									<!-- 
									<tr class="table-module-b1">
										<td valign="middle" class='checkbox_td'>
											<input type='checkbox' id='product_sku_qty' title='订单货品包含指定货品且数量'/>
										</td>
										<td valign="top" title="订单货品包含指定货品且数量指定范围">
											订单货品包含指定货品且数量
											<a href="javascript:void(0);" class="tdu">指定范围</a>
										</td>
									</tr>
									
									<tr class="table-module-b1">
										<td valign="middle" class='checkbox_td'>
											<input type='checkbox' id='category' title='订单货品分类属于'/>
										</td>
										<td valign="top" title="订单货品属于指定分类">
											订单货品分类属于
											<a href="javascript:void(0);" class="tdu"> 指定分类</a>
										</td>
									</tr>
									 -->
								</tbody>
							</table>
							<!-- 
							<p>买家</p>
							<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
								<tbody>
									<tr class="table-module-b1">
										<td valign="middle" class='checkbox_td'>
											<input type='checkbox' id='negative_feedback' title='买家曾经给过我差评'/>
										</td>
										<td valign="top" title="买家曾经给过我差评">买家曾经给过我差评</td>
									</tr>
									<tr class="table-module-b1">
										<td valign="middle" class='checkbox_td'>
											<input type='checkbox' id='has_case' title='买家曾经发起过纠纷'/>
										</td>
										<td valign="top" title="买家曾经发起过纠纷">买家曾经发起过纠纷</td>
									</tr>
									<tr class="table-module-b1">
										<td valign="middle" class='checkbox_td'>
											<input type='checkbox' id='has_refound' title='买家曾经有过退款记录'/>
										</td>
										<td valign="top" title="买家曾经有过退款记录">买家曾经有过退款记录</td>
									</tr>
									<tr class="table-module-b1">
										<td valign="middle" class='checkbox_td'>
											<input type='checkbox' id='has_redelivery' title='买家曾经有过补发货记录'/>
										</td>
										<td valign="top" title="买家曾经有过补发货记录">买家曾经有过补发货记录</td>
									</tr>
								</tbody>
							</table>
							 -->
						</div>
					</td>
				</tr>
				<tr class="table-module-b1">
					<td height='120' valign='bottom'>
						<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
							<tbody>
								<tr class="table-module-title">
									<td>设定动作</td>
								</tr>
							</tbody>
							<tbody>
								<tr class="table-module-b1">
									<td valign="top">
										仓库:
										<select id="ship_warehouse" name="warehouse_id" style="width: 150px;" class="input_select">
											<option value="">请选择</option>
											<{foreach from=$warehouseArr name=o item=o}>
											<option value='<{$o.warehouse_id}>'><{$o.warehouse_code}>[<{$o.warehouse_desc}>]</option>
											<{/foreach}>
										</select>
										&nbsp;&nbsp;运输方式:
										<select name="shipping_method" id='shipping_method' style="width: 150px;" class="input_select">
											<option value="">请先选择仓库</option>
										</select>
										&nbsp;&nbsp;优先级:
										<select name='allot_level' id='allot_level' title='越大优先级越高' class="input_select">
										    <{assign var='count' value=50}>
                    					    <{section name=loop loop=$count}>
                                                <option value='<{$smarty.section.loop.index}>'><{$smarty.section.loop.index}></option>
                                            <{/section}> 	
										</select>
										<span style="float:right; display: block;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;width: 13%;cursor: pointer;" title="数值越大优先级越高">数值越大优先级越高</span>
									</td>
								</tr>
								<tr class="table-module-b1">
									<td valign="top">
										规则名称:*
										<input type="text" placeholder="请输入规则名称" style="width: 400px;" name="allot_set_name" class="input_text " id='allot_set_name'>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
		<input type='hidden' name='allot_set_id' value='' id='allot_set_id' />
	</form>
</div>
<div id='user_account_div' title='选择订单来源账号' class='dialog_div'>
	<ul style='max-height:400px;overflow:auto;'>
		<{foreach from=$platUser name=o item=o}>
		<li val='<{$o.user_account}>' class='li_<{$o.user_account}>'>[<{$o.platform}>]<{$o.user_account}>[<{$o.platform_user_name}>]</li>
		<{/foreach}>
	</ul>
</div>

<div id='order_site_div' title='选择订单站点来源' class='dialog_div'>
	<ul style='max-height:400px;overflow:auto;'>
		<{foreach from=$sites name=ob item=ob}>
			<!-- <option value='<{$ob.site_code}>'>[<{$ob.platform}>]<{$ob.site_name_cn}></option> -->
			<li val='<{$ob.site_code}>' class='li_<{$ob.site_code}>'>[<{$ob.platform}>]<{$ob.site_name_cn}></li>
		<{/foreach}>
	</ul>
</div>
<div id='shipping_method_platform_div' title='选择买家选择的运输类型' class='dialog_div'>
	<ul style='max-height:400px;overflow:auto;'>
		<{foreach from=$shippingMethodPlatform name=o item=o}>
		<li val='<{$o.shipping_method_code}>' class='li_<{$o.shipping_method_code}>'>[<{$o.platform}>]<{$o.shipping_method_code}></li>
		<{/foreach}>
	</ul>
</div>
<div id='consignee_country_div' title='选择订单目的国家' class='dialog_div'>
	<ul style='max-height:400px;overflow:auto;'>
		<{foreach from=$country name=o item=o}>
		<li val='<{$o.country_code}>' class='li_<{$o.country_code}>'><{$o.country_code}>[<{$o.country_name_en}>][<{$o.country_name}>]</li>
		<{/foreach}>
	</ul>
</div>
<div id='order_puchase_div' title='订单总金额范围' class='dialog_div'>
    <select id='purchase_from_type' name='purchase_from_type'  class='input_text_select input_select'>
	    <option value='ge'>大于等于</option>
	    <option value='gt'>大于</option>
	</select>
	<input type="text" class="input_text " name="purchase_from" id='purchase_from' style='width:50px;'>
	
	<select id='purchase_to_type' name='purchase_to_type' class='input_text_select input_select'>
	    <option value='le'>小于等于</option>
	    <option value='lt'>小于</option>
	</select>
	<input type="text" class="input_text " name="purchase_to" id='purchase_to' style='width:50px;'>
	<select id='purchase_currency' name='purchase_currency' class='input_text_select input_select'>
	   <{foreach from=$currency name=o item=o}>
		<option value='<{$o.currency_code}>'><{$o.currency_code}>[<{$o.currency_name}>]</option>
   		<{/foreach}>
	</select>
</div>
<div id='ship_fee_div' title='买家支付的运费范围' class='dialog_div'>
    <select id='shipfee_from_type' name='shipfee_from_type'  class='input_text_select input_select'>
	    <option value='ge'>大于等于</option>
	    <option value='gt'>大于</option>
	</select>
	<input type="text" class="input_text " name="shipfee_from" id='shipfee_from' style='width:50px;'>
	
	<select id='shipfee_to_type' name='shipfee_to_type' class='input_text_select input_select'>
	    <option value='le'>小于等于</option>
	    <option value='lt'>小于</option>
	</select>
	<input type="text" class="input_text " name="shipfee_to" id='shipfee_to' style='width:50px;'>
	<select id='shipfee_currency' name='shipfee_currency' class='input_text_select input_select'>
	    <{foreach from=$currency name=o item=o}>
		<option value='<{$o.currency_code}>'><{$o.currency_code}>[<{$o.currency_name}>]</option>
		<{/foreach}>
	</select>
</div>
<div id='product_sku_div' title='订单货品包含SKU' class='dialog_div'>
		<form class="submitReturnFalse" name="searchForm" id="productSearchForm">
			<div class="block ">
				<table cellspacing="3" cellpadding="3">
					<tbody>
						<tr>
							<th>
							<select name='search_type' id='sku_search_type' class="input_select">
							    <option value='0' p='SKU模糊搜索'>模糊搜索</option>
							    <option value='1' p='可输入多个sku，每个sku以空格或者逗号隔开'>SKU精确搜索</option>
							</select>:</th>
							<td>
								<input type="text" class="" value="" name="product_sku" id='search_product_sku' size='40' style='width: 300px;' placeholder='SKU模糊搜索'>
								&nbsp;
								<input type="submit" class="baseBtn searchProductBtn submitToSearch" value="Search">
								<span class='checked_count' style='padding:5px 10px;float:right;'></span>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</form>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td width='100'>SKU</td>
					<td>SKU Name</td>
					<td>Category</td>
					<td width='30'><input type="checkbox" class="checkAll"></td>
				</tr>
			</tbody>
			<tbody class='table-module-list-data'>
			</tbody>
		</table>
		<div class="pagination"></div>
</div>
<div id='product_count_div' title='订单货品总数量指定范围' class='dialog_div'>	
	<select id='qty_from_type' name='qty_from_type'  class='input_text_select input_select'>
	    <option value='ge'>大于等于</option>
	    <option value='gt'>大于</option>
	</select>
	<input type="text" class="input_text " name="qty_from" id='qty_from' style='width:50px;'>
	
	<select id='qty_to_type' name='qty_to_type' class='input_text_select input_select'>
	    <option value='le'>小于等于</option>
	    <option value='lt'>小于</option>
	</select>
	<input type="text" class="input_text " name="qty_to" id='qty_to' style='width:50px;'>
</div>

<div id='weight_div' title='订单重量范围' class='dialog_div'>	
	<select id='weight_from_type' name='weight_from_type'  class='input_text_select input_select'>
	    <option value='ge'>大于等于</option>
	    <option value='gt'>大于</option>
	</select>
	<input type="text" class="input_text " name="weight_from" id='weight_from' style='width:50px;'>
	
	<select id='weight_to_type' name='weight_to_type' class='input_text_select input_select'>
	    <option value='le'>小于等于</option>
	    <option value='lt'>小于</option>
	</select>
	<input type="text" class="input_text " name="weight_to" id='weight_to' style='width:50px;'> KG
</div>
