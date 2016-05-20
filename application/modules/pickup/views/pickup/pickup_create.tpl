<link type="text/css" rel="stylesheet" href="/css/public/layout.css" />
<style>
<!--
.input_text {
	
}

.hide {
	display: none;
}

#loading {
	display: none;
}

#search-module {
	overflow: auto;
}

.msg {
	color: red;
	padding: 0 5px;
}

.module-title {
	text-align: right;
}

.info .Validform_wrong {
	background: url("/images/error.png") no-repeat scroll left center
		rgba(0, 0, 0, 0);
	color: red;
	padding-left: 20px;
	white-space: nowrap;
}

.Validform_checktip {
	color: #999;
	font-size: 12px;
	height: 20px;
	line-height: 20px;
	margin-left: 8px;
	overflow: hidden;
}

.Validform_checktip {
	margin-left: 0;
}

.info {
	border: 1px solid #ccc;
	padding: 2px 20px 2px 5px;
	color: #666;
	position: absolute;
	margin-top: -32px;
	margin-left: 125px;
	display: none;
	line-height: 20px;
	background-color: #fff;
	float: left;
}

.dec {
	bottom: -8px;
	display: block;
	height: 8px;
	overflow: hidden;
	position: absolute;
	left: 10px;
	width: 17px;
}

.dec s {
	font-family: simsun;
	font-size: 16px;
	height: 19px;
	left: 0;
	line-height: 21px;
	position: absolute;
	text-decoration: none;
	top: -9px;
	width: 17px;
}

.dec .dec1 {
	color: #ccc;
}

.dec .dec2 {
	color: #fff;
	top: -10px;
}
-->
</style>
<script type="text/javascript">
<{include file='pickup/js/pickup/pickup_create.js'}>
</script>
<div id='loading'>Loading...</div>
<div id="module-container">

	<div id="module-table" style='clear: both; padding: 10px 20px;'>
		<div class="feedback_tips">
			<div class="feedback_tips_text">
				<p id="create_feedback_div"></p>
			</div>
			<div style="clear: both;"></div>
		</div>
		<form method='POST' action='' onsubmit='return false;' id='orderForm'>
			<h2
				style='padding: 5px 0 0 0; margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC;'>
				1、<span class='submiter_wrap'><{t}>提货地址<{/t}></span><span
					class="msg">*</span> <a href='javascript:;'
					style='font-weight: normal; font-size: 12px; padding: 0 10px;'
					onclick='getUserAddress();'><{t}>刷新<{/t}></a>
			</h2>
			<div style='line-height: 22px;' id='submiter_wrap'></div>

			<h2
				style='padding: 15px 0 0 0; margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC;'>
				2、<{t}>货物信息<{/t}><span class="msg">*</span>
			</h2>
			<table cellspacing="0" cellpadding="0" width="100%" border="0"
				class="table-module">
				<tbody>
					<tr class="table-module-title">
						<td width="150"><{t}>产品<{/t}></td>
						<td width="150"><{t}>数量<{/t}></td>
						<td width="150"><{t}>重量<{/t}>(KG)</td>
						<td width="100"><{t}>操作<{/t}></td>
					</tr>
				</tbody>
				<tbody id="products" class="table-module-list-data">
					<{if $detail}> <{foreach from=$detail name=i item=i}>
					<tr class="table-module-b1">
						<td><select
							default="<{if $i.product_code}><{$i.product_code}><{else}><{/if}>"
							name="detail[product_code][]" class="input_select product_code">
								<{foreach from=$productKind item=c name=c}>
								<option value='<{$c.product_code}>'><{$c.product_code}>
									[<{$c.product_cnname}> <{$c.product_enname}>]</option>
								<{/foreach}>
						</select> <span class="msg"></span></td>
						<td><input type='text' class='input_text pieces'
							name='detail[pieces][]' value='<{$i.pieces}>'> <span class="msg">*</span>
						</td>
						<td><input type='text' class='input_text weight'
							name='detail[weight][]' value='<{$i.weight}>'> <span class="msg">*</span>
						</td>

						<td><a href='javascript:;' class='delInvoiceBtn'><{t}>delete<{/t}></a>
						</td>
					</tr>
					<{/foreach}> <{else}>
					
					<{/if}>
				</tbody>
			</table>
			<div>
				<a href='javascript:;'
					style='font-weight: normal; font-size: 12px; line-height: 35px;'
					class='addInvoiceBtn'><{t}>点击添加<{/t}></a>
			</div>
			<table width="100%" cellspacing="0" cellpadding="0" border="0"
				class="table-module" style='margin-top: 10px;'>
				<tbody class="table-module-list-data">


					<tr class=''>
						<td class="module-title"><{t}>总袋数<{/t}>：</td>
						<td>
							<div class="info"
								style="display: block; color: red; display: none;">
								<span class="Validform_checktip Validform_wrong">须为整数</span> <span
									class="dec"> <s class="dec1">◆</s> <s class="dec2">◆</s>
								</span>
							</div> <input type='text' class='input_text bags'
							value='<{if isset($order)}><{$order.bags}><{/if}>' name='bags'
							id='bags' /> <span class="msg"></span>
						</td>
						<td class="module-title"><{t}>总票数<{/t}>：</td>
						<td>
							<div class="info"
								style="display: block; color: red; display: none;">
								<span class="Validform_checktip Validform_wrong">须为整数</span> <span
									class="dec"> <s class="dec1">◆</s> <s class="dec2">◆</s>
								</span>
							</div> <input type='text' class='input_text pieces'
							value='<{if isset($order)}><{$order.pieces}><{/if}>'
							name='pieces' id='pieces' /> <span class="msg"></span>

						</td>
						<td class="module-title"><{t}>总重量<{/t}>：</td>
						<td>
							<div class="info"
								style="display: block; color: red; display: none;">
								<span class="Validform_checktip Validform_wrong">须为数字</span> <span
									class="dec"> <s class="dec1">◆</s> <s class="dec2">◆</s>
								</span>
							</div> <input type='text' class='input_text weight'
							value='<{if isset($order)}><{$order.weight}><{/if}>'
							name='weight' id='weight' /> (KG) <span class="msg"></span>

						</td>
						<td class="module-title"><{t}>参考提货时间<{/t}>：</td>
						<td>
							<div class="info"
								style="display: block; color: red; display: none;">
								<span class="Validform_checktip Validform_wrong">须为整数</span> <span
									class="dec"> <s class="dec1">◆</s> <s class="dec2">◆</s>
								</span>
							</div> <select id='pickup_server_id' name='pickup_server_id'
							default='<{if isset($order)&&$order.pickup_server_id}><{$order.pickup_server_id}><{/if}>'></select>

							<span class="msg"></span>

						</td>
					</tr>

				</tbody>
			</table>


			<input type="hidden" name='pickup_order_id'
				value='<{if isset($order)&&$order.pickup_order_id}><{$order.pickup_order_id}><{/if}>'
				id='pickup_order_id'>
			<div
				style="width: 100%; text-align: center; margin-top: 15px; padding-top: 5px; border-top: 1px dashed #CCCCCC;">

				<input type='button' value='<{t}>提交申请<{/t}>'
					class='baseBtn submitBtn orderSubmitVerifyBtn orderSubmitBtn'
					status='P' style="padding: 5px 30px; margin: 5px;" />

			</div>
		</form>
	</div>
