<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="x-ua-compatible" content="ie=7" />
<title>仓配管理系统</title>
</head>
<body>
	<style>
*{padding:0;margin:0;}
#wrap {
	width: 7.8cm;
	height: 9cm;
	font: 12px/150% Arial, Helvetica, sans-serif, '宋体';
}

table {
	border-collapse: collapse;
}

table td {
	padding: 0;
}

#table_2 td{
	text-align: center;
}
</style>
	<div id='wrap'>

		<table id="head_a" border='0' width='100%'>
			<tr>
				<td colspan="2"><img src="/default/index/barcode/code/<{$orderCode}>" style="height: 62px; float: left; padding-left: 5px;"/></td>
			</tr>
			<tr>
				<td>收件人：<{$address.oab_lastname}><{$address.oab_firstname}></td>
				<td>国家：<{$country.country_name_en}></td>
			</tr>
			<tr>
				<td colspan="2">邮箱：<{$address.oab_email}></td>
			</tr>
		</table>
		<table   border='1' id = "table_2"  width='100%' style='margin-top:2px;'>
			<tr>
				<td>货位</td>
				<td>SKU</td>
				<td>名称</td>
				<td>数量</td>
			</tr>
			<{foreach from=$product item=val key=key}>
		    	<tr>
					<td><{$val.lc_code}></td>
					<td><{$val.product_barcode}></td>
					<td><{$val.product_title_en}></td>
					<td><{$val.pd_quantity}></td>
				</tr>
	        <{/foreach}>
		</table>
	</div>
</body>
</html>