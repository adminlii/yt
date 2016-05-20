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

<!-- 新加坡小包挂号 -->
<table cellpadding="0" cellspacing="0" class="tabBq">
    <tr>
        <td valign="top" align="left" style="border: 0px; font-size: 8px; padding: 0; width: 2.5cm; font-style: italic">
            <b>Return Mail Address:</b> <br/>
            <{$data.sm.sm_return_address}>
        </td>
        <td align="right" valign="top" style="width: 4.9cm;TEXT-ALIGN: center;MARGIN-RIGHT: auto;MARGIN-LEFT: auto;">

            <div style="float:left;margin-top:0.8cm;margin-left:5px;">
                <font style="font-weight:bold;">A</font>
            </div>
            <img src="/images/label/sggh.jpg" style="width: 4cm; height: 2cm"/>
        </td>
    </tr>
    <tr>
        <td valign="top" colspan="2"
            style="border: 1px; border-top: 0px; border-top-style: dashed; width: 7.5cm; padding: 0; height: 2.9cm"
            align="left">
            <div style="font-size: 14px; float: left; word-spacing: normal; width: 210px">
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
            <div
                    style="font-size: 7px; float: left; word-spacing: normal; text-align: right; width: 1.7cm">
                <div style="padding-top: 5px" align="right">
                    <table
                            style="font-size: 7px; border-collapse: collapse; border: none; width: 1.5cm">
                        <tr>
                            <td
                                    style="border: solid #000 1px; font-size: 6px; line-height: 10px;"
                                    align="center"><b>AIR MAIL<br/> 航PAR AVION空</b>
                            </td>
                        </tr>

                        <tr>
                            <td style="border: solid #000 1px; font-size: 10px;"
                                align="center"><b>zone</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: solid #000 1px; font-size: 20px;"
                                align="center"><b> <{$data.pick_area}> </b>
                            </td>
                        </tr>

                    </table>

                    <div align="center">
                        <table width="90" border="0"
                               style="border: none; font-size: 4px; width: 1cm" cellpadding="0"
                               cellspacing="0">
                            <tr><!-- 保险 -->

                                <!-- 可否退件 -->

                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td style="border: 0px; width: 7.5cm" colspan="2" align="left">
            <table cellpadding="0" cellspacing="0"
                   style="table-layout: fixed; border-bottom: 0; border-left: 0; border-right: 0; border-top: 0; font-size: 8px;">
                <tr>
                    <td style="width:7.2cm; padding-left: 3px;padding-right:3px; border: solid #000 1px;"
                        align="center">
                        <table cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td width="15px;"></td>
                                <td style="width: 0.6cm; font-size: 18px;" align="center"><b>R</b></td>
                                <td style="font-size: 13px;" align="left">&nbsp;&nbsp;&nbsp;&nbsp;
                                    <b><{$data.ship.tracking_number}> </b>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" colspan="3" style="border-top:1px solid;padding-top: 2px;">
                                    <div style="width:100%;height:30px;overflow:hidden;margin-bottom:3px;">
                                        <img src="/default/index/barcode/code/<{$data.ship.tracking_number}>"/>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="border: solid #000 1px; font-size: 8px" colspan="2">
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
                <tr style="padding-top: 0px; margin-top: 0px;">
                    <td style="font-size: 6px; border: 0px; padding-top: 0px; margin-top: 0px;" colspan="2">
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