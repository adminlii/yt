<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta http-equiv="x-ua-compatible" content="ie=7"/>
    <title>WMS-10*10</title>
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
        height:8.5cm;
        width: 10cm;
        word-wrap:break-word;
        table-layout: fixed;
    }
    -->
</style>
<table cellpadding="0" cellspacing="0" class="tabBq">
    <tr>
        <td valign="top" align="left" colspan="2"
            style="border: 0px; font-size: 8px; padding: 0; width: 6cm; font-style: italic">
            <!-- FROM: --> <b>Return Mail Address:</b> <br/>
            <{$data.sm.sm_return_address}>
        </td>
    </tr>
    <tr>
        <td valign="top" colspan="2"
            style="width: 7.5cm; padding: 0; height: 3.1cm" align="left">
            <div style="font-size: 14px; float: left; word-spacing: normal; width: 250px;">
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
                    	<{$data.address.oab_state}>&nbsp;<{$data.address.oab_postcode}><br/>
                    <{/if}>
                    <{if $data.address.postcode!=''}>
	                    <{$data.address.postcode}>
	                	<br/>
                    <{/if}>
                    <!-- 
                    <{$data.address.country.country_name_en}>&nbsp;
                     -->
                </div>
                <strong> <{$data.address.country.country_name_en}></strong>&nbsp;
                <{$data.address.country.country_name}>
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
                                align="center"><b> <{$data.pick_area}> </b>
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
                   style="table-layout: fixed; border-bottom: 0; border-left: 0; border-right: 0; border-top: 0; font-size: 8px; width: 9.5cm; height: 2cm">
                <tr>

                    <td colspan="2"
                        style="border: solid #000 1px; width: 9cm; padding-left: 3px"
                        align="center">
                        <table cellpadding="0" cellspacing="0">

                            <tr>
                                <td valign="bottom" align="center" style="font-size: 10px">
                                   &nbsp;
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="center" style="font-size: 13px">
                                    <b><{$data.ship.tracking_number}></b>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" align="center">
                                    <div style="width:100%;height:39px;overflow:hidden;margin-bottom:3px;">
                                    <img src="/default/index/barcode/code/<{$data.ship.tracking_number}>" />
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="border: solid #000 1px; font-size: 8px" colspan="2">
                        <div style="float:left;width:39%;text-align:left;">&nbsp;<{$data.sm.sm_short_name}></div>
                        <div style="float:right;width:60%;text-align:right;">Ref No: <{$data.order.order_code}>&nbsp;</div>
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 12px; border: solid #000 0px;" colspan="2">
                        <{foreach from=$data.orderProduct key=key item=val}>
                        <div style="width: 4.6cm;float: left;font-weight: bold;">
	                        &nbsp;<{$val.product_barcode}> X <{$val.op_quantity}>
                        </div>
                        <{/foreach}>
                    </td>
                </tr>
                <tr>
                    <td style="border: 0px" colspan="2">
                        <div style="text-align: right;">
							<font style="font-weight: bold;font-size: 10px;"><{$data.order.print_sort}></font>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>