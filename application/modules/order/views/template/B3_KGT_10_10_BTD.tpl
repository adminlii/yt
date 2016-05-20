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
    <title>WMS</title>
</head>
<body>
<style>
    <!--
    body {
    	font-family:黑体;
        margin: 0px; padding:0px;
    }
	.tabBq {
	    border: 1px dashed #cccccc;
	    table-layout: fixed;
	    width: 9.8cm;
	    word-wrap: break-word;
	}
	#bgd{
		width:9.8cm
	}

	#bgd .part1 {
	    width: 98mm;
	}
	#bgd .part2 {
		width: 98mm;
	}
	#bgd .part3 {
	    width: 98mm;
	}	
	#bgd .part4 {
	    width: 98mm;
	}	
	#bgd .table td{
		font-size: 10px;
		padding-left: 1px;
		padding-right: 1px;
	}

	#bgd .table .th {
    	font-size: 10px;
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
<!-- 地址标签 -->
<table cellpadding="0" cellspacing="0" class="tabBq" style="height:1.8cm;">
	<!-- 新增标题 -->
	<tr>
		<td style="font-size: 7px; border: solid #000 1px;border-bottom:#000 solid 0px;height: 0.8cm;" colspan="2" >
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td style="width: 25%;" align="center"><img src="/images/label/post.bmp" style="width:72px;height:20px;"/></td>
					<td style="width: 45%;" align="center"><b style="font-size: 9px">航空<br/>Small packet<br/>BY AIR<br /></b></td>
					<td style="width: 35%;" align="center"><b style="font-size: 16px"><{$data.address.country.country_code}>&nbsp;&nbsp;<{$data.address.country.country_name}></b></td>
				</tr>
			</table>
		</td>
	</tr>
	<!-- 新增协议客户 -->
	<tr>
		<td style="font-size: 10px; border: solid #000 1px;height: 0.8cm ;" colspan="2" >
			协议客户:<span style="font-family: 黑体;">晋江通拓电子商务有限公司(35058202849000)</span>
		</td>
	</tr>
