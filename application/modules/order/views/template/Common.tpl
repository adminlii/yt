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
    .d1{
        width:5cm;height:1.2cm; text-algin:center;overflow:hidden;padding-top:10px;margin-left: 2px;
    }
    .d1_1{
        width:5cm;height:2cm; text-algin:center;overflow:hidden;
    }
    .d2{
        height:0.9cm;overflow:hidden;width:100%;text-align:center;
    }
    .d3{
        width:100%;text-align:center;overflow:hidden;height:17px;
    }
    .d4{
        width:100%;text-align:center;overflow:hidden;height:17px;
    }
    .d5{
        display:inline-block;*display:inline;zoom:1;width:1.2cm;text-align:right;overflow:hidden;height:17px;
    }
    .d6{
        display:inline-block;width:3.7cm;*display:inline;zoom:1;text-align:right;overflow:hidden;height:17px;
    }
    .i1{
        width:5cm;margin-left:2px;
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
<{foreach from=$result name=o item=o}>
<{$data=$o.data}>
<table cellpadding="0" cellspacing="0" class="tabBq" style='<{if !$smarty.foreach.result.last}>page-break-after:always<{/if}>'>
    <tr>
        <td valign="top" align="left" colspan="2"
            style="border: 0px; font-size: 8px; padding: 0; width: 2.5cm; font-style: italic">
            <!-- FROM: --> <b>Return Mail Address:</b> <br/>
            <{$data.sm.sm_return_address}>
        </td>
    </tr>
    <tr>
        <td valign="top" colspan="2"
            style="width: 7.5cm; padding: 0; height: 3.1cm" align="left">
            <div style="font-size: 14px; float: left; word-spacing: normal; width: 201px;">
                <b style="font-size: 14px">To:&nbsp;</b>
                <b style="font-size: 14px"><{$data.shipper_consignee.consignee_name}></b>
                <br/><b>Tel:&nbsp;</b><{$data.shipper_consignee.consignee_telephone}>
                <br/>
                <div class="address" style="font-weight:200;font-family: Arial;">
                    <{if $data.shipper_consignee.consignee_street!=''}><{$data.shipper_consignee.consignee_street}><br/><{/if}>
                    
                    <{if $data.shipper_consignee.consignee_city!=''}>
                    <{$data.shipper_consignee.consignee_city}>
                    <{/if}>
                    
                    <{if $data.shipper_consignee.consignee_postcode==''}>
                    &nbsp;<{$data.shipper_consignee.consignee_postcode}>
                    <{/if}>
                    <br/>
                        
                    <{if $data.shipper_consignee.consignee_province!=''}>
                    <{$data.shipper_consignee.consignee_province}>&nbsp;
                    <br/>
                    <{/if}>
                    
                    <{$data.shipper_consignee.consignee_countrycode}>&nbsp;
                </div>
                <br/>
                <strong> <{$data.shipper_consignee.country_name_en}></strong>&nbsp;
                <{$data.shipper_consignee.country_name}>
            </div>
            <div style="display:none;font-size: 10px; float: left; word-spacing: normal; text-align: right; width: 2cm">
                <div style="padding-top: 10px" align="right">
                    <table style="font-size: 8px; border-collapse: collapse; border: none; width: 1.5cm">
                        <tr>
                            <td style="border: solid #000 1px; font-size: 10px;" align="center"><b>zone</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: solid #000 1px; font-size: 20px;"
                                align="center"><b> <!-- <{$data.pick_area}> --> </b>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td style="border: 0px; width: 7.1cm;" colspan="2" align="left">
            <table cellpadding="1" cellspacing="1"
                   style="table-layout: fixed; border-bottom: 0; border-left: 0; border-right: 0; border-top: 0; font-size: 8px; width: 7.1cm; height: 2cm">
                <tr>

                    <td colspan="2"
                        style="border: solid #000 1px; width: 7.1cm; padding-left: 3px"
                        align="center">
                        <table cellpadding="0" cellspacing="0">

                            <tr>
                                <td valign="bottom" align="center" style="font-size: 10px">
                                   &nbsp;
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="center" style="font-size: 13px">
                                    <b><{$data.order.server_hawbcode}></b>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" align="center">
                                    <div style="width:100%;height:39px;overflow:hidden;margin-bottom:3px;">
                                    <img src="/default/index/barcode/code/<{$data.order.shipper_hawbcode}>"
                                        />
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="border: solid #000 1px; font-size: 8px" colspan="2">
                        <div style="float:left;width:39%;text-align:left;">&nbsp;<{$data.order.product_code}></div>
                        <div style="float:right;width:60%;text-align:right;">Ref No: <{$data.order.shipper_hawbcode}>&nbsp;</div>
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 7px; border: solid #000 1px;" colspan="2">
                        <div>
                        &nbsp;<{foreach from=$data.invoice key=key item=val}>
                        <{$val.invoice_enname}> X <{$val.invoice_quantity}>&nbsp;&nbsp;
                        <{/foreach}>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 6px; border: 0px" colspan="2">
                        <div>

                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<{/foreach}>
</body>
</html>