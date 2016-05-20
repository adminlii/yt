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
    <title>WMS-HG</title>
</head>
<body>
<style>
    <!--
    body {
        margin: 0px; padding:0px;
    }
	.tabBq {
	    border: 1px dashed #cccccc;
	    table-layout: fixed;
	    width: 9.8cm;
	    word-wrap: break-word;
	}
    -->
.jd_table {
    border: medium none;
    border-collapse: collapse;
    float: right;
    height: auto;
    overflow: hidden;
	height: 55px;
}
.jd_table td {
    border: 1px solid #222222;
    font: 8px "verdana";
    height: auto;
    text-align: center;
}    
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
<table cellpadding="0" cellspacing="0" class="tabBq" style="height:1.65cm;">
	<!-- 新增标题 -->
	<tr>
		<td style="font-size: 9px; border: solid #000 1px;border-bottom:#000 solid 0px;" valign="top">
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td style="width: 55%;font-size: 11px;text-align: left;" align="center">
						<b>From :</b>
						<b><{$data.sm.sm_return_address}></b>
					</td>
					<td style="width: 45%;" align="center" valign="top">
						<table align="right" class="jd_table"  cellspacing="0" cellpadding="0">
							<tbody>
								<tr>
									<td colspan="3">
										<font style="font-size: 12px;font-weight: bold;">HK挂号</font>
									</td>
								</tr>
	                    		<tr>
			                        <td style="width: 20%;">
			                        	<font style="font-size: 12px;font-weight: bold;">E</font>
			                        </td>
									<td style="width: 58%;">POSTAGE PAID HONG KONG PORT PAYE</td>
									<td style="width: 22%;">
											PERMIT<br> NO.<br>T4330
									</td>
	                    		</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<table cellpadding="0" cellspacing="0" class="tabBq" style="height: 6.9cm;">
	<tr>
		<td valign="top"style="border-left: solid #000 1px; width: 75%; padding: 0; height:3.4cm;font-size:11px;" align="left">
			<table width="100%" height="100%" cellspacing="0" cellpadding="0" style="font-size:12px;font-weight: bold;">
					<tr>
						<td valign="top" style="font-weight:bold;width:0.9cm">
							<b>To:</b>
						</td>
						<td valign="top" style="width:6.5cm;font-size:12px;">
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
		<td style="border-right: solid #000 1px;width: 25%" valign="top">
			<table width="100%" height="100%" cellspacing="0" cellpadding="0" class="jd_table">
				<tbody>
					<tr>
						<td  style="font-weight:bold;height: 25px;">
							
						</td>
					</tr>
					<tr>
						<td valign="top" style="font-weight:bold;">
						Signature<br/>
						Required 
						</td>
					</tr>
					<tr>
						<td valign="top" style="font-weight:bold;">
						BY AIR MAIL<br/>
						航PAR AVION空 
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
	<tr>
		<td style="font-size: 12px;border: solid #000 1px; height:80px;" colspan="2" align="center" >
			<div style="width:100%;">
				<div style="width: 30%;float: left;font-weight: bold;font-size: 24px;">
					R
				</div>
				<div style="width: 70%;font-weight: bold;float: left;text-align: left;">
					HONG KONG<br/> 
					<{$data.ship.tracking_number}>
				</div>
			</div>
			<div style="width:100%;height:40px;overflow:hidden;margin-bottom:3px;border-top: solid #000 1px;">
				<img src="/default/index/barcode/code/<{$data.ship.tracking_number}>"/>
			</div>			
		</td>
	</tr>
	
	<tr>
		<td style="font-size: 9px;border-left: 1px solid #000;line-height:0.5cm; border-right:1px solid #000; border-bottom:1px solid #000; height:0.5cm;" colspan="2" align="left" >
			 &nbsp;Ref No: <{$data.order.order_code}>
		</td>
	</tr>
	
	<!-- SKU * 数量 -->
	<tr>
	    <td style="font-size: 12px; border: solid #000 1px;" colspan="2" valign="top">
	        <div>
	            &nbsp;<{foreach from=$data.orderProduct key=key item=val}>
	            <{$val.product_barcode}> * <{$val.op_quantity}>&nbsp;&nbsp;
	            <{/foreach}>
	        </div>
	    </td>
	</tr>
	
</table>

