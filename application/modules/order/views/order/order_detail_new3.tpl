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
.disabled{background:#dddddd;}
.depotState h2{display:none;}
.tabContent{padding:5px 10px;}
.table-module{ background: none repeat scroll 0 0 #FFFFFF;}
-->
</style>
<script type="text/javascript">
<{include file='order/js/order/order_detail_new3.js'}>
</script>
<div id='loading' title='操作提示'>Loading...</div>
<div id="module-container" style='padding:0 5px;'>
	<div id='search-module' style='display: none;' title='Product List&nbsp;&nbsp;[each SKU separate with space or ","]'>
		<form class="submitReturnFalse" name="searchForm" id="searchForm">
			<div class="block ">
				<table cellspacing="3" cellpadding="3">
					<tbody>
						<tr>
							<th>SKUs:</th>
							<td>
								<input type="text" class="" value="" name="product_sku" id='product_sku' size='80' style='width: 550px;' placeholder='可输入多个sku，每个sku以空格或者逗号隔开'>
								&nbsp;
								<input type="submit" class="baseBtn searchProductBtn submitToSearch" value="Search">
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</form>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td>SKU</td>
					<td>SKU Name</td>
					<td>Category</td>
					<td>操作</td>
				</tr>
			</tbody>
			<tbody id="table-module-list-data" class='table-module-list-data'>
			</tbody>
		</table>
		<div class="pagination"></div>
	</div>
	<div id="module-table">
		<form id='order_form' onsubmit='return false;'>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
				<tbody>
					<tr class="table-module-title">
						<td colspan='8'>
							订单基本信息
							<a id="logBtn" href='javascript:;' style='margin-left: 10px;'>订单操作日志</a>
							<{if $view==''&&($order.order_status=='2'||$order.order_status=='7'||$order.order_status=='5')}>
							<input type='hidden' name='order_id' value='<{$order.order_id}>' />
							<input type="button" class="baseBtn btnRight" id="changeBtn" value="修改地址" />
							<input type="submit" class="baseBtn btnRight orderSubmitBtn" id="orderSubmitBtn" value="保存修改">
							<{if $order.order_status==5||$order.order_status==7}>
							<input type="submit" class="baseBtn btnRight orderSubmitBtn" id="orderSubmitToVerifyBtn" value="保存并转待发货审核">
							<{/if}> <{/if}>
						</td>
					</tr>
				</tbody>
			</table>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
				<tbody class="table-module-list-data orderInfo">
					<tr class="table-module-b1" style='height: 35px;'>
						<th width='150'>订单编号</th>
						<td><{$order.refrence_no_platform}></td>
						<th width='150'>买家账号/买家姓名</th>
						<td><{$order.buyer_id}> / <{$order.buyer_name}></td>
						<th width='100'>卖家账号</th>
						<td><{$order.user_account}></td>
						<th width='100'>目的国家</th>
						<td>
							<span class='span_text'><{$order.address.Country}>|<{$order.address.CountryName}></span>
							<select class='input_change input_text' name='CountryName' style='width: auto;'>
								<{foreach from=$countryArr name=ob item=ob}>
								<option value='<{$ob.country_code}>|<{$ob.country_name_en}>'<{if $order.address.Country==$ob.country_code}>selected<{/if}>><{$ob.country_code}>[<{$ob.country_name}>]</option>
								<{/foreach}>
							</select>
						</td>
					</tr>
					<tr class="table-module-b1" style='height: 35px;'>
						<th>订单总金额(含运费)</th>
						<td><{$order.amountpaid}></td>
						<th>交易额/运费</th>
						<td>
							<{$order.subtotal}> /
							<span class='span_text1'><{$order.ship_fee}></span>
						</td>
						<th>手续费</th>
						<td>
							<span class='span_text1'><{$order.platform_fee}></span>
						</td>
						<th>成交费</th>
						<td>
							<span class='span_text1'><{$order.finalvaluefee}></span>
						</td>
					</tr>
					<tr class="table-module-b1" style='height: 35px;'>
						<th>省份/州</th>
						<td>
							<span class='span_text'><{$order.address.StateOrProvince}></span>
							<input type='text' class='input_text input_change' name='StateOrProvince' value='<{$order.address.StateOrProvince}>' />
							<input type='hidden' class='' name='ShippingAddress_Id' value='<{$order.address.ShippingAddress_Id}>' />
						</td>
						<th>城市</th>
						<td>
							<span class='span_text'><{$order.address.CityName}></span>
							<input type='text' class='input_text input_change' name='CityName' value='<{$order.address.CityName}>' />
						</td>
						<th>地址</th>
						<td>
							<span class='span_text'><{$order.address.Street1}></span>
							<input type='text' class='input_text input_change' name='Street1' value='<{$order.address.Street1}>' />
							<span class='span_text'><{$order.address.Street2}> <{$order.address.Street3}></span>
							<input type='text' class='input_text input_change' name='Street2' value='<{$order.address.Street2}> <{$order.address.Street3}>' />
						</td>
						<th>收件人</th>
						<td>
							<span class='span_text'><{$order.address.Name}></span>
							<input type='text' class='input_text input_change' name='Name' value='<{$order.address.Name}>' />
						</td>
					</tr>
					<tr class="table-module-b2" style='height: 35px;'>
						<th>邮编</th>
						<td>
							<span class='span_text'><{$order.address.PostalCode}></span>
							<input type='text' class='input_text input_change' name='PostalCode' value='<{$order.address.PostalCode}>' />
						</td>
						<th>站点</th>
						<td><{$order.site}></td>
						<th>发货仓库</th>
						<td><{if isset($order.warehouse_name)}><{$order.warehouse_name}><{else}>未分配发货仓库<{/if}></td>
						<th>重量</th>
						<td><{if isset($order.order_weight)}><{$order.order_weight}><{else}>未称重<{/if}></td>
					</tr>
					<tr class="table-module-b2" style='height: 35px;'>
						<th>创建时间</th>
						<td><{$order.date_create_platform}></td>
						<th>付款时间</th>
						<td><{$order.date_paid_platform}></td>
						<th>出库时间</th>
						<td><{$order.date_warehouse_shipping}></td>
						<th>Email</th>
						<td>
						    <span class='span_text'><{$order.buyer_mail}></span>
							<input type='text' class='input_text input_change' name='Email' value='<{$order.buyer_mail}>' />
							
						</td>
					</tr>
					<tr class="table-module-b1" style='height: 35px;'>
						<th>运输方式</th>
						<td><{$order.shipping_method}></td>
						<th>参考号</th>
						<td><{$order.refrence_no}></td>
						<th>仓库单号</th>
						<td><{$order.refrence_no_warehouse}></td>
						<th>跟踪号</th>
						<td><{$order.shipping_method_no}></td>
					</tr>
					<tr class="table-module-b2" style='height: 35px;'>
						<th>订单备注</th>
						<td colspan='7'><{$order.order_desc}></td>
					</tr>
				</tbody>
			</table>
		</form>
		<form id='order_product_form' onsubmit='return false;'>
		    <input type='hidden' name='order_id' value='<{$order.order_id}>' />
			<table width="100%" cellspacing="0" cellpadding="0" border="0" style='margin-top: 10px;' class="table-module">
				<tbody>
					<tr class="table-module-title">
						<td>图片</td>
						<td>SKU</td>
						<td>产品Title</td>
						<td>数量</td>
						<td>单价</td>
						<td width='250'>
							<input type="button" id="addProductLine" value="添加产品" disabled="disabled" style='float: right;'>
							
							<input type="button" class="baseBtn btnRight" id="changeOrderProductBtn" value="修改SKU" />
							<input type="submit" class="baseBtn btnRight orderProductSubmitBtn" id="orderProductSubmitBtn" value="保存">
							<{if $order.order_status==5||$order.order_status==7}>
							<input type="submit" class="baseBtn btnRight orderProductSubmitToVerifyBtn" id="orderProductSubmitToVerifyBtn" value="保存并转待发货审核">
							<{/if}>
						</td>
					</tr>
				</tbody>
				<tbody id="table-module-list-data" class='orderInfo'>
					<{foreach from=$order.order_product_mult item=op name=op}>
					<{if $op.give_up=='1'}> 
					<{assign var="disabled" value="disabled"}> 
					<{assign var="count" value="1"}> 
					<{assign var="stopText" value="取消不发"}> 
					<{assign var="productCancel" value='&nbsp;&nbsp;&nbsp;&nbsp;该产品不发'}> 
					<{else}> 
					<{assign var="disabled" value=""}> 
					<{assign var="count" value="0"}> 
					<{assign var="stopText" value="不发"}> 
					<{assign var="productCancel" value=""}> 
					<{/if}>
					<tr class='table-module-b1 tr_<{$op.product_sku}>'>
						<td>
							<img width="75" height="75" src="/product/product/eb-img/item_id/<{$op.op_ref_item_id}>">
						</td>
						<td>
							<span class='span_text'><{$op.product_sku}></span>
							<input <{$disabled}> type=text class='input_text input_change product_sku <{$disabled}>' name='product_sku_mult[<{$op.product_sku}>]' value='<{$op.product_sku}>' id='product_sku_<{$op.product_type}>_<{$op.product_sku}>' />
						</td>
						<td id='op_title_<{$op.product_sku}>'>
							<span><{$op.product_title}></span>
						</td>
						<td>
							<span class='span_text'><{$op.op_quantity}></span>
							<input <{$disabled}> type=text class='input_text input_change <{$disabled}>' name='op_quantity_mult[<{$op.product_sku}>]' value='<{$op.op_quantity}>' id='op_quantity_<{$op.product_type}>_<{$op.product_sku}>' />
						</td>
						<td  title='单价/单个手续费/单个成交费:<{$op.unit_price}>/<{$op.unit_platformfee}>/<{$op.unit_finalvaluefee}>'><{$op.unit_price}> <{$order.currency}></td>
						<td>
							
							<a href='javascript:;' class='stopBtn' key='<{$op.product_sku}>' style='display: none;'  count='<{$count}>'><{$stopText}></a>
							<span style='color:red;'><{$productCancel}></span>
							<!-- 
							<{$op.create_type}>
							 -->
						</td>
						<!-- -->
					</tr>
					<{/foreach}> <{foreach from=$order.order_product_single item=op name=op}> 
					<{if $op.give_up=='1'}> 
					<{assign var="disabled" value="disabled"}> 
					<{assign var="count" value="1"}> 
					<{assign var="stopText" value="取消不发"}> 
					<{assign var="productCancel" value='&nbsp;&nbsp;&nbsp;&nbsp;该产品不发'}> 
					<{else}> 
					<{assign var="disabled" value=""}> 
					<{assign var="count" value="0"}> 
					<{assign var="stopText" value="不发"}> 
					<{assign var="productCancel" value=""}> 
					<{/if}>
					<tr class='table-module-b1 tr_<{$op.op_id}>'>
						<td>
							<img width="75" height="75" src="/product/product/eb-img/item_id/<{$op.op_ref_item_id}>">
						</td>
						<td>
							<span class='span_text'><{$op.product_sku}></span>
							<input type='text'
							<{$disabled}> class='input_text input_change product_sku <{$disabled}>' name='product_sku_single[<{$op.op_id}>]' value='<{$op.product_sku}>' id='product_sku_<{$op.product_type}>_<{$op.product_sku}>' />
						</td>
						<td id='op_title_<{$op.op_id}>'>
							<span><{$op.product_title}></span>
						</td>
						<td>
							<span class='span_text'><{$op.op_quantity}></span>
							<input type='text'
							<{$disabled}> class='input_text input_change <{$disabled}>' name='op_quantity_single[<{$op.op_id}>]' value='<{$op.op_quantity}>' id='op_quantity_<{$op.product_type}>_<{$op.product_sku}>' />
						</td>
						<td title='单价/单个手续费/单个成交费:<{$op.unit_price}>/<{$op.unit_platformfee}>/<{$op.unit_finalvaluefee}>'><{$op.unit_price}> <{$order.currency}></td>
						<td>
							<{if $op.create_type=='hand'}>
							<a href='javascript:;' class='delBtn' key='<{$op.op_id}>' style='display: none;'>删除</a>
							<{else}>
							<a href='javascript:;' class='stopBtn' key='<{$op.op_id}>' style='display: none;' count='<{$count}>'><{$stopText}></a>
							<{/if}>
							<span style='color:red;'><{$productCancel}></span>
							<!-- 
							<{$op.create_type}>
							 -->
						</td>
						<!-- -->
					</tr>
					<{/foreach}>
				</tbody>
			</table>
		</form>
		<div style='color:red;font-weight:bold;line-height:25px;'>
			说明    ：
			<ul style=' list-style-type: decimal;padding-left: 20px;'>
			    <li>如果需要换货，请修改对应的SKU</li>			    
			    <li>新添加的产品，默认是作为赠品处理，无价格，不计费。<br />如果某个订单客户线下需要新增某个产品，需要将新增的产品，建立一个线下订单，然后将两个订单做合并处理<br />通过以上操作，确保订单费用的准确，便于报表统计费用</li>
			    <li>非赠品，不可删除</li>
			</ul>
		</div>
	</div>
</div>
<div class='depotState' style='border: 1px solid #91BCDF;margin-top:0px;'>    
   <div class="depotTab2">
    	<ul>
    		<li class="tab" id="tab_order" fun='get-customer-order'>
    			<a style="cursor: pointer" href="javascript:void(0);">历史订单</a>
    		</li>
    		<li class="tab" id="tab_message" fun='get-customer-message'>
    			<a style="cursor: pointer" href="javascript:void(0);">邮件</a>
    		</li>
    		<li class="tab" id="tab_case" fun='get-customer-case'>
    			<a style="cursor: pointer" href="javascript:void(0);">CASE</a>
    		</li>
    		<li class="tab" id="tab_feedback" fun='get-customer-feedback'>
    			<a style="cursor: pointer" href="javascript:void(0);">评价</a>
    		</li>
    	</ul>
    </div>
    <div class='tabContent' id='tabContent_order'>
	  	<h2>历史订单</h2>	
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td>订单号</td>
					<td>交易额</td>
					<td>运费</td>
					<td>平台费</td>
					<td>成交费</td>
					<td>状态</td>
					<td>创建日期</td>
				</tr>
			</tbody>
			<tbody class='table-module-list-data'>
				
			</tbody>
		</table>
    </div>
    <div class='tabContent' id='tabContent_message'>
    	<h2>Message</h2>
    	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
    		<tbody>
    			<tr class="table-module-title">
    				<td width='100'>收件人</td>
    				<td>标题</td>
    				<td width='150'>消息类型</td>
    				<td width='120'>发送时间</td>
    			</tr>
    		</tbody>
    		<tbody class='table-module-list-data'>
    			
    		</tbody>
    	</table>
    </div>
    <div class='tabContent' id='tabContent_case'>
    	<h2>Case</h2>
    	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
    		<tbody>
    			<tr class="table-module-title">
    				<td>类型</td>
    				<td>物品</td>
    				<td>状态</td>
    				<td>发起时间</td>
    			</tr>
    		</tbody>
    		<tbody class='table-module-list-data'>
    			
    		</tbody>
    	</table>
    </div>
    <div class='tabContent' id='tabContent_feedback'>
    	<h2>评价</h2>
    	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
    		<tbody>
    			<tr class="table-module-title">
    				<td>产品信息</td>
    				<td>买家评价</td>
    				<td width='120'>评价类型</td>
    				<td width='120'>评价时间</td>
    			</tr>
    		</tbody>
    		<tbody class='table-module-list-data'>
    			
    		</tbody>
    	</table>
    </div>
</div>
<div class='pagination'></div>