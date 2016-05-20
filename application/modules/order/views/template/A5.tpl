<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <script type="text/javascript" src="/js/jquery-1.8.3.min.js"></script>
    <meta http-equiv="x-ua-compatible" content="ie=7"/>
    <link href="/css/label/bgd.css" rel="stylesheet" type="text/css">
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
        padding-left:5px;
    }

    .tabBq {
        border: 1px dashed #cccccc;
        height: 8.5cm;
        width: 7.5cm;
        word-wrap: break-word;
        table-layout: fixed;
    }

    -->
</style>
<script type="text/javascript">
$(function(){
    $(".inputcheckbox").each(function(k,v){
        if( $(this).val()=='5'){
            $(this).attr("checked",true);
        }else{
            $(this).attr("checked",false);
        }
    });
});
</script>
<!-- 小包挂号-->
<table cellpadding="0" cellspacing="0" class="tabBq">
    <tr>
        <td valign="top" align="left" colspan="2"
            style="border: 0px; font-size: 8px; padding: 0; width: 2.5cm; font-style: italic">
            <!-- FROM: --> <b>Return Mail Address:</b> <br/>
            <{$data.sm.sm_return_address}>
        </td>
    </tr>
    <tr>
        <td valign="top" colspan="2" style="width: 7.5cm; padding: 0; height: 3.0cm" align="left">
            <div style="font-size: 14px; float: left; word-spacing: normal; width: 201px;">
                <b style="font-size: 14px">To:&nbsp;</b>
                <b style="font-size: 14px"><{$data.address.oab_firstname}>&nbsp;<{$data.address.oab_lastname}></b>
                <br/>
                <b style="font-size: 14px">Tel:&nbsp;</b>
                <b style="font-size: 14px"><{$data.address.oab_phone}></b>
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

            </div>
            <div style="font-size: 10px; float: left; word-spacing: normal; text-align: right; width: 2cm">
                <div style="padding-top: 10px" align="right">
                    <table
                            style="font-size: 8px; border-collapse: collapse; border: none; width: 1.5cm">
                        <tr>
                            <td style="border: solid #000 1px; font-size: 10px;"
                                align="center"><b>zone</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: solid #000 1px; font-size: 20px;"
                                align="center"><b> <{$data.zone_area}> </b>
                            </td>
                        </tr>

                    </table>
                    <table width="90" border="0"
                           style="border: none; font-size: 4px; width: 1.5cm"
                           cellpadding="0" cellspacing="0">
                        <tr>
                            <!-- 保险 -->

                            <!-- 退件 -->
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
                        <table cellpadding="0" cellspacing="0" style="float:left;width:18%;">
                            <tr>
                                <td style="width:1cm;" align="center">&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="font-size:30px;display:none;" align="center"><b>R</b></td>
                            </tr>
                         </table>
                        <table cellpadding="0" cellspacing="0" style="float:left;width:80%;">
                            <tr>
                                <td valign="bottom" align="center" style="font-size: 10px">
                                    <b></b>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="center" style="font-size: 13px">
                                    <b><{$data.ship.tracking_number}> </b>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" align="center">
                                    <div style="width:100%;height:36px;overflow:hidden;margin-bottom:3px;">
                                        <img src="/default/index/barcode/code/<{$data.ship.tracking_number}>"/>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="border: solid #000 1px; font-size: 8px" colspan="2">
                        &nbsp;<{$data.sm.sm_short_name}>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ref No: <{$data.order.order_code}>
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
                    <td style="font-size: 6px; border: 0px" colspan="2">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<div id="bgd" style="clear:both;page-break-after:always;padding-top:4px;">
    <div class="part1">
        <div class="part0_0">
            <ul>
                <li class="part0_0_1">报关签条</li>
                <li class="part0_0_2">CUSTOMS DECLARATION</li>
                <li class="part0_0_3">可以径行开拆</li>
                <li class="part0_0_4">May be opened officially</li>
            </ul>
        </div>
        <div class="part0_1">
            <ul>
                <li class="part0_1_0">&nbsp;</li>
                <li class="part0_1_1">邮&nbsp;&nbsp;&nbsp;2113</li>
                <li class="part0_1_2">&nbsp;&nbsp;&nbsp;&nbsp;CN22</li>
            </ul>
        </div>
    </div>
    <div class="part2">
        <span class="part2_1">
            <!--<img style="height:0.6cm" src="/images/label/china_post.jpg">-->
            <p style="font-size:11px;font-weight:bold;">中国邮政</p><p style="font-size: 9px;line-height: 9px;">CHINA POST</p>
        </span>
    <span class="part2_2"><p>请先阅读背面的注意事项</p><p class="part2_3">See instructions on the back</p>
