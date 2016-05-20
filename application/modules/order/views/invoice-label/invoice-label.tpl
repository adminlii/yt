<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>打印形式发票</title>
<link href="/css/invoice.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<{foreach from=$orderArr name=o item=o}>
	<div class="content">
		<div style="float: right;">
			<!-- 如果是fedex产品打印fedex的形式发票格式 -->
			<img src="/default/index/barcode/code/<{$o.order.server_hawbcode}>">
		</div>
		<div class="title"><{$o.shipper_consignee.shipper_company}></div>
		<!-- <div class="title">INDUSTRY CO LTD</div> -->

		<div class="b_from">
			<div class="b_from_content3_title">FROM:</div>
			<div class="b_from_content3_1"><{$o.shipper_consignee.shipper_street}><br/><{$o.shipper_consignee.shipper_city}><br/><{$o.shipper_consignee.shipper_province}>&nbsp;</div>
			<div class="b_from_content3">&nbsp;</div>
		</div>
		<div class="b_from">
			<div class="b_from_title_to">TO Messrs:</div>
			<div class="b_from_content_to"><{$o.shipper_consignee.consignee_name}> &nbsp;</div>
		</div>
		<div class="b_from">

			<div class="b_from_content3">
				<div class="b_from_content3_title">Company:</div>
				<div class="b_from_content3_1">&nbsp;<{$o.shipper_consignee.consignee_company}></div>
				<div class="b_from_content3_title">Add:</div>
				<div class="b_from_content3_1">
					<{$o.shipper_consignee.consignee_street}> <{$o.shipper_consignee.consignee_city}> <{$o.shipper_consignee.consignee_province}>&nbsp; <br /> 
					<{$o.shipper_consignee.consignee_country_name}>
				</div>
			</div>

			<div class="b_from_content3">
				<div class="b_from_content3_title">Phone:</div>
				<div class="b_from_content3_1"><{$o.shipper_consignee.consignee_telephone}>&nbsp;</div>
				<div class="b_from_content3_title">Fax:</div>
				<div class="b_from_content3_1"><{$o.shipper_consignee.consignee_fax}>&nbsp;</div>
				<div class="b_from_content3_title">Zip_code:</div>
				<div class="b_from_content3_1"><{$o.shipper_consignee.consignee_postcode}> &nbsp;</div>
				<div class="b_from_content3_title">Air Waybill NO.</div>
				<div class="b_from_content3_1">&nbsp;</div>
				<div class="b_from_content3_2"><{$o.order.server_hawbcode}> &nbsp;</div>
			</div>
		</div>

	</div>

	<div class="content">
		<div class="title">PRO-FORMA INVOICE</div>
		<div>
			<br />
		</div>
		<table border="0" align="center" cellpadding="0" cellspacing="0"
			class="table">
			<tr>
				<td class="t_td">Commodities and Specifications <br />&nbsp;
				</td>
				<td class="t_td">Quantities <br />&nbsp;
				</td>
				<td class="t_td">Declare unit<br />&nbsp;
				</td>
				<td class="t_td">Unit Price <br /> (USD)
				</td>
				<td class="t_td1">Amount<br /> (USD)
				</td>
			</tr>
			<{foreach from=$o.invoice name=iv item=iv}>
			<tr>
				<td class="t_td1"><{$iv.invoice_enname}></td>
				<td class="t_td1"><{$iv.invoice_quantity}></td>
				<td class="t_td1"><{$iv.unit_code}></td>
				<td class="t_td1"><{$iv.invoice_unitcharge}></td>
				<td class="t_td1"><{$iv.invoice_totalcharge}></td>
			</tr>
			<{/foreach}>
			<tr>
				<td class="t_td2">&nbsp;</td>
				<td class="t_td2">&nbsp;</td>
				<td class="t_td2">&nbsp;</td>
				<td class="t_td2">Total Amount(USD):</td>
				<td class="t_td2"><{$o.order.invoice_total}></td>
			</tr>
		</table>

	</div>

	<div class="content">
		<div class="b_from1">
			<div class="b_from_title1">REASON FOR EXPORT:</div>
		</div>
		<div class="b_from2">SAMPLE OF NO COMMERCIAL VALUE</div>
		<div class="b_from3">
			I/we hereby certify that the information on this invoice is true and
			correct<br /> and that the contents of this shipment are as stated
			above.
		</div>

		<div class="b_from">
			<div class="b_from_content3">
				SHIPPER'S SIGNATURE & WILL<br /><{$o.shipper_consignee.shipper_name}>
			</div>
			<div class="b_from_content3">
				DATE<br /><{$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}>
			</div>
		</div>

	</div>
	<{if !$smarty.foreach.o.last}> 
	<h1 style="page-break-after: always; clear: both;background:red;"></h1>
 	<{/if}>
	<{/foreach}>

</body>
</html>