</div>
<div id="tr_tpl" style='display: none;'>
	<table>
		<tr>
			<td>
				<div class="info" style="display: block; color: red; display: none;">
					<span class="Validform_checktip Validform_wrong">必选</span> <span
						class="dec"> <s class="dec1">◆</s> <s class="dec2">◆</s>
					</span>
				</div> <select default="PCE" name="detail[product_code][]"
				class="input_select product_code"> <{foreach from=$productKind item=c
					name=c}>
					<option value='<{$c.product_code}>'><{$c.product_code}>
						[<{$c.product_cnname}> <{$c.product_enname}>]</option>
					<{/foreach}>
			</select> <span class="msg"></span>
			</td>
			<td>
				<div class="info" style="display: block; color: red; display: none;">
					<span class="Validform_checktip Validform_wrong">须为整数</span> <span
						class="dec"> <s class="dec1">◆</s> <s class="dec2">◆</s>
					</span>
				</div> <input type='text' class='input_text pieces'
				name='detail[pieces][]'> <span class="msg"></span>
			</td>
			<td>
				<div class="info" style="display: block; color: red; display: none;">
					<span class="Validform_checktip Validform_wrong">须为数字</span> <span
						class="dec"> <s class="dec1">◆</s> <s class="dec2">◆</s>
					</span>
				</div> <input type='text' class='input_text weight'
				name='detail[weight][]'> <span class="msg"></span>
			</td>

			<td><a href='javascript:;' class='delInvoiceBtn'><{t}>delete<{/t}></a>
			</td>
		</tr>
	</table>
</div>