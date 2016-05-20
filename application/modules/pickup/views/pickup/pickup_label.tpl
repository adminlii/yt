<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>面单打印</title>
<link href="/css/pickup_label.css" rel="stylesheet" type="text/css" />
</head>
<body> 
	<{foreach from=$orderArr name=o item=o key=k}>
	<div class="content">
		<div class="div1">			
			<img style="float:left;width:100px;margin:10px 0 0 10px;" alt="" src="/images/base/zjslogo.png">
			<span style='float:left;font-size:15px;'>(国内请扫描此标签)</span>
			<span style='float:right;padding-right:20px;font-size:40px;'><{$og.address_sign}></span>
		</div>
		<div class="div2">
			<div class='div_in div2_in fl div2_in1'><{$og.state}></div>
			<div class='div_in div2_in fl div2_in2'><{$og.city}></div>
			<div class='div_in div2_in fl div2_in3'><{$og.district}></div>
			<div class='clear div2_zone' >
				&nbsp;&nbsp;&nbsp;&nbsp;<{$og.street}>
			</div>
			<div class='div_in  fl div2_sj pl8' style='font-weight:bold;font-size:22px;line-height:30px;'><{$og.contact}></div>
			<div class='div_in  fl div2_contact'>
			<p style='font-weight:bold;font-size:20px;'><{$og.phone}></p>			
			<!-- <p>0755-01424</p> -->
			</div>
				 
		</div>
		
		<div class="div3">
			<div class="div3_barcode"><img src='/default/index/barcode2/?code=<{$o.track_number}>' style='padding-top:2mm;width:55mm;height:13mm;margin: auto;'></div>
			<div class="div3_barcode_msg">
				<p style='padding-top:2mm;'>打印单位:<!-- A 1323 --></p>
				<p>受理单位:&nbsp;<!-- A --></p>
				<p>声明价值: &nbsp;&nbsp;0.00</p>
			</div>
			<div class="clear" style='height:0px;line-height:0px;'></div>
			<div class="div3_01 fl">
				<p style='font-weight:bold;'><{$o.track_number}></p>
				<p style='font-weight:bold;'>客户:&nbsp;<{$kehu}></p>
				<p style=''>订单号: &nbsp;&nbsp;<{$o.pickup_order_id}></p>
				<p style='font-weight:bold;font-size:25px;line-height:35px;'>签收人: &nbsp;&nbsp;</p>

			</div>
			<div class="div3_02 fl">
				<p style='font-weight:bold;font-size:25px;line-height:40px;'>代收金额：</p>
				<p style='font-weight:bold;font-size:20px;line-height:35px;'>￥0.00</p>
				<p style='text-align:right;'>&nbsp;&nbsp;&nbsp;&nbsp;年 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;月 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;日&nbsp;</p> 
			</div>			 
			<p class='clear pl8'>重要提示: &nbsp;&nbsp;<{$tishi}></p>
		</div>
		 
		<div class="div4">
			 <div class="div4_01">
				 <p class='pl8'>宅急送</p>
				</div>
			<div class="div4_02">
				 <p class='pl8'><{$smarty.now|date_format:"%Y-%m-%d"}></p>
				
			</div>
		</div>
		 
		<div class="div5">
			 <div class="div5_01">
				<p class='fb' style='line-height:30px;font-size:22px;'><{$og.contact}></p>
				<p>品名：</p>
				</div>
			<div class="div5_02">
				<p class='fb' style='line-height:30px;font-size:22px;'><{$og.phone}></p>
				<p>订单号：<{$o.pickup_order_id}></p>
			</div>
			<p class='fb clear' style='font-size:16px;'><{$o.track_number}></p>
		</div>

	</div>	
	<{if !$smarty.foreach.o.last}> 
	<h1 style="page-break-after: always; clear: both;background:red;"></h1>
 	<{/if}>
 	<{/foreach}>
</body>
</html>