<!-- 分页 -->
<div style="page-break-after: always;height:0px;">&nbsp;</div>
<!-- 报关单 -->
<style>
#bgd{
	width: 9.8cm;
	height: 8.9cm;
}
#bgd .part1{
	width: 9.7cm;
	height: 1.4cm;
}
#bgd .part2{
	width: 9.7cm;
	height: 6mm;
}
#bgd .part3{
	width: 9.7cm;
}
#bgd .part4{
	width: 9.7cm;
}
#bgd .table {
    font-size: 10px;
}

#bgd .table .th {
    font-size: 10px;
    line-height: 9px;
}
</style>
<div id="bgd">
  <div class="part1">
	<table width="100%" height="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td style="width: 70%;" align="left">
				<b style="font-size: 12px">報關單</b><br/>
				<b style="font-size: 12px">CUSTOMS DECLARATION</b><br/>
			</td>
			<td style="width: 30%;" align="center">
				<b style="font-size: 11px">CN&nbsp;&nbsp;22<br />Pos 401G(4/08)</b>
			</td>
		</tr>
	</table>
  </div>
  <div class="part2" style="line-height: 6mm;font-size: 12px;">
      	本件得由海關開拆&nbsp;&nbsp;May be opened officially
  </div>
  <div class="part3">
  	<div style="width: 28mm;float: left;height: 14mm;font-size:11px">
  		郵件種類 Category of item<br>
  		請在適當的內容前"√"
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
  				<td style="border-bottom: solid #000 1px;border-right: solid #000 1px;"><label><font style="font-size: 11px;">禮物 Gift</font></label></td>
  				<td style="border-bottom: solid #000 1px;border-right: solid #000 1px;width: 18px;">
  					<!-- 
  					<input name="mctCode" class="inputcheckbox" id="commercial100027-140530-0005" onclick="selpeihuo('commercial','100027-140530-0005',this);" type="checkbox" value="3" disabled="disabled"/>
  					 -->
  					 &nbsp;
  				</td>
  				<td style="border-bottom: solid #000 1px;"><label><font style="font-size: 11px;">商用樣本 Commercial sample</font></label></td>
  			</tr>
  			<tr>
  				<td style="border-right: solid #000 1px;">
  					<!-- 
  					<input name="mctCode"  class="inputcheckbox" id="documents100027-140530-0005" onclick="selpeihuo('documents','100027-140530-0005',this);" type="checkbox" value="2" disabled="disabled"/>
  					 -->
  					 &nbsp;
  				</td>
  				<td style="border-right: solid #000 1px;"><label><font style="font-size: 11px;">文件 Document</font></label></td>
  				<td style="border-right: solid #000 1px;text-align: center;">
  					<!-- 
  					<input name="mctCode" class="inputcheckbox" id="tick100027-140530-0005" onclick="selpeihuo('tick','100027-140530-0005',this);" type="checkbox" checked="checked" value="5" disabled="disabled"/>
  					 -->
  					 <b>√</b>
  				</td>
  				<td><label><font style="font-size: 11px;">其他 Other</font></label></td>
  			</tr>
  		</table>
  	</div>
    <div class="clear"></div>
  </div>
  <div class="part4">
<table class="table" width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size: 11px;">
  <tr>
    <td width="62%" >(1)內載物品詳情及(2)數量<br/>
Quantity(2) and detailed description of contents(1)</td>
    <td width="18%">(3)重量(千克)<br/>Weight&nbsp;(kg)</td>
    <td width="18%">(5)價值<br />Value</td>
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
    <td  class="th" style="height: 12mm;">
    	只適用於商品 For commercial items only<br/>
		(7)協制編號及(8)物品原產地(如有)<br/>
		If known, HS tariff number(7) and country of origin of goods(8)
    <td class="th">(4)總重量(千克)<br />Total Weight (kg)<br />
	</td>
    <td class="th">(6)總共價值<br />Total Value<br/>
     
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
    <td colspan="3" class="th" style="font-size: 11px;height: 18mm;">
    在下面簽署，以證明此報關單上列資料全屬正確，及此郵件並不載有任何法例或郵政規例或海關條例所禁寄的危險物品。本人的姓名及地址已載於郵件上。
I, the undersigned, whose name and address are given on the item, certify that the particulars given in this declaration are correct and that this item does not contain any dangerous article or articles prohibited by legislation or by postal or customs regulations.
	 (15)日期及寄件人簽署 Date and sender's signature 
	</td>
    </tr>
</table>
</div>
</div>

</body>
</html>