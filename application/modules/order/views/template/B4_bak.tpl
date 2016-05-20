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

    .d1 {
        width: 5cm;
        height: 1.2cm;
        text-algin: center;
        overflow: hidden;
        padding-top: 10px;
        margin-left: 2px;
    }

    .d1_1 {
        width: 5cm;
        height: 2cm;
        text-algin: center;
        overflow: hidden;
    }

    .d2 {
        height: 0.9cm;
        overflow: hidden;
        width: 100%;
        text-align: center;
    }

    .d3 {
        width: 100%;
        text-align: center;
        overflow: hidden;
        height: 17px;
    }

    .d4 {
        width: 100%;
        text-align: center;
        overflow: hidden;
        height: 17px;
    }

    .d5 {
        display: inline-block;
        *display: inline;
        zoom: 1;
        width: 1.2cm;
        text-align: right;
        overflow: hidden;
        height: 17px;
    }

    .d6 {
        display: inline-block;
        width: 3.7cm;
        *display: inline;
        zoom: 1;
        text-align: right;
        overflow: hidden;
        height: 17px;
    }

    .i1 {
        width: 5cm;
        margin-left: 2px;
    }

    .tabBq {
        border: 1px dashed #cccccc;
        height: 7.5cm;
        width: 7.5cm;
        word-wrap: break-word;
        table-layout: fixed;
    }
    -->
</style>

<!-- 香港小包平邮 -->
<table cellpadding="0" cellspacing="0" class="tabBq">
<tr>
    <td valign="top" align="left"
        style="border: 0px; font-size: 8px; padding: 3px; width: 5cm; font-style: italic;">
        <b>Return Mail Address:</b> <br/>
        <{$data.sm.sm_return_address}>
    </td>
    <td align="right" valign="top" style="border: 0px; width: 2cm">
        <table cellpadding="0" cellspacing="0"
               style="font-size: 8px; border-collapse: collapse; border: none;">
            <tr>
                <td align="center" style="border: solid #000 1px;width:4px">1</td>
                <td align="center" valign="middle"
                    style="font-size: 8px; border: solid #000 1px">POSTAGE <br/>
                    PAID <br/> HONG KONG
                </td>
                <td align="center" style="font-size: 8px; border: solid #000 1px;">
                    PERMIT <br/> NO. <br/> <b>4623</b>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td valign="top"
        style="border: 0px; border-top: 0px; border-top-style: dashed; width: 5cm; padding: 3px; height: 3.5cm; word-wrap: break-word"
        align="left">
        <div style="font-size: 14px; float: left; word-spacing: normal; width: 5cm; word-wrap: break-word;">
            <b style="font-size: 14px">To:&nbsp;</b>
            <b style="font-size: 14px"><{$data.address.oab_firstname}>&nbsp;<{$data.address.oab_lastname}></b>
            <br/><b>Tel:&nbsp;</b><{$data.address.oab_phone}>
            <br/>
            <div class="address" style="font-weight:200;font-family: Arial;">
                <{if $data.address.oab_street_address1!=''}><{$data.address.oab_street_address1}><br/><{/if}>
                <{if $data.address.oab_street_address2!=''}><{$data.address.oab_street_address2}><br/><{/if}>
                <{if $data.address.oab_city!=''}>
                <{$data.address.oab_city}>
                <{if $data.address.oab_state==''}>
                &nbsp;<{$data.address.oab_postcode}>
                <{/if}>
            <br/>
                <{/if}>
                <{if $data.address.oab_state!=''}>
                <{$data.address.oab_state}>&nbsp;<{$data.address.oab_postcode}>
            <br/>
                <{/if}>
                <{if $data.address.postcode!=''}>
                <{$data.address.postcode}>
            <br/>
                <{/if}>
                <{$data.address.country.country_name_en}>
            </div>
            &nbsp;
            <br/>
            <strong><{$data.address.country.country_name_en}></strong>
        </div>
    </td>
    <td align="right" valign="top" style="width: 2cm">
        <div align="right" style="padding-right: 1px; width: 2cm">
            <table border="0"
                   style="border-collapse: collapse; border: none; font-size: 4px; width: 1.5cm"
                   cellpadding="0" cellspacing="0">
            </table>
            <table border="0"
                   style="border-collapse: collapse; border: none; font-size: 4px; width: 1.5cm"
                   cellpadding="0" cellspacing="0">
                <tr>
                    <!-- 可否退件  -->
                </tr>
            </table>
        </div>
    </td>
</tr>
<tr>
<td style="border: 0px;height: 3cm;" colspan="2">
<table border="1" cellpadding="1" cellspacing="1"
       style="border-bottom: 0; border-left: 0; border-right: 0; border-top: 0; font-size: 8px; width: 7cm; table-layout: fixed;">
<tr>
    <td align="center" style="font-size: 8px; width: 1.6cm;border: solid #000 1px; line-height: 12px; height: 1.3cm"><b>BY
            AIR MAIL<br/> 航PAR AVION空</b>
    </td>
    <td rowspan="2" style="width: 5.5cm; padding: 0;border: solid #000 1px;" align="center">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td align="center" valign="bottom" style="font-size: 10px">
                    <b></b>
                </td>
            </tr>
            <tr>
                <td align="center" valign="top" style="font-size: 13px"><b>
                        <{$data.ship.tracking_number}> </b>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <div style="width:100%;height:36px;overflow:hidden;margin-bottom:3px;">
                        <img src="/default/index/barcode/code/<{$data.ship.tracking_number}>"
                            />
                    </div>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr>
<td align="center" style="width: 2cm; font-size: 8px;border: solid #000 1px;line-height: 14px">
<{$data.sm.sm_name_cn}>
</td>
</tr>
<tr>
    <td colspan="2" style="font-size: 10px; border: solid #000 1px;" align="left">&nbsp;<{$data.sm.sm_short_name}>&nbsp;&nbsp;&nbsp;&nbsp;<b>[</b>&nbsp;755268496&nbsp;<b>]</b>&nbsp;Ref No: <{$data.order.order_code}>
    </td>
</tr>
<tr>
    <td style="font-size: 7px; border: solid #000 1px;" colspan="2">
        <div>
            &nbsp;<{foreach from=$data.orderProduct key=key item=val}>
            <{$val.product_barcode}> X <{$val.op_quantity}>&nbsp;&nbsp;
            <{/foreach}>
        </div>
    </td>
</tr>
<tr>
    <td style="font-size: 6px; border: 0px" colspan="2" align="left">
        <div>
        </div>
    </td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>