</span>
    </div>
    <div class="part3">
        <div class="part3_left">
            <ul>
                <li>邮件种类<br>Category of item<br>(在适当的文件前划"√")<br>Tick as appropriate</li>
            </ul>
        </div>
        <div class="part3_right">
            <ul>
                <li style="width:11px;border-top:0px;line-height:23px;">&nbsp;</li>
                <li style="width:54px;text-align:center;border:0px;border-bottom:1px solid #000;">礼品<br>Gift</li>
                <li style="width:10px;border-top:0px;line-height:23px;">&nbsp;</li>
                <li style="width:100px;text-align:center;border:0px;border-bottom:1px solid #000;">商品货样<br>Commercial Sample</li>
            </ul>
            <ul>
                <li style="width:11px;border-top:0px;border-bottom:0px;line-height:23px; ">&nbsp;</li>
                <li style="width:54px;text-align:center;border:0px;">文件<br>Documents</li>
                <li style="width:10px;border-top:0px;border-bottom:0px;line-height:23px;">&nbsp;√</li>
                <li style="width:100px;text-align:center;border:0px;">其他<br>Other</li>
            </ul>
        </div>
    </div>
    <div class="part4">
        <table class="table" cellpadding="0" cellspacing="0" border="0" width="100%">
            <tbody>
            <tr>
                <td width="60%">内件详细名称与数量<br>Quantity and detailed description of contents</td>
                <td width="24%">重量(千克)<br>Weight(kg)</td>
                <td width="16%">价值<br>Value</td>
            </tr>
            <tr>
                <td id="value" style="height:100%;">
                    <{assign var=num value='0'}>
                    <{foreach from=$data.orderProductCategory key=key item=val}>
                        <{$num=$num+1}>
                        <{if $num<2}>
                            <!-- 
			                	<div none="1" name="value"><{$val.ig_name_en}></div>
			                -->
			                <div none="1" name="value"><{$val.declared_name}></div>
                        <{/if}>
                    <{/foreach}>
                </td>
                <td>
                    <{$data.ship.so_weight}>
                </td>
                <td>
                    <div name="mm"><{$data.ship.declared_value}></div>
                </td>
            </tr>
            <tr>
                <td class="th">
                    协调系统税则号列和货物原产国<br>(只对商品邮件填写)
                    <br>
                    HS tariff number and country of origin<br>
                    of goods(For commercial items only)
                </td>
                <td class="th">总重量<br>Total Weight<br>
                </td>
                <td class="th">总价值<br>Total Value
                </td>
            </tr>

            <tr>
                <td>&nbsp;</td>
                <td>
                    <{$data.ship.so_weight}>
                </td>
                <td>
                    <div name="mm"><{$data.ship.declared_value}></div>
                </td>
            </tr>
            </tbody>
        </table>
        <div style="font-size: 8px;line-height: 10px;padding:2px;">
            我保证上述申报准确无误,本函件内未寄法律或邮政和海关规章禁止寄递的任何危险物品
            <br>
            I,the undersigned,certify that the particulars given in this declaration are correct and
            this item dose not contain any dangerous article or articles
            prohibited by legislation or by postal or customs regulations.
            <br>
            寄件人签字 Sender`s signature
        </div>
    </div>
</div>


</body>
</html>