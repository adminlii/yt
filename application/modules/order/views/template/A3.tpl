<!-- 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
         -->
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <script type="text/javascript" src="/js/jquery-1.8.3.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/css/label/bgd3.css?20140611">
    <link rel="stylesheet" type="text/css" href="/css/label/table2.css?20140611">
    <link rel="stylesheet" type="text/css" href="/css/label/styleBaoguan.css?20140611">
    <title>中国邮政EMS标签打印,建议使用10x10或75x80或80x90的标签纸进行打印</title>
</head>
<body>
<style>
    <!--
    body {
        margin: 0px; padding:0px;text-align:center;
    }
    .wrap{margin:0 auto;width:75mm;}
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
<div class='wrap'>
<{foreach from=$result name=o item=o}>
<{$data=$o.data}>
<{if !$smarty.foreach.o.first}>
<div style="page-break-after: always;height:0px;clear:both;">&nbsp;</div>
<{/if}>
<!-- 地址标签 -->
<table cellpadding="0" cellspacing="0" class="tabBq">
	<!-- 新增标题 -->
	<tr>
		<td style="font-size: 7px; border: solid #000 1px;border-bottom:#000 solid 0px;height: 0.8cm;" colspan="2" >
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td style="width: 33%;" align="center"><img src="/images/label/post.bmp" style="width:72px;height:20px;"/></td>
					<td style="width: 34%;" align="center"><b style="font-size: 9px">航空<br/>Small packet<br/>BY AIR<br /></b></td>
					<td style="width: 33%;" align="center"><b style="font-size: 16px"><{$data.shipper_consignee.consignee_countrycode}>&nbsp;&nbsp;<{$data.shipper_consignee.consignee_countryname_cn}></b></td>
				</tr>
			</table>
		</td>
	</tr>
	<!-- 新增协议客户 -->
	<tr>
		<td style="font-size: 10px; border: solid #000 1px;height: 0.8cm ;" colspan="2" >
			协议客户:<span style="font-family: 黑体;"><{$customer_name}></span>
		</td>
	</tr>
	
	<tr>
		<td valign="top" rowspan="2" style="border-left:#000 solid 1px; border-bottom:#000 solid 1px; width: 6.5cm;height: 1.2cm;font-size: 10px;padding: 0px; ">
			<table cellspacing="0" cellpadding="0" border="0" style="width: 6.5cm;text-align: left;">
				<tr>
					<td style="width: 1.2cm;" valign="top"><b style="font-size:12px;">From:</b>
					</td>
					<td style="width: 5.3cm;font-size: 10px">
						<{$data.shipper_consignee.shipper_street}> <br>
                        <{$data.shipper_consignee.shipper_city}>&nbsp;<{$data.shipper_consignee.shipper_province}><br/>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="height:0.4cm;">
					<table style="width: 100%;height: 100%; font-size:12px;" cellspacing="0" cellpadding="0">
					<tr>
						<td width="40%"><b style="font-size:12px;">Zip:</b>
							 <{$data.shipper_consignee.shipper_postcode}>
						</td>
						<td width="60%"><b style="font-size:12px;">Tel:</b>
								 <{$data.shipper_consignee.shipper_telephone}>
						</td>
					</tr>
					</table>
					</td>
				</tr>
			</table>
		</td>
		<td align="center" style="width:1cm;height:0.4cm; border-bottom: 1px #000 solid;border-left: 1px #000 solid;border-right: 1px #000 solid;">zone</td>
	</tr>
	
	<tr>
		<td align="center" style=" height:0.4cm; border-left: 1px #000 solid;border-bottom: 1px #000 solid;border-right: 1px #000 solid;">&nbsp;<b><{$data.shipper_consignee.consignee_areacode}></b></td>
	</tr>
	
	<tr>
		<td valign="top" colspan="2" style="border-left: solid #000 1px;border-right: solid #000 1px;  width: 7.5cm; padding: 0; height:1.8cm;font-size:11px;" align="left">
			<table width="100%" height="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td valign="top" style="font-weight:bold;width:0.9cm">
							<b>To:</b>
						</td>
						<td valign="top" style="width:6.5cm;font-size:11px;">
                            <{$data.shipper_consignee.consignee_street}><br/>                           
                            <{$data.shipper_consignee.consignee_city}>&nbsp;<{$data.shipper_consignee.consignee_province}><br/>
                            <{$data.shipper_consignee.consignee_countryname_en}>(<{$data.shipper_consignee.consignee_countryname_cn}>)
						</td>
						
					</tr>
					<tr>
						<td colspan="2">
						<table  style="width: 100%;height: 100%; font-size:12px;" cellspacing="0" cellpadding="0">
						<tr>
							<td width="40%"><b>Zip:</b>
							 <{$data.shipper_consignee.consignee_postcode}>
		                    </td>
							<td width="60%"><b>Tel:</b><{$data.shipper_consignee.consignee_telephone}></td>
						</tr>
						</table>
						</td>
					</tr>
			</table>
		</td>
	</tr>
	<!-- 新加退件单位 -->
	<tr>
		<td style="font-size: 10px; border: solid #000 1px; height:0.5cm;" colspan="2">
			退件单位:<span style="font-family: 黑体;"><{$return_address}></span>
		</td>
	</tr>
	
	<tr>
		<td style="font-size: 12px;border-left: 1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; height:1.4cm;" colspan="2" align="center" >
			<div style="width:100%;height:30px;overflow:hidden;margin-bottom:3px;">
				<img src="/default/index/barcode1/code/<{$data.order.shipper_hawbcode}>"/>
			</div>
			<div style="width:100%;">
				<b><{$data.order.shipper_hawbcode}></b>
			</div>
			
		</td>
	</tr>
	
	<tr>
		<td style="font-size: 9px;border-left: 1px solid #000;line-height:0.5cm; border-right:1px solid #000; border-bottom:1px solid #000; height:0.5cm;" colspan="2" align="left" >
			 【<{$data.csi_customer.customer_code}>】&nbsp;Ref No: <{$data.order.shipper_hawbcode}>
		</td>
	</tr>
	
	<tr>
		<td style="font-size:10px; height:0.4cm;" colspan="2" align="left" >
			<{$label_desc}>
		</td>
	</tr>
	<!-- SKU * 数量
	<tr>
        <td style="font-size: 7px; border: solid #000 1px;" colspan="2">
			<div>
				<{foreach from=$data.orderProduct key=key item=val}>
					<{$val.product_barcode}> X <{$val.op_quantity}>&nbsp;&nbsp;
				<{/foreach}>
			</div>
		</td>
	</tr>
	 -->
	 
</table>

<!-- 分页 -->
<div style="page-break-after: always;height:0px;"></div>
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
    <{assign var=num value='0'}>
    <{foreach from=$data.invoice key=key item=val}>
	<{if $num<=2}>
    <tr>
    	<{$num=$num+1}>
    	<td id="value" style="height: 16px;">
            <div none="1" name="value"><{$val.invoice_enname}></div>
    			
    	</td>
       	<td>
       		
       	</td>
        <td>
        	<div><{$val.invoice_totalcharge}></div>
        </td>
    </tr>
    <{/if}>
    <{/foreach}>
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
      		<div name="mm"><{$data.order.declared_value}></div>
      	</td>
   </tr>
      <tr>
    <td colspan="3" class="th">
    我保证上述申报准确无误，本函件内未装寄法律或邮政和海关规章禁止寄递的任何危险物品<br />
    I,the undersigned,whose name and address are given on the item,
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
</div>
</body>
</html>