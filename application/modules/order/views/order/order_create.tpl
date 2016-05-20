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
	margin-left: 10px;
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

.input_text {
	width: 135px;
}

.quantity {
	width: 26px;
}

.invoice_unitcharge {
	width: 35px;
}
.weight{
     width:30px;
}
-->
</style>
<script>
$(function(){
	//alert(0);
	//$('#country_code').chosen();
});

</script>

<script type="text/javascript">
<{include file='order/js/order/order_create.js'}>
</script>
<div id='loading'>Loading...</div>
<div id="module-container">
	<div class="Tab">
		<ul>
			<li id="normal" style="position: relative;"
				class="mainStatus chooseTag"><a href="javascript:;"
				class="statusTag "
				onclick="leftMenu('order_create','<{t}>单票录入<{/t}>','/order/order/create?quick=52')">
					<span class="order_title"><{t}>单票录入<{/t}></span>
			</a></li>
			<li id="abnormal" style="position: relative;" class="mainStatus "><a
				href="javascript:;" class="statusTag "
				onclick="leftMenu('order_import','<{t}>批量上传<{/t}>','/order/order/import?quick=24')">
					<span class="order_title"><{t}>批量上传<{/t}></span>
			</a></li>
		</ul>
	</div>
	<div id="module-table" style='clear: both; padding: 10px 20px;'>
		<div class="feedback_tips">
			<div class="feedback_tips_text">
				<p id="create_feedback_div"></p>
			</div>
			<div style="clear: both;"></div>
		</div>
		<form method='POST' action='' onsubmit='return false;' id='orderForm'>
			<h2 style='margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC;'>1、<{t}>基本信息<{/t}></h2>
			<table width="100%" cellspacing="0" cellpadding="0" border="0"
				class="table-module" style='margin-top: 10px;'>
				<tbody class="table-module-list-data">
					<tr class=''>
						<td width="150" class="module-title"><{t}>运输方式<{/t}>：</td>
						<td width="480"><select class='input_select product_code'
							name='order[product_code]'
							default='<{if isset($order)}><{$order.product_code}><{/if}>'
							id='product_code'>
								<option value='' class='ALL'><{t}>-select-<{/t}></option>
								<{foreach from=$productKind item=c name=c}>
								<option value='<{$c.product_code}>'><{$c.product_code}>
									[<{$c.product_cnname}> <{$c.product_enname}>]</option>
								<{/foreach}>
						</select> <span class="msg">*</span></td>
						<td width="150" class="module-title"><{t}>consignee_country<{/t}>：</td>
						<td><select class='input_select country_code'
							name='order[country_code]'
							default='<{if isset($order)}><{$order.country_code}><{/if}>'
							id='country_code' style='width: 400px; max-width: 400px;'>
								<option value='' class='ALL'><{t}>-select-<{/t}></option>
								<!-- 
								<{foreach from=$country item=c name=c}>
								<option value='<{$c.country_code}>' country_id='<{$c.country_id}>' class='<{$c.country_code}>'><{$c.country_code}> [<{$c.country_name}>  <{$c.country_name_en}>]</option>
								<{/foreach}>
								 -->
						</select> <span class="msg">*</span></td>
					</tr>
					<tr class=''>
						<td class="module-title"><{t}>客户单号<{/t}>：</td>
						<td><input type='text' style='width: 220px;'
							class='input_text refer_hawbcode'
							value='<{if isset($order)}><{$order.refer_hawbcode}><{/if}>'
							name='order[refer_hawbcode]' id='refer_hawbcode' /> <span
							class="msg">*</span></td>
						<td class="module-title"><{t}>货物重量<{/t}>：</td>
						<td>
							<div class="info"
								style="display: block; color: red; display: none;">
								<span class="Validform_checktip Validform_wrong">须为整数</span> <span
									class="dec"> <s class="dec1">◆</s> <s class="dec2">◆</s>
								</span>
							</div> <input type='text' class='input_text weight'
							value='<{if isset($order)}><{$order.order_weight}><{/if}>'
							name='order[order_weight]' id='order_weight' /> (KG) <span
							class="msg">*</span>

						</td>
					</tr>
					<tr class=''>
						<td class="module-title"><{t}>外包装件数<{/t}>：</td>
						<td><input type='text' class='input_text quantity'
							value='<{if isset($order)}><{$order.order_pieces}><{else}>1<{/if}>'
							name='order[order_pieces]' id='order_pieces' /> <span class="msg">*</span>
							
							
						</td>
						
						<td class="module-title"><{t}>附加服务<{/t}>：</td>
						<td><span class='product_extraservice_wrap'
							id='product_extraservice_wrap'> <!-- 
    							<label><input type='checkbox' class='' value='A1' name='extraservice[]' /><{t}>购买保险<{/t}></label> 
    							<label><input type='checkbox' class='' value='11' name='extraservice[]' /><{t}>支持退件<{/t}></label>
    							 -->
						</span> <!-- --> <span class="msg"></span> <span
							id='insurance_value_div' style='display: none;'><{t}>投保金额<{/t}>：<input
								type='text' class='input_text insurance_value'
								value='<{if isset($order)}><{$order.insurance_value}><{else}><{/if}>'
								name='order[insurance_value]' id='insurance_value' disabled
								style='width: 30px;' /> USD
						</span></td>
					</tr>
					<tr>
					<td class="module-title">体积：</td>
						<td colspan="3">
						<span class=""> 长：</span>
							<input type='text' name='order[order_length]' id='order_length' class='input_text order_volume' 
							value='<{if isset($order)}><{$order.length}><{/if}>' style="width:30px;"/>CM
							<span
							class="msg">*</span>
							<span class="">宽：</span>
							<input type='text' name='order[order_width]' id='order_width'class='input_text order_volume' 
							value='<{if isset($order)}><{$order.width}><{/if}>'style="width:30px;"/>CM
							<span
							class="msg">*</span>
							<span class="">高：</span>
							<input type='text' name='order[order_height]'id='order_height'class='input_text order_volume'
							value='<{if isset($order)}><{$order.height}><{/if}>'style="width:30px;"/>CM
						    <span
							class="msg">*</span>
						</td>
						
					</tr>
					
				</tbody>
			</table>
			<h2
				style='padding: 35px 0 0 0; margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC;'>2、<{t}>consignee_info<{/t}></h2>
			<table width="100%" cellspacing="0" cellpadding="0" border="0"
				class="table-module" style='margin-top: 10px;'>
				<tbody class="table-module-list-data">
					<tr class=''>
						<td width="150" class="module-title"><{t}>consignee_company<{/t}>：</td>
						<td colspan='3'><input type='text'
							class='input_text consignee_company checkchar'
							value="<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_company}><{/if}>"
							name='consignee[consignee_company]' id='consignee_company' /> <span
							class="msg">*</span></td>
					</tr>
					<tr class=''>
						<td class="module-title"><{t}>consignee_state<{/t}>：</td>
						<td width='480'><input type='text'
							placeholder='如果国家没有州应不填写'
							class='input_text consignee_state consignee_province checkchar1'
							value="<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_province}><{/if}>"
							name='consignee[consignee_province]' id='consignee_province' /> <span
							class="msg"></span></td>
						<td width="150" class="module-title"><{t}>consignee_name<{/t}>：</td>
						<td><input type='text' class='input_text consignee_name checkchar3'
							value="<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_name}><{/if}>"
							name='consignee[consignee_name]' id='consignee_name' /> <span
							class="msg">*</span></td>
					</tr>
					<tr class=''>
						<td class="module-title"><{t}>consignee_city<{/t}>：</td>
						<td><input type='text' class='input_text consignee_city checkchar1'
							value="<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_city}><{/if}>"
							name='consignee[consignee_city]' id='consignee_city' /> <span
							class="msg">*</span></td>
						<td class="module-title"><{t}>consignee_phone<{/t}>：</td>
						<td><input type='text' class='input_text consignee_telephone order_phone'
							value='<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_telephone}><{/if}>'
							name='consignee[consignee_telephone]' id=consignee_telephone /> <span
							class="msg">*</span></td>
					</tr>
					<tr class=''>
						<td class="module-title"><{t}>consignee_zip<{/t}>：</td>
						<td><input type='text' class='input_text consignee_postcode'
							value='<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_postcode}><{/if}>'
							name='consignee[consignee_postcode]' id='consignee_postcode' /> <span
							class="msg">*</span></td>
						<td class="module-title"><{t}>consignee_email<{/t}>：</td>
						<td><input type='text' class='input_text consignee_email'
							value='<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_email}><{/if}>'
							name='consignee[consignee_email]' id='consignee_email' /> <span
							class="msg">*</span></td>
					</tr>
					<tr>
					    <td class="module-title"><{t}>收件人手机<{/t}>：</td>
						<td><input type='text' class='input_text order_phone'
							value='<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_mobile}><{/if}>'
							name='consignee[consignee_mobile]' id='consignee_mobile' /> <span
							class="msg"></span></td>
						<td class="module-title"><{t}>地址1<{/t}>：</td>
						<td colspan="1"><input type='text'
							class='input_text consignee_street checkchar2' style='width: 66%;'
							value="<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_street}><{/if}>"
							name='consignee[consignee_street]' id='consignee_street' /> <span
							class="msg">*</span>
						    <!--<span class="module-title"><{t}>收件人门牌号<{/t}>：</span>-->
						    <input type='hidden' class='input_text doorplate'
							value='<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_doorplate}><{/if}>'
							name='consignee[consignee_doorplate]' id='consignee_doorplate' />
						</td>
						  
							
					</tr>
					<tr>
						<td class="module-title"><{t}>地址2<{/t}>：</td>
						<td colspan="1"><input type='text'
							class='input_text consignee_street2 checkchar2' style='width: 90%;'
							value="<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_street2}><{/if}>"
							name='consignee[consignee_street2]' id='consignee_street2' />  <span
							class="msg"></span> </td>
						<td class="module-title"><{t}>地址3<{/t}>：</td>
						<td><input type='text' class='input_text consignee_street3 checkchar2'
							style='width: 90%;'
							value="<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_street3}><{/if}>"
							name='consignee[consignee_street3]' id='consignee_street3' /> <span
							class="msg"></span></td>
					</tr>
					<tr class=''>
						<td class="module-title"><{t}>证件类型<{/t}>：</td>
						<td colspan="3"><select
							name="consignee[consignee_certificatetype]"
							class="input_select consignee_certificatetype"
							id='consignee_certificatetype'
							default='<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_certificatetype}><{/if}>'>
								<{foreach from=$certificates name=s item=s key=k}>
								<option value="<{$s.certificate_type}>"><{$s.certificate_type_enname}>(<{$s.certificate_type_cnname}>)</option>
								<{/foreach}>
						</select> <span class="msg"></span> &nbsp;&nbsp;&nbsp;&nbsp;
							<{t}>号码<{/t}>： <input type='text'
							class='input_text consignee_certificatecode'
							value='<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_certificatecode}><{/if}>'
							name='consignee[consignee_certificatecode]'
							id='consignee_certificatecode' /> <span class="msg">*</span>
							&nbsp;&nbsp;&nbsp;&nbsp; <{t}>有效期<{/t}>： <input type='text'
							class='input_text datepicker consignee_credentials_period'
							value='<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_credentials_period}><{/if}>'
							name='consignee[consignee_credentials_period]'
							id='consignee_credentials_period' /> <span class="msg"></span></td>
					</tr>
					<tr class=''>
						<td class="module-title"><{t}>买家ID<{/t}>：</td>
						<td colspan="3"><input type='text' class='input_text buyer_id'
							value='<{if isset($order)}><{$order.buyer_id}><{/if}>'
							name='order[buyer_id]' id='buyer_id' /> <span class="msg">*</span>
						</td>
					</tr>
					<tr class=''>
						<td class="module-title"><{t}>包裹申报种类<{/t}>：</td>
						<td colspan="3"><select
							default="<{if isset($order)}><{$order.mail_cargo_type}><{/if}>"
							name="order[mail_cargo_type]"
							class="input_select mail_cargo_type" id='mail_cargo_type'>
								<option value='' class='ALL'><{t}>-select-<{/t}></option>
								<{foreach from=$mailCargoTypes name=s item=s key=k}>
								<option value="<{$s.mail_cargo_code}>"><{$s.mail_cargo_enname}>(<{$s.mail_cargo_cnname}>)</option>
								<{/foreach}>
						</select> <span class="msg">*</span></td>
					</tr>
				</tbody>
			</table>
			<h2
				style='padding: 35px 0 0 0; margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC; line-height: 30px;'>
				3、<span class='invoice_wrap'><{t}>海关申报信息<{/t}></span><span
					class="msg">*</span> <a href='javascript:;'
					style='font-weight: normal; font-size: 12px; padding: 0 10px; display: none;'><{t}>选择品名<{/t}></a>
				<span style='font-weight: normal; font-size: 12px;'>
					<!-- 注意：如果是Fedex联邦产品，请在备注中注明‘材质’和‘用途’，以便相关 -->
				</span>
			</h2>
			<table cellspacing="0" cellpadding="0" width="100%" border="0"
				class="table-module">
				<tbody>
					<tr class="table-module-title">
						<td width="165"><{t}>英文品名<{/t}></td>
						<td width="165"><{t}>中文品名<{/t}></td>
						<td width="60"><{t}>数量<{/t}></td>
						<td width="60"><{t}>单位<{/t}></td>
						<td width="60"><{t}>单价<{/t}>($)</td>
						<td width="50"><{t}>总价<{/t}></td>
						<td width="70"><{t}>重量<{/t}>(kg)</td>
						<td width="40"><{t}>总重<{/t}></td>
						<td width="70"><{t}>SKU<{/t}></td>
						<td width="70"><{t}>海关协制编号<{/t}></td>
						<td width="70"><{t}>配货信息<{/t}></td>
						<td width="70"><{t}>销售地址<{/t}></td>
						<td><{t}>操作<{/t}></td>
					</tr>
				</tbody>
				<tbody id="products" class="table-module-list-data">
					<{if $invoice}> <{foreach from=$invoice name=i item=i}>
					<tr class="table-module-b1">
						<td><input type='text' class='input_text invoice_enname'
							name='invoice[invoice_enname][]' value='<{$i.invoice_enname}>'> <span
							class="msg">*</span></td>
						<td><input type='text' class='input_text invoice_cnname'
							name='invoice[invoice_cnname][]' value='<{$i.invoice_cnname}>'> <span
							class="msg">*</span></td>
						<td><input type='text' class='input_text quantity'
							name='invoice[invoice_quantity][]'
							value='<{$i.invoice_quantity}>'> <span class="msg">*</span></td>
						<td><select default="<{if $i.unit_code}><{$i.unit_code}><{else}>PCS<{/if}>" name="invoice[unit_code][]" class="input_select unit_code">
								<{foreach from=$units name=s item=s key=k}>
								<option value="<{$s.unit_code}>"><{$s.unit_enname}>(<{$s.unit_cnname}>)</option>
								<{/foreach}>
							</select><span class="msg"></span></td>
						<td><input type='text' class='input_text invoice_unitcharge'
							name='invoice[invoice_unitcharge][]'
							value='<{$i.invoice_unitcharge}>'> <span class="msg">*</span></td>
						<td class=''><span class='total invoice_totalcharge'><{$i.invoice_totalcharge}></span><span
							class="msg"></span></td>
							
							<!--  -->
							<td><input type='text' class='input_text weight'
							name='invoice[invoice_weight][]' value='<{$i.invoice_weight}>'><span style="display:inline;color:red"> * </span>
						    </td>
						    <td class='totalWeight'><span class='totalWeight invoice_totalWeight'><{$i.invoice_totalWeight}></span><span class="msg"></span>
						    </td>
							<!--  -->
							
						<td><input type='text' class='input_text sku'
							name='invoice[sku][]' value='<{$i.sku}>'> <span class="msg"></span>
						</td>
						<td><input type='text' class='input_text hs_code'
							name='invoice[hs_code][]' value='<{$i.hs_code}>'> <span
							class="msg"></span></td>
						<td><input type='text' class='input_text invoice_note'
							name='invoice[invoice_note][]' value='<{$i.invoice_note}>'> <span
							class="msg"></span></td>
						<td><input type='text' class='input_text invoice_url'
							name='invoice[invoice_url][]' value='<{$i.invoice_url}>'> <span
							class="msg"></span></td>
						<td><a href='javascript:;' class='delInvoiceBtn'><{t}>delete<{/t}></a>
						</td>
					</tr>
					<{/foreach}> <{else}>
					<tr class="table-module-b1">
						<td><input type='text' class='input_text invoice_enname'
							name='invoice[invoice_enname][]'> <span class="msg">*</span></td>
						<td><input type='text' class='input_text invoice_cnname'
							name='invoice[invoice_cnname][]' value=''> <span class="msg">*</span>
						</td>
						<td><input type='text' class='input_text quantity'
							name='invoice[invoice_quantity][]'> <span class="msg">*</span></td>
						<td><select default="PCE" name="invoice[unit_code][]"
							class="input_select unit_code"> <{foreach from=$units name=s
								item=s key=k}>
								<option value="<{$s.unit_code}>"><{$s.unit_enname}>(<{$s.unit_cnname}>)</option>
								<{/foreach}>
						</select> <span class="msg"></span></td>
						<td><input type='text' class='input_text invoice_unitcharge'
							name='invoice[invoice_unitcharge][]'> <span class="msg">*</span>
						</td>
						<td class='total'><span class='total invoice_totalcharge'>0</span><span
							class="msg"></span></td>
							
							<!--  -->
							<td><input type='text' class='input_text weight'
							name='invoice[invoice_weight][]'><span class="msg">*</span>
						    </td>
						    <td class='totalWeight'><span class='totalWeight invoice_totalWeight' >0</span><span class="msg"></span>
						    </td>
							<!--  -->
						
						<td><input type='text' class='input_text sku'
							name='invoice[sku][]' value='<{$i.sku}>'> <span class="msg"></span>
						</td>
						<td><input type='text' class='input_text hs_code'
							name='invoice[hs_code][]'> <span class="msg"></span></td>
						<td><input type='text' class='input_text invoice_note'
							name='invoice[invoice_note][]' value=''> <span class="msg"></span>
						</td>
						<td><input type='text' class='input_text invoice_url'
							name='invoice[invoice_url][]' value=''> <span class="msg"></span>
						</td>
						<td><a href='javascript:;' class='delInvoiceBtn'><{t}>delete<{/t}></a>
						</td>
					</tr>
					<{/if}>
				</tbody>
			</table>
			<div>
				<a href='javascript:;'
					style='font-weight: normal; font-size: 12px; line-height: 35px;'
					class='addInvoiceBtn'><{t}>点击添加<{/t}></a>
			</div>
			<h2
				style='padding: 35px 0 0 0; margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC;'>
				4、<span class='submiter_wrap'><{t}>发件人信息<{/t}></span><span
					class="msg">*</span> <a href='javascript:;'
					style='font-weight: normal; font-size: 12px; padding: 0 10px;'
					onclick='getSubmiter();'><{t}>刷新<{/t}></a>
			</h2>
			<div style='line-height: 22px;' id='submiter_wrap'></div>
			<input type="hidden" name='order[order_id]'
				value='<{if isset($order)&&$order.order_id}><{$order.order_id}><{/if}>'
				id='order_id'>
			<div
				style="width: 100%; text-align: center; margin-top: 15px; padding-top: 5px; border-top: 1px dashed #CCCCCC;">

				<input type='button' value='<{t}>提交预报<{/t}>'
					class='baseBtn submitBtn orderSubmitVerifyBtn orderSubmitBtn'
					status='P' style="padding: 5px 30px; margin: 5px;" /> <{if
				!$smarty.get.cpy}> <{/if}> <input type='button'
					value='<{t}>保存草稿<{/t}>'
					class='baseBtn  orderSubmitDraftBtn orderSubmitBtn' status='D'
					style="padding: 5px 30px; margin: 5px;" />
			</div>
		</form>
	</div>
</div>