</table>
<table cellpadding="0" cellspacing="0" class="tabBq" style="height:6.7cm;">
	<tr>
		<td valign="top" colspan="2" style="border: solid #000 1px; width: 7.5cm;height: 1.2cm;font-size: 10px;padding: 0px; ">
			<table cellspacing="0" cellpadding="0" border="0" style="width: 7.5cm;text-align: left;">
				<tr>
					<td style="width: 1.2cm;" valign="top"><b style="font-size:12px;">From:</b>
					</td>
					<td style="width: 6.3cm;font-size: 10px">
						NO.7 TIANCHI ROAD PINGFANG <br>
						DISTRICT HAERBIN CITY HEILONGJIANG STATE CHINA
					</td>
				</tr>
				<tr>
					<td style="width: 1.2cm;" valign="top"><b style="font-size:12px;">Order:</b>
					</td>
					<td colspan="2" style="height:0.4cm;">
						<div style="width:100%;height:22px;overflow:hidden;margin-bottom:3px;">
							<img src="/default/index/barcode/code/<{$data.order.ref_tracking_number}>"/>
						</div>
						<div style="width:100%; padding-left: 30px;">
							<b><{$data.order.ref_tracking_number}></b>
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td valign="top" colspan="2" style="border-left: solid #000 1px;border-right: solid #000 1px;  width: 7.5cm; padding: 0; height:2.8cm;font-size:11px;" align="left">
			<table width="100%" height="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td valign="top" style="font-weight:bold;width:0.9cm">
							<b>To:</b>
						</td>
						<td valign="top" style="width:6.5cm;font-size:11px;">
                            <{$data.address.oab_firstname}>&nbsp;<{$data.address.oab_lastname}><br>
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
		                    <strong><{$data.address.country.country_name_en}></strong> <{$data.address.country.country_name}>
						</td>
						
					</tr>
					<tr>
						<td colspan="2">
						<table  style="width: 100%;height: 100%; font-size:12px;" cellspacing="0" cellpadding="0">
						<tr>
							<td width="30%"><b>Zip:</b>
							 <{$data.address.oab_postcode}>
		                    </td>
							<td width="70%"><b>Tel:</b><{$data.address.oab_phone}></td>
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
			退件单位:<span style="font-family: 黑体;">晋江市邮政局</span><span style="float: right;">BKGTKJ&nbsp;&nbsp;</span>
		</td>
	</tr>
	
	<tr>
		<td style="font-size: 12px;border-left: 1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; height:1.4cm;" colspan="2" align="center" >
			<div style="float: left;width: 10%;">
				<font style="font-size: 3em;">R</font>
			</div>
			<div style="float: left;width: 88%;">
				<div style="width:100%;height:30px;overflow:hidden;margin-bottom:3px;">
					<img src="/default/index/barcode/code/<{$data.ship.tracking_number}>"/>
				</div>
				<div style="width:100%;">
					<b><{$data.ship.tracking_number}></b>
				</div>
			</div>
		</td>
	</tr>
	
	<!-- SKU * 数量 -->
	<tr>
	    <td style="font-size: 8px; border-left: 1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000;" colspan="2">
	        <div style="padding: 1px 0px;">
	            <{foreach from=$data.orderProduct key=key item=val}>
	            	&nbsp;<{$val.product_barcode}> * <{$val.op_quantity}>&nbsp;&nbsp;
	            <{/foreach}>
	        </div>
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
  	<div style="width: 25mm;float: left;height: 14mm;font-size:10px">
  		邮件种类<br />
  		Category of item<br>
  		请在适当的内容前"√"
  	</div>
  	<div style="float: right;width: 59mm;border-left: solid 1px #000;">
  		<table style="width:100%; border-collapse:collapse;">
  			<tr>
  				<td style="border-bottom: solid #000 1px;border-right: solid #000 1px;width: 18px;"> 
  					<!--  
  					<input name="mctCode" class="inputcheckbox"  id="gift100027-140530-0005" onclick="selpeihuo('gift','100027-140530-0005',this);" type="checkbox" value="1" disabled="disabled"/>
  					-->
  					&nbsp;
			    </td>
  				<td style="border-bottom: solid #000 1px;border-right: solid #000 1px;"><label><font size="1">礼品 Gift</font></label></td>
  				<td style="border-bottom: solid #000 1px;border-right: solid #000 1px;width: 18px;">
  					<!-- 
  					<input name="mctCode" class="inputcheckbox" id="commercial100027-140530-0005" onclick="selpeihuo('commercial','100027-140530-0005',this);" type="checkbox" value="3" disabled="disabled"/>
  					 -->
  					 &nbsp;
  				</td>
  				<td style="border-bottom: solid #000 1px;"><label><font size="1">商品货样 Commercial sample</font></label></td>
  			</tr>
  			<tr>
  				<td style="border-right: solid #000 1px;">
  					<!-- 
  					<input name="mctCode"  class="inputcheckbox" id="documents100027-140530-0005" onclick="selpeihuo('documents','100027-140530-0005',this);" type="checkbox" value="2" disabled="disabled"/>
  					 -->
  					 &nbsp;
  				</td>
  				<td style="border-right: solid #000 1px;"><label><font size="1">文件 Document</font></label></td>
  				<td style="border-right: solid #000 1px;text-align: center;">
  					<!-- 
  					<input name="mctCode" class="inputcheckbox" id="tick100027-140530-0005" onclick="selpeihuo('tick','100027-140530-0005',this);" type="checkbox" checked="checked" value="5" disabled="disabled"/>
  					 -->
  					 <b>√</b>
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
		<{foreach from=$data.orderProductCategory key=key item=val}>
			<{$num=$num+1}>
			<{if $num<2}>
				<div none="1" name="value"><{$val.pc_name_en}></div>
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
  	<td style="height: 55px;">&nbsp;</td>
  	<td>&nbsp;</td>
  	<td>&nbsp;</td>
  </tr>
  <tr>
    <td class="th">
    	协调系统税则号列和货物原产国(只对商品邮件填写)<br />
    	HS tarifff number and country of origin of goods(For commercial items only)
    <td class="th">
    	总重量<br />Total Weight<br />(kg)
	</td>
    <td class="th">
    	总价值<br />Total Value<br/>
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
    <td colspan="3" class="th" style="font-size: 9px;">
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

</body>
</html>