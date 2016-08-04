<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>打印形式发票</title>
<link href="/css/invoice.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<{foreach from=$orderArr name=o item=o}>
	 <{if $o.order.product_code eq "G_DHL"}>
	 <style>
*{margin:0; border:0; padding:0; font-family:"微软雅黑","宋体"; font-size:14px;}
.bt{border:2px solid black;}
.bt1{border-top:2px solid black;border-right:2px solid black;}
.bt2{border-left:2px solid black;}
.bt3{border-bottom:2px solid black;}
.bt4{border-right:2px solid black;}
.div1{width:500px;height:200px;float:left;}
.div2{width:500px;height:200px;float:left;}
.div3{width:500px;height:260px;float:left;}
.div4{height:352px;width:1002px}
.div5{height:50px;}
.clear{clear:both;width:0px;height:0px;}
#warp{margin:auto;width:1006px;page-break-inside:avoid;}
p{height:20px;line-height:20px;}
.p1{height:30px;line-height:30px;}
.comment{height:48px;}
.invoice{height:30px;text-align:center;}
.total{margin-left:675px;width:325px;height:40px;}
.span1{line-height: 30px;height: 30px;display: inline-block;width:497px}
</style>
</style>
<{assign var="count" value=2}>
<{section name=loop loop=$count}>
<if>
 <{if $smarty.section.loop.index eq 0 }>
 	<{if $o.order.invoice_type eq 1 }>
 	<{include file='order/views/invoice-label/dhl-label-pdf.tpl'}>
  	<{/if}>
 <h1 style="page-break-after: always; clear: both;background:red;"></h1>
 <{else}>
 	<{if $o.order.invoice_type eq 2 }>
 	<{include file='order/views/invoice-label/dhl-label-pdf1.tpl'}>
 	<{/if}>
 <{if !$smarty.foreach.o.last}> 
	<h1 style="page-break-after: always; clear: both;background:red;"></h1>
 <{/if}>
 <{/if}> 
<{/section}> 
		 
	 <{else}>
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
 	<{/if}>
	<{/foreach}>

</body>
</html>