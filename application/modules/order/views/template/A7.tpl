<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta http-equiv="x-ua-compatible" content="ie=7"/>
    <title>A7</title>
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
<!-- 联邮通平邮 -->
<table class="tabBq" border="0" cellpadding="0" cellspacing="0">
    <tbody>
    <tr>
        <td style="border: 0px; font-size: 7px; width: 2.4cm; padding-top: 0cm;" align="left" valign="top">
            <b>Return Mail Address:</b><br>
            <{$data.sm.sm_return_address}>
        </td>
        <td style="width: 5.0cm; height: 2.0cm; padding: 0; font-size: 25px;" align="right" valign="top">
            <img src="/images/label/ppi_eng_24_30x106.jpg"
                 style="width:4.9cm ;height: 1.6cm;">
        </td>
    </tr>
    <tr>
        <td colspan="2"
            style="border: 0px; border-top: 0px; border-top-style: dashed; width: 7.5cm; padding: 0; height: 3.5cm"
            align="left" valign="top">
            <div style="font-size: 15px; float: left; font-family:Arial, Helvetica, sans-serif; word-spacing: normal; width: 6cm;">
                <div>
                    <b style="font-size: 15px; font-family:Arial, Helvetica, sans-serif;"> Attn:&nbsp;<{$data.address.oab_firstname}>&nbsp;<{$data.address.oab_lastname}> </b>
                </div>
                <b>Adds:&nbsp;</b>
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
                &nbsp;
                <br>
                    <strong><{$data.address.country.country_name_en}></strong>
            </div>
            <div style="float: left; width: 1.3cm; font-size: 25px; text-align: right">
                <b> <{$data.address.country.country_code}></b>
            </div>
        </td>
    </tr>
    <tr>
        <td style="border: 0" colspan="2">
            <table style="border-bottom: 0; border-left: 0; border-right: 0; border-top: 0; font-size: 8px; width: 7.3cm; table-layout: fixed;"
                   border="0" cellpadding="1" cellspacing="1">
                <tbody>
                <tr>
                    <td style="text-align: center;" colspan="2">
                        <table cellpadding="0" cellspacing="0">
                            <tbody>
                            <tr>
                                <td style="font-size: 10px" align="center" valign="bottom">
                                </td>
                            </tr>

                            <tr>
                                <td style="font-size: 10px;width:7.1cm; text-align: center;" valign="top">
                                    <{$data.ship.tracking_number}>
                                </td>
                            </tr>
                            <tr>
                                <td align="center">
                                    <div style="width:100%;height:30px;overflow:hidden;margin-bottom:3px;">
                                        <img src="/default/index/barcode/code/<{$data.ship.tracking_number}>"/>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-left: 3px;border: solid #000 1px;" align="left">
                        <{$data.sm.sm_short_name}>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; <b>[</b>&nbsp;755268496&nbsp;<b>]</b>&nbsp;Ref No: <{$data.order.order_code}>
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 7px; border: solid #000 1px;" colspan="2">
                        <div style="float: left">
                                &nbsp;<{foreach from=$data.orderProduct key=key item=val}>
                                <{$val.product_barcode}> X <{$val.op_quantity}>&nbsp;&nbsp;
                                <{/foreach}>
                        </div>
                        <div style="float: right;">
                          <!--date-->
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 6px; border: 0px" colspan="2" align="left">
                        <div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>

</body>
</html>