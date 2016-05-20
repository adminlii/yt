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

    .tabBq {
        border: 1px dashed #cccccc;
        height: 7.5cm;
        width: 7.5cm;
        word-wrap: break-word;
        table-layout: fixed;
    }

    -->
</style>

<!-- 香港小包挂号 -->
<table cellpadding="0" cellspacing="0" class="tabBq">
<tr>
    <td valign="top" align="left"
        style="border: 0px; font-size: 8px; padding: 3px; width: 4.5cm; font-style: italic">
        <b>Return Mail Address:</b> <br/>
        <{$data.sm.sm_return_address}>
    </td>
    <td align="right" valign="top" style="border: 0px; width: 2.5cm">
        <table cellpadding="0" cellspacing="0"
               style="font-size: 8px; border-collapse: collapse; border: none">
            <tr>
                <td align="center" style="border: solid #000 1px;">E</td>
                <td align="center" valign="middle"
                    style="font-size: 8px; border: solid #000 1px">POSTAGE <br/>
                    PAID <br/> HONG KONG
                </td>
                <td align="center" style="font-size: 8px; border: solid #000 1px;">
                    PERMIT <br/> NO. <br/> <b>T0332</b>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td valign="top"
        style="border: 0px; border-top: 0px; border-top-style: dashed; width: 4.5cm; padding: 3px; height: 3.1cm"
        align="left">
        <div style="font-size: 14px; float: left; word-spacing: normal; width: 4.5cm;">
            <b style="font-size: 14px">To:&nbsp;</b>
            <b style="font-size: 14px"><{$data.address.oab_firstname}>&nbsp;<{$data.address.oab_lastname}></b>
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
    <td align="right" valign="top"
        style="border: 0px; width: 2.5cm; padding-top: 8px; padding-top: 0cm">
        <table width="90" border="1"
               style="border: dashed; border: 1px; font-size: 8px; width: 2.5cm"
               cellpadding="0" cellspacing="0">
            <tr>
                <td align="center" bordercolor="#D4D0C8" style="border: 0px"
                    colspan="2">投保易网邮保险服务 <br/> (iMail insurance) <br/>
                    <input type="checkbox" onclick="return false"/>Y
                    <input type="checkbox" checked="checked" onclick="return false"/>N
                </td>
            </tr>
        </table>

        <table width="55" border="0"
               style="border-collapse: collapse; border: none;">
            <tr>
                <td style="border: solid #000 1px; font-size: 10px;"
                    align="center"><b>zone</b>
                </td>
            </tr>
            <tr>
                <td style="border: solid #000 1px; font-size: 20px;"
                    align="center"><b><{$data.pick_area}> </b>
                </td>
            </tr>
        </table>

        <table width="55" border="0"
               style="border-collapse: collapse; border: none; font-size: 4px;"
               cellpadding="0" cellspacing="0">
            <tr>


            </tr>
        </table>
    </td>
</tr>
<tr>
<td style="border: 0px; height: 3cm; width: 7cm" colspan="2">
<table border="1" cellpadding="1" cellspacing="1"
       style="border-bottom: 0; border-left: 0; border-right: 0; border-top: 0; font-size: 8px; width: 6.8; table-layout: fixed;">
<tr>
    <td align="center" style="width: 1.15cm; font-size: 6px;border: solid #000 1px;"><b>Signature
            required</b>
    </td>
    <td rowspan="3" style="width: 6.0cm; padding: 0; border: solid #000 1px;" align="center">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td rowspan="2" style="font-size: 30px;" width="5px"
                    valign="bottom" align="right"><b>R</b>
                </td>
                <td align="left" valign="bottom" style="font-size: 10px"><b>HONG
                        KONG</b>
                </td>
            </tr>

            <tr>
                <td align="left" valign="top" style="font-size: 13px">
                    <b><{$data.ship.tracking_number}></b>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="left">
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
    <td align="center" style="font-size: 6px;border: solid #000 1px;"><b>BY AIR MAIL<br/>
            PAR AVION <br/>航空 </b>
    </td>
</tr>
<tr>
<td style="font-size: 6px; border: solid #000 1px;" align="center">
    <{$data.sm.sm_name_cn}>
</td>
</tr>
<tr>
    <td colspan="2" style="font-size: 10px; border: solid #000 1px;">
        &nbsp;<{$data.sm.sm_short_name}>&nbsp;&nbsp;&nbsp;&nbsp;<b>[</b>&nbsp;755268496&nbsp;<b>]</b>&nbsp;Ref No: <{$data.order.order_code}>
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