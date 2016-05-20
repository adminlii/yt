<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta http-equiv="x-ua-compatible" content="ie=7"/>
    <title>WMS</title>
</head>
<body>
<style>
    <!--
    body {
        font-family: Arial;
        font-size: 12px;
        margin: 0;
        text-align: center;
    }
    .tabBq{
        border:1px dashed #cccccc;
        height:7.5cm;
        width: 7.5cm;
        word-wrap:break-word;
        table-layout: fixed;
    }
    -->
</style>
<table cellpadding="0" cellspacing="0" class="tabBq">
    <tr>
        <td style="color:red;font-size:16px">
            没有找到此运输方式标签模板.
        </td>
    </tr>
    <tr>
        <td valign="top" align="center" style="font-size: 13px">
            <b>运输方式代码: <{$data.order.sm_code}>&nbsp;</b>
        </td>
    </tr>
    <tr>
        <td  align="center">
            <div style="width:100%;margin-bottom:3px;">
                <img src="/default/index/barcode/code/<{$data.order.order_code}>" />
            </div>
        </td>
    </tr>
    <tr>
        <td style="font-size: 7px; border: solid #000 1px;">
            <div>
                &nbsp;<{foreach from=$data.orderProduct key=key item=val}>
                <{$val.product_barcode}> X <{$val.op_quantity}>&nbsp;&nbsp;
                <{/foreach}>
            </div>
        </td>
    </tr>
</table>
</body>
</html>