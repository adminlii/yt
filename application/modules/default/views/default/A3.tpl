<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>表格</title>
<meta name="description" content="">
<meta name="keywords" content="">
<style>
*{padding: 0px;margin: 0px;font-style: normal;list-style-type: none;text-decoration: none;font-family: "微软雅黑";font-size: 28px;border:0 none;line-height: 32px;color: #333; }
    .warpdiv{width: 660px;height:970px;page-break-inside:avoid;}
	table{border-left: 1px solid #333;border-top:1px solid #333;width: 660px;height:970px;}
    table img{width: 90%;display: block;margin:0 auto;}
	table td{border-right: 1px solid #333;border-bottom: 1px solid #333;padding: 0 15px;}
    table .bold td{font-weight: bold;text-align:center;}
    .codenumr{text-align:center;}
    table li{overflow: hidden;}
    .shop{float: left;width: 165px;text-align: right;}
    table li .text{overflow: hidden;}
    .txtRt{text-align: right;}
</style>	
</head>
<body>	
	<{foreach from=$data  name=o item=o}>
	
		<{foreach from=$o.codeArr  name=ol item=ol}>
		<div class="warpdiv">
		<table cellspacing="0" cellspadding="0" border="0">
    	<tr class="bold">
    		<td  colspan="2">FBA头程<{$o.consignee_countrycode}>/ 空运</td>
    	</tr>
    	<tr class="bold">
    		<td>Country / 寄达国</td>
    		<td>Facility Code / 分拣代码</td>
    	</tr>
    	<tr class="bold">
    		<td><{$o.country_three_code}> <{$o.country_cnname}></td>
    		<td>FYYXGA</td>
    	</tr>
    	<tr>
    		<td colspan="2" class="codenumr">
    		<img src="/default/index/barcode1/code/<{$ol}>" alt="" />
    		<p><{$ol}></p>
    		</td>
    	</tr>
        <tr>
            <td colspan="2">
                <ul>
                    <li>
                        <p class="shop">SHOP TO：</p>
                        <div class="text">
                            <p><{$o.storage}></p>
                            <p><{$o.consignee_street}></p>
                            <p><{$o.consignee_city}> <{$o.consignee_province}> <{$o.consignee_postcode}></p>
                        </div>
                    </li>
                    <li><p class="shop">Tel：</p> null</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td class="txtRt" colspan="2"><{$o.create_date}></td>
        </tr>
        <tr>
            <td colspan="2"> &nbsp;</td>
        </tr>
    </table>	
	</div>	
		<{/foreach}>	
	<{/foreach}>	
</body>
</html>			