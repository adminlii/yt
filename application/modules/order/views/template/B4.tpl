<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta http-equiv="x-ua-compatible" content="ie=7"/>
    <link rel="stylesheet" type="text/css" href="/css/label/styleBaoguan.css?20140611">
    <link rel="stylesheet" type="text/css" href="/css/label/bgd3.css?20140611">
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
<{if !$smarty.foreach.o.first}>
<div style="page-break-after: always;height:0px;clear:both;">&nbsp;</div>
<{/if}>
<table cellpadding="0" cellspacing="0" class="tabBq" style=''>
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
                <b style="font-size: 14px"><{$data.shipper_consignee.consignee_name}>&nbsp;<{$data.shipper_consignee.consignee_name}></b>
                <br/><b>Tel:&nbsp;</b><{$data.shipper_consignee.consignee_telephone}>
                <br/>
                <div class="address" style="font-weight:200;font-family: Arial;">
                    <{if $data.shipper_consignee.consignee_street!=''}><{$data.shipper_consignee.consignee_street}><br/><{/if}>
                   
                    <{if $data.shipper_consignee.consignee_city!=''}>
                        <{$data.shipper_consignee.consignee_city}>
                        <{if $data.shipper_consignee.consignee_province==''}>
                        &nbsp;<{$data.shipper_consignee.consignee_province}>
                        <{/if}>
                        <br/>
                    <{/if}>
                    <{if $data.shipper_consignee.consignee_province!=''}>
                        <{$data.shipper_consignee.consignee_province}>
                        <br/>
                    <{/if}>
                    <{if $data.shipper_consignee.consignee_postcode!=''}>
                        <{$data.shipper_consignee.consignee_postcode}>
                        <br/>
                    <{/if}>
                    <{$data.shipper_consignee.consignee_countrycode}>
                &nbsp;
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
<!-- 分页 -->
<div style="page-break-after: always;height:0px;">&nbsp;</div>
<!-- 报关单 -->
<div id="bgd" >
  <div class="part1">
	<table width="100%" height="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td style="width: 30%;" align="center"><img src="/images/label/post.bmp" style="width:72px;height:17px;"/></td>
			<td style="width: 40%;" align="center"><b style="font-size: 9px">报关签条</b><br/><b style="font-size: 7px">CUSTOMS DECLARATION</b></td>
			<td style="width: 30%;" align="center"><b style="font-size: 9px">
				邮&nbsp;&nbsp;&nbsp;&nbsp;2113<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CN22
			</td>
		</tr>
	</table>
  </div>
  <div class="part2" style="line-height: 4mm;">
      	可以径行开拆&nbsp;&nbsp;May be opened officially
  </div>
  <div class="part3">
  	<div style="width: 14mm;float: left;height: 14mm;font-size:8px">
  		邮件种类<br />
  		Category of item<br>
  		请在适当的内容前"√"
  	</div>
  	<div style="float: right;width: 59mm;border-left: solid 1px #000;">
  		<table style="width:100%; border-collapse:collapse;">
  			<tr>
  				<td style="border-bottom: solid #000 1px;border-right: solid #000 1px;"> 
  					<input name="mctCode" class="inputcheckbox"  id="gift100027-140530-0005" onclick="selpeihuo('gift','100027-140530-0005',this);" type="checkbox" value="1" disabled="disabled"/>
			    </td>
  				<td style="border-bottom: solid #000 1px;border-right: solid #000 1px;"><label><font size="1">礼品 Gift</font></label></td>
  				<td style="border-bottom: solid #000 1px;border-right: solid #000 1px;">
  					<input name="mctCode" class="inputcheckbox" id="commercial100027-140530-0005" onclick="selpeihuo('commercial','100027-140530-0005',this);" type="checkbox" value="3" disabled="disabled"/>
  				</td>
  				<td style="border-bottom: solid #000 1px;"><label><font size="1">商品货样 Commercial sample</font></label></td>
  			</tr>
  			<tr>
  				<td style="border-right: solid #000 1px;">
  					<input name="mctCode"  class="inputcheckbox" id="documents100027-140530-0005" onclick="selpeihuo('documents','100027-140530-0005',this);" type="checkbox" value="2" disabled="disabled"/>
  				</td>
  				<td style="border-right: solid #000 1px;"><label><font size="1">文件 Document</font></label></td>
  				<td style="border-right: solid #000 1px;">
  					<input name="mctCode" class="inputcheckbox" id="tick100027-140530-0005" onclick="selpeihuo('tick','100027-140530-0005',this);" type="checkbox" checked="checked" value="5" disabled="disabled"/>
  				</td>
  				<td><label><font size="1">其他 Other</font></label></td>
  			</tr>
  		</table>
  	</div>
    <div class="clear"></div>
  </div>
  <div class="part4">
<table class="table" width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="64%" >内件详细名称和数量<br/>Quantity and detailed description of contents</td>
    <td width="18%">重量(千克)<br/>Weight<br/>(kg)</td>
    <td width="18%">价值<br />Value</td>
  </tr>
  <tr>
	<td id="value" style="height: 16px;">
		<{assign var=num value='0'}>
		<{foreach from=$data.invoice key=key item=val}>
			<{$num=$num+1}>
			<{if $num<2}>
				<div none="1" name="value"><{$val.invoice_enname}></div>
			<{/if}>
		<{/foreach}>
	</td>
   	<td>
   		<!-- 
   		<{$data.ship.so_weight}>
   		 -->
   	</td>
    <td>
    	<div><{$data.ship.declared_value}></div>
    </td>
  </tr>
  <tr>
  	<td style="height: 10px;">&nbsp;</td>
  	<td>&nbsp;</td>
  	<td>&nbsp;</td>
  </tr>
  <tr>
  	<td style="height: 10px;">&nbsp;</td>
  	<td>&nbsp;</td>
  	<td>&nbsp;</td>
  </tr>
  <tr>
    <td  class="th">
    	协调系统税则号列和货物原产国(只对商品邮件填写)<br />
    	HS tarifff number and country of origin of goods(For commercial items only)
    <td class="th">总重量(千克)<br />Total Weight(kg)<br />
	</td>
    <td class="th">总价值<br />Total Value<br/>
     
    </td>
  </tr>
  
   <tr>
		<td>&nbsp;</td>
       	<td class="th">
       		<!-- 
			<{$data.ship.so_weight}>
			 -->
		</td>
      	<td class="th">
      		<div name="mm"><{$data.ship.declared_value}></div>
      	</td>
   </tr>
      <tr>
    <td colspan="3" class="th">
    我保证上述申报准确无误，本函件内未装寄法律或邮政和海关规章禁止寄递的任何危险物品<br />
    I,the undersigned,whose name and address are given on the itme,
certify that particulars given in this declaration are correct and
that this item dose not contain any dangerous article or artices
prohibited by legislation or by postal or customs regulations
Date and sender`s signature<br />
	寄件人签字 Sender's signature_________
	</td>
    </tr>
</table>
</div>
</div>
<{/foreach}>


</body>
